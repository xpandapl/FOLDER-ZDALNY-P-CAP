<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use App\Models\Result;
use App\Models\User;
use App\Models\Competency;
use App\Models\Team;
use App\Models\CompetencyTeamValue;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResultsMail;
use App\Exports\ResultsExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Illuminate\Support\Facades\Log;
use App\Models\BlockDate; // dodaj na górze

use Carbon\Carbon;
use Illuminate\Support\Str;


class SelfAssessmentController extends Controller
{
    // Helper: pobierz aktywny cykl (bez cache'u - zawsze pobiera aktualny)
    private function activeCycleId(): ?int
    {
        return \App\Models\AssessmentCycle::where('is_active', true)->value('id');
    }

    // STEP0 landing
    public function startLanding()
    {
        $cycleId = $this->activeCycleId();
        $cycle = null;
        if ($cycleId) {
            $cycle = \App\Models\AssessmentCycle::find($cycleId);
        }
        return view('self-assessment.start', [
            'cycle' => $cycle,
        ]);
    }

    // Formularz weterana (kod dostępu) – na razie placeholder (logika kodów w następnym kroku)
    public function startVeteranForm()
    {
        $cycle = null;
        if ($id = $this->activeCycleId()) {
            $cycle = \App\Models\AssessmentCycle::find($id);
        }
        return view('self-assessment.veteran', [
            'cycle' => $cycle,
            'status' => session('veteran_status'),
        ]);
    }

    // Obsługa submit weterana – tymczasowo tylko walidacja pustego pola i redirect (prawdziwa weryfikacja w zadaniu nr 5)
    public function startVeteranSubmit(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'access_code' => 'required|string|max:60',
        ], [
            'access_code.required' => 'Podaj kod dostępu.'
        ]);

        $raw = trim($request->input('access_code'));
        $cycleId = $this->activeCycleId();
        if(!$cycleId){
            return redirect()->route('start.veteran.form')->withErrors('Brak aktywnego cyklu.');
        }

        // Hash IP (anonimizacja)
        $ip = $request->ip() ?: '0.0.0.0';
        $ipHash = hash('sha256', $ip);

        // Rate limiting attempts per IP per active cycle
        $attempt = \App\Models\EmployeeCodeAttempt::firstOrCreate([
            'cycle_id' => $cycleId,
            'ip_hash'  => $ipHash,
        ], [
            'attempts' => 0,
        ]);

        if($attempt->locked_until && now()->lt($attempt->locked_until)){
            $mins = $attempt->locked_until->diffInMinutes(now());
            return redirect()->route('start.veteran.form')->withErrors("Zbyt wiele prób. Spróbuj ponownie za {$mins} min.");
        }

        // Szukamy dopasowania po raw_last4 (przyspieszenie) i później verify hash
        $last4 = substr(preg_replace('/[^A-Za-z0-9]/','',$raw), -4);
        $query = \App\Models\EmployeeCycleAccessCode::where('cycle_id',$cycleId);
        if($last4){
            $query->where('raw_last4',$last4);
        }
        $candidates = $query->get();
        if($candidates->isEmpty()){
            // fallback: pełne skanowanie (kosztowne tylko przy bardzo dużej skali – akceptowalne tutaj)
            $candidates = \App\Models\EmployeeCycleAccessCode::where('cycle_id',$cycleId)->get();
        }

        $matched = null;
        foreach($candidates as $code){
            if(password_verify($raw, $code->code_hash)){
                $matched = $code; break;
            }
        }

        if(!$matched){
            $attempt->attempts += 1;
            // Lock after 3 attempts
            if($attempt->attempts >= 3){
                $attempt->locked_until = now()->addMinutes(15);
                $attempt->attempts = 0; // reset after lock window starts
            }
            $attempt->save();
            return redirect()->route('start.veteran.form')->withErrors('Nieprawidłowy kod lub wygasły.');
        }

        if($matched->expires_at && now()->gt($matched->expires_at)){
            return redirect()->route('start.veteran.form')->withErrors('Kod wygasł.');
        }

        // Mamy poprawny kod — ustaw employee i przejdź do formularza samooceny (poziom 1)
        $employee = $matched->employee; // istniejący rekord z poprzednich lat
        if(!$employee){
            return redirect()->route('start.veteran.form')->withErrors('Błąd powiązania pracownika z kodem.');
        }

        // Reset prób dla IP (sukces)
        $attempt->attempts = 0; $attempt->locked_until = null; $attempt->save();

        // Upewniamy się że istnieją rekordy w aktywnym cyklu (już sklonowane przy starcie cyklu)
        // W razie gdyby brakowało – opcjonalnie można by je tu dogenerować (pomijamy – cykl start-cycle robi to hurtem)

        return redirect()->route('self.assessment', ['level' => 1, 'uuid' => $employee->uuid]);
    }

    // Ensure only admins can access upload endpoints (supermanager or temporary 'pag')
    private function ensureAdmin()
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }
        if ($user->role === 'supermanager' || $user->username === 'pag') {
            return; // allowed
        }
        abort(403);
    }

    // Wyświetlanie formularza do uploadu Excel
    public function showUploadForm()
    {
        $this->ensureAdmin();
        return view('upload_excel');
    }

    // Przesyłanie i przetwarzanie pliku Excel
    public function uploadExcel(Request $request)
    {
        $this->ensureAdmin();
        try {
            Log::info('Rozpoczęto przesyłanie pliku Excel.');
    
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
    
            Log::info('Plik Excel został załadowany pomyślnie.');
    
            // Pobierz wszystkie zespoły i utwórz mapę nazw do ID
            $teams = Team::pluck('id', 'name')->toArray(); // ['Team Name' => team_id]
    
            // Process the data
            $rows = $sheet->toArray(null, true, true, true); // Use column letters as keys
            $updatedCount = 0;
    
            foreach ($rows as $key => $row) {
                if ($key == 1) continue; // Skip headers
    
                // Check if competency_name is empty (now in column C)
                if (empty($row['C'])) {
                    Log::info("Pominięto wiersz $key - brak nazwy kompetencji.");
                    continue; // Skip the row if competency_name is empty
                }
    
                // Read data from the correct columns and trim whitespace
                $level = trim($row['A']); // Excel Column A
                $competencyType = trim($row['B']); // Excel Column B
                $competencyName = trim($row['C']); // Excel Column C
                $description075to1 = trim($row['D'] ?? '');
                $description025 = trim($row['F'] ?? ''); // NOWA definicja dla 0,25 (kolumna F)
                $description0to05 = trim($row['E'] ?? '');
                $descriptionAboveExpectations = trim($row['G'] ?? ''); // above expectations teraz w G
    
                // Read team values (columns H to P)
                $teamValues = [
                    'Production' => isset($row['I']) ? intval($row['I']) : 0, // Column I
                    'Sales' => isset($row['J']) ? intval($row['J']) : 0, // Column J
                    'Growth' => isset($row['K']) ? intval($row['K']) : 0, // Column K
                    'Logistyka' => isset($row['L']) ? intval($row['L']) : 0, // Column L
                    'People & Culture' => isset($row['M']) ? intval($row['M']) : 0, // Column M
                    'Zarząd' => isset($row['N']) ? intval($row['N']) : 0, // Column N
                    'Order Care' => isset($row['O']) ? intval($row['O']) : 0, // Column O
                    'Finanse i Kadry' => isset($row['P']) ? intval($row['P']) : 0, // Column P
                ];
    
                // Create or update the competency
                $competency = Competency::updateOrCreate(
                    [
                        'competency_name' => $competencyName,
                        'level' => $level,
                        'competency_type' => $competencyType,
                    ],
                    [
                        'description_075_to_1' => $description075to1,
                        'description_025' => $description025, // dodaj do fillable i migracji jeśli nie ma
                        'description_0_to_05' => $description0to05,
                        'description_above_expectations' => $descriptionAboveExpectations,
                    ]
                );
    
                Log::info("Zaktualizowano lub utworzono kompetencję: $competencyName (ID: {$competency->id}).");
    
                // Update or create competency team values
                foreach ($teamValues as $teamName => $value) {
                    $teamNameTrimmed = trim($teamName);
    
                    if (!isset($teams[$teamNameTrimmed])) {
                        Log::error("Zespół nie znaleziony: $teamNameTrimmed");
                        continue;
                    }
    
                    $teamId = $teams[$teamNameTrimmed];
    
                    CompetencyTeamValue::updateOrCreate(
                        ['competency_id' => $competency->id, 'team_id' => $teamId],
                        ['value' => $value]
                    );
    
                    Log::info("Zaktualizowano wartość zespołu: $teamNameTrimmed dla kompetencji ID: {$competency->id} (Wartość: $value).");
                }
    
                $updatedCount++;
            }
    
            Log::info("Przesyłanie zakończone. Zaktualizowano $updatedCount pytań.");
            return redirect()->back()->with('message', "Pytania zostały zaktualizowane. Zaktualizowano $updatedCount pytań.");
    
        } catch (\Exception $e) {
            Log::error('Błąd podczas przetwarzania pliku Excel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Wystąpił błąd podczas przetwarzania pliku Excel. Proszę spróbować ponownie.');
        }
    }

    // Provide a sample Excel template download
    public function downloadTemplate()
    {
        $this->ensureAdmin();
        $headers = [
            'A' => 'Poziom (1..5)',
            'B' => 'Rodzaj kompetencji (1.,2.,3.X)',
            'C' => 'Nazwa kompetencji',
            'D' => 'Opis 0,75–1',
            'E' => 'Opis 0–0,5',
            'F' => 'Opis 0,25',
            'G' => 'Powyżej oczekiwań',
            'H' => 'Rezerwowe/nieużywane',
            'I' => 'Produkcja (wartość)',
            'J' => 'Sprzedaż (wartość)',
            'K' => 'Growth (wartość)',
            'L' => 'Logistyka (wartość)',
            'M' => 'People & Culture (wartość)',
            'N' => 'Zarząd (wartość)',
            'O' => 'Order Care (wartość)',
        ];
        $data = [array_values($headers)];
        return \Maatwebsite\Excel\Facades\Excel::download(
            new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
                private $data; public function __construct($d){$this->data=$d;} public function array(): array { return $this->data; }
            },
            'pcap_template.xlsx'
        );
    }

    public function getManagersByDepartment(Request $request)
    {
        $department = $request->input('department');
        $managers = [];

        if ($department) {
            // Fetch managers for the selected department
            $managers = User::where('department', $department)
                            ->whereIn('role', ['manager', 'supermanager', 'head'])
                            ->pluck('name', 'id'); // Adjust field names as per your User model
        }

        return response()->json($managers);
    }

    
    
    
    // Add this method to your SelfAssessmentController
    public function showResults()
    {
        $name = session('name');

        // Retrieve the employee
        $employee = Employee::where('name', $name)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Nie znaleziono danych użytkownika.');
        }

        // Get the team based on the employee's department
        $team = Team::where('name', $employee->department)->first();

        if (!$team) {
            return redirect()->back()->with('error', 'Nie znaleziono zespołu dla użytkownika.');
        }

        // Fetch results with the necessary relationships
        $results = Result::where('employee_id', $employee->id)
            ->with([
                'competency' => function ($query) use ($team) {
                    $query->with(['competencyTeamValues' => function ($q) use ($team) {
                        $q->where('team_id', $team->id);
                    }]);
                },
                'employee'
            ])
            ->get();

        // Pass data to the view
        return view('results', compact('results', 'employee', 'team'));
    }


public function showStep1Form()
{
    // Define the block date
    $blockDateRecord = BlockDate::first();
    $blockDate = $blockDateRecord ? \Carbon\Carbon::parse($blockDateRecord->block_date) : \Carbon\Carbon::parse('2025-12-15');

    // Check if the current date is after the block date
    if (Carbon::now()->gt($blockDate)) {
        // Redirect to a view that displays the message that submission is blocked
        return view('self-assessment.blocked');
    }

    $departments = [
        'Sales', 'Growth', 'Production', 'Logistyka', 'People & Culture', 'Zarząd', 'Finanse i Kadry'
    ];

    // Początkowo pusta lista przełożonych
    $managers = [];

    return view('self-assessment.step1', compact('departments', 'managers'));
}

    
    
    
    // Obsługa przesłania pierwszego kroku formularza (przekierowanie do pytań)
    public function saveStep1(Request $request)
    {
        // Define the block date
        $blockDateRecord = BlockDate::first();
        $blockDate = $blockDateRecord ? \Carbon\Carbon::parse($blockDateRecord->block_date) : \Carbon\Carbon::parse('2025-12-15');
    
        // Check if the current date is after the block date
        if (Carbon::now()->gt($blockDate)) {
            // Return the blocked view
            return view('self-assessment.blocked');
        }
    
        // Clear session data
        session()->flush();
    
        // Validate input
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'department' => 'required|string|max:255',
            'manager' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
        ]);
    
        // Create a new Employee record with a unique UUID
        $employee = Employee::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'name' => trim($request->input('first_name') . ' ' . $request->input('last_name')), // For backwards compatibility
            'email' => $request->input('email'),
            'department' => $request->input('department'),
            'manager_username' => $request->input('manager'),
            'job_title' => $request->input('job_title'),
            'uuid' => Str::uuid(),
        ]);
    
        // Redirect to the first level of questions with UUID
        return redirect()->route('self.assessment', ['level' => 1, 'uuid' => $employee->uuid]);
    }

    // Wyświetlanie formularza samooceny (krok 2)
    public function showForm($level = 1, $uuid = null)
    {
        // Sprawdź czy istnieje aktywny cykl
        $activeCycleId = $this->activeCycleId();
        if (!$activeCycleId) {
            return redirect()->route('start.landing')->with('error', 'Samoocena jest obecnie zablokowana. Skontaktuj się z administratorem.');
        }

        // Pobierz pracownika na podstawie UUID
        $employee = Employee::where('uuid', $uuid)->first();

        if (!$employee) {
            return redirect()->route('start.landing')->withErrors('Nie znaleziono danych użytkownika.');
        }

        // Mapowanie działów do kodów
        $departmentCodes = [
            'Growth' => 'G',
            'Logistyka' => 'L',
            'Production' => 'P',
            'Sales' => 'S',
            'Zarząd' => 'Z',
            'Order Care' => 'O',
            'People & Culture' => 'H',
            'Finanse i Kadry' => 'F',
        ];

        // Pobranie wybranego działu z danych pracownika
        $department = $employee->department;
        $departmentCode = isset($departmentCodes[$department]) ? $departmentCodes[$department] : null;

        // Upewnij się, że poziom mieści się w aktywnych zakresach zgodnie z konfiguracją
        $maxLevel = (int) config('levels.max', 5);
        if ($level < 1) { $level = 1; }
        if ($level > $maxLevel) {
            // Przekieruj bezpiecznie do maksymalnego poziomu, aby nie wyświetlać wycofanego poziomu
            return redirect()->route('self.assessment', ['level' => $maxLevel, 'uuid' => $uuid]);
        }

        // Pobranie kompetencji dla danego poziomu i działu
        $competencies = DB::table('competencies')
            ->where('level', 'like', "{$level}%")
            ->where(function ($query) use ($departmentCode) {
                if ($departmentCode) {
                    $query->where('competency_type', 'not like', '3.%')
                        ->orWhere('competency_type', 'like', '3.' . $departmentCode . '%');
                } else {
                    // Jeśli dział nie jest wybrany, wykluczamy wszystkie pytania "Zawodowe"
                    $query->where('competency_type', 'not like', '3.%');
                }
            })
            ->get();

        // Jeśli nie ma pytań dla danego poziomu
        if ($competencies->isEmpty()) {
            return redirect()->back()->with('error', "Brak pytań dla poziomu: {$level}");
        }

        // Definicja nazw aktywnych poziomów z konfiguracji
        $levelNames = config('levels.active', [
            1 => 'Junior',
            2 => 'Specjalista',
            3 => 'Senior',
            4 => 'Supervisor',
            5 => 'Manager',
        ]);

        // Ustawienie nazwy aktualnego poziomu
        $currentLevelName = $levelNames[$level] ?? 'Poziom nieznany';

        // Przypisanie poziomu do zmiennej $currentLevel
        $currentLevel = $level;

        $activeCycleId = $this->activeCycleId();
        // Pobierz zapisane odpowiedzi z bazy danych dla tego pracownika i poziomu - BEZ filtrowania cyklu
        // żeby eager loading parent mogło załadować dane z poprzedniego cyklu
        $allResults = Result::where('employee_id', $employee->id)
            ->whereHas('competency', function ($query) use ($level) {
                $query->where('level', 'like', "{$level}%");
            })
            ->with(['competency','parent'])
            ->get();
            
        // Filtruj wyniki z aktywnego cyklu w PHP
        $results = $allResults->where('cycle_id', $activeCycleId);

        // Znajdź poprzedni cykl (najwyższy cycle_id różny od aktywnego)
        // Może być wyższy lub niższy - nie zakładamy chronologii
        $previousCycleId = Result::where('employee_id', $employee->id)
            ->where('cycle_id', '!=', $activeCycleId)
            ->max('cycle_id');

        // Załaduj dane z poprzedniego cyklu bezpośrednio
        $previousResults = collect([]);
        if ($previousCycleId) {
            $previousResults = Result::where('employee_id', $employee->id)
                ->where('cycle_id', $previousCycleId)
                ->whereHas('competency', function ($query) use ($level) {
                    $query->where('level', 'like', "{$level}%");
                })
                ->with('competency')
                ->get()
                ->keyBy('competency_id');
        }

        // Mapowanie wyników do $savedAnswers + poprzedni cykl
        $savedAnswers = [];
        $prevAnswers = [];
        
        foreach ($results as $result) {
            $savedAnswers['competency_id'][] = $result->competency_id;
            $savedAnswers['score'][$result->competency_id] = $result->score;
            
            // DEBUG: Log każdego mapowania
            \Log::info("Mapping result to savedAnswers", [
                'competency_id' => $result->competency_id,
                'score' => $result->score,
                'result_id' => $result->id,
                'cycle_id' => $result->cycle_id
            ]);
            if ($result->above_expectations) {
                $savedAnswers['above_expectations'][$result->competency_id] = 1;
            }
            if ($result->comments) {
                $savedAnswers['comments'][$result->competency_id] = $result->comments;
                $savedAnswers['add_description'][$result->competency_id] = 1;
            }
            
            // Spróbuj załadować poprzednie dane z relacji parent
            if ($result->parent) {
                $prevAnswers['score'][$result->competency_id] = $result->parent->score;
                if ($result->parent->above_expectations) {
                    $prevAnswers['above_expectations'][$result->competency_id] = 1;
                }
                if ($result->parent->comments) {
                    $prevAnswers['comments'][$result->competency_id] = $result->parent->comments;
                }
                if ($result->parent->feedback_manager) {
                    $prevAnswers['manager_feedback'][$result->competency_id] = $result->parent->feedback_manager;
                }
            }
        }

        // Jeśli nie mamy danych z parent relacji, użyj bezpośredniego zapytania do poprzedniego cyklu
        foreach ($competencies as $competency) {
            if (!isset($prevAnswers['score'][$competency->id]) && isset($previousResults[$competency->id])) {
                $prevResult = $previousResults[$competency->id];
                $prevAnswers['score'][$competency->id] = $prevResult->score;
                if ($prevResult->above_expectations) {
                    $prevAnswers['above_expectations'][$competency->id] = 1;
                }
                if ($prevResult->comments) {
                    $prevAnswers['comments'][$competency->id] = $prevResult->comments;
                }
                if ($prevResult->feedback_manager) {
                    $prevAnswers['manager_feedback'][$competency->id] = $prevResult->feedback_manager;
                }
            }
        }

        // DEBUG: Lista wszystkich dostępnych cycle_id dla tego employee
        $availableCycles = Result::where('employee_id', $employee->id)
            ->distinct()
            ->pluck('cycle_id')
            ->sort()
            ->values();
            
        // DEBUG: Log query results and prevAnswers
        \Log::info('DEBUG - Form data:', [
            'employee_id' => $employee->id,
            'level' => $level,
            'activeCycleId' => $activeCycleId,
            'availableCycles' => $availableCycles,
            'previousCycleId' => $previousCycleId,
            'all_results_count' => $allResults->count(),
            'filtered_results_count' => $results->count(),
            'previous_results_count' => $previousResults->count(),
            'results_with_parent' => $results->filter(function($r) { return $r->parent !== null; })->count(),
            'results_with_parent_result_id' => $results->filter(function($r) { return $r->parent_result_id !== null; })->count(),
            'prevAnswers' => $prevAnswers,
            'previousResults_sample' => $previousResults->take(3)->map(function($r) {
                return [
                    'competency_id' => $r->competency_id,
                    'score' => $r->score,
                    'comments' => substr($r->comments ?? '', 0, 50),
                    'cycle_id' => $r->cycle_id
                ];
            }),
            'all_results_detailed' => $allResults->map(function($r) {
                return [
                    'id' => $r->id,
                    'competency_id' => $r->competency_id,
                    'cycle_id' => $r->cycle_id,
                    'parent_result_id' => $r->parent_result_id,
                    'has_parent_loaded' => $r->parent ? true : false,
                    'parent_id' => $r->parent ? $r->parent->id : null,
                    'parent_cycle_id' => $r->parent ? $r->parent->cycle_id : null,
                    'parent_comments' => $r->parent ? $r->parent->comments : null,
                ];
            }),
            'filtered_results_detailed' => $results->map(function($r) {
                return [
                    'id' => $r->id,
                    'competency_id' => $r->competency_id,
                    'cycle_id' => $r->cycle_id,
                    'parent_result_id' => $r->parent_result_id,
                    'has_parent_loaded' => $r->parent ? true : false,
                    'parent_id' => $r->parent ? $r->parent->id : null,
                    'parent_cycle_id' => $r->parent ? $r->parent->cycle_id : null,
                    'parent_comments' => $r->parent ? $r->parent->comments : null,
                ];
            })
        ]);
        
        // DEBUG: Final savedAnswers przed widokiem
        \Log::info('DEBUG - Final savedAnswers:', [
            'savedAnswers_scores' => $savedAnswers['score'] ?? [],
            'competency_3_score' => $savedAnswers['score'][3] ?? 'NOT_SET',
            'total_saved_scores' => count($savedAnswers['score'] ?? [])
        ]);

        // Przekazanie zmiennych do widoku
    $showPrev = true; // domyślnie pokażemy toggle, UI ukryje jeśli brak danych
    return view('self-assessment.form', compact('competencies', 'currentLevel', 'currentLevelName', 'levelNames', 'savedAnswers', 'prevAnswers', 'uuid', 'employee', 'showPrev'));
    }

    public function sendCopy(Request $request)
    {
        $email = $request->input('email');
        $uuid = $request->input('uuid');

        // Find the employee based on UUID
        $employee = Employee::where('uuid', $uuid)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Nie znaleziono danych użytkownika.');
        }

        // Fetch results
        $results = Result::where('employee_id', $employee->id)->with('competency')->get();

        // Send email with results
        \Mail::to($email)->send(new \App\Mail\ResultsMail($results));

        return redirect()->back()->with('success', 'Kopia wyników została wysłana na Twój e-mail.');
    }


    
    
    
    public function completeAssessment($uuid)
    {
        // Find the employee based on UUID with necessary relationships
        $employee = Employee::with(['team', 'overriddenCompetencyValues'])
            ->where('uuid', $uuid)
            ->first();

        if (!$employee) {
            return redirect()->route('start.landing')->with('error', 'Nie znaleziono danych użytkownika.');
        }

        // Calculate level and percentage based on results
        $assessmentSummary = $this->calculateAssessmentSummary($employee);

        // Return the complete view with UUID and summary
        return view('self-assessment.complete', compact('uuid', 'employee', 'assessmentSummary'));
    }

    /**
     * Calculate assessment summary (level and percentage) for employee
     */
    private function calculateAssessmentSummary($employee)
    {
        $activeCycleId = $this->activeCycleId();
        
        // Get employee results with competency relationships - same logic as ManagerController
        $results = $employee->results()
            ->where('cycle_id', $activeCycleId)
            ->with('competency.competencyTeamValues')
            ->get();

        $levelSummaries = [];

        // Calculate per-level summaries - same logic as ManagerController
        $levels = $results->groupBy('competency.level');
        
        foreach ($levels as $level => $levelResults) {
            $earnedPointsEmployee = 0;
            $maxPoints = 0;
            
            foreach ($levelResults as $result) {
                // Use the same competency value calculation as ManagerController
                $competencyValue = $employee->getCompetencyValue($result->competency_id) ?? 0;

                // Employee's score
                $scoreEmployee = $result->score;

                // Only include in calculations if score > 0 (not "Nie dotyczy")
                // This matches the PDF logic where "Nie dotyczy" entries are excluded from both earned and max points
                if ($scoreEmployee > 0 && $competencyValue > 0) {
                    $earnedPointsEmployee += $competencyValue * $scoreEmployee;
                    $maxPoints += $competencyValue;
                }
            }

            // Calculate percentage
            $percentageEmployee = $maxPoints > 0 ? ($earnedPointsEmployee / $maxPoints) * 100 : 0;
            
            $levelSummaries[$level] = [
                'levelName' => config("levels.active.{$level}", "Poziom {$level}"),
                'earnedPoints' => $earnedPointsEmployee,
                'maxPoints' => $maxPoints,
                'percentage' => round($percentageEmployee, 2)
            ];
        }

        // Determine achieved level based on percentages
        $achievedLevel = $this->determineAchievedLevel($levelSummaries);

        return [
            'levelSummaries' => $levelSummaries,
            'achievedLevel' => $achievedLevel
        ];
    }

    /**
     * Determine achieved level based on level summaries - sequential from lowest to highest
     */
    private function determineAchievedLevel($levelSummaries)
    {
        $achievedLevel = 'Brak danych';
        
        // Define thresholds for each level
        $thresholds = [
            1 => 80, // Junior ≥80%
            2 => 85, // Specjalista ≥85%
            3 => 85, // Senior ≥85%
            4 => 80, // Supervisor ≥80%
            5 => 80, // Manager ≥80%
        ];
        
        // Sort levels numerically and check sequentially from lowest to highest
        $sortedLevels = [];
        foreach ($levelSummaries as $levelNumber => $summary) {
            // Extract numeric part from level (e.g., "1 Junior" -> 1)
            preg_match('/^(\d+)/', $levelNumber, $matches);
            $numericLevel = isset($matches[1]) ? (int)$matches[1] : 0;
            $sortedLevels[$numericLevel] = [$levelNumber, $summary];
        }
        ksort($sortedLevels);
        
        // Always assign at least the lowest level if any data exists
        if (!empty($sortedLevels)) {
            $firstLevel = reset($sortedLevels);
            $achievedLevel = $firstLevel[1]['levelName']; // Default to first level
        }
        
        // Check levels sequentially - must pass each level to achieve higher ones
        foreach ($sortedLevels as $numericLevel => $levelData) {
            [$levelNumber, $summary] = $levelData;
            
            $requiredThreshold = $thresholds[$numericLevel] ?? 50;
            
            if ($summary['percentage'] >= $requiredThreshold) {
                $achievedLevel = $summary['levelName']; // Passed this level
            } else {
                // Failed this level - stay at previous level and stop checking
                break;
            }
        }
        
        return $achievedLevel;
    }


    
    
    public function getDefinition($id)
    {
        $competency = DB::table('competencies')->where('id', $id)->first();
    
        // Przygotuj treść do wyświetlenia w modalu
        return view('partials.competency_definition', compact('competency'));
    }
    

    public function generatePdf($uuid)
{
    // Find the employee based on UUID
    $employee = Employee::with('team')->where('uuid', $uuid)->first();

    if (!$employee) {
        return redirect()->back()->with('error', 'Nie znaleziono danych użytkownika.');
    }

    $team = $employee->team;

    if (!$team) {
        return redirect()->back()->with('error', 'Nie znaleziono zespołu dla użytkownika.');
    }

    // Fetch the results of the user with necessary relations
    $activeCycleId = $this->activeCycleId();
    $results = Result::where('employee_id', $employee->id)
        ->when($activeCycleId, fn($q)=>$q->where('cycle_id',$activeCycleId))
        ->with('competency.competencyTeamValues')
        ->get();

    // Generate the PDF
    $pdf = PDF::loadView('exports.results_pdf', [
        'results' => $results,
        'employee' => $employee,
        'team' => $team // Pass the $team variable to the view
    ])->setPaper('a4', 'landscape');

    $date = now()->format('Y-m-d_H-i');
    $name = str_replace(' ', '_', $employee->name);
    $filename = "P-CAP Raport Full-{$date}_{$name}.pdf";
    return $pdf->download($filename);
}

public function generateXls($uuid)
{
    // Find the employee based on UUID
    $employee = Employee::where('uuid', $uuid)->first();

    if (!$employee) {
        return redirect()->back()->with('error', 'Nie znaleziono danych użytkownika.');
    }

    $team = Team::where('name', $employee->department)->first();

    if (!$team) {
        return redirect()->back()->with('error', 'Nie znaleziono zespołu dla użytkownika.');
    }

    // Fetch the results of the user with necessary relations
    $activeCycleId = $this->activeCycleId();
    $results = Result::where('employee_id', $employee->id)
        ->when($activeCycleId, fn($q)=>$q->where('cycle_id',$activeCycleId))
        ->with('competency.competencyTeamValues')
        ->get();

    // Export to Excel
    $date = now()->format('Y-m-d_H-i');
    $name = str_replace(' ', '_', $employee->name);
    $filename = "P-CAP Raport Full-{$date}_{$name}.xlsx";
    return Excel::download(new ResultsExport($results, $team), $filename);
}
    
    


    public function saveResults(Request $request)
    {
        $currentLevel = $request->input('current_level');
        $uuid = $request->input('uuid');
    
        if (!$uuid) {
            return redirect()->route('start.landing')->with('error', 'Brakuje identyfikatora użytkownika.');
        }
    
        // Find the employee based on UUID
        $employee = Employee::where('uuid', $uuid)->first();
    
        if (!$employee) {
            return redirect()->route('start.landing')->with('error', 'Nie znaleziono danych użytkownika.');
        }
    
    // Determine which action was requested (prefer explicit hidden field 'action')
    $action = strtolower($request->input('action', ''));
    $goBack = $action === 'back' || $request->has('back');
    $saveAndExit = $action === 'save_and_exit' || $request->has('save_and_exit');
    $submit = $action === 'submit' || $request->has('submit');
    
    // CRITICAL: For navigation, save data for the level user is LEAVING FROM (current level)
    // This ensures we only save data that is actually in the form
    $levelForFiltering = $currentLevel;

        // Save the form data (ALWAYS save before redirecting, including when going Back)
        $competencyIds = $request->input('competency_id', []);
        $scores = $request->input('score', []);
        $aboveExpectations = $request->input('above_expectations', []);
        $comments = $request->input('comments', []);
    
        $activeCycleId = $this->activeCycleId();
        
        // Validate active cycle exists
        if (!$activeCycleId) {
            return redirect()->route('start.landing')->with('error', 'Brak aktywnego cyklu oceny. Skontaktuj się z administratorem.');
        }
        
        // Get competencies for target level (not current form level)
        $validCompetencyIds = DB::table('competencies')
            ->where('level', 'like', "{$levelForFiltering}%")
            ->pluck('id')
            ->toArray();
            
        // Debug: log saveResults activity with detailed data
        // Basic logging for monitoring
        \Log::info('saveResults processing', [
            'action' => $action,
            'current_level' => $currentLevel,
            'total_competencies' => count($competencyIds),
            'employee_id' => $employee->id
        ]);
        
        // Iterate through competency IDs
        $processedCount = 0;
        $skippedCount = 0;
        
        foreach ($competencyIds as $index => $competencyId) {
            // Skip invalid competency IDs (can happen with stale forms)
            if (!$competencyId || $competencyId <= 0) {
                $skippedCount++;
                continue;
            }
            
            // CRITICAL: Only save competencies that belong to target level
            if (!in_array($competencyId, $validCompetencyIds)) {
                $skippedCount++;
                continue; // Skip competencies not from target level
            }
            
            // Verify competency exists
            if (!DB::table('competencies')->where('id', $competencyId)->exists()) {
                $skippedCount++;
                continue; // Skip non-existent competencies
            }
            
            $isAboveExpectations = intval($aboveExpectations[$competencyId] ?? 0) ? 1 : 0;
        
            // Get score using competency ID as key (score array is keyed by competency ID)
            $scoreValue = $scores[$competencyId] ?? 0;
            
            // Jeśli above_expectations jest zaznaczone, ustaw score na 1
            if ($isAboveExpectations) {
                $scoreValue = 1;
            }
            
            $processedCount++;
            
            Result::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'competency_id' => $competencyId,
                    'cycle_id' => $activeCycleId,
                ],
                [
                    'score' => $scoreValue,
                    'above_expectations' => $isAboveExpectations,
                    'comments' => $comments[$competencyId] ?? null,
                    'updated_at' => now(),
                ]
            );
        }

        \Log::info('saveResults completed', [
            'processed_count' => $processedCount,
            'skipped_count' => $skippedCount,
            'total_received' => count($competencyIds)
        ]);

        // Redirect according to requested action
        if ($saveAndExit) {
            // User clicked "Save and finish later"
            // Save data and redirect back to the same form with modal
            return redirect()->route('self.assessment', ['level' => $currentLevel, 'uuid' => $uuid])->with('show_modal', true);
        } elseif ($submit) {
            // User clicked "Submit"
            return redirect()->route('self.assessment.complete', ['uuid' => $uuid]);
        } else {
            // User clicked either "Back" or "Next"
            if ($goBack) {
                $previousLevel = $currentLevel - 1;
                if ($previousLevel < 1) { $previousLevel = 1; }
                return redirect()->route('self.assessment', ['level' => $previousLevel, 'uuid' => $uuid]);
            } else {
                $nextLevel = $currentLevel + 1;
                return redirect()->route('self.assessment', ['level' => $nextLevel, 'uuid' => $uuid]);
            }
        }
    }
    
    

    public function edit($uuid)
    {
        // Retrieve the employee based on UUID
        $employee = Employee::where('uuid', $uuid)->first();
    
        if (!$employee) {
            return redirect()->route('self.assessment.complete')->withErrors('Nie znaleziono użytkownika.');
        }
    
        // Check if editing is allowed
        $blockDate = Carbon::parse(config('app.block_date', '2025-12-15'));
        if (Carbon::now()->gt($blockDate)) {
            return redirect()->route('self.assessment.complete', ['uuid' => $uuid])->withErrors('Edycja formularza jest już zablokowana.');
        }
    
        // Redirect to the first level of the form with UUID
        return redirect()->route('self.assessment', ['level' => 1, 'uuid' => $uuid]);
    }
    
    



    public function store(Request $request)
    {
        // Store values in session
        session(['self_assessment_values' => $request->all()]);
        
        // Generate and save UUID (assuming save functionality exists in model)
        $uuid = Str::uuid();
        // Save UUID to database here

        // Redirect to complete page with generated UUID link
        return redirect()->route('form.complete', ['uuid' => $uuid]);
    }

    public function autosave(Request $request)
    {
        $uuid = $request->input('uuid');
        if (!$uuid) {
            return response()->json(['status' => 'error', 'message' => 'Brakuje identyfikatora użytkownika.'], 400);
        }

        $employee = \App\Models\Employee::where('uuid', $uuid)->first();
        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Nie znaleziono użytkownika.'], 404);
        }

        $currentLevel = $request->input('current_level', 1);
        $competencyIds = $request->input('competency_id', []);
        $scores = $request->input('score', []);
        $aboveExpectations = $request->input('above_expectations', []);
        $comments = $request->input('comments', []);
        
        // Debug: log received data to see what's being sent
        \Log::info('Autosave received data', [
            'competency_count' => count($competencyIds),
            'score_count' => count($scores),
            'first_5_scores' => array_slice($scores, 0, 5),
            'employee_id' => $employee->id,
            'cycle_id' => $this->activeCycleId(),
            'current_level' => $currentLevel,
            'user_agent' => $request->header('User-Agent'),
            'referer' => $request->header('Referer'),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type')
        ]);
        
        // Get competencies for current level to ensure we only save data for competencies that should be on this level
        $validCompetencyIds = DB::table('competencies')
            ->where('level', 'like', "{$currentLevel}%")
            ->pluck('id')
            ->toArray();

        foreach ($competencyIds as $competencyId) {
            // Only process competencies that belong to the current level
            if (!in_array($competencyId, $validCompetencyIds)) {
                continue; // Skip competencies not from current level
            }
            
            // IMPORTANT: do not use isset() here; inputs exist for every competency with value "0" when not selected
            // Using isset would incorrectly set all items to 1. Respect the actual submitted value 0/1.
            $isAboveExpectations = intval($aboveExpectations[$competencyId] ?? 0) ? 1 : 0;
            $scoreValue = $isAboveExpectations ? 1 : ($scores[$competencyId] ?? 0);
            
            // Check if record already exists with same data - don't save if nothing changed
            $existingResult = \App\Models\Result::where([
                'employee_id' => $employee->id,
                'competency_id' => $competencyId,
                'cycle_id' => $this->activeCycleId(),
            ])->first();
            
            // If record exists and data is identical, skip saving
            if ($existingResult && 
                $existingResult->score == $scoreValue && 
                $existingResult->above_expectations == $isAboveExpectations &&
                $existingResult->comments == ($comments[$competencyId] ?? null)) {
                \Log::info("Skipping competency {$competencyId} - no changes (score: {$scoreValue})");
                continue; // Skip - no changes to save
            }
            
            // CRITICAL: Only protect from cross-level overwrites in autosave
            // Check if this competency belongs to current level - if not, and we're trying to save 0, it's likely cross-level contamination
            $competencyLevel = DB::table('competencies')->where('id', $competencyId)->value('level');
            $belongsToCurrentLevel = strpos($competencyLevel, (string)$currentLevel) === 0;
            
            if ($existingResult && $existingResult->score > 0 && $scoreValue == 0 && !$isAboveExpectations && !$belongsToCurrentLevel) {
                \Log::info("PROTECTION: Skipping competency {$competencyId} - protecting from cross-level overwrite", [
                    'current_level' => $currentLevel,
                    'competency_level' => $competencyLevel,
                    'existing_score' => $existingResult->score,
                    'new_score' => $scoreValue
                ]);
                continue;
            }
            
            \Log::info("Saving competency {$competencyId}", [
                'old_score' => $existingResult ? $existingResult->score : 'new',
                'new_score' => $scoreValue,
                'changed' => !$existingResult || $existingResult->score != $scoreValue
            ]);

            \App\Models\Result::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'competency_id' => $competencyId,
                    'cycle_id' => $this->activeCycleId(),
                ],
                [
                    'score' => $scoreValue,
                    'above_expectations' => $isAboveExpectations,
                    'comments' => $comments[$competencyId] ?? null,
                    'updated_at' => now(),
                ]
            );
        }

        \Log::info('Autosave completed', [
            'processed_competencies' => count(array_intersect($competencyIds, $validCompetencyIds)),
            'total_received' => count($competencyIds)
        ]);
        
        return response()->json(['status' => 'success']);
    }
}
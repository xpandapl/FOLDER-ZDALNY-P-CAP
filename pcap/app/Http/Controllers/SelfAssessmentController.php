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

use Carbon\Carbon;
use Illuminate\Support\Str;


class SelfAssessmentController extends Controller
{
    // Wyświetlanie formularza do uploadu Excel
    public function showUploadForm()
    {
        return view('upload_excel');
    }

    // Przesyłanie i przetwarzanie pliku Excel
    public function uploadExcel(Request $request)
    {
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
                $description0to05 = trim($row['E'] ?? '');
                $descriptionAboveExpectations = trim($row['F'] ?? '');
    
                // Read team values (columns H to N)
                $teamValues = [
                    'Production' => isset($row['I']) ? intval($row['I']) : 0, // Column I
                    'Sales' => isset($row['J']) ? intval($row['J']) : 0, // Column J
                    'Growth' => isset($row['K']) ? intval($row['K']) : 0, // Column K
                    'Logistyka' => isset($row['L']) ? intval($row['L']) : 0, // Column L
                    'People & Culture' => isset($row['M']) ? intval($row['M']) : 0, // Column M
                    'Zarząd' => isset($row['N']) ? intval($row['N']) : 0, // Column N
                    'Order Care' => isset($row['O']) ? intval($row['O']) : 0, // Column O
                ];
    
                // Create or update the competency
                $competency = Competency::updateOrCreate(
                    ['competency_name' => $competencyName, 'level' => $level],
                    [
                        'competency_type' => $competencyType,
                        'description_075_to_1' => $description075to1,
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
    $blockDate = Carbon::parse('2024-11-29');

    // Check if the current date is after the block date
    if (Carbon::now()->gt($blockDate)) {
        // Redirect to a view that displays the message that submission is blocked
        return view('self-assessment.blocked');
    }

    $departments = [
        'Sales', 'Growth', 'Production', 'Logistyka', 'People & Culture', 'Zarząd'
    ];

    // Początkowo pusta lista przełożonych
    $managers = [];

    return view('self-assessment.step1', compact('departments', 'managers'));
}

    
    
    
    // Obsługa przesłania pierwszego kroku formularza (przekierowanie do pytań)
    public function saveStep1(Request $request)
    {
        // Define the block date
        $blockDate = Carbon::parse('2024-11-29');
    
        // Check if the current date is after the block date
        if (Carbon::now()->gt($blockDate)) {
            // Return the blocked view
            return view('self-assessment.blocked');
        }
    
        // Clear session data
        session()->flush();
    
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'department' => 'required|string|max:255',
            'manager' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
        ]);
    
        // Create a new Employee record with a unique UUID
        $employee = Employee::create([
            'name' => $request->input('name'),
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

        // Pobierz pracownika na podstawie UUID
        $employee = Employee::where('uuid', $uuid)->first();

        if (!$employee) {
            return redirect()->route('self.assessment.step1')->withErrors('Nie znaleziono danych użytkownika.');
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
        ];

        // Pobranie wybranego działu z danych pracownika
        $department = $employee->department;
        $departmentCode = isset($departmentCodes[$department]) ? $departmentCodes[$department] : null;

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

        // Definicja nazw poziomów
        $levelNames = [
            1 => 'Junior',
            2 => 'Specjalista',
            3 => 'Senior',
            4 => 'Supervisor',
            5 => 'Manager',
            6 => 'Head of'
        ];

        // Ustawienie nazwy aktualnego poziomu
        $currentLevelName = $levelNames[$level] ?? 'Poziom nieznany';

        // Przypisanie poziomu do zmiennej $currentLevel
        $currentLevel = $level;

        // Pobierz zapisane odpowiedzi z bazy danych dla tego pracownika i poziomu
        $results = Result::where('employee_id', $employee->id)
            ->whereHas('competency', function ($query) use ($level) {
                $query->where('level', 'like', "{$level}%");
            })
            ->with('competency')
            ->get();

        // Mapowanie wyników do $savedAnswers
        $savedAnswers = [];
        foreach ($results as $result) {
            $savedAnswers['competency_id'][] = $result->competency_id;
            $savedAnswers['score'][$result->competency_id] = $result->score;
            if ($result->above_expectations) {
                $savedAnswers['above_expectations'][$result->competency_id] = 1;
            }
            if ($result->comments) {
                $savedAnswers['comments'][$result->competency_id] = $result->comments;
                $savedAnswers['add_description'][$result->competency_id] = 1;
            }
        }

        // Przekazanie zmiennych do widoku
        return view('self-assessment.form', compact('competencies', 'currentLevel', 'currentLevelName', 'savedAnswers', 'uuid', 'employee'));
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
        // Find the employee based on UUID
        $employee = Employee::where('uuid', $uuid)->first();

        if (!$employee) {
            return redirect()->route('self.assessment.step1')->with('error', 'Nie znaleziono danych użytkownika.');
        }

        // Return the complete view with UUID
        return view('self-assessment.complete', compact('uuid', 'employee'));
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
    $results = Result::where('employee_id', $employee->id)
        ->with('competency.competencyTeamValues') // Eager load without filtering
        ->get();

    // Generate the PDF
    $pdf = PDF::loadView('exports.results_pdf', [
        'results' => $results,
        'employee' => $employee,
        'team' => $team // Pass the $team variable to the view
    ])->setPaper('a4', 'landscape');

    return $pdf->download('raport.pdf');
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
    $results = Result::where('employee_id', $employee->id)
        ->with('competency.competencyTeamValues') // Eager load without filtering
        ->get();

    // Export to Excel
    return Excel::download(new ResultsExport($results, $team), 'raport.xlsx');
}

    
    


    public function saveResults(Request $request)
    {
        $currentLevel = $request->input('current_level');
        $uuid = $request->input('uuid');
    
        if (!$uuid) {
            return redirect()->route('self.assessment.step1')->with('error', 'Brakuje identyfikatora użytkownika.');
        }
    
        // Find the employee based on UUID
        $employee = Employee::where('uuid', $uuid)->first();
    
        if (!$employee) {
            return redirect()->route('self.assessment.step1')->with('error', 'Nie znaleziono danych użytkownika.');
        }
    
        if ($request->has('back')) {
            // User clicked "Back"
            $previousLevel = $currentLevel - 1;
            if ($previousLevel < 1) {
                $previousLevel = 1;
            }
            return redirect()->route('self.assessment', ['level' => $previousLevel, 'uuid' => $uuid]);
        }
    
        // Save the form data
        $competencyIds = $request->input('competency_id', []);
        $scores = $request->input('score', []);
        $aboveExpectations = $request->input('above_expectations', []);
        $comments = $request->input('comments', []);
    
        // Iterate through competency IDs
        foreach ($competencyIds as $competencyId) {
            Result::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'competency_id' => $competencyId,
                ],
                [
                    'score' => $scores[$competencyId] ?? 0,
                    'above_expectations' => isset($aboveExpectations[$competencyId]) ? 1 : 0,
                    'comments' => $comments[$competencyId] ?? null,
                    'updated_at' => now(),
                ]
            );
        }
    
        if ($request->has('save_and_exit')) {
            // User clicked "Save and finish later"
            // Save data and redirect back to the same form with modal
            return redirect()->route('self.assessment', ['level' => $currentLevel, 'uuid' => $uuid])->with('show_modal', true);
        } elseif ($request->has('submit')) {
            // User clicked "Submit"
            return redirect()->route('self.assessment.complete', ['uuid' => $uuid]);
        } else {
            // User clicked "Next"
            $nextLevel = $currentLevel + 1;
            return redirect()->route('self.assessment', ['level' => $nextLevel, 'uuid' => $uuid]);
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
        $blockDate = Carbon::parse('2024-11-29');
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
}
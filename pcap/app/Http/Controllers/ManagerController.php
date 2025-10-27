<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Result;
use App\Models\CompetencyTeamValue;
use App\Exports\TeamReportExport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\Team;
use App\Models\EmployeeCompetencyValue;
use Illuminate\Support\Facades\Auth;
use App\Models\AssessmentCycle;
use App\Models\EmployeeCycleAccessCode;


class ManagerController extends Controller
{
    private $levelNames;

    public function __construct()
    {
        // Load manager labels from config (defaults to 1–5)
        $this->levelNames = config('levels.manager', [
            1 => '1. Junior',
            2 => '2. Specjalista',
            3 => '3. Senior',
            4 => '4. Supervisor',
            5 => '5. Manager',
        ]);
    }
    
    // Active cycle helper (bez cache'u - zawsze pobiera aktualny)
    private function activeCycleId(): ?int
    {
        return AssessmentCycle::where('is_active', true)->value('id');
    }

    // Selected cycle: query ?cycle -> session -> active
    private function selectedCycleId(Request $request): ?int
    {
        $fromQuery = $request->query('cycle');
        if ($fromQuery) {
            $cycle = AssessmentCycle::find($fromQuery);
            if ($cycle) {
                session(['manager_selected_cycle' => $cycle->id]);
                return (int)$cycle->id;
            }
        }
        $fromSession = session('manager_selected_cycle');
        if ($fromSession && AssessmentCycle::find($fromSession)) {
            return (int)$fromSession;
        }
        return $this->activeCycleId();
    }

    public function index(Request $request)
{
    $manager = auth()->user();

    if (!$manager) {
        abort(403, 'Użytkownik nie jest zalogowany.');
    }

    $levelNames = $this->levelNames;

    // Cycles
    $selectedCycleId = $this->selectedCycleId($request);
    $selectedCycle = $selectedCycleId ? AssessmentCycle::find($selectedCycleId) : null;
    $isSelectedCycleActive = $selectedCycle ? (bool)$selectedCycle->is_active : false;
    $cycles = AssessmentCycle::orderByDesc('year')->orderByDesc('period')->get();

    // Get the employee ID from the request (if selected)
    $employeeId = $request->input('employee');

    // Pobierz pracowników według nowej hierarchii
    $employees = $this->getEmployeesForManager($manager, $selectedCycleId);
    
    // Grupowanie pracowników według poziomu hierarchii
    $employeesByLevel = $this->groupEmployeesByLevel($employees, $manager);
    
    // Statystyki dla zakładek
    $stats = $this->calculateEmployeeStats($employees, $manager);

    // For supermanagers, fetch all employees with necessary relationships
    if ($manager->role == 'supermanager') {
        $allEmployees = Employee::with([
                'team:id,name',
                'team.competencyTeamValues:id,team_id,competency_id,value',
                'overriddenCompetencyValues:id,employee_id,competency_id,value',
                'results' => function ($q) use ($selectedCycleId) {
                    $q->select('id','employee_id','competency_id','score','score_manager','cycle_id')
                      ->when($selectedCycleId, function($qq) use ($selectedCycleId){ $qq->where('cycle_id', $selectedCycleId); });
                },
                'results.competency' => function ($q) {
                    $q->select('id','level');
                },
            ])
            ->get(['id','name','job_title','department']);
    } else {
        $allEmployees = collect(); // Empty collection for non-supermanagers
    }

    // Dla head - używamy nowej logiki hierarchii
    if ($manager->role == 'head') {
        // Pobierz wszystkie działy gdzie head ma struktury hierarchii
        $headDepartments = \App\Models\HierarchyStructure::where('head_username', $manager->username)
            ->pluck('department')
            ->unique()
            ->toArray();
            
        $departmentEmployees = Employee::with([
                'team:id,name',
                'team.competencyTeamValues:id,team_id,competency_id,value',
                'overriddenCompetencyValues:id,employee_id,competency_id,value',
                'results' => function ($q) use ($selectedCycleId) {
                    $q->select('id','employee_id','competency_id','score','score_manager','cycle_id')
                      ->when($selectedCycleId, function($qq) use ($selectedCycleId){ $qq->where('cycle_id', $selectedCycleId); });
                },
                'results.competency' => function ($q) {
                    $q->select('id','level');
                },
            ])
            ->where(function($q) use ($manager, $headDepartments) {
                $q->where('head_username', $manager->username)
                  ->orWhere('manager_username', $manager->username)
                  ->orWhere('supervisor_username', $manager->username)
                  ->orWhereIn('department', $headDepartments);
            })
            ->get(['id','name','job_title','department']);
    } else {
        $departmentEmployees = collect(); // Empty collection for non-heads
    }

    // Initialize variables
    $employee = null;
    $results = collect(); // Empty collection
    $levelSummaries = [];
    $overriddenValues = collect(); // Initialize here to ensure it's always defined
    $accessCode = null; // existing access code for selected employee + cycle

    if ($employeeId) {
        $employee = Employee::with(['team', 'results.competency.competencyTeamValues'])->find($employeeId);

        // Check if manager has access to this employee - nowa logika hierarchii
        if ($employee && $this->hasAccessToEmployee($manager, $employee)) {
            // Access allowed
            $results = $employee->results()
                ->when($selectedCycleId, function($q) use ($selectedCycleId){ $q->where('cycle_id', $selectedCycleId); })
                ->with('competency.competencyTeamValues')->get();

            // Initialize level summaries
            $levelSummaries = [];

            // Calculate per-level summaries for individual tab
            $levels = $results->groupBy('competency.level');
            foreach ($levels as $level => $levelResults) {
                $earnedPointsEmployee = 0;
                $earnedPointsManager = 0;
                $maxPoints = 0;
                $competencyCount = $levelResults->count();
            
            foreach ($levelResults as $result) {
                $competencyValue = $employee->getCompetencyValue($result->competency_id) ?? 0;

                // Employee's score
                $scoreEmployee = $result->score;

                // Manager's score (if exists, else employee's score)
                $scoreManager = ($result->score_manager !== null) ? $result->score_manager : $result->score;

                // Calculate earned points if score > 0
                if ($scoreEmployee > 0 && $competencyValue > 0) {
                    $earnedPointsEmployee += $competencyValue * $scoreEmployee;
                }
                if ($scoreManager > 0 && $competencyValue > 0) {
                    $earnedPointsManager += $competencyValue * $scoreManager;
                }
                
                // Add to maxPoints only if either employee or manager scored > 0 (not "Nie dotyczy")
                if (($scoreEmployee > 0 || $scoreManager > 0) && $competencyValue > 0) {
                    $maxPoints += $competencyValue;
                }
            }                // Calculate percentages
                $percentageEmployee = $maxPoints > 0 ? ($earnedPointsEmployee / $maxPoints) * 100 : 'N/D';
                $percentageManager = $maxPoints > 0 ? ($earnedPointsManager / $maxPoints) * 100 : 'N/D';

                // Store in levelSummaries with all required keys
                $levelSummaries[$level] = [
                    'earnedPointsEmployee' => $earnedPointsEmployee ?? 0,
                    'earnedPointsManager' => $earnedPointsManager ?? 0,
                    'maxPoints' => $maxPoints ?? 0,
                    'percentageEmployee' => $percentageEmployee ?? 'N/D',
                    'percentageManager' => $percentageManager ?? 'N/D',
                    'count' => $competencyCount ?? 0,
                ];
            }
            // Fetch overridden values
            $overriddenValues = EmployeeCompetencyValue::where('employee_id', $employee->id)
                ->pluck('value', 'competency_id');

            // Fetch existing access code for this employee and selected cycle
            if ($selectedCycleId) {
                $accessCode = EmployeeCycleAccessCode::where('employee_id', $employee->id)
                    ->where('cycle_id', $selectedCycleId)
                    ->first();
            }

        } else {
            // Unauthorized access
            abort(403, 'Nie masz dostępu do tego pracownika.');
        }
    }
        

        // Threshold for level completion
        $threshold = 80; // Adjust as needed

        // Prepare data for the manager's own team
        $teamEmployeesData = $this->prepareEmployeesData($employees, $levelNames);

        // Initialize variables for non-supermanager roles
        $hrData = [];
        $organizationEmployeesData = [];
        $departmentEmployeesData = [];
        $departmentData = [];

        // Prepare HR data
        if ($manager->role == 'supermanager') {
            // Load minimal employee data with results_count instead of full results
            $teams = Team::with(['employees' => function($q) use ($selectedCycleId){
                $q->select('id','department')->withCount(['results' => function($qq) use ($selectedCycleId){
                    $qq->when($selectedCycleId, function($qqq) use ($selectedCycleId){ $qqq->where('cycle_id', $selectedCycleId); });
                }]);
            }])->get();

            foreach ($teams as $team) {
                $completedCount = $team->employees->where('results_count', '>', 0)->count();
                $hrData[] = [
                    'team_name' => $team->name,
                    'completed_count' => $completedCount,
                ];
            }

            // Prepare data for the entire organization
            $organizationEmployeesData = $this->prepareEmployeesData($allEmployees, $levelNames);
        }

        if ($manager->role == 'head') {
            // Przygotuj dane pracowników działu
            $departmentEmployeesData = $this->prepareEmployeesData($departmentEmployees, $levelNames);
        
            // Przygotuj podsumowanie dla działu (podobnie jak dla HR)
            $teamsInDepartment = Team::with(['employees' => function($q) use ($selectedCycleId){
                    $q->select('id','department')->withCount(['results' => function($qq) use ($selectedCycleId){
                        $qq->when($selectedCycleId, function($qqq) use ($selectedCycleId){ $qqq->where('cycle_id', $selectedCycleId); });
                    }]);
                }])
                ->whereHas('employees', function ($query) use ($manager) {
                    $query->where('department', $manager->department);
                })
                ->get();
        
            foreach ($teamsInDepartment as $team) {
                // Count employees who have at least one result
                $completedCount = $team->employees->where('results_count', '>', 0)->count();
        
                $departmentData[] = [
                    'team_name' => $team->name,
                    'completed_count' => $completedCount,
                ];
            }
        }

        

        

        // Fetch all teams
        $teams = Team::all();

        // Build a map of access codes for the selected cycle for all relevant employees
        $allRelevantEmployeeIds = collect()
            ->merge($employees->pluck('id'))
            ->merge(($allEmployees ?? collect())->pluck('id'))
            ->merge(($departmentEmployees ?? collect())->pluck('id'))
            ->unique()
            ->values();

        $employeeAccessCodes = collect();
        if ($selectedCycleId && $allRelevantEmployeeIds->isNotEmpty()) {
            $employeeAccessCodes = EmployeeCycleAccessCode::whereIn('employee_id', $allRelevantEmployeeIds)
                ->where('cycle_id', $selectedCycleId)
                ->get()
                ->keyBy('employee_id');
        }

        // Pass variables to the view
        return view('manager_panel', compact(
            'employees',
            'employeesByLevel',
            'stats',
            'allEmployees',
            'departmentEmployees',
            'teamEmployeesData',
            'organizationEmployeesData',
            'departmentEmployeesData',
            'departmentData',
            'results',
            'employee',
            'levelSummaries',
            'levelNames',
            'hrData',
            'manager',
            'overriddenValues',
            'teams',
            'cycles',
            'selectedCycleId',
            'selectedCycle',
            'isSelectedCycleActive',
            'employeeAccessCodes'
        ));
        
    }

    // Generate or regenerate an access code for active cycle; show full code once
    public function generateAccessCode(Request $request, $employeeId)
    {
        $manager = auth()->user();
        $employee = Employee::with('team')->findOrFail($employeeId);

        // Access check
        if (!($manager->role == 'supermanager' ||
            $employee->manager_username == $manager->name ||
            ($manager->role == 'head' && $employee->department == $manager->department))) {
            abort(403);
        }

        // Must be active cycle
        $cycleId = $this->activeCycleId();
        if (!$cycleId) {
            return redirect()->back()->with('error', 'Brak aktywnego cyklu.');
        }

        // Generate readable code; allow optional length and ttl
        $length = (int)($request->input('length', 12));
        if ($length < 8) $length = 12;
        $ttl = $request->input('ttl'); // minutes (optional)

        $code = $this->generateHumanCode($length);
        $normalized = preg_replace('/[^A-Za-z0-9]/','',$code);
        $last4 = substr($normalized, -4);

        $payload = [
            'code_hash' => password_hash($code, PASSWORD_BCRYPT),
            'raw_last4' => $last4,
            'expires_at' => $ttl ? now()->addMinutes((int)$ttl) : null,
        ];

        $existing = EmployeeCycleAccessCode::where('employee_id',$employee->id)
            ->where('cycle_id',$cycleId)
            ->first();
        if ($existing) {
            $existing->update($payload);
            $existing->setFullCode($code); // Zapisz zaszyfrowany pełny kod
            $existing->save();
        } else {
            $accessCode = EmployeeCycleAccessCode::create(array_merge($payload, [
                'employee_id' => $employee->id,
                'cycle_id' => $cycleId,
            ]));
            $accessCode->setFullCode($code); // Zapisz zaszyfrowany pełny kod
            $accessCode->save();
        }

        return redirect()->back()->with([
            'generated_code' => $code,
            'generated_code_employee_id' => $employee->id,
        ]);
    }

    // Same generator as in console command (subset)
    private function generateHumanCode(int $length): string
    {
        $alphabet = str_split('ABCDEFGHJKMNPQRSTUVWXYZ23456789');
        $raw = '';
        while (strlen($raw) < $length) {
            $raw .= $alphabet[random_int(0, count($alphabet)-1)];
        }
        $raw = substr($raw, 0, $length);
        return strtoupper(implode('-', str_split($raw, 4)));
    }

    public function exportDepartment(Request $request)
    {
        $manager = Auth::user();

        if ($manager->role != 'head') {
            abort(403, 'Unauthorized');
        }

        $levelNames = $this->levelNames;

        // Cycle-aware filtering
        $cycleId = $request->query('cycle') ?: $this->selectedCycleId($request);

        // Fetch the department employees data
        $departmentEmployees = Employee::with([
                'team',
                'results' => function ($q) use ($cycleId) {
                    $q->when($cycleId, function($qq) use ($cycleId){ $qq->where('cycle_id', $cycleId); });
                },
                'results.competency.competencyTeamValues'
            ])
            ->where('department', $manager->department)
            ->get();

        $departmentEmployeesData = $this->prepareEmployeesData($departmentEmployees, $levelNames);

        // Prepare data for Excel
        $data = [];
        // Add headers
        $headers = ['Imię i nazwisko', 'Nazwa stanowiska'];
        foreach ($levelNames as $levelKey => $levelName) {
            $headers[] = $levelName;
        }
        $headers[] = 'Poziom';

        $data[] = $headers;

        foreach ($departmentEmployeesData as $emp) {
            $row = [
                $emp['name'],
                $emp['job_title'],
            ];
            foreach ($levelNames as $levelKey => $levelName) {
                $percentageValueManager = $emp['levelPercentagesManager'][$levelName] ?? null;
                $percentageManager = is_numeric($percentageValueManager) ? number_format((float)$percentageValueManager, 2) . '%' : 'N/D';
                $row[] = $percentageManager;
            }
            $row[] = $emp['highestLevelManager'];
            $data[] = $row;
        }

        // Generate and download the Excel file
        $date = now()->format('Y-m-d_H-i');
        $cycle = $cycleId ? AssessmentCycle::find($cycleId) : null;
        $cycleSuffix = $cycle ? ('_' . $cycle->label) : '';
        return Excel::download(
            new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
                private $data;

                public function __construct(array $data)
                {
                    $this->data = $data;
                }

                public function array(): array
                {
                    return $this->data;
                }
            },
            'P-CAP Raport Dział-' . $date . '_' . str_replace(' ', '_', $manager->department) . $cycleSuffix . '.xlsx'
        );
    }


    /**
     * Prepare employee data for team and organization summaries
     */
    private function prepareEmployeesData($employees, $levelNames)
    {
        $employeesData = [];

        foreach ($employees as $emp) {
            if (!$emp->team) {
                Log::warning('No team found for employee', [
                    'employee_id' => $emp->id,
                    'employee_name' => $emp->name,
                    'department' => $emp->department,
                ]);
                continue;
            }

            // Arrays to store percentages for each level
            $levelPercentagesEmployee = [];
            $levelPercentagesManager = [];

            foreach ($levelNames as $levelKey => $levelName) {
                // Map config keys to actual database level values
                // Extract level number from level name (e.g., "1. Junior" -> 1)
                $actualLevel = intval(substr($levelName, 0, 1));
                

                
                $levelResults = $emp->results->filter(function ($result) use ($actualLevel) {
                    return (int)$result->competency->level === $actualLevel;
                });

                if ($levelResults->isEmpty()) {
                    $levelPercentagesEmployee[$levelName] = null;
                    $levelPercentagesManager[$levelName] = null;
                    continue;
                }

                $earnedPointsEmployee = 0;
                $earnedPointsManager = 0;
                $maxPoints = 0;

                foreach ($levelResults as $result) {
                    // Use the competency value specific to the employee's team
                    $competencyValue = $emp->getCompetencyValue($result->competency_id);

                    // Employee's score
                    $scoreEmployee = $result->score;

                    // Manager's score (if exists, else employee's score)
                    $scoreManager = ($result->score_manager !== null) ? $result->score_manager : $result->score;

                    if ($scoreEmployee > 0) {
                        $earnedPointsEmployee += $competencyValue * $scoreEmployee;
                    }
                    if ($scoreManager > 0) {
                        $earnedPointsManager += $competencyValue * $scoreManager;
                    }
                    // Add to maxPoints only if either employee or manager scored > 0 (not "Nie dotyczy")
                    if (($scoreEmployee > 0 || $scoreManager > 0) && $competencyValue > 0) {
                        $maxPoints += $competencyValue;
                    }
                }

                $percentageEmployee = $maxPoints > 0 ? ($earnedPointsEmployee / $maxPoints) * 100 : 0;
                $percentageManager = $maxPoints > 0 ? ($earnedPointsManager / $maxPoints) * 100 : 0;



                $levelPercentagesEmployee[$levelName] = $percentageEmployee;
                $levelPercentagesManager[$levelName] = $percentageManager;
            }

            // Dotychczasowa logika wyliczenia najwyższego poziomu:
            $highestLevelEmployee = $this->determineHighestLevel(
                $levelPercentagesEmployee['1. Junior'] ?? 0,
                $levelPercentagesEmployee['2. Specjalista'] ?? 0,
                $levelPercentagesEmployee['3. Senior'] ?? 0,
                $levelPercentagesEmployee['4. Supervisor'] ?? 0,
                $levelPercentagesEmployee['5. Manager'] ?? 0
            );
            
            $highestLevelManager = $this->determineHighestLevel(
                $levelPercentagesManager['1. Junior'] ?? 0,
                $levelPercentagesManager['2. Specjalista'] ?? 0,
                $levelPercentagesManager['3. Senior'] ?? 0,
                $levelPercentagesManager['4. Supervisor'] ?? 0,
                $levelPercentagesManager['5. Manager'] ?? 0
            );
            // Po wyliczeniu $highestLevelEmployee i $highestLevelManager
            // Jeśli $highestLevelEmployee lub $highestLevelManager == "3/4. Senior/Supervisor"
            // to w statystykach i sumowaniach powinniśmy liczyć go jako "4. Supervisor"

            if ($highestLevelEmployee === "3/4. Senior/Supervisor") {
                // Wyświetlanie w widoku indywidualnym zostaje takie samo
                // Nic nie zmieniamy dla indywidualnego widoku
            }

            if ($highestLevelManager === "3/4. Senior/Supervisor") {
                // Dla podsumowań:
                // W samym $employeesData pozostawiamy "3/4. Senior/Supervisor" tak, żeby w tabelach
                // indywidualnych i w XLS wyświetlało się "3/4. Senior/Supervisor".
                // Ale przy zliczaniu levelCounts i w podsumowaniach użyjemy mapowania
                // "3/4. Senior/Supervisor" -> "4. Supervisor".
                // Aby to było łatwe, można to mapowanie zrobić nieco później.
            }

            

            // Add data to employeesData
            $employeesData[] = [
                'id' => $emp->id,
                'name' => $emp->name ?? 'Brak danych',
                'job_title' => $emp->job_title ?? 'Brak danych',
                'department' => $emp->department ?? 'Brak danych',
                'levelPercentagesEmployee' => $levelPercentagesEmployee,
                'levelPercentagesManager' => $levelPercentagesManager,
                'highestLevelEmployee' => $highestLevelEmployee,
                'highestLevelManager' => $highestLevelManager,
            ];
        }

        return $employeesData;
    }




    public function downloadTeamReport(Request $request)
    {
        $teamId = $request->input('team_id');
        $format = $request->input('format');
    
        $team = Team::findOrFail($teamId);
    
        // Cycle-aware
        $cycleId = $request->query('cycle') ?: $this->selectedCycleId($request);

        // Fetch employees of the team based on department
        $employees = Employee::with([
                'team',
                'results' => function ($q) use ($cycleId) {
                    $q->when($cycleId, function($qq) use ($cycleId){ $qq->where('cycle_id', $cycleId); });
                },
                'results.competency'
            ])
            ->where('department', $team->name)
            ->get();
    
        // Prepare data
        $employeesData = $this->prepareEmployeesData($employees, $this->levelNames);
        // Defensively sanitize against legacy level 6 (Head of)
        $allowedLevelKeys = array_values($this->levelNames); // e.g., ['1. Junior', ..., '5. Manager']
        $allowedMap = array_fill_keys($allowedLevelKeys, true);
        foreach ($employeesData as &$empData) {
            if (isset($empData['levelPercentagesManager']) && is_array($empData['levelPercentagesManager'])) {
                $empData['levelPercentagesManager'] = array_intersect_key($empData['levelPercentagesManager'], $allowedMap);
            }
            if (!empty($empData['highestLevelManager'])) {
                $hl = (string)$empData['highestLevelManager'];
                if (strpos($hl, '6') === 0 || stripos($hl, 'Head of') !== false) {
                    $empData['highestLevelManager'] = '5. Manager';
                }
            }
        }
        unset($empData);
    
        if ($format === 'pdf') {
            // Generate PDF
            $pdf = PDF::loadView('exports.team_report_pdf', [
                'team' => $team,
                'employeesData' => $employeesData,
                'levelNames' => $this->levelNames
            ])->setPaper('a4', 'landscape');
    
            $date = now()->format('Y-m-d_H-i');
            $cycle = $cycleId ? AssessmentCycle::find($cycleId) : null;
            $cycleSuffix = $cycle ? ('_' . $cycle->label) : '';
            return $pdf->download('P-CAP Raport Zespół-' . $date . '_' . str_replace(' ', '_', $team->name) . $cycleSuffix . '.pdf');
        } elseif ($format === 'xls') {
            // Prepare data for Excel
            // Najpierw definiujemy nagłówki
            $headers = ['Imię i nazwisko', 'Nazwa stanowiska'];
            foreach ($this->levelNames as $levelName) {
                $headers[] = $levelName;
            }
            $headers[] = 'Poziom';
    
            // Usuwamy powtórną definicję headers:
            // $headers = ['Imię i nazwisko', 'Nazwa stanowiska'];
            // foreach ($this->levelNames as $levelName) {
            //     $headers[] = $levelName;
            // }
            // $headers[] = 'Poziom';
    
            // Budujemy dane do eksportu
            $data = [];
            foreach ($employeesData as $empData) {
                $row = [
                    $empData['name'],
                    $empData['job_title'],
                ];
                foreach ($this->levelNames as $levelName) {
                    // Korzystamy z managerowskich procentów
                    $percentageValueManager = $empData['levelPercentagesManager'][$levelName] ?? null;
                    $percentageManager = is_numeric($percentageValueManager) ? number_format($percentageValueManager, 2) . '%' : 'N/D';
                    $row[] = $percentageManager;
                }
                // Używamy najwyższego poziomu menedżerskiego
                $row[] = $empData['highestLevelManager'];
                $data[] = $row;
            }
    
            // Eksport do Excela
            $date = now()->format('Y-m-d_H-i');
            $teamName = str_replace(' ', '_', $team->name);
            $cycle = $cycleId ? AssessmentCycle::find($cycleId) : null;
            $cycleSuffix = $cycle ? ('_' . $cycle->label) : '';
            $filename = "P-CAP Raport Zespół-{$date}_{$teamName}{$cycleSuffix}.xlsx";
            return Excel::download(
                new TeamReportExport($data, $headers),
                $filename
            );
        }
    }


    public function update(Request $request)
    {
        // Only allow updates for results in the active cycle
        $activeCycleId = $this->activeCycleId();
        if (!$activeCycleId) {
            return redirect()->back()->with('error', 'Brak aktywnego cyklu. Zapis zablokowany.');
        }
        // Update manager's assessments (handle empty submissions safely)
        $scores = (array) $request->input('score_manager', []);
        $feedbacks = (array) $request->input('feedback_manager', []);
        $skipped = 0;
        foreach ($scores as $resultId => $scoreManager) {
            $result = Result::find($resultId);

            if ($result) {
                if ((int)($result->cycle_id ?? 0) !== (int)$activeCycleId) {
                    // Skip updates to historical cycles
                    $skipped++;
                    continue;
                }
                if ($scoreManager === 'above_expectations') {
                    $result->score_manager = 1.0;
                    $result->above_expectations_manager = 1;
                } elseif ($scoreManager === null || $scoreManager === '') {
                    $result->score_manager = null;
                    $result->above_expectations_manager = 0;
                } else {
                    $result->score_manager = (float)$scoreManager;
                    $result->above_expectations_manager = 0;
                }
                $result->feedback_manager = $feedbacks[$resultId] ?? null;
                $result->save();
            }
        }

        $employeeId = $request->input('employee_id');
        $employee = Employee::find($employeeId);

         // Handle overridden competency values
    $competencyValues = (array) $request->input('competency_values', []);
    $deleteCompetencyValues = (array) $request->input('delete_competency_values', []);

        // Process deletions
        foreach ($deleteCompetencyValues as $competencyId) {
            EmployeeCompetencyValue::where('employee_id', $employeeId)
                ->where('competency_id', $competencyId)
                ->delete();
        }

        // Save or update overridden competency values
        foreach ($competencyValues as $competencyId => $value) {
            EmployeeCompetencyValue::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'competency_id' => $competencyId,
                ],
                [
                    'value' => $value,
                ]
            );
        }

        // Redirect back to the manager panel with the employee parameter and cycle context
        $msg = $skipped > 0
            ? 'Oceny zostały zaktualizowane dla aktywnego cyklu. Zmiany w cyklach historycznych zostały zablokowane.'
            : 'Oceny zostały zaktualizowane.';
        return redirect()->action([ManagerController::class, 'index'], ['employee' => $employeeId, 'cycle' => $activeCycleId])->with('success', $msg);
    }

    private function determineHighestLevel($percJunior, $percSpecialist, $percSenior, $percSupervisor, $percManager)
    {
        // Thresholds for each level (same as SelfAssessmentController)
        $levels = [
            1 => ['percentage' => $percJunior, 'threshold' => 80, 'name' => '1. Junior'],
            2 => ['percentage' => $percSpecialist, 'threshold' => 85, 'name' => '2. Specjalista'],
            3 => ['percentage' => $percSenior, 'threshold' => 85, 'name' => '3. Senior'],
            4 => ['percentage' => $percSupervisor, 'threshold' => 80, 'name' => '4. Supervisor'],
            5 => ['percentage' => $percManager, 'threshold' => 80, 'name' => '5. Manager'],
        ];
        
        $achievedLevel = '1. Junior'; // Default to first level
        
        // Check levels sequentially - must pass each level to achieve higher ones
        foreach ($levels as $levelNumber => $level) {
            $percentage = is_numeric($level['percentage']) ? $level['percentage'] : 0;
            
            if ($percentage >= $level['threshold']) {
                $achievedLevel = $level['name']; // Passed this level, continue
            } else {
                // Failed this level - stay at previous level and stop checking
                break;
            }
        }
        
        return $achievedLevel;
    }

    
    

    public function generatePdf($employeeId)
    {
        $manager = auth()->user();

        // Fetch employee with team relationship
        $employee = Employee::with('team')->findOrFail($employeeId);

        // Check if manager has access to this employee
        if (!($manager->role == 'supermanager' ||
            $employee->manager_username == $manager->name ||
            ($manager->role == 'head' && $employee->department == $manager->department))) {
            abort(403);
        }

        // Cycle-aware results
        $cycleId = request()->query('cycle') ?: $this->selectedCycleId(request());
        $results = Result::where('employee_id', $employeeId)
            ->when($cycleId, function($q) use ($cycleId){ $q->where('cycle_id', $cycleId); })
            ->with('competency')
            ->get()
            // Pomijamy wyniki dla wycofanego poziomu 6 (Head of)
            ->filter(function($r){
                $lvl = trim((string)($r->competency->level ?? ''));
                return strpos($lvl, '6') !== 0;
            });

        // Generate PDF
        $pdf = PDF::loadView('pdf.employee_report', compact('employee', 'results'))
            ->setPaper('a4', 'landscape');

        $date = now()->format('Y-m-d_H-i');
        $name = str_replace(' ', '_', $employee->name);
        $cycle = $cycleId ? AssessmentCycle::find($cycleId) : null;
        $cycleSuffix = $cycle ? ('_'.$cycle->label) : '';
        $filename = "P-CAP Raport Full-{$date}_{$name}{$cycleSuffix}.pdf";
        return $pdf->download($filename);
    }

    public function generateXls($employeeId)
    {
        $manager = auth()->user();

        // Fetch employee with team relationship
        $employee = Employee::with('team')->findOrFail($employeeId);

        // Check if manager has access to this employee
        if (!($manager->role == 'supermanager' ||
            $employee->manager_username == $manager->name ||
            ($manager->role == 'head' && $employee->department == $manager->department))) {
            abort(403);
        }

        // Fetch results with necessary relationships (cycle-aware)
        $cycleId = request()->query('cycle') ?: $this->selectedCycleId(request());
        $results = Result::where('employee_id', $employeeId)
            ->when($cycleId, function($q) use ($cycleId){ $q->where('cycle_id', $cycleId); })
            ->with([
                'competency' => function ($query) use ($employee) {
                    $query->with(['competencyTeamValues' => function ($q) use ($employee) {
                        $q->where('team_id', $employee->team->id);
                    }]);
                }
            ])
            ->get()
            ->sortBy(function ($result) {
                return $result->competency->level;
            })
            // Pomijamy poziom 6 (Head of)
            ->filter(function($r){
                $lvl = trim((string)($r->competency->level ?? ''));
                return strpos($lvl, '6') !== 0;
            });

        // Prepare data for Excel
        $data = [];
        // Add headers
        $data[] = [
            'Kompetencja',
            'Poziom',
            'Rodzaj kompetencji',
            'Ocena użytkownika',
            'Czy powyżej oczekiwań (użytkownik)',
            'Argumentacja użytkownika',
            'Wartość',
            'Ocena managera',
            'Czy powyżej oczekiwań (manager)',
            'Feedback od managera',
            'Punkty uzyskane',
        ];

        // Initialize variables
        $currentLevel = null;
        $levelEarnedPoints = 0;
        $levelPossiblePoints = 0;

        // Inicjalizacja sum ogólnych
        $totalEarnedPoints = 0;
        $totalPossiblePoints = 0;

        foreach ($results as $result) {
            $competency = $result->competency;
            $competencyLevel = $competency->level;

            // If we're starting a new level
            if ($currentLevel !== $competencyLevel) {
                // If not the first level, add the summary row for the previous level
                if ($currentLevel !== null) {
                    // Calculate percentage for the previous level
                    $percentage = $levelPossiblePoints > 0 ? ($levelEarnedPoints / $levelPossiblePoints) * 100 : 'N/D';
                    // Add summary rows to data
                    $data[] = ['', '', '', '', '', '', '', '', '', '', '']; // Empty row
                    $data[] = ['', '', '', '', '', '', '', '', '', 'Razem dla poziomu ' . $currentLevel, ''];
                    $data[] = ['', '', '', '', '', '', '', '', '', 'Punkty możliwe do zdobycia', $levelPossiblePoints];
                    $data[] = ['', '', '', '', '', '', '', '', '', 'Punkty uzyskane', $levelEarnedPoints];
                    $data[] = ['', '', '', '', '', '', '', '', '', 'Procent uzyskany', is_numeric($percentage) ? number_format($percentage, 2) . '%' : $percentage];
                    // Add empty row as separator
                    $data[] = ['', '', '', '', '', '', '', '', '', '', ''];
                }
                // Reset level sums
                $levelEarnedPoints = 0;
                $levelPossiblePoints = 0;
                // Update current level
                $currentLevel = $competencyLevel;

                // Add a row indicating the new level
                $data[] = ['', '', '', '', '', '', '', '', '', 'Poziom: ' . $currentLevel, ''];
            }

            // Get competency value
            $competencyValue = $competency->getValueForTeam($employee->team->id);

            // Use manager's score if available, otherwise employee's score
            $score = $result->score_manager !== null ? $result->score_manager : $result->score;

            // Jeśli ocena > 0, uwzględnij w obliczeniach
            if ($score > 0) {
                $earnedPoints = $competencyValue * $score;
                $levelEarnedPoints += $earnedPoints;
                $levelPossiblePoints += $competencyValue;

                // Akumuluj sumy ogólne
                $totalEarnedPoints += $earnedPoints;
                $totalPossiblePoints += $competencyValue;
            } else {
                $earnedPoints = 'N/D';
            }

            // Przygotuj wyświetlaną ocenę użytkownika
            $displayScoreUser = $result->score > 0 ? $result->score : 'N/D';

            // Przygotuj wyświetlaną ocenę managera
            if ($result->score_manager !== null) {
                if ($result->score_manager > 0) {
                    $displayScoreManager = $result->score_manager;
                } elseif ($result->score_manager == 0) {
                    $displayScoreManager = 'N/D';
                } else {
                    $displayScoreManager = $result->score_manager;
                }
            } else {
                $displayScoreManager = '';
            }

            // Add data row
            $data[] = [
                $competency->competency_name,
                $competency->level,
                $competency->competency_type,
                $displayScoreUser,
                $result->above_expectations ? 'Tak' : 'Nie',
                $result->comments,
                $competencyValue,
                $displayScoreManager,
                $result->above_expectations_manager ? 'Tak' : 'Nie',
                $result->feedback_manager,
                is_numeric($earnedPoints) ? $earnedPoints : $earnedPoints,
            ];
        }

        // After the loop, add summary for the last level
        if ($currentLevel !== null) {
            // Calculate percentage for the last level
            $percentage = $levelPossiblePoints > 0 ? ($levelEarnedPoints / $levelPossiblePoints) * 100 : 'N/D';
            // Add summary rows to data
            $data[] = ['', '', '', '', '', '', '', '', '', '', '']; // Empty row
            $data[] = ['', '', '', '', '', '', '', '', '', 'Razem dla poziomu ' . $currentLevel, ''];
            $data[] = ['', '', '', '', '', '', '', '', '', 'Punkty możliwe do zdobycia', $levelPossiblePoints];
            $data[] = ['', '', '', '', '', '', '', '', '', 'Punkty uzyskane', $levelEarnedPoints];
            $data[] = ['', '', '', '', '', '', '', '', '', 'Procent uzyskany', is_numeric($percentage) ? number_format($percentage, 2) . '%' : $percentage];
        }

        // Opcjonalnie, dodaj podsumowanie ogólne
        $data[] = ['', '', '', '', '', '', '', '', '', '', '']; // Pusta linia
        $data[] = ['', '', '', '', '', '', '', '', '', 'Podsumowanie ogólne', ''];
        $data[] = ['', '', '', '', '', '', '', '', '', 'Punkty możliwe do zdobycia', $totalPossiblePoints];
        $data[] = ['', '', '', '', '', '', '', '', '', 'Punkty uzyskane', $totalEarnedPoints];
        $totalPercentage = $totalPossiblePoints > 0 ? ($totalEarnedPoints / $totalPossiblePoints) * 100 : 'N/D';
        $data[] = ['', '', '', '', '', '', '', '', '', 'Ogólny procent uzyskany', is_numeric($totalPercentage) ? number_format($totalPercentage, 2) . '%' : $totalPercentage];

        // Export
        $date = now()->format('Y-m-d_H-i');
        $name = str_replace(' ', '_', $employee->name);
        $cycle = $cycleId ? AssessmentCycle::find($cycleId) : null;
        $cycleSuffix = $cycle ? ('_'.$cycle->label) : '';
        $filename = "P-CAP Raport Full-{$date}_{$name}{$cycleSuffix}.xlsx";
        return Excel::download(
            new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
                private $data;
                public function __construct(array $data) { $this->data = $data; }
                public function array(): array { return $this->data; }
            },
            $filename
        );
    }

    // Nowe metody dla obsługi 3-poziomowej hierarchii
    private function getEmployeesForManager($manager, $selectedCycleId)
    {
        $query = Employee::with([
            'team:id,name',
            'supervisor:username,name',
            'manager:username,name', 
            'head:username,name',
            'team.competencyTeamValues:id,team_id,competency_id,value',
            'overriddenCompetencyValues:id,employee_id,competency_id,value',
            'results' => function ($q) use ($selectedCycleId) {
                $q->select('id', 'employee_id', 'competency_id', 'score', 'score_manager', 'cycle_id')
                  ->when($selectedCycleId, function($qq) use ($selectedCycleId) {
                      $qq->where('cycle_id', $selectedCycleId);
                  });
            },
            'results.competency' => function ($q) {
                $q->select('id', 'level');
            },
        ]);

        // Sprawdź czy są struktury hierarchiczne (pracownicy z przypisaną hierarchią)
        $hasHierarchyStructures = Employee::where(function($q) {
            $q->whereNotNull('supervisor_username')
              ->orWhereNotNull('manager_username') 
              ->orWhereNotNull('head_username');
        })->exists();
        
        if (!$hasHierarchyStructures) {
            // Gdy brak struktur hierarchicznych, wszyscy (włącznie z supermanagerem) 
            // powinni mieć pustą kolekcję w zakładkach zespołowych
            return collect();
        }

        switch ($manager->role) {
            case 'supervisor':
                return $query->where('supervisor_username', $manager->username)->get();
                
            case 'manager':
                return $query->where(function($q) use ($manager) {
                    $q->where('manager_username', $manager->username)
                      ->orWhere('supervisor_username', $manager->username);
                })->get();
                
            case 'head':
                // Head w zakładkach individual/zespół widzi tylko bezpośrednio podlegających
                return $query->where('head_username', $manager->username)->get();
                
            case 'supermanager':
                // Supermanager w zakładkach zespołowych powinien widzieć tylko swoich bezpośrednich
                // pracowników zgodnie z hierarchią (może być supervisor/manager/head w strukturze)
                return $query->where(function($q) use ($manager) {
                    $q->where('head_username', $manager->username)
                      ->orWhere('manager_username', $manager->username)
                      ->orWhere('supervisor_username', $manager->username);
                })->get();
                
            default:
                return collect();
        }
    }

    private function groupEmployeesByLevel($employees, $manager)
    {
        $grouped = [
            'direct' => collect(),
            'supervisors' => collect(),
            'managers' => collect(),
        ];
        
        foreach ($employees as $employee) {
            $relationship = $employee->getRelationshipToManager($manager);
            
            if ($relationship['type'] === 'direct') {
                $grouped['direct']->push($employee);
            } elseif ($relationship['type'] === 'indirect') {
                if ($relationship['through'] === 'supervisor') {
                    $grouped['supervisors']->push($employee);
                } elseif ($relationship['through'] === 'manager') {
                    $grouped['managers']->push($employee);
                }
            }
        }
        
        return $grouped;
    }

    private function groupEmployeesByCompetencyLevel($employees, $selectedCycleId)
    {
        $grouped = [
            1 => collect(),
            2 => collect(),
            3 => collect(),
            4 => collect(),
            5 => collect(),
        ];
        
        if (!$selectedCycleId) {
            return $grouped;
        }
        
        foreach ($employees as $employee) {
            try {
                // Pobierz wyniki dla danego pracownika w wybranym cyklu
                $results = Result::where('employee_id', $employee->id)
                    ->where('cycle_id', $selectedCycleId)
                    ->with('competency:id,level')
                    ->get();
                    
                if ($results->isNotEmpty()) {
                    // Oblicz średni poziom kompetencji dla pracownika
                    $levels = $results->map(function($result) {
                        if ($result->competency && is_numeric($result->competency->level)) {
                            return (float) $result->competency->level;
                        }
                        return 1.0; // Domyślny poziom jeśli brak danych
                    })->filter(); // Usuń puste wartości
                    
                    if ($levels->isNotEmpty()) {
                        $avgLevel = $levels->avg();
                        // Zaokrąglij do najbliższego poziomu (1-5)
                        $level = max(1, min(5, round($avgLevel)));
                        $grouped[$level]->push($employee);
                    } else {
                        // Brak prawidłowych poziomów - domyślnie poziom 1
                        $grouped[1]->push($employee);
                    }
                } else {
                    // Brak wyników - domyślnie poziom 1
                    $grouped[1]->push($employee);
                }
            } catch (\Exception $e) {
                // W przypadku błędu - domyślnie poziom 1
                $grouped[1]->push($employee);
            }
        }
        
        return $grouped;
    }

    private function calculateEmployeeStats($employees, $manager)
    {
        $stats = [
            'total' => $employees->count(),
            'direct' => 0,
            'through_supervisors' => 0,
            'through_managers' => 0,
            'with_results' => 0,
            'pending' => 0,
        ];
        
        foreach ($employees as $employee) {
            $relationship = $employee->getRelationshipToManager($manager);
            
            // Liczenie bezpośrednich vs pośrednich
            if ($relationship['type'] === 'direct') {
                $stats['direct']++;
            } elseif ($relationship['type'] === 'indirect') {
                if ($relationship['through'] === 'supervisor') {
                    $stats['through_supervisors']++;
                } elseif ($relationship['through'] === 'manager') {
                    $stats['through_managers']++;
                }
            }
            
            // Liczenie statusów ocen
            if ($employee->results->isNotEmpty()) {
                $stats['with_results']++;
            } else {
                $stats['pending']++;
            }
        }
        
        return $stats;
    }

    private function hasAccessToEmployee($manager, $employee)
    {
        // Supermanager widzi wszystkich
        if ($manager->role == 'supermanager') {
            return true;
        }
        
        // Supervisor widzi tylko swoich bezpośrednich pracowników
        if ($manager->role == 'supervisor' && $employee->supervisor_username == $manager->username) {
            return true;
        }
        
        // Manager widzi swoich bezpośrednich pracowników i pracowników swoich supervisorów
        if ($manager->role == 'manager') {
            return $employee->manager_username == $manager->username || 
                   $employee->supervisor_username == $manager->username;
        }
        
        // Head widzi wszystkich w swojej hierarchii - sprawdzamy wszystkie struktury hierarchii gdzie head jest przypisany
        if ($manager->role == 'head') {
            // Sprawdź bezpośrednie przypisanie
            if ($employee->head_username == $manager->username ||
                $employee->manager_username == $manager->username ||
                $employee->supervisor_username == $manager->username) {
                return true;
            }
            
            // Sprawdź czy head ma dostęp przez struktury hierarchii w różnych działach
            $hasAccess = \App\Models\HierarchyStructure::where('head_username', $manager->username)
                ->where('department', $employee->department)
                ->exists();
                
            return $hasAccess;
        }
        
        return false;
    }

    /**
     * Show the new modern manager panel
     */
    public function showNew(Request $request)
    {
        // Reuse the same logic as index() but return the new view
        $data = $this->getManagerPanelData($request);
        return view('manager_panel_new', $data);
    }

    /**
     * Extract common data preparation logic
     */
    private function getManagerPanelData(Request $request)
    {
        $manager = auth()->user();

        if (!$manager) {
            abort(403, 'Użytkownik nie jest zalogowany.');
        }

        $levelNames = $this->levelNames;

        // Cycles
        $selectedCycleId = $this->selectedCycleId($request);
        $selectedCycle = $selectedCycleId ? AssessmentCycle::find($selectedCycleId) : null;
        $isSelectedCycleActive = $selectedCycle ? (bool)$selectedCycle->is_active : false;
        $cycles = AssessmentCycle::orderByDesc('year')->orderByDesc('period')->get();

        // Get the employee ID from the request (if selected)
        $employeeId = $request->input('employee');

        // Pobierz pracowników według nowej hierarchii
        $employees = $this->getEmployeesForManager($manager, $selectedCycleId);
        
        // Grupowanie pracowników według poziomu hierarchii
        $employeesByLevel = $this->groupEmployeesByLevel($employees, $manager);
        
        // Grupowanie według poziomów kompetencji (1-5) dla dashboardu
        $employeesByCompetencyLevel = $this->groupEmployeesByCompetencyLevel($employees, $selectedCycleId);
        
        // Statystyki dla zakładek
        $stats = $this->calculateEmployeeStats($employees, $manager);

        // For supermanagers, fetch all employees with necessary relationships
        if ($manager->role == 'supermanager') {
            $allEmployees = Employee::with([
                    'team:id,name',
                    'team.competencyTeamValues:id,team_id,competency_id,value',
                    'overriddenCompetencyValues:id,employee_id,competency_id,value',
                    'results' => function ($q) use ($selectedCycleId) {
                        $q->select('id','employee_id','competency_id','score','score_manager','cycle_id')
                          ->when($selectedCycleId, function($qq) use ($selectedCycleId){ $qq->where('cycle_id', $selectedCycleId); });
                    },
                    'results.competency' => function ($q) {
                        $q->select('id','level');
                    },
                ])
                ->get(['id','name','job_title','department']);
        } else {
            $allEmployees = collect(); // Empty collection for non-supermanagers
        }

        // Dla head - używamy nowej logiki hierarchii
        if ($manager->role == 'head') {
            // Pobierz wszystkie działy gdzie head ma struktury hierarchii
            $headDepartments = \App\Models\HierarchyStructure::where('head_username', $manager->username)
                ->pluck('department')
                ->unique()
                ->toArray();
                
            $departmentEmployees = Employee::with([
                    'team:id,name',
                    'team.competencyTeamValues:id,team_id,competency_id,value',
                    'overriddenCompetencyValues:id,employee_id,competency_id,value',
                    'results' => function ($q) use ($selectedCycleId) {
                        $q->select('id','employee_id','competency_id','score','score_manager','cycle_id')
                          ->when($selectedCycleId, function($qq) use ($selectedCycleId){ $qq->where('cycle_id', $selectedCycleId); });
                    },
                    'results.competency' => function ($q) {
                        $q->select('id','level');
                    },
                ])
                ->where(function($q) use ($manager, $headDepartments) {
                    $q->where('head_username', $manager->username)
                      ->orWhere('manager_username', $manager->username)
                      ->orWhere('supervisor_username', $manager->username)
                      ->orWhereIn('department', $headDepartments);
                })
                ->get(['id','name','job_title','department']);
        } else {
            $departmentEmployees = collect(); // Empty collection for non-heads
        }

        // Initialize variables
        $employee = null;
        $results = collect();
        $levelSummaries = [];
        $overriddenValues = collect();
        $accessCode = null;
        $previousCycleResults = collect();

        if ($employeeId) {
            $employee = Employee::with(['team', 'manager', 'supervisor', 'head', 'results.competency.competencyTeamValues'])->find($employeeId);

            // Check if manager has access to this employee
            if ($employee && $this->hasAccessToEmployee($manager, $employee)) {
                $results = $employee->results()
                    ->when($selectedCycleId, function($q) use ($selectedCycleId){ $q->where('cycle_id', $selectedCycleId); })
                    ->with('competency.competencyTeamValues')->get();

                // Get previous cycle results for comparison
                $previousCycleResults = collect();
                if ($selectedCycleId) {
                    $previousCycle = AssessmentCycle::where('id', '<', $selectedCycleId)
                        ->orderBy('id', 'desc')
                        ->first();
                    
                    if ($previousCycle) {
                        $previousCycleResults = $employee->results()
                            ->where('cycle_id', $previousCycle->id)
                            ->with('competency.competencyTeamValues')
                            ->get()
                            ->keyBy('competency_id');
                    }
                }

                // Initialize level summaries
                $levelSummaries = [];

                // Calculate per-level summaries for individual tab
                $levels = $results->groupBy('competency.level');
                foreach ($levels as $level => $levelResults) {
                    $earnedPointsEmployee = 0;
                    $earnedPointsManager = 0;
                    $maxPoints = 0;
                    $competencyCount = $levelResults->count();
                
                    foreach ($levelResults as $result) {
                        $competencyValue = $employee->getCompetencyValue($result->competency_id) ?? 0;

                        // Employee's score
                        $scoreEmployee = $result->score;

                        // Manager's score (if exists, else employee's score)
                        $scoreManager = ($result->score_manager !== null) ? $result->score_manager : $result->score;

                        // Calculate earned points if score > 0
                        if ($scoreEmployee > 0 && $competencyValue > 0) {
                            $earnedPointsEmployee += $competencyValue * $scoreEmployee;
                        }
                        if ($scoreManager > 0 && $competencyValue > 0) {
                            $earnedPointsManager += $competencyValue * $scoreManager;
                        }
                        
                        // Add to maxPoints only if either employee or manager scored > 0 (not "Nie dotyczy")
                        if (($scoreEmployee > 0 || $scoreManager > 0) && $competencyValue > 0) {
                            $maxPoints += $competencyValue;
                        }
                    }
                    
                    // Calculate percentages
                    $percentageEmployee = $maxPoints > 0 ? ($earnedPointsEmployee / $maxPoints) * 100 : 'N/D';
                    $percentageManager = $maxPoints > 0 ? ($earnedPointsManager / $maxPoints) * 100 : 'N/D';

                    // Store in levelSummaries with all required keys
                    $levelSummaries[$level] = [
                        'earnedPointsEmployee' => $earnedPointsEmployee ?? 0,
                        'earnedPointsManager' => $earnedPointsManager ?? 0,
                        'maxPoints' => $maxPoints ?? 0,
                        'percentageEmployee' => $percentageEmployee ?? 'N/D',
                        'percentageManager' => $percentageManager ?? 'N/D',
                        'count' => $competencyCount ?? 0,
                    ];
                }

                // Fetch overridden values
                $overriddenValues = EmployeeCompetencyValue::where('employee_id', $employee->id)
                    ->pluck('value', 'competency_id');

                // Fetch existing access code
                if ($selectedCycleId) {
                    $accessCode = EmployeeCycleAccessCode::where('employee_id', $employee->id)
                        ->where('cycle_id', $selectedCycleId)
                        ->first();
                }

            } else {
                abort(403, 'Nie masz dostępu do tego pracownika.');
            }
        }

        // Prepare data for teams
        $teamEmployeesData = $this->prepareEmployeesData($employees, $levelNames);

        // Initialize variables for different roles
        $hrData = [];
        $organizationEmployeesData = [];
        $departmentEmployeesData = [];
        $teams = collect();

        // Prepare role-specific data
        if ($manager->role == 'supermanager') {
            $teams = Team::with(['employees' => function($q) use ($selectedCycleId){
                $q->select('id','department')->withCount(['results' => function($qq) use ($selectedCycleId){
                    $qq->when($selectedCycleId, function($qqq) use ($selectedCycleId){ $qqq->where('cycle_id', $selectedCycleId); });
                }]);
            }])->get();

            foreach ($teams as $team) {
                $completedCount = $team->employees->where('results_count', '>', 0)->count();
                $totalCount = $team->employees->count();
                $hrData[] = [
                    'team_name' => $team->name,
                    'completed_count' => $completedCount,
                    'total_count' => $totalCount,
                ];
            }

            $organizationEmployeesData = $this->prepareEmployeesData($allEmployees, $levelNames);
        }

        if ($manager->role == 'head') {
            $departmentEmployeesData = $this->prepareEmployeesData($departmentEmployees, $levelNames);
        }

        // Prepare access codes for the codes section
        $employeeAccessCodes = collect();
        if ($selectedCycleId) {
            $allEmployeesForCodes = collect();
            if ($manager->role == 'supermanager') {
                $allEmployeesForCodes = $allEmployees;
            } elseif ($manager->role == 'head') {
                $allEmployeesForCodes = $departmentEmployees;
            } else {
                $allEmployeesForCodes = $employees;
            }

            $codes = EmployeeCycleAccessCode::where('cycle_id', $selectedCycleId)
                ->whereIn('employee_id', $allEmployeesForCodes->pluck('id'))
                ->get()
                ->keyBy('employee_id');
            
            $employeeAccessCodes = $codes;
        }

        return compact(
            'manager',
            'levelNames',
            'selectedCycleId',
            'selectedCycle',
            'isSelectedCycleActive',
            'cycles',
            'employees',
            'allEmployees',
            'departmentEmployees',
            'employee',
            'results',
            'levelSummaries',
            'overriddenValues',
            'accessCode',
            'teamEmployeesData',
            'hrData',
            'organizationEmployeesData',
            'departmentEmployeesData',
            'teams',
            'employeeAccessCodes',
            'employeesByLevel',
            'employeesByCompetencyLevel',
            'stats',
            'previousCycleResults'
        );
    }

    /**
     * Get cycle comparison data for an employee
     */
    public function cycleComparison(Request $request)
    {
        $manager = auth()->user();
        $employeeId = $request->input('employee');
        $comparisonCycleId = $request->input('cycle');
        $currentCycleId = $request->input('current');
        
        if (!$employeeId || !$comparisonCycleId || !$currentCycleId) {
            return response()->json(['success' => false, 'message' => 'Missing parameters']);
        }
        
        $employee = Employee::find($employeeId);
        if (!$employee || !$this->hasAccessToEmployee($manager, $employee)) {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }
        
        // Get current cycle data
        $currentResults = $employee->results()->where('cycle_id', $currentCycleId)->get();
        $currentCompleted = $currentResults->count();
        
        // Get comparison cycle data
        $comparisonResults = $employee->results()->where('cycle_id', $comparisonCycleId)->get();
        $comparisonCompleted = $comparisonResults->count();
        
        $currentCycle = AssessmentCycle::find($currentCycleId);
        $comparisonCycle = AssessmentCycle::find($comparisonCycleId);
        
        return response()->json([
            'success' => true,
            'current' => [
                'cycle_name' => $currentCycle->label ?? 'Nieznany',
                'completed' => $currentCompleted,
            ],
            'previous' => [
                'cycle_name' => $comparisonCycle->label ?? 'Nieznany', 
                'completed' => $comparisonCompleted,
            ],
            'difference' => $currentCompleted - $comparisonCompleted
        ]);
    }

    /**
     * Get employee history across cycles
     */
    public function employeeHistory(Request $request)
    {
        $manager = auth()->user();
        $employeeId = $request->input('employee');
        $historyCycleId = $request->input('cycle');
        $currentCycleId = $request->input('current');
        
        if (!$employeeId || !$historyCycleId || !$currentCycleId) {
            return response()->json(['success' => false, 'message' => 'Missing parameters']);
        }
        
        $employee = Employee::find($employeeId);
        if (!$employee || !$this->hasAccessToEmployee($manager, $employee)) {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }
        
        // Get data for both cycles
        $currentResults = $employee->results()->where('cycle_id', $currentCycleId)->get();
        $historyResults = $employee->results()->where('cycle_id', $historyCycleId)->get();
        
        $currentCycle = AssessmentCycle::find($currentCycleId);
        $historyCycle = AssessmentCycle::find($historyCycleId);
        
        $currentSelfAvg = $currentResults->avg('score') ?: 0;
        $currentManagerAvg = $currentResults->avg('score_manager') ?: 0;
        $historySelfAvg = $historyResults->avg('score') ?: 0;
        $historyManagerAvg = $historyResults->avg('score_manager') ?: 0;
        
        return response()->json([
            'success' => true,
            'current' => [
                'cycle_name' => $currentCycle->label ?? 'Nieznany',
                'self_avg' => round($currentSelfAvg, 2),
                'manager_avg' => round($currentManagerAvg, 2),
                'completed' => $currentResults->count(),
            ],
            'previous' => [
                'cycle_name' => $historyCycle->label ?? 'Nieznany',
                'self_avg' => round($historySelfAvg, 2),
                'manager_avg' => round($historyManagerAvg, 2),
                'completed' => $historyResults->count(),
            ],
            'change' => [
                'self_change' => round($currentSelfAvg - $historySelfAvg, 2),
                'manager_change' => round($currentManagerAvg - $historyManagerAvg, 2),
            ]
        ]);
    }

    /**
     * Generate access codes for all employees in manager's scope
     */
    public function generateAllCodes(Request $request)
    {
        $manager = auth()->user();
        $cycleId = $request->input('cycle_id');
        
        if (!$cycleId) {
            return response()->json(['success' => false, 'message' => 'Cycle ID required']);
        }
        
        $cycle = AssessmentCycle::find($cycleId);
        if (!$cycle || !$cycle->is_active) {
            return response()->json(['success' => false, 'message' => 'Invalid or inactive cycle']);
        }
        
        // Use same logic as codes section to determine which employees to include
        $codesEmployees = collect();
        if ($manager->role == 'supermanager') {
            // For supermanager, get all employees
            $codesEmployees = Employee::all();
        } elseif ($manager->role == 'head') {
            // For head, get all department employees (same as department tab)
            $codesEmployees = Employee::whereIn('department', $this->getHeadDepartments($manager))->get();
        } else {
            // For manager/supervisor, use standard employee list
            $codesEmployees = $this->getEmployeesForManager($manager, $cycleId);
        }
        
        $generated = 0;
        
        foreach ($codesEmployees as $employee) {
            // Generate access code for each employee
            $accessCode = $this->generateHumanCode(12);
            
            $codeRecord = EmployeeCycleAccessCode::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'cycle_id' => $cycleId,
                ],
                [
                    'code_hash' => password_hash($accessCode, PASSWORD_BCRYPT),
                    'raw_last4' => substr($accessCode, -4),
                    'expires_at' => now()->addDays(30),
                    'updated_at' => now(),
                ]
            );
            
            // Zapisz zaszyfrowany pełny kod
            $codeRecord->setFullCode($accessCode);
            $codeRecord->save();
            
            $generated++;
        }
        
        return response()->json([
            'success' => true,
            'message' => "Wygenerowano kody dla {$generated} pracowników",
            'count' => $generated
        ]);
    }

    // Endpoint do pobrania pełnego kodu dostępu
    public function getFullAccessCode(Request $request, $employeeId)
    {
        $manager = auth()->user();
        $employee = Employee::findOrFail($employeeId);
        $selectedCycleId = $this->selectedCycleId($request);

        // Access check
        if (!$this->hasAccessToEmployee($manager, $employee)) {
            return response()->json(['success' => false, 'message' => 'Brak uprawnień'], 403);
        }

        $accessCode = EmployeeCycleAccessCode::where('employee_id', $employee->id)
            ->where('cycle_id', $selectedCycleId)
            ->first();

        if (!$accessCode || !$accessCode->hasFullCode()) {
            return response()->json(['success' => false, 'message' => 'Kod nie został znaleziony']);
        }

        $fullCode = $accessCode->getFullCode();
        if (!$fullCode) {
            return response()->json(['success' => false, 'message' => 'Nie można odszyfrować kodu']);
        }

        return response()->json([
            'success' => true, 
            'code' => $fullCode,
            'employee_name' => $employee->name,
            'last4' => $accessCode->raw_last4
        ]);
    }

    // Eksport wszystkich kodów dostępu
    public function exportAccessCodes(Request $request)
    {
        $manager = auth()->user();
        $cycleId = $request->input('cycle');
        
        if (!$cycleId) {
            return redirect()->back()->with('error', 'Brak wybranego cyklu');
        }

        // Determine which employees to include
        $codesEmployees = collect();
        if ($manager->role == 'supermanager') {
            $codesEmployees = Employee::all();
        } elseif ($manager->role == 'head') {
            $codesEmployees = Employee::whereIn('department', $this->getHeadDepartments($manager))->get();
        } else {
            $codesEmployees = $this->getEmployeesForManager($manager, $cycleId);
        }

        $codes = EmployeeCycleAccessCode::with('employee')
            ->whereIn('employee_id', $codesEmployees->pluck('id'))
            ->where('cycle_id', $cycleId)
            ->get();

        $data = [];
        foreach ($codes as $code) {
            $fullCode = $code->getFullCode();
            $data[] = [
                'Pracownik' => $code->employee->name ?? 'Nieznany',
                'Stanowisko' => $code->employee->job_title ?? '',
                'Dział' => $code->employee->department ?? '',
                'Pełny kod' => $fullCode ?? 'Błąd odczytu',
                'Ostatnie 4 cyfry' => $code->raw_last4 ?? '',
                'Data wygaśnięcia' => $code->expires_at ? $code->expires_at->format('Y-m-d H:i') : 'Brak',
                'Status' => $code->expires_at && $code->expires_at->isFuture() ? 'Aktywny' : ($code->expires_at ? 'Wygasły' : 'Bez terminu')
            ];
        }

        $filename = 'kody_dostepu_' . date('Y-m-d_H-i') . '.csv';
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for proper Excel encoding
            fputs($file, "\xEF\xBB\xBF");
            
            if (!empty($data)) {
                // Headers
                fputcsv($file, array_keys($data[0]), ';');
                
                // Data
                foreach ($data as $row) {
                    fputcsv($file, $row, ';');
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Revoke access code for specific employee
     */
    public function revokeCode(Request $request, $employeeId)
    {
        $manager = auth()->user();
        $employee = Employee::find($employeeId);
        
        if (!$employee || !$this->hasAccessToEmployee($manager, $employee)) {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }
        
        $deleted = EmployeeCycleAccessCode::where('employee_id', $employeeId)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Kod dostępu został unieważniony'
        ]);
    }

    /**
     * Revoke all access codes in manager's scope
     */
    public function revokeAllCodes(Request $request)
    {
        $manager = auth()->user();
        
        // Use same logic as codes section to determine which employees to include
        $codesEmployees = collect();
        if ($manager->role == 'supermanager') {
            // For supermanager, get all employees
            $codesEmployees = Employee::all();
        } elseif ($manager->role == 'head') {
            // For head, get all department employees (same as department tab)
            $codesEmployees = Employee::whereIn('department', $this->getHeadDepartments($manager))->get();
        } else {
            // For manager/supervisor, use standard employee list
            $codesEmployees = $this->getEmployeesForManager($manager, null);
        }
        
        $deleted = EmployeeCycleAccessCode::whereIn('employee_id', $codesEmployees->pluck('id'))->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Unieważniono {$deleted} kodów dostępu"
        ]);
    }

    /**
     * Export access codes list
     */
    public function exportCodes(Request $request)
    {
        $manager = auth()->user();
        $cycleId = $request->input('cycle');
        
        // This would generate Excel/PDF with access codes
        // Implementation would depend on your export library preferences
        
        return response()->json(['success' => false, 'message' => 'Export not yet implemented']);
    }

    /**
     * Export team results to Excel
     */
    public function exportTeam(Request $request)
    {
        $manager = auth()->user();
        $cycleId = $request->input('cycle');
        $format = $request->input('format', 'xlsx');
        
        $employees = $this->getEmployeesForManager($manager, $cycleId);
        $employeesData = $this->prepareEmployeesData($employees, $this->levelNames);
        
        // This would implement the Excel export using your preferred library
        // For now, return a placeholder response
        
        return response()->json([
            'success' => true,
            'message' => 'Export team functionality - to be implemented',
            'count' => $employees->count()
        ]);
    }

    /**
     * Export organization results (supermanager only)
     */
    public function exportOrganization(Request $request)
    {
        $manager = auth()->user();
        
        if ($manager->role !== 'supermanager') {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }
        
        $cycleId = $request->input('cycle');
        $format = $request->input('format', 'xlsx');
        
        // Get all employees for organization export
        $allEmployees = Employee::with([
            'team:id,name',
            'results' => function ($q) use ($cycleId) {
                $q->when($cycleId, function($qq) use ($cycleId){ $qq->where('cycle_id', $cycleId); });
            }
        ])->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Export organization functionality - to be implemented',
            'count' => $allEmployees->count()
        ]);
    }

    /**
     * Export analytics data
     */
    public function exportAnalytics(Request $request)
    {
        $manager = auth()->user();
        $cycleId = $request->input('cycle');
        $format = $request->input('format', 'xlsx');
        $type = $request->input('type', 'summary');
        
        if ($manager->role !== 'supermanager') {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }
        
        // This would implement analytics export
        return response()->json([
            'success' => true,
            'message' => 'Export analytics functionality - to be implemented',
            'type' => $type
        ]);
    }

    /**
     * Get HR dashboard data for supermanager
     */
    public function getHRData(Request $request)
    {
        $manager = auth()->user();
        
        if ($manager->role !== 'supermanager') {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }
        
        $cycleId = $request->input('cycle');
        
        $teams = Team::with(['employees' => function($q) use ($cycleId){
            $q->select('id','team_id','department')->withCount(['results' => function($qq) use ($cycleId){
                $qq->when($cycleId, function($qqq) use ($cycleId){ $qqq->where('cycle_id', $cycleId); });
            }]);
        }])->get();

        $hrData = [];
        foreach ($teams as $team) {
            $completedCount = $team->employees->where('results_count', '>', 0)->count();
            $totalCount = $team->employees->count();
            $hrData[] = [
                'team_name' => $team->name,
                'completed_count' => $completedCount,
                'total_count' => $totalCount,
                'completion_rate' => $totalCount > 0 ? round(($completedCount / $totalCount) * 100, 1) : 0,
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $hrData
        ]);
    }

    /**
     * Regenerate access code for specific employee
     */
    public function regenerateCode(Request $request, $employeeId)
    {
        $manager = auth()->user();
        $employee = Employee::find($employeeId);
        $cycleId = $request->input('cycle_id');
        
        if (!$employee || !$this->hasAccessToEmployee($manager, $employee)) {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }
        
        if (!$cycleId) {
            return response()->json(['success' => false, 'message' => 'Cycle ID required']);
        }
        
        $accessCode = \Str::random(8);
        
        EmployeeCycleAccessCode::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'cycle_id' => $cycleId,
            ],
            [
                'access_code' => bcrypt($accessCode),
                'raw_last4' => substr($accessCode, -4),
                'expires_at' => now()->addDays(30),
                'updated_at' => now(),
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Kod dostępu został wygenerowany ponownie',
            'code_last4' => substr($accessCode, -4)
        ]);
    }

    /**
     * Get departments that a head manages
     */
    private function getHeadDepartments($manager)
    {
        return \App\Models\HierarchyStructure::where('head_username', $manager->username)
            ->pluck('department')
            ->unique()
            ->toArray();
    }

    /**
     * Change manager password
     */
    public function changePassword(Request $request)
    {
        $manager = auth()->user();
        
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        // Check if current password is correct
        if (!Hash::check($request->current_password, $manager->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Obecne hasło jest nieprawidłowe'
            ], 400);
        }

        // Update password
        $manager->password = Hash::make($request->new_password);
        $manager->save();

        return response()->json([
            'success' => true,
            'message' => 'Hasło zostało zmienione pomyślnie'
        ]);
    }

}
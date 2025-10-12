<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Result;
use App\Models\CompetencyTeamValue;
use App\Exports\TeamReportExport;

use Illuminate\Http\Request;
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
    
    // Active cycle helper
    private function activeCycleId(): ?int
    {
        static $cached = null;
        if ($cached !== null) return $cached;
        $cached = AssessmentCycle::where('is_active', true)->value('id');
        return $cached;
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

    // Fetch employees under the manager with necessary relationships
    $employees = Employee::with([
        'team:id,name', // Only load team name and ID
        'team.competencyTeamValues:id,team_id,competency_id,value', // Preload team competency values to avoid N+1 in getCompetencyValue
        'overriddenCompetencyValues:id,employee_id,competency_id,value', // Preload overridden values
        'results' => function ($query) use ($selectedCycleId) {
            // Load only the fields used in calculations
            $query->select('id', 'employee_id', 'competency_id', 'score', 'score_manager', 'cycle_id')
                  ->when($selectedCycleId, function($q) use ($selectedCycleId){ $q->where('cycle_id', $selectedCycleId); });
        },
        'results.competency' => function ($query) {
            // Only fields required for grouping by level
            $query->select('id', 'level');
        },
    ])->where('manager_username', $manager->name)
      ->get(['id', 'name', 'job_title', 'department']);
    

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

    if ($manager->role == 'head') {
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
            ->where('department', $manager->department)
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

        // Check if manager has access to this employee
        if ($employee && (
            $manager->role == 'supermanager' ||
            $employee->manager_username == $manager->name ||
            ($manager->role == 'head' && $employee->department == $manager->department)
        )) {
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
            
                foreach ($levelResults as $result) {
                    $competencyValue = $employee->getCompetencyValue($result->competency_id) ?? 0;

                    // Employee's score
                    $scoreEmployee = $result->score;

                    // Manager's score (if exists, else employee's score)
                    $scoreManager = ($result->score_manager !== null) ? $result->score_manager : $result->score;

                    // Include in calculations if score > 0
                    if ($scoreEmployee > 0) {
                        $earnedPointsEmployee += $competencyValue * $scoreEmployee;
                    }
                    if ($scoreManager > 0) {
                        $earnedPointsManager += $competencyValue * $scoreManager;
                    }
                    if ($competencyValue > 0) {
                        $maxPoints += $competencyValue;
                    }
                }

                // Calculate percentages
                $percentageEmployee = $maxPoints > 0 ? ($earnedPointsEmployee / $maxPoints) * 100 : 'N/D';
                $percentageManager = $maxPoints > 0 ? ($earnedPointsManager / $maxPoints) * 100 : 'N/D';

                // Store in levelSummaries
                $levelSummaries[$level] = [
                    'earnedPointsEmployee' => $earnedPointsEmployee,
                    'earnedPointsManager' => $earnedPointsManager,
                    'maxPoints' => $maxPoints,
                    'percentageEmployee' => $percentageEmployee,
                    'percentageManager' => $percentageManager,
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
        } else {
            EmployeeCycleAccessCode::create(array_merge($payload, [
                'employee_id' => $employee->id,
                'cycle_id' => $cycleId,
            ]));
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
        $cycleId = $request->query('cycle') ?: $this->activeCycleId();

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
                $levelResults = $emp->results->filter(function ($result) use ($levelKey) {
                    return (int)$result->competency->level === $levelKey;
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
                    if ($competencyValue > 0) {
                        $maxPoints += $competencyValue;
                    }
                }

                $percentageEmployee = $maxPoints > 0 ? ($earnedPointsEmployee / $maxPoints) * 100 : 'N/D';
                $percentageManager = $maxPoints > 0 ? ($earnedPointsManager / $maxPoints) * 100 : 'N/D';

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
                'name' => $emp->name ?? 'Brak danych',
                'job_title' => $emp->job_title ?? 'Brak danych',
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
        $cycleId = $request->query('cycle') ?: $this->activeCycleId();

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
    // Zgodnie z logiką z formuły Excel i wcześniejszymi ustaleniami:
    // E=Junior (percJunior)
    // F=Specjalista (percSpecialist)
    // G=Senior (percSenior)
    // H=Supervisor (percSupervisor)
    // I=Manager (percManager)
    //
    // Porównania:
    // Junior≥80% => daje możliwość awansu dalej
    // Specialist≥85% => kolejny krok
    // Senior≥85% lub (≥60% i inne warunki) => senior/supervisor logic
    // Supervisor≥80%
    // Manager≥80%
    //
    // Oraz:
    // 1. Junior
    // 2. Specjalista
    // 3. Senior
    // 4. Supervisor
    // 5. Manager
    // Senior/Supervisor => "3/4. Senior/Supervisor"

    // Junior check
    if ($percJunior < 80) {
        return "1. Junior";
    }

    // Specialist check
    if ($percSpecialist < 85) {
        return "1. Junior";
    }

    // Minimum to "2. Specjalista" na tym etapie
    // Sprawdzamy dalej Senior, Supervisor i Manager

    // Senior≥85%?
    if ($percSenior >= 85) {
        // Mamy Senior
        if ($percSupervisor >= 80) {
            // Senior i Supervisor
            if ($percManager >= 80) {
                // Manager osiągnięty
                return "5. Manager";
            } else {
                // Senior≥80% i Supervisor≥80%, Manager<80%
                return "3/4. Senior/Supervisor";
            }
        } else {
            // Supervisor nie osiągnięty
            // Sam Senior≥85%
            return "3. Senior";
        }
    } else {
        // Senior nie osiągnął 80%
        // Sprawdzamy Supervisor≥80% i Senior≥60%
        if ($percSupervisor >= 80 && $percSenior >= 60) {
            // Supervisor
            // Nie osiągnęliśmy Senior≥80%, ale mamy minimalnie Senior≥60% i Supervisor≥80%
            // To daje "4. Supervisor"
            // Manager sprawdzany tylko przy Senior≥80% i Supervisor≥80%, tu go nie sprawdzamy
            return "4. Supervisor";
        } else {
            // Nie spełniamy warunków wyższych niż Specjalista
            return "2. Specjalista";
        }
    }
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
        $cycleId = request()->query('cycle') ?: $this->activeCycleId();
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
        $cycleId = request()->query('cycle') ?: $this->activeCycleId();
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

}
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


class ManagerController extends Controller
{
    private $levelNames = [
        1 => "1. Junior",
        2 => "2. Specjalista",
        3 => "3. Senior",
        4 => "4. Supervisor",
        5 => "5. Manager"
    ];
    
    public function index(Request $request)
{
    $manager = auth()->user();

    if (!$manager) {
        abort(403, 'Użytkownik nie jest zalogowany.');
    }

    $levelNames = [
        1 => '1. Junior',
        2 => '2. Specjalista',
        3 => '3. Senior',
        4 => '4. Supervisor',
        5 => '5. Manager'
    ];

    // Get the employee ID from the request (if selected)
    $employeeId = $request->input('employee');

    // Fetch employees under the manager with necessary relationships
    $employees = Employee::with([
        'team:id,name', // Only load team name and ID
        'results' => function ($query) {
            $query->select('id', 'employee_id', 'competency_id', 'score', 'score_manager', 'above_expectations', 'comments', 'feedback_manager');
        },
        'results.competency' => function ($query) {
            $query->select('id', 'competency_name', 'level', 'competency_type');
        },
        // Load overridden competency values if necessary
    ])->where('manager_username', $manager->name)->get(['id', 'name', 'job_title', 'department']);
    

    // For supermanagers, fetch all employees with necessary relationships
    if ($manager->role == 'supermanager') {
        $allEmployees = Employee::with(['team', 'results.competency.competencyTeamValues'])->get();
    } else {
        $allEmployees = collect(); // Empty collection for non-supermanagers
    }

    if ($manager->role == 'head') {
        $departmentEmployees = Employee::with(['team', 'results.competency.competencyTeamValues'])
            ->where('department', $manager->department)
            ->get();
    } else {
        $departmentEmployees = collect(); // Empty collection for non-heads
    }

    // Initialize variables
    $employee = null;
    $results = collect(); // Empty collection
    $levelSummaries = [];
    $overriddenValues = collect(); // Initialize here to ensure it's always defined

    if ($employeeId) {
        $employee = Employee::with(['team', 'results.competency.competencyTeamValues'])->find($employeeId);

        // Check if manager has access to this employee
        if ($employee && (
            $manager->role == 'supermanager' ||
            $employee->manager_username == $manager->name ||
            ($manager->role == 'head' && $employee->department == $manager->department)
        )) {
            // Access allowed
            $results = $employee->results()->with('competency.competencyTeamValues')->get();

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
            // Get all teams with employees and their results
            $teams = Team::with(['employees.results'])->get();

            foreach ($teams as $team) {
                // Get count of employees who have completed the self-assessment
                $completedCount = $team->employees->filter(function ($emp) {
                    return $emp->results->isNotEmpty();
                })->count();

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
            $teamsInDepartment = Team::with(['employees.results'])
                ->whereHas('employees', function ($query) use ($manager) {
                    $query->where('department', $manager->department);
                })
                ->get();
        
            foreach ($teamsInDepartment as $team) {
                // Get count of employees who have completed the self-assessment
                $completedCount = $team->employees->filter(function ($emp) {
                    return $emp->results->isNotEmpty();
                })->count();
        
                $departmentData[] = [
                    'team_name' => $team->name,
                    'completed_count' => $completedCount,
                ];
            }
        }

        

        

        // Fetch all teams
        $teams = Team::all();

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
            'teams'
        ));
        
    }

    public function exportDepartment(Request $request)
    {
        $manager = Auth::user();

        if ($manager->role != 'head') {
            abort(403, 'Unauthorized');
        }

        $levelNames = $this->levelNames;

        // Fetch the department employees data
        $departmentEmployees = Employee::with(['team', 'results.competency.competencyTeamValues'])
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
            'raport_dział_' . $manager->department . '.xlsx'
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
    
        // Fetch employees of the team based on department
        $employees = Employee::with(['results', 'team'])
            ->where('department', $team->name)
            ->get();
    
        // Prepare data
        $employeesData = $this->prepareEmployeesData($employees, $this->levelNames);
    
        if ($format === 'pdf') {
            // Generate PDF
            $pdf = PDF::loadView('exports.team_report_pdf', [
                'team' => $team,
                'employeesData' => $employeesData,
                'levelNames' => $this->levelNames
            ])->setPaper('a4', 'landscape');
    
            return $pdf->download('raport_' . $team->name . '.pdf');
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
            $filename = "P-CAP Raport Full-{$date}_{$teamName}.xlsx";
            return Excel::download(
                new TeamReportExport($data, $headers),
                $filename
            );
        }
    }


    public function update(Request $request)
    {
        // Update manager's assessments
        foreach ($request->score_manager as $resultId => $scoreManager) {
            $result = Result::find($resultId);

            if ($result) {
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
                $result->feedback_manager = $request->feedback_manager[$resultId] ?? null;
                $result->save();
            }
        }

        $employeeId = $request->input('employee_id');
        $employee = Employee::find($employeeId);

         // Handle overridden competency values
        $competencyValues = $request->input('competency_values', []);
        $deleteCompetencyValues = $request->input('delete_competency_values', []);

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

        // Redirect back to the manager panel with the employee parameter
        return redirect()->action([ManagerController::class, 'index'], ['employee' => $employeeId])->with('success', 'Oceny zostały zaktualizowane.');
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
    

        // Fetch employee results with competency relationship (without constraints)
        $results = Result::where('employee_id', $employeeId)
            ->with('competency') // Load competency without constraints
            ->get();

        // Generate PDF
        $pdf = PDF::loadView('pdf.employee_report', compact('employee', 'results'))
            ->setPaper('a4', 'landscape');

        $date = now()->format('Y-m-d_H-i');
        $name = str_replace(' ', '_', $employee->name);
        $filename = "P-CAP Raport Full-{$date}_{$name}.pdf";
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

    
        // Fetch results with necessary relationships
        $results = Result::where('employee_id', $employeeId)
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
    
        // Use an anonymous class to export data
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
            'raport_' . $employee->name . '.xlsx'
        );
    }    
}
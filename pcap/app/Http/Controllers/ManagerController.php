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

class ManagerController extends Controller
{
    private $levelNames = [
        1 => 'Junior',
        2 => 'Specjalista',
        3 => 'Senior',
        4 => 'Supervisor',
        5 => 'Manager',
        6 => 'Head of'
    ];

    public function index(Request $request)
    {
        $manager = auth()->user();

        if (!$manager) {
            abort(403, 'Użytkownik nie jest zalogowany.');
        }    

        $levelNames = [
            1 => 'Junior',
            2 => 'Specjalista',
            3 => 'Senior',
            4 => 'Supervisor',
            5 => 'Manager',
            6 => 'Head of'
        ];

        // Get the employee ID from the request (if selected)
        $employeeId = $request->input('employee');

        // Fetch employees under the manager with necessary relationships
        $employees = Employee::with(['team', 'results.competency.competencyTeamValues'])
            ->where('manager_username', $manager->name)
            ->get();

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

        if ($employeeId) {
            $employee = Employee::with(['team', 'results.competency.competencyTeamValues'])->find($employeeId);

            // Check if manager has access to this employee
            if ($employee && (
                $manager->role == 'supermanager' ||
                $employee->manager_username == $manager->name ||
                ($manager->role == 'head' && $employee->department == $manager->department)
            )) {
                // Access allowed
                $results = $employee->results;
            
                // Calculate per-level summaries for individual tab
                $levels = $results->groupBy('competency.level');
                foreach ($levels as $level => $levelResults) {
                    $earnedPoints = 0;
                    $maxPoints = 0;
                    foreach ($levelResults as $result) {
                        $competencyValue = $result->competency->competencyTeamValues->first()->value ?? 0;
                        // Choose manager's score if available, otherwise user's score
                        $score = $result->score_manager !== null ? $result->score_manager : $result->score;
            
                        // Include in calculations if score > 0
                        if ($score > 0) {
                            $earnedPoints += $competencyValue * $score;
                            $maxPoints += $competencyValue;
                        }
                    }
                    $percentage = $maxPoints > 0 ? ($earnedPoints / $maxPoints) * 100 : 'N/D';
                    $levelSummaries[$level] = [
                        'earnedPoints' => $earnedPoints,
                        'maxPoints' => $maxPoints,
                        'percentage' => $percentage,
                    ];
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
            'teams'
        ));
        
    }

    /**
     * Prepare employee data for team and organization summaries
     */
    private function prepareEmployeesData($employees, $levelNames)
    {
        $employeesData = [];

        foreach ($employees as $emp) {
            // Get the team based on the employee's department
            if (!$emp->team) {
                Log::warning('No team found for employee', [
                    'employee_id' => $emp->id,
                    'employee_name' => $emp->name,
                    'department' => $emp->department,
                ]);
                continue;
            }

            $teamId = $emp->team->id;

            // Build an array of competency values for the employee's team
            $competencyValues = CompetencyTeamValue::where('team_id', $teamId)
                ->whereIn('competency_id', $emp->results->pluck('competency_id'))
                ->pluck('value', 'competency_id');

            // Array to store percentages for each level
            $levelPercentages = [];

            foreach ($levelNames as $levelKey => $levelName) {
                $levelResults = $emp->results->filter(function ($result) use ($levelKey) {
                    $levelNumber = (int)$result->competency->level;
                    return $levelNumber == $levelKey;
                });

                if ($levelResults->isEmpty()) {
                    $levelPercentages[$levelName] = null;
                } else {
                    $earnedPoints = 0;
                    $maxPoints = 0;

                    foreach ($levelResults as $result) {
                        // Use the competency value specific to the employee's team
                        $competencyValue = $competencyValues[$result->competency_id] ?? 0;
                        $score = $result->score_manager !== null ? $result->score_manager : $result->score;

                        if ($score > 0) {
                            $earnedPoints += $competencyValue * $score;
                            $maxPoints += $competencyValue;
                        }
                    }

                    $percentage = $maxPoints > 0 ? ($earnedPoints / $maxPoints) * 100 : 'N/D';
                    $levelPercentages[$levelName] = $percentage;
                }
            }

            // **Determine highest level**
            $highestLevel = 'Junior'; // Default level
            foreach (array_reverse($levelNames, true) as $levelKey => $levelName) {
                if (isset($levelPercentages[$levelName]) && is_numeric($levelPercentages[$levelName]) && $levelPercentages[$levelName] >= 80) {
                    $highestLevel = $levelName;
                    break; // Stop after finding the highest level
                }
            }

            // Add data to employeesData
            $employeesData[] = [
                'name' => $emp->employee_name ?? $emp->name ?? 'Brak danych',
                'job_title' => $emp->position ?? $emp->job_title ?? 'Brak danych',
                'levelPercentages' => $levelPercentages,
                'highestLevel' => $highestLevel,
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
    $data = []; // Data rows
    $headers = ['Imię i nazwisko', 'Nazwa stanowiska'];
    foreach ($this->levelNames as $levelName) {
        $headers[] = $levelName;
    }
    $headers[] = 'Poziom';

    foreach ($employeesData as $empData) {
        $row = [
            $empData['name'],
            $empData['job_title'],
        ];
        foreach ($this->levelNames as $levelName) {
            $percentageValue = $empData['levelPercentages'][$levelName] ?? null;
            $percentage = is_numeric($percentageValue) ? number_format($percentageValue, 2) . '%' : 'N/D';
            $row[] = $percentage;
        }
        $row[] = $empData['highestLevel'];
        $data[] = $row;
    }

    // Use a proper export class
    return Excel::download(
        new TeamReportExport($data, $headers),
        'raport_' . $team->name . '.xlsx'
    );
    }
}


    // Helper method to determine the highest level achieved
    private function determineHighestLevel($levelPercentages)
    {
        $highestLevel = 'Junior'; // Default level
        // Iterate from highest to lowest level
        foreach (array_reverse($this->levelNames, true) as $levelKey => $levelName) {
            if (isset($levelPercentages[$levelName]) && is_numeric($levelPercentages[$levelName]) && $levelPercentages[$levelName] >= 80) {
                $highestLevel = $levelName;
                break;
            }
        }
        return $highestLevel;
    }




    public function update(Request $request)
    {
        // Update manager's assessments
        foreach ($request->score_manager as $resultId => $scoreManager) {
            $result = Result::find($resultId);

            if ($result) {
                if ($scoreManager === 'above_expectations') {
                    $result->score_manager = 1;
                    $result->above_expectations_manager = 1;
                } elseif ($scoreManager !== null && $scoreManager !== '') {
                    $result->score_manager = $scoreManager;
                    $result->above_expectations_manager = 0;
                } else {
                    $result->score_manager = null;
                    $result->above_expectations_manager = 0;
                }
                $result->feedback_manager = $request->feedback_manager[$resultId] ?? null;
                $result->save();
            }
        }

        $employeeId = $request->input('employee_id');

        // Redirect back to the manager panel with the employee parameter
        return redirect()->action([ManagerController::class, 'index'], ['employee' => $employeeId])->with('success', 'Oceny zostały zaktualizowane.');
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

        return $pdf->download('raport_' . $employee->name . '.pdf');
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
            $competencyValue = $competency->competencyTeamValues->first()->value ?? 0;
    
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
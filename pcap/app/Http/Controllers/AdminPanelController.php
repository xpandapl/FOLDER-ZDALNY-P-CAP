<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\AssessmentCycle;
use Carbon\Carbon;

class AdminPanelController extends Controller
{
    public function __construct()
    {
        // Ensure the user is logged in and has role 'supermanager'
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                if (Auth::user()->role === 'supermanager') {
                    return $next($request);
                }

                // BEGIN temporary access for user 'pag'
                // This section allows the user with username 'pag' to access the admin panel
                if (Auth::user()->username === 'pag') {
                    return $next($request);
                }
                // END temporary access for user 'pag'
            }
            return redirect('/')->with('error', 'Brak dostępu do panelu administratora.');
        });
    }


    public function index()
    {
        $employees = \App\Models\Employee::orderBy('created_at', 'desc')->get();
    
        // Pobierz aktualną datę blokady
        $blockDate = Carbon::parse(config('app.block_date', '2024-12-10'));
    
        // Pobierz listę użytkowników
        $users = \App\Models\User::whereIn('role', ['supervisor', 'manager','head','supermanager'])->orderBy('name')->get();
        $managerNameByUsername = $users->pluck('name', 'username');
        $roles = ['supervisor', 'manager', 'head', 'supermanager'];
        
        // Pobierz ustawienia aplikacji
        $appSettings = \App\Models\AppSetting::orderBy('label')->get();
    
        return view('admin_panel_new', compact('employees', 'blockDate', 'users', 'managerNameByUsername', 'roles', 'appSettings'));
    }
    
    public function showAdminPanel()
    {
        $employees = \App\Models\Employee::orderBy('created_at', 'desc')->get();
        $users = \App\Models\User::whereIn('role', ['supervisor', 'manager','head','supermanager'])->orderBy('name')->get();
        $managerNameByUsername = $users->pluck('name', 'username');
        
        $blockDateRecord = \App\Models\BlockDate::first();
        if ($blockDateRecord && $blockDateRecord->block_date) {
            $blockDate = Carbon::parse($blockDateRecord->block_date);
        } else {
            $blockDate = Carbon::parse(config('app.block_date', '2025-12-15'));
        }
        
        $teams = \App\Models\Team::pluck('name');
        $roles = ['supervisor', 'manager', 'head', 'supermanager'];
        $cycles = AssessmentCycle::orderByDesc('year')->orderByDesc('period')->get();
        
        // Pobierz ustawienia aplikacji
        $appSettings = \App\Models\AppSetting::orderBy('label')->get();
        
        // Dane dla sekcji hierarchii
        $hierarchyStructures = \App\Models\HierarchyStructure::with(['supervisor', 'manager', 'head'])->orderBy('department')->orderBy('team_name')->get();
        $departmentCounts = $hierarchyStructures->groupBy('department')->map(function($items) {
            return $items->count();
        });
        $totalStructures = $hierarchyStructures->count();
        $uniqueDepartments = $hierarchyStructures->pluck('department')->unique()->count();

        // Dane dla sekcji kompetencji - ładowane przez AJAX
        $totalCompetencies = \App\Models\Competency::count();

        return view('admin_panel_new', compact('employees', 'users', 'blockDate', 'teams', 'managerNameByUsername', 'roles', 'cycles', 'appSettings', 'hierarchyStructures', 'departmentCounts', 'totalStructures', 'uniqueDepartments', 'totalCompetencies'));
    }

    // UI: start new cycle (optionally mark active). Cloning handled via CLI if needed.
    public function startCycle(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'period' => 'nullable|string|max:20',
            'label' => 'nullable|string|max:100',
            'activate' => 'sometimes|boolean',
        ]);

        DB::transaction(function () use ($validated) {
            $activate = (bool)($validated['activate'] ?? false);

            if ($activate) {
                // Deactivate and lock previous active cycle(s)
                AssessmentCycle::where('is_active', true)->get()->each(function($c){
                    $c->is_active = false;
                    if (!$c->locked_at) { $c->locked_at = now(); }
                    $c->save();
                });
            }

            AssessmentCycle::create([
                'year' => $validated['year'],
                'period' => $validated['period'] ?? null,
                'label' => $validated['label'] ?? ($validated['year'].($validated['period']?(' H'.$validated['period']):'')),
                'is_active' => $activate,
            ]);
        });

        return redirect()->route('admin.panel')->with('success', 'Nowy cykl został utworzony'.(($request->boolean('activate'))?' i ustawiony jako aktywny.':'.'));
    }

    // UI: activate a cycle and lock the previously active one
    public function activateCycle($id)
    {
        DB::transaction(function () use ($id) {
            AssessmentCycle::where('is_active', true)->get()->each(function($c){
                $c->is_active = false;
                if (!$c->locked_at) { $c->locked_at = now(); }
                $c->save();
            });
            $target = AssessmentCycle::findOrFail($id);
            $target->is_active = true;
            // keep locked_at null for active
            $target->locked_at = null;
            $target->save();
        });

        return redirect()->route('admin.panel')->with('success', 'Cykl został ustawiony jako aktywny.');
    }

    // UI: lock a specific cycle (prevents edits, informational)
    public function lockCycle($id)
    {
        $cycle = AssessmentCycle::findOrFail($id);
        $cycle->locked_at = now();
        $cycle->save();
        return redirect()->route('admin.panel')->with('success', 'Cykl został zablokowany do edycji.');
    }

    public function updateDates(Request $request)
    {
        $request->validate([
            'block_date' => 'required|date',
        ]);

        $blockDate = $request->input('block_date');

        // Zapisz do bazy danych
        $record = \App\Models\BlockDate::first();
        if ($record) {
            $record->block_date = $blockDate;
            $record->save();
        } else {
            \App\Models\BlockDate::create(['block_date' => $blockDate]);
        }

        // (Opcjonalnie) Zapisz do .env jeśli nadal chcesz
        // $path = base_path('.env');
        // if (file_exists($path)) {
        //     file_put_contents($path, str_replace(
        //         'APP_BLOCK_DATE=' . config('app.block_date'),
        //         'APP_BLOCK_DATE=' . $blockDate,
        //         file_get_contents($path)
        //     ));
        //     \Artisan::call('config:clear');
        // }

        return redirect()->route('admin.panel')->with('success', 'Data blokady została zaktualizowana.');
    }

    
    public function deleteEmployee(Request $request)
    {
        $employeeId = $request->input('employee_id');
    
        // Usuń pracownika i powiązane wyniki
        $employee = \App\Models\Employee::find($employeeId);
        if ($employee) {
            // Usuń powiązane wyniki
            \App\Models\Result::where('employee_id', $employeeId)->delete();
    
            // Usuń pracownika
            $employee->delete();
    
            return redirect()->route('admin.panel')->with('success', 'Formularz został usunięty.');
        }
    
        return redirect()->route('admin.panel')->with('error', 'Nie znaleziono pracownika.');
    }

    public function getManager($id)
    {
        $user = \App\Models\User::find($id);
        if ($user) {
            return response()->json($user);
        }
        return response()->json(['message' => 'Nie znaleziono użytkownika.'], 404);
    }

    public function updateManager(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $request->input('user_id'),
            'email' => 'required|email|max:255|unique:users,email,' . $request->input('user_id'),
            'role' => 'required|string|in:supervisor,manager,head,supermanager',
            'department' => 'required|string',
        ]);

        $user = \App\Models\User::find($request->input('user_id'));
        if ($user) {
            $user->name = $request->input('name');
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            $user->role = $request->input('role');
            $user->department = $request->input('department');
            $user->save();
            
            return redirect()->route('admin.panel', ['section' => 'managers'])->with('success', 'Dane managera zostały zaktualizowane.');
        }
        return redirect()->route('admin.panel', ['section' => 'managers'])->with('error', 'Nie znaleziono użytkownika.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::find($request->input('user_id'));
        if ($user) {
            $user->password = bcrypt($request->input('password'));
            $user->save();
            
            return redirect()->route('admin.panel', ['section' => 'managers'])->with('success', 'Hasło zostało zresetowane.');
        }
        return redirect()->route('admin.panel', ['section' => 'managers'])->with('error', 'Nie znaleziono użytkownika.');
    }

    public function deleteManager($id)
    {
        try {
            $user = \App\Models\User::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Nie znaleziono użytkownika.'], 404);
            }

            // Prevent self-deletion
            if ($user->id === auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Nie możesz usunąć własnego konta.'], 403);
            }

            // Check if user has employees assigned
            $assignedEmployees = \App\Models\Employee::where('supervisor_username', $user->username)
                ->orWhere('manager_username', $user->username)
                ->orWhere('head_username', $user->username)
                ->count();

            if ($assignedEmployees > 0) {
                return response()->json([
                    'success' => false, 
                    'message' => "Nie można usunąć managera. Ma przypisanych {$assignedEmployees} pracowników."
                ], 400);
            }

            $user->delete();
            
            return response()->json(['success' => true, 'message' => 'Manager został usunięty.']);
        } catch (\Exception $e) {
            Log::error("Error deleting manager {$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Wystąpił błąd podczas usuwania managera.'], 500);
        }
    }

    public function getEmployee($id)
    {
        $employee = \App\Models\Employee::find($id);
        if ($employee) {
            // Znajdź aktualną strukturę hierarchii dla pracownika
            $currentStructure = \App\Models\HierarchyStructure::where('department', $employee->department)
                ->where(function($query) use ($employee) {
                    if ($employee->supervisor_username) {
                        $query->where('supervisor_username', $employee->supervisor_username);
                    } else if ($employee->manager_username) {
                        $query->where('manager_username', $employee->manager_username)
                              ->whereNull('supervisor_username');
                    } else if ($employee->head_username) {
                        $query->where('head_username', $employee->head_username)
                              ->whereNull('supervisor_username')
                              ->whereNull('manager_username');
                    }
                })
                ->first();
            
            $employeeData = $employee->toArray();
            $employeeData['hierarchy_structure_id'] = $currentStructure ? $currentStructure->id : null;
            
            return response()->json([
                'success' => true,
                'employee' => $employeeData
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Nie znaleziono pracownika.'
        ], 404);
    }

    public function updateEmployee(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'hierarchy_structure_id' => 'nullable|exists:hierarchy_structure,id',
        ]);

        $employee = \App\Models\Employee::find($request->input('employee_id'));
        if ($employee) {
            // Podstawowe dane pracownika
            $employee->first_name = $request->input('first_name');
            $employee->last_name = $request->input('last_name');
            $employee->job_title = $request->input('job_title');
            
            // Jeśli wybrano strukturę hierarchii, przypisz pracownika do niej
            $hierarchyStructureId = $request->input('hierarchy_structure_id');
            if ($hierarchyStructureId) {
                $success = \App\Models\HierarchyStructure::assignHierarchyToEmployee($employee, $hierarchyStructureId);
                if (!$success) {
                    return redirect()->route('admin.panel')->with('error', 'Nie udało się przypisać pracownika do struktury hierarchii.');
                }
            } else {
                // Jeśli nie wybrano struktury, wyczyść hierarchię
                $employee->supervisor_username = null;
                $employee->manager_username = null;
                $employee->head_username = null;
                $employee->save();
            }

            return redirect()->route('admin.panel')->with('success', 'Dane pracownika zostały zaktualizowane.');
        }

        return redirect()->route('admin.panel')->with('error', 'Nie znaleziono pracownika.');
    }


    public function addManager(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:supervisor,manager,head,supermanager',
            'department' => 'required|string|max:255',
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'department' => $request->department,
        ]);

        return redirect()->route('admin.panel', ['section' => 'managers'])->with('success', 'Użytkownik został dodany.');
    }

    // Lazy-loaded JSON: competencies summary with counts and averages
    public function competenciesSummary(Request $request)
    {
        // Accept pagination and optional department filter
        $perPage = (int) $request->input('per_page', 20);
        if ($perPage < 1) { $perPage = 20; }
        if ($perPage > 100) { $perPage = 100; }
    $department = $request->input('department'); // nullable
    $level = $request->input('level'); // nullable
    $ctype = $request->input('competency_type'); // nullable

        $query = \DB::table('competencies as c')
            ->leftJoin('results as r', 'r.competency_id', '=', 'c.id');

        if ($department) {
            $query->leftJoin('employees as e', 'e.id', '=', 'r.employee_id')
                  ->where('e.department', $department);
        }

        if ($level !== null && $level !== '') {
            // Match levels like "1. Junior" when filtering by "1"
            $query->where('c.level', 'like', $level . '%');
        }
        if ($ctype !== null && $ctype !== '') {
            // Allow partial contains match for types, e.g. entering "3.G" matches any containing it
            $query->where('c.competency_type', 'like', '%' . $ctype . '%');
        }

        $query->select([
            'c.id',
            'c.competency_name',
            'c.level',
            'c.competency_type',
            'c.description_025',
            'c.description_0_to_05',
            'c.description_075_to_1',
            'c.description_above_expectations',
            \DB::raw('SUM(CASE WHEN r.score IS NOT NULL AND r.score > 0 THEN 1 ELSE 0 END) as response_count'),
            \DB::raw('AVG(NULLIF(CASE WHEN r.score IS NOT NULL AND r.score > 0 THEN r.score ELSE NULL END, NULL)) as avg_score'),
        ])
        ->groupBy('c.id')
        ->orderBy('c.level')
        ->orderBy('c.competency_name');

        $results = $query->paginate($perPage);

        return response()->json([
            'data' => $results->items(),
            'current_page' => $results->currentPage(),
            'per_page' => $results->perPage(),
            'total' => $results->total(),
            'last_page' => $results->lastPage(),
        ]);
    }
    
    public function updateSettings(Request $request)
    {
        $settings = $request->input('settings', []);
        
        foreach ($settings as $key => $value) {
            \App\Models\AppSetting::set($key, $value);
        }
        
        return redirect()->back()->with('success', 'Ustawienia zostały zaktualizowane.');
    }

    public function searchCompetencies(Request $request)
    {
        $search = $request->get('search', '');
        
        // Jeśli nie ma wyszukiwania, zwróć cache'owany wynik
        if (empty($search)) {
            $cacheKey = 'competencies_default_list';
            $result = Cache::remember($cacheKey, 300, function() {
                $competencies = \App\Models\Competency::select('id', 'competency_name', 'competency_type', 'level', 'description_025', 'description_0_to_05', 'description_075_to_1')
                    ->orderBy('level')
                    ->orderBy('competency_name')
                    ->limit(20) // Jeszcze mniej dla domyślnego widoku
                    ->get();
                
                $totalCompetencies = \App\Models\Competency::count();
                
                return [
                    'competencies' => $competencies,
                    'totalCompetencies' => $totalCompetencies
                ];
            });
            
            $competencies = $result['competencies'];
            $totalCompetencies = $result['totalCompetencies'];
        } else {
            // Dla wyszukiwania - bez cache
            $competencies = \App\Models\Competency::select('id', 'competency_name', 'competency_type', 'level', 'description_025', 'description_0_to_05', 'description_075_to_1')
                ->where(function ($q) use ($search) {
                    $q->where('competency_name', 'LIKE', '%' . $search . '%')
                      ->orWhere('competency_type', 'LIKE', '%' . $search . '%')
                      ->orWhere('level', 'LIKE', '%' . $search . '%');
                })
                ->orderBy('level')
                ->orderBy('competency_name')
                ->limit(40)
                ->get();
            
            $totalCompetencies = \App\Models\Competency::count();
        }
        
        $html = view('admin.partials.competencies_list', [
            'competencies' => $competencies,
            'totalCompetencies' => $totalCompetencies,
            'search' => $search
        ])->render();
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'total' => $totalCompetencies,
            'displayed' => $competencies->count()
        ]);
    }

    public function loadSection($section)
    {
        try {
            // Validate section
            $allowedSections = ['employees', 'managers', 'hierarchy', 'dates', 'competencies', 'cycles', 'settings'];
            if (!in_array($section, $allowedSections)) {
                return response()->json(['error' => 'Invalid section'], 400);
            }

            // Get data needed for the section (reuse logic from showAdminPanel)
            $employees = \App\Models\Employee::with(['results', 'supervisor', 'manager', 'head'])->get();
            $users = \App\Models\User::all();
            
            // Fix blockDate handling
            $blockDateRecord = \App\Models\BlockDate::first();
            if ($blockDateRecord && $blockDateRecord->block_date) {
                $blockDate = \Carbon\Carbon::parse($blockDateRecord->block_date);
            } else {
                $blockDate = \Carbon\Carbon::parse(config('app.block_date', '2025-12-15'));
            }
            
            $teams = \App\Models\Team::pluck('name');
            $managerNameByUsername = \App\Models\User::pluck('name', 'username')->toArray();
            $roles = ['supervisor', 'manager', 'head', 'supermanager'];
            $cycles = \App\Models\AssessmentCycle::orderBy('created_at', 'desc')->get();
            
            // Fix appSettings - return as collection, not array
            $appSettings = \App\Models\AppSetting::orderBy('label')->get();
            
            $hierarchyStructures = \App\Models\HierarchyStructure::with(['supervisor', 'manager', 'head'])->get();
            $departmentCounts = $hierarchyStructures->groupBy('department')->map->count();
            $totalStructures = $hierarchyStructures->count();
            $uniqueDepartments = $hierarchyStructures->pluck('department')->unique()->count();
            $totalCompetencies = \App\Models\Competency::count();
            
            // Competencies statistics - cache for 5 minutes
            $competencyStats = \Cache::remember('competency_stats', 300, function() {
                return [
                    'total' => \App\Models\Competency::count(),
                    'levels' => \App\Models\Competency::distinct('level')->count('level'),
                    'types' => \App\Models\Competency::distinct('competency_type')->count('competency_type'),
                    'with_descriptions' => \App\Models\Competency::where(function($query) {
                        $query->whereNotNull('description_025')
                              ->orWhereNotNull('description_0_to_05')
                              ->orWhereNotNull('description_075_to_1')
                              ->orWhereNotNull('description_above_expectations');
                    })->count()
                ];
            });

            $html = view("admin.sections.{$section}", compact(
                'employees', 'users', 'blockDate', 'teams', 'managerNameByUsername', 
                'roles', 'cycles', 'appSettings', 'hierarchyStructures', 
                'departmentCounts', 'totalStructures', 'uniqueDepartments', 'totalCompetencies', 'competencyStats'
            ))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'section' => $section
            ]);
        } catch (\Exception $e) {
            \Log::error("Error loading section {$section}: " . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Błąd podczas ładowania sekcji: ' . $e->getMessage()
            ], 500);
        }
    }
}

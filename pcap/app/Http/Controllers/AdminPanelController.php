<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    $users = \App\Models\User::whereIn('role', ['manager','head','supermanager'])->orderBy('name')->get();
    $managerNameByUsername = $users->pluck('name', 'username');
    $roles = ['manager', 'head', 'supermanager'];
    
    return view('admin_panel', compact('employees', 'blockDate', 'users', 'managerNameByUsername', 'roles'));
    }
    
    public function showAdminPanel()
    {
    $employees = \App\Models\Employee::all();
    $users = \App\Models\User::whereIn('role', ['manager','head','supermanager'])->orderBy('name')->get();
    $managerNameByUsername = $users->pluck('name', 'username');
        $blockDate = \App\Models\BlockDate::first();
        if (!$blockDate) {
            $blockDate = (object)['block_date' => now()->format('Y-m-d')];
        }
        $teams = \App\Models\Team::pluck('name');
    $roles = ['manager', 'head', 'supermanager'];
    $cycles = AssessmentCycle::orderByDesc('year')->orderByDesc('period')->get();

    return view('admin_panel', compact('employees', 'users', 'blockDate', 'teams', 'managerNameByUsername', 'roles', 'cycles'));
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
            'role' => 'required|string|in:manager,head,supermanager',
            'department' => 'required|string|exists:teams,name',
        ]);

        $user = \App\Models\User::find($request->input('user_id'));
        if ($user) {
            $user->role = $request->input('role');
            $user->department = $request->input('department');
            $user->save();
            return redirect()->route('admin.panel')->with('success', 'Dane managera zostały zaktualizowane.');
        }
        return redirect()->route('admin.panel')->with('error', 'Nie znaleziono użytkownika.');
    }

    public function getEmployee($id)
    {
        $employee = \App\Models\Employee::find($id);
        if ($employee) {
            return response()->json($employee);
        }
        return response()->json(['message' => 'Nie znaleziono pracownika.'], 404);
    }

    public function updateEmployee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            // Now storing full manager name directly in employees.manager_username
            'manager_username' => 'nullable|string|max:255',
        ]);

        $employee = \App\Models\Employee::find($request->input('employee_id'));
        if ($employee) {
            $employee->name = $request->input('name');
            $employee->job_title = $request->input('job_title');
            $employee->manager_username = $request->input('manager_username');
            $employee->save();

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
            'department' => 'required|string|max:255',
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'manager',
            'department' => $request->department,
        ]);

        return redirect()->back()->with('success', 'Manager został dodany.');
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
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HierarchyStructure;
use App\Models\User;
use App\Models\Employee;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HierarchyController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Ensure the user is logged in and has role 'supermanager'
            if (auth()->check()) {
                if (auth()->user()->role === 'supermanager') {
                    return $next($request);
                }
            }
            return redirect('/login')->with('error', 'Nie masz dostępu do tej strony.');
        });
    }

    public function index(Request $request)
    {
        $query = HierarchyStructure::with(['supervisor', 'manager', 'head']);
        
        // Filtrowanie
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('department', 'like', "%{$search}%")
                  ->orWhereHas('supervisor', function($qq) use ($search) {
                      $qq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('manager', function($qq) use ($search) {
                      $qq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('head', function($qq) use ($search) {
                      $qq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $hierarchies = $query->paginate(20);
        
        // Dostępne działy z bazy danych
        $departments = $this->getAvailableDepartments();
        
        // Statystyki
        $stats = [
            'total_structures' => HierarchyStructure::count(),
            'departments_count' => HierarchyStructure::distinct('department')->count('department'),
            'employees_assigned' => Employee::where(function($query) {
                $query->whereNotNull('supervisor_username')
                      ->orWhereNotNull('manager_username')
                      ->orWhereNotNull('head_username');
            })->count(),
            'employees_unassigned' => Employee::where(function($query) {
                $query->whereNull('supervisor_username')
                      ->whereNull('manager_username')
                      ->whereNull('head_username');
            })->count(),
        ];
        
        return view('hierarchy.index', compact('hierarchies', 'departments', 'stats'));
    }

    public function create()
    {
        $departments = $this->getAvailableDepartments();
        
        $users = User::whereIn('role', ['supervisor', 'manager', 'head'])
                    ->select('username', 'name', 'role', 'department')
                    ->orderBy('role')
                    ->orderBy('name')
                    ->get();
        
        return view('hierarchy.create_new', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $availableDepartments = $this->getAvailableDepartments();
        
        $request->validate([
            'department' => 'required|string|in:' . implode(',', $availableDepartments),
            'team_name' => 'required|string|max:255',
            'supervisor_username' => 'nullable|exists:users,username',
            'manager_username' => 'nullable|exists:users,username',
            'head_username' => 'required|exists:users,username',
        ]);

        // Sprawdź unikalność team_name w departamencie
        $existingTeam = HierarchyStructure::where('department', $request->department)
                                         ->where('team_name', $request->team_name)
                                         ->exists();
        
        if ($existingTeam) {
            return redirect()->back()
                           ->withErrors(['team_name' => 'Zespół o tej nazwie już istnieje w tym dziale.'])
                           ->withInput();
        }

        // Sprawdź czy użytkownicy mają odpowiednie role
        $this->validateUserRoles($request);

        HierarchyStructure::create($request->all());

        // Automatyczne przypisanie pracowników jeśli zaznaczono
        if ($request->has('auto_assign')) {
            $this->autoAssignEmployees($request->department, HierarchyStructure::latest()->first());
        }

        return redirect()->route('admin.panel', ['section' => 'hierarchy'])
                        ->with('success', 'Struktura hierarchii została utworzona pomyślnie.');
    }

    public function show(HierarchyStructure $hierarchy)
    {
        $hierarchy->load(['supervisor', 'manager', 'head']);
        
        // Pracownicy przypisani do tej struktury hierarchii
        $employees = Employee::where(function($query) use ($hierarchy) {
            if ($hierarchy->supervisor_username) {
                // Struktura ma supervisora
                $query->where('supervisor_username', $hierarchy->supervisor_username);
            } else if ($hierarchy->manager_username) {
                // Struktura bez supervisora - pracownicy pod managerem
                $query->where('manager_username', $hierarchy->manager_username)
                      ->whereNull('supervisor_username');
            } else {
                // Struktura bez supervisora i managera - pracownicy pod headem
                $query->where('head_username', $hierarchy->head_username)
                      ->whereNull('supervisor_username')
                      ->whereNull('manager_username');
            }
        })
        ->where('department', $hierarchy->department)
        ->select('first_name', 'last_name', 'job_title', 'email', 'created_at')
        ->get();
        
        return view('hierarchy.show', compact('hierarchy', 'employees'));
    }

    public function edit(HierarchyStructure $hierarchy)
    {
        $departments = $this->getAvailableDepartments();
        
        $users = User::whereIn('role', ['supervisor', 'manager', 'head'])
                    ->select('username', 'name', 'role', 'department')
                    ->orderBy('role')
                    ->orderBy('name')
                    ->get();
        
        // Pracownicy przypisani do tej struktury
        $assignedEmployees = Employee::where(function($query) use ($hierarchy) {
            if ($hierarchy->supervisor_username) {
                // Struktura ma supervisora
                $query->where('supervisor_username', $hierarchy->supervisor_username);
            } else if ($hierarchy->manager_username) {
                // Struktura bez supervisora - pracownicy pod managerem
                $query->where('manager_username', $hierarchy->manager_username)
                      ->whereNull('supervisor_username');
            } else {
                // Struktura bez supervisora i managera - pracownicy pod headem
                $query->where('head_username', $hierarchy->head_username)
                      ->whereNull('supervisor_username')
                      ->whereNull('manager_username');
            }
        })
        ->where('department', $hierarchy->department)
        ->get();
        
        return view('hierarchy.edit_new', compact('hierarchy', 'departments', 'users', 'assignedEmployees'));
    }

    public function update(Request $request, HierarchyStructure $hierarchy)
    {
        $availableDepartments = $this->getAvailableDepartments();
        
        $request->validate([
            'department' => 'required|string|in:' . implode(',', $availableDepartments),
            'team_name' => 'required|string|max:255',
            'supervisor_username' => 'nullable|exists:users,username',
            'manager_username' => 'nullable|exists:users,username',
            'head_username' => 'required|exists:users,username',
        ]);

        // Sprawdź unikalność team_name w departamencie (z wyłączeniem aktualnego rekordu)
        $existingTeam = HierarchyStructure::where('department', $request->department)
                                         ->where('team_name', $request->team_name)
                                         ->where('id', '!=', $hierarchy->id)
                                         ->exists();
        
        if ($existingTeam) {
            return redirect()->back()
                           ->withErrors(['team_name' => 'Zespół o tej nazwie już istnieje w tym dziale.'])
                           ->withInput();
        }

        $this->validateUserRoles($request);

        $oldSupervisor = $hierarchy->supervisor_username;
        $hierarchy->update($request->all());

        // Zaktualizuj pracowników jeśli supervisor się zmienił
        if ($oldSupervisor !== $request->supervisor_username) {
            $this->updateEmployeeHierarchy($oldSupervisor, $hierarchy);
        }

        return redirect()->route('admin.panel', ['section' => 'hierarchy'])
                        ->with('success', 'Struktura hierarchii została zaktualizowana pomyślnie.');
    }

    public function destroy(HierarchyStructure $hierarchy)
    {
        // Sprawdź czy są przypisani pracownicy
        $employeesCount = Employee::where('supervisor_username', $hierarchy->supervisor_username)->count();
        
        if ($employeesCount > 0) {
            return redirect()->route('admin.hierarchy.index')
                           ->with('error', "Nie można usunąć struktury. Jest {$employeesCount} pracowników przypisanych do tego supervisora.");
        }

        $hierarchy->delete();

        return redirect()->route('admin.hierarchy.index')
                        ->with('success', 'Struktura hierarchii została usunięta pomyślnie.');
    }

    // Nowa metoda do przypisywania pracowników
    public function assignEmployees(Request $request, HierarchyStructure $hierarchy)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        foreach ($request->employee_ids as $employeeId) {
            $employee = Employee::find($employeeId);
            $employee->supervisor_username = $hierarchy->supervisor_username;
            $employee->manager_username = $hierarchy->manager_username;
            $employee->head_username = $hierarchy->head_username;
            $employee->save();
        }

        return redirect()->route('admin.hierarchy.index')
                        ->with('success', 'Pracownicy zostali przypisani do struktury hierarchii.');
    }

    /**
     * Helper method to get available departments from database
     */
    private function getAvailableDepartments()
    {
        return Team::pluck('name')->sort()->values()->toArray();
    }

    public function massUpdate(Request $request)
    {
        $request->validate([
            'action' => 'required|in:bulk_delete,change_department,reassign_manager',
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'exists:hierarchy_structure,id',
        ]);

        $hierarchies = HierarchyStructure::whereIn('id', $request->selected_ids);

        switch ($request->action) {
            case 'bulk_delete':
                $this->bulkDelete($hierarchies->get());
                break;
            case 'change_department':
                $this->bulkChangeDepartment($request);
                break;
            case 'reassign_manager':
                $this->bulkReassignManager($request);
                break;
        }

        return redirect()->route('admin.hierarchy.index')
                        ->with('success', 'Operacja została wykonana pomyślnie.');
    }

    public function getUsersByRole(Request $request)
    {
        $role = $request->input('role');
        $department = $request->input('department');
        
        $query = User::where('role', $role);
        
        if ($department) {
            $query->where('department', $department);
        }
        
        $users = $query->select('username', 'name')
                      ->orderBy('name')
                      ->get()
                      ->pluck('name', 'username');
        
        return response()->json($users);
    }

    public function getOrphanedEmployees()
    {
        $orphaned = Employee::whereNull('supervisor_username')
                           ->orWhereDoesntHave('supervisor')
                           ->select('id', 'first_name', 'last_name', 'department', 'job_title')
                           ->get();
        
        return view('hierarchy.orphaned', compact('orphaned'));
    }

    public function assignOrphanedEmployee(Request $request, Employee $employee)
    {
        $request->validate([
            'supervisor_username' => 'required|exists:hierarchy_structure,supervisor_username'
        ]);

        $hierarchy = HierarchyStructure::where('supervisor_username', $request->supervisor_username)->first();
        
        if ($hierarchy) {
            $employee->update([
                'supervisor_username' => $hierarchy->supervisor_username,
                'manager_username' => $hierarchy->manager_username,
                'head_username' => $hierarchy->head_username,
            ]);
        }

        return redirect()->back()->with('success', 'Pracownik został przypisany do hierarchii.');
    }

    private function validateUserRoles(Request $request)
    {
        $supervisor = $request->supervisor_username ? User::where('username', $request->supervisor_username)->first() : null;
        $manager = $request->manager_username ? User::where('username', $request->manager_username)->first() : null;
        $head = User::where('username', $request->head_username)->first();

        if ($supervisor && $supervisor->role !== 'supervisor') {
            throw new \Exception('Wybrany supervisor nie ma roli "supervisor".');
        }

        if ($manager && $manager->role !== 'manager') {
            throw new \Exception('Wybrany manager nie ma roli "manager".');
        }

        if ($head && $head->role !== 'head') {
            throw new \Exception('Wybrany head nie ma roli "head".');
        }
    }

    private function autoAssignEmployees($department, $hierarchy)
    {
        // Przypisz pracowników z danego działu do nowej struktury
        Employee::where('department', $department)
                ->whereNull('supervisor_username') // Tylko nieprzypisanych
                ->update([
                    'supervisor_username' => $hierarchy->supervisor_username,
                    'manager_username' => $hierarchy->manager_username,
                    'head_username' => $hierarchy->head_username,
                ]);
    }

    private function updateEmployeeHierarchy($oldSupervisor, $hierarchy)
    {
        Employee::where('supervisor_username', $oldSupervisor)
                ->update([
                    'supervisor_username' => $hierarchy->supervisor_username,
                    'manager_username' => $hierarchy->manager_username,
                    'head_username' => $hierarchy->head_username,
                ]);
    }

    private function bulkDelete($hierarchies)
    {
        foreach ($hierarchies as $hierarchy) {
            $employeesCount = Employee::where('supervisor_username', $hierarchy->supervisor_username)->count();
            if ($employeesCount === 0) {
                $hierarchy->delete();
            }
        }
    }

    private function bulkChangeDepartment(Request $request)
    {
        $request->validate(['new_department' => 'required|string']);
        
        HierarchyStructure::whereIn('id', $request->selected_ids)
                          ->update(['department' => $request->new_department]);
    }

    private function bulkReassignManager(Request $request)
    {
        $request->validate([
            'new_manager' => 'required|exists:users,username',
            'new_head' => 'required|exists:users,username'
        ]);
        
        HierarchyStructure::whereIn('id', $request->selected_ids)
                          ->update([
                              'manager_username' => $request->new_manager,
                              'head_username' => $request->new_head
                          ]);
    }
}
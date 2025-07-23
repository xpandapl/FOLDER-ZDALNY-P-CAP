<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $users = \App\Models\User::orderBy('name')->get();
    
        return view('admin_panel', compact('employees', 'blockDate', 'users'));
    }
    
    public function showAdminPanel()
    {
        $employees = \App\Models\Employee::all();
        $users = \App\Models\User::where('role', 'manager')->get();
        $blockDate = \App\Models\BlockDate::first();
        if (!$blockDate) {
            $blockDate = (object)['block_date' => now()->format('Y-m-d')];
        }
        $teams = \App\Models\Team::pluck('name');

        return view('admin_panel', compact('employees', 'users', 'blockDate', 'teams'));
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
        ]);

        $employee = \App\Models\Employee::find($request->input('employee_id'));
        if ($employee) {
            $employee->name = $request->input('name');
            $employee->job_title = $request->input('job_title');
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
}

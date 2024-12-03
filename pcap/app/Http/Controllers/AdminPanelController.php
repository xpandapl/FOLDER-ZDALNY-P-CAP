<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminPanelController extends Controller
{
    public function __construct()
    {
        // Upewnij się, że użytkownik jest zalogowany i ma rolę 'supermanager'
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role === 'supermanager') {
                return $next($request);
            }
            return redirect('/')->with('error', 'Brak dostępu do panelu administratora.');
        });
    }

    public function index()
    {
        $employees = \App\Models\Employee::orderBy('created_at', 'desc')->get();
    
        // Pobierz aktualną datę blokady
        $blockDate = Carbon::parse(config('app.block_date', '2024-12-05'));
    
        // Pobierz listę użytkowników
        $users = \App\Models\User::orderBy('name')->get();
    
        return view('admin_panel', compact('employees', 'blockDate', 'users'));
    }
    

    public function updateDates(Request $request)
    {
        $request->validate([
            'block_date' => 'required|date',
        ]);

        // Zapisz nową datę blokady (np. w pliku konfiguracyjnym lub bazie danych)
        $blockDate = $request->input('block_date');

        // Jeśli używasz pliku konfiguracyjnego
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                'APP_BLOCK_DATE=' . config('app.block_date'),
                'APP_BLOCK_DATE=' . $blockDate,
                file_get_contents($path)
            ));
        }

        // Wyczyść cache konfiguracji
        \Artisan::call('config:clear');

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


    
    
}

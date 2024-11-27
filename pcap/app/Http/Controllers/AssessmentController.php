<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\Competency;
use App\Models\Result; // Dodaj ten import
use Illuminate\Support\Facades\Mail; // Dodaj import dla Mail
use App\Mail\ResultsMail; // Dodaj import dla ResultsMail

class AssessmentController extends Controller
{
    public function sendCopy(Request $request)
{
    $email = $request->input('email');
    $name = session('name');

    // Znajdź pracownika na podstawie nazwy
    $employee = Employee::where('name', $name)->first();

    if (!$employee) {
        return redirect()->back()->with('error', 'Nie znaleziono danych użytkownika.');
    }

    // Pobierz wyniki użytkownika
    $results = Result::where('employee_id', $employee->id)->with('competency')->get();

    // Wyślij e-mail z wynikami
    Mail::to($email)->send(new ResultsMail($results));

    return redirect()->back()->with('success', 'Kopia wyników została wysłana na Twój e-mail.');
}

}

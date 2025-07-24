<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SelfAssessmentController;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\ManagerController;
use Illuminate\Support\Facades\Auth;

// Nowa trasa główna
Route::get('/', [SelfAssessmentController::class, 'showStep1Form'])->name('self.assessment.step1'); // Pierwszy krok - dane osobowe

// Opcja: Przekierowanie z /self-assessment do /
Route::redirect('/self-assessment', '/');

// Trasy samooceny
Route::post('/self-assessment/step1', [SelfAssessmentController::class, 'saveStep1'])->name('self.assessment.step1.save'); // Zapisz dane osobowe
Route::post('/self-assessment/get-managers', [SelfAssessmentController::class, 'getManagersByDepartment']);
Route::post('/get-managers', [SelfAssessmentController::class, 'getManagersByDepartment'])->name('get.managers');
Route::get('/self-assessment/{level}/{uuid?}', [SelfAssessmentController::class, 'showForm'])
    ->where('level', '[0-9]+')
    ->name('self.assessment');

// Trasa zakończenia samooceny
Route::get('/self-assessment/complete/{uuid}', [SelfAssessmentController::class, 'completeAssessment'])->name('self.assessment.complete');

// Obsługa wyników
Route::post('/submit-assessment', [SelfAssessmentController::class, 'submitAssessment'])->name('submit.assessment'); // Obsługa odpowiedzi z poziomu samooceny
Route::post('/save-results', [SelfAssessmentController::class, 'saveResults'])->name('save_results'); // Zapis wyników po wypełnieniu całego formularza

// Routes for uploading Excel
Route::get('/upload-excel', [SelfAssessmentController::class, 'showUploadForm'])->name('upload.excel');
Route::post('/upload-excel', [SelfAssessmentController::class, 'uploadExcel'])->name('upload.excel.post');

// Admin panel
Route::get('/admin', [AdminPanelController::class, 'showAdminPanel'])->name('admin.panel');
Route::delete('/admin/delete-employee', [AdminPanelController::class, 'deleteEmployee'])->name('admin.delete_employee');
Route::post('/admin/update-dates', [AdminPanelController::class, 'updateDates'])->name('admin.update_dates');
// Fetch employee data
Route::get('/admin/employee/{id}', [AdminPanelController::class, 'getEmployee'])->name('admin.get_employee');
// Update employee data
Route::put('/admin/update-employee', [AdminPanelController::class, 'updateEmployee'])->name('admin.update_employee');
Route::post('/admin/add-manager', [AdminPanelController::class, 'addManager'])->name('admin.add_manager');



// Manager panel
Route::post('/manager-panel/update', [ManagerController::class, 'update'])->name('manager.panel.update');

Route::get('/manager-panel', [ManagerController::class, 'index'])
    ->name('manager_panel')
    ->middleware(['auth', 'manager']);

Route::get('/manager-panel/generate-pdf/{employeeId}', [ManagerController::class, 'generatePdf'])
    ->name('manager.generate_pdf')
    ->middleware('manager');

Route::get('/manager-panel/generate-xls/{employeeId}', [ManagerController::class, 'generateXls'])
    ->name('manager.generate_xls')
    ->middleware('manager');

Route::post('/manager/download-team-report', [ManagerController::class, 'downloadTeamReport'])->name('manager.download_team_report');
Route::get('/department/export', [ManagerController::class, 'exportDepartment'])->name('department.export');

// Uwierzytelnianie
Auth::routes();
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');

// Dodatkowe trasy
Route::post('/send-copy', [SelfAssessmentController::class, 'sendCopy'])->name('send.copy');
Route::get('/competency-definition/{id}', [SelfAssessmentController::class, 'getDefinition']);

Route::get('/self-assessment/generate-pdf/{uuid}', [SelfAssessmentController::class, 'generatePdf'])->name('self.assessment.generate_pdf');
Route::get('/self-assessment/generate-xls/{uuid}', [SelfAssessmentController::class, 'generateXls'])->name('self.assessment.generate_xls');

// Trasa autosave
Route::post('/self-assessment/autosave', [SelfAssessmentController::class, 'autosave'])->name('self_assessment.autosave');


// Trasa debugowania (opcjonalnie)
Route::get('/debug', function () {
    dd(auth()->user());
});

// Trasy testowe (opcjonalnie)
Route::get('/test-login', function () {
    return '<form method="POST" action="/test-login">' .
           csrf_field() .
           '<input type="text" name="username" placeholder="Username">' .
           '<input type="password" name="password" placeholder="Password">' .
           '<button type="submit">Login</button>' .
           '</form>';
});

Route::post('/test-login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->only('username', 'password');
    if (\Auth::attempt($credentials)) {
        return 'Zalogowano pomyślnie';
    } else {
        return 'Niepoprawne dane logowania';
    }
});

Route::get('/form/edit/{uuid}', [SelfAssessmentController::class, 'edit'])->name('form.edit');

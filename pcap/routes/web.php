<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SelfAssessmentController;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\ManagerController;
use Illuminate\Support\Facades\Auth;

// Global temporary maintenance gate (front + manager/admin panels)
if (config('app.maintenance')) {
    // Optional: allow a simple health endpoint for monitoring
    Route::get('/health', function(){ return response('OK', 200); });

    // Catch-all that shows maintenance page with 503 status
    Route::any('/{any?}', function(){
        return response()->view('maintenance', [], 503);
    })->where('any', '.*');

    // Stop registering other routes
    return;
}

// Główna strona aplikacji - widok start z wyborem ścieżki
Route::get('/', [SelfAssessmentController::class, 'startLanding'])->name('start.landing');

// Formularz step1 (Świeżak) przeniesiony pod /step1
Route::get('/step1', [SelfAssessmentController::class, 'showStep1Form'])->name('self.assessment.step1'); // Pierwszy krok - dane osobowe

// Ścieżka Weteran
Route::get('/start/veteran', [SelfAssessmentController::class, 'startVeteranForm'])->name('start.veteran.form');
Route::post('/start/veteran', [SelfAssessmentController::class, 'startVeteranSubmit'])->name('start.veteran.submit');

// Przekierowania dla zachowania kompatybilności
Route::redirect('/start', '/');
Route::redirect('/self-assessment', '/');

// Trasy samooceny
Route::post('/self-assessment/step1', [SelfAssessmentController::class, 'saveStep1'])->name('self.assessment.step1.save'); // Zapisz dane osobowe
Route::post('/self-assessment/get-managers', [SelfAssessmentController::class, 'getManagersByDepartment']);
Route::post('/get-managers', [SelfAssessmentController::class, 'getManagersByDepartment'])->name('get.managers');
Route::post('/get-supervisors', [SelfAssessmentController::class, 'getSupervisorsByDepartment'])->name('get.supervisors');
Route::post('/get-hierarchy', [SelfAssessmentController::class, 'getHierarchyPreview'])->name('get.hierarchy');
Route::get('/self-assessment/{level}/{uuid?}', [SelfAssessmentController::class, 'showForm'])
    ->where('level', '[0-9]+')
    ->name('self.assessment');

// Trasa zakończenia samooceny
Route::get('/self-assessment/complete/{uuid}', [SelfAssessmentController::class, 'completeAssessment'])->name('self.assessment.complete');

// Obsługa wyników
Route::post('/submit-assessment', [SelfAssessmentController::class, 'submitAssessment'])->name('submit.assessment'); // Obsługa odpowiedzi z poziomu samooceny
Route::post('/save-results', [SelfAssessmentController::class, 'saveResults'])->name('save_results'); // Zapis wyników po wypełnieniu całego formularza

// Routes for uploading Excel
// Admin-only: upload/update question base
Route::middleware(['auth'])->group(function(){
    Route::get('/upload-excel', [SelfAssessmentController::class, 'showUploadForm'])->name('upload.excel');
    Route::post('/upload-excel', [SelfAssessmentController::class, 'uploadExcel'])->name('upload.excel.post');
    Route::get('/upload-excel/template', [SelfAssessmentController::class, 'downloadTemplate'])->name('upload.excel.template');
    
    // Clear all cache - admin only
    Route::get('/clear-all-cache', function() {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        \Artisan::call('view:clear');
        \Artisan::call('config:clear');
        \Artisan::call('cache:clear');
        return 'Cache cleared successfully';
    })->name('admin.clear_cache');
});

// Admin panel
Route::get('/admin', [AdminPanelController::class, 'showAdminPanel'])->name('admin.panel');
Route::delete('/admin/delete-employee', [AdminPanelController::class, 'deleteEmployee'])->name('admin.delete_employee');
Route::post('/admin/update-dates', [AdminPanelController::class, 'updateDates'])->name('admin.update_dates');
// Cycle management (admin)
Route::post('/admin/cycles/start', [AdminPanelController::class, 'startCycle'])->name('admin.cycles.start');
Route::post('/admin/cycles/{id}/activate', [AdminPanelController::class, 'activateCycle'])->name('admin.cycles.activate');
Route::post('/admin/cycles/{id}/lock', [AdminPanelController::class, 'lockCycle'])->name('admin.cycles.lock');
// Fetch employee data
Route::get('/admin/employee/{id}', [AdminPanelController::class, 'getEmployee'])->name('admin.get_employee');
// Update employee data
Route::put('/admin/update-employee', [AdminPanelController::class, 'updateEmployee'])->name('admin.update_employee');
Route::post('/admin/add-manager', [AdminPanelController::class, 'addManager'])->name('admin.add_manager');
// Manager edit endpoints
Route::get('/admin/manager/{id}', [AdminPanelController::class, 'getManager'])->name('admin.get_manager');
Route::put('/admin/update-manager', [AdminPanelController::class, 'updateManager'])->name('admin.update_manager');
Route::post('/admin/reset-password', [AdminPanelController::class, 'resetPassword'])->name('admin.reset_password');
Route::delete('/admin/manager/{id}', [AdminPanelController::class, 'deleteManager'])->name('admin.delete_manager');
// Competencies summary (lazy JSON)
Route::get('/admin/competencies/summary', [AdminPanelController::class, 'competenciesSummary'])->name('admin.competencies_summary');
Route::get('/admin/competencies/search', [AdminPanelController::class, 'searchCompetencies'])->name('admin.competencies_search');
Route::get('/admin/section/{section}', [AdminPanelController::class, 'loadSection'])->name('admin.load_section');
Route::post('/admin/settings/update', [AdminPanelController::class, 'updateSettings'])->name('admin.settings.update');

// Admin hierarchy management - integracja z istniejącym panelem
Route::middleware(['auth'])->group(function () {
    // Sprawdzenie roli w kontrolerze
    Route::get('/admin/hierarchy', [\App\Http\Controllers\Admin\HierarchyController::class, 'index'])->name('admin.hierarchy.index');
    Route::get('/admin/hierarchy/create', [\App\Http\Controllers\Admin\HierarchyController::class, 'create'])->name('admin.hierarchy.create');
    Route::post('/admin/hierarchy', [\App\Http\Controllers\Admin\HierarchyController::class, 'store'])->name('admin.hierarchy.store');
    Route::get('/admin/hierarchy/{hierarchy}', [\App\Http\Controllers\Admin\HierarchyController::class, 'show'])->name('admin.hierarchy.show');
    Route::get('/admin/hierarchy/{hierarchy}/edit', [\App\Http\Controllers\Admin\HierarchyController::class, 'edit'])->name('admin.hierarchy.edit');
    Route::put('/admin/hierarchy/{hierarchy}', [\App\Http\Controllers\Admin\HierarchyController::class, 'update'])->name('admin.hierarchy.update');
    Route::delete('/admin/hierarchy/{hierarchy}', [\App\Http\Controllers\Admin\HierarchyController::class, 'destroy'])->name('admin.hierarchy.destroy');
    Route::post('/admin/hierarchy/{hierarchy}/assign-employees', [\App\Http\Controllers\Admin\HierarchyController::class, 'assignEmployees'])->name('admin.hierarchy.assign_employees');
    Route::post('/admin/hierarchy/mass-update', [\App\Http\Controllers\Admin\HierarchyController::class, 'massUpdate'])->name('admin.hierarchy.mass-update');
    Route::get('/admin/hierarchy-users-by-role', [\App\Http\Controllers\Admin\HierarchyController::class, 'getUsersByRole'])->name('admin.hierarchy.users-by-role');
    Route::get('/admin/hierarchy/orphaned-employees', [\App\Http\Controllers\Admin\HierarchyController::class, 'getOrphanedEmployees'])->name('admin.hierarchy.orphaned');
    Route::post('/admin/hierarchy/assign-orphaned/{employee}', [\App\Http\Controllers\Admin\HierarchyController::class, 'assignOrphanedEmployee'])->name('admin.hierarchy.assign-orphaned');
});



// Legacy manager panel (old interface)
Route::get('/manager-panel-old', [ManagerController::class, 'index'])
    ->name('manager_panel_old')
    ->middleware(['auth', 'manager']);

Route::post('/manager-panel-old/update', [ManagerController::class, 'update'])
    ->middleware('enforce.active.cycle.manager')
    ->name('manager.panel.update');

Route::get('/manager-panel-old/generate-pdf/{employeeId}', [ManagerController::class, 'generatePdf'])
    ->name('manager.generate_pdf')
    ->middleware('manager');

Route::get('/manager-panel-old/generate-xls/{employeeId}', [ManagerController::class, 'generateXls'])
    ->name('manager.generate_xls')
    ->middleware('manager');

// Generate access code for an employee for the active cycle (legacy endpoint)
Route::post('/manager-panel-old/access-code/{employeeId}', [ManagerController::class, 'generateAccessCode'])
    ->name('manager.generate_access_code')
    ->middleware(['auth','manager']);

Route::post('/manager/download-team-report', [ManagerController::class, 'downloadTeamReport'])->name('manager.download_team_report');
Route::get('/department/export', [ManagerController::class, 'exportDepartment'])->name('department.export');

// Main manager panel (new interface)
Route::get('/manager-panel', [ManagerController::class, 'showNew'])
    ->name('manager_panel')
    ->middleware(['auth', 'manager']);

// Cycle comparison endpoints
Route::get('/manager/cycle-comparison', [ManagerController::class, 'cycleComparison'])
    ->name('manager.cycle_comparison')
    ->middleware(['auth', 'manager']);

Route::get('/manager/employee-history', [ManagerController::class, 'employeeHistory'])
    ->name('manager.employee_history')
    ->middleware(['auth', 'manager']);

// Code management endpoints
Route::post('/manager/generate-all-codes', [ManagerController::class, 'generateAllCodes'])
    ->name('manager.generate_all_codes')
    ->middleware(['auth', 'manager']);

Route::post('/manager/revoke-code/{employeeId}', [ManagerController::class, 'revokeCode'])
    ->name('manager.revoke_code')
    ->middleware(['auth', 'manager']);

Route::post('/manager/revoke-all-codes', [ManagerController::class, 'revokeAllCodes'])
    ->name('manager.revoke_all_codes')
    ->middleware(['auth', 'manager']);

Route::get('/manager/export-codes', [ManagerController::class, 'exportCodes'])
    ->name('manager.export_codes')
    ->middleware(['auth', 'manager']);

// New: Get full access code for employee
Route::get('/manager/get-full-code/{employeeId}', [ManagerController::class, 'getFullAccessCode'])
    ->name('manager.get_full_code')
    ->middleware(['auth', 'manager']);

// New: Export access codes with full codes
Route::get('/manager/export-access-codes', [ManagerController::class, 'exportAccessCodes'])
    ->name('manager.export_access_codes')
    ->middleware(['auth', 'manager']);

// Manager password change
Route::post('/manager/change-password', [ManagerController::class, 'changePassword'])
    ->name('manager.change_password')
    ->middleware(['auth', 'manager']);

// Export endpoints
Route::get('/manager/export-team-pdf', [ManagerController::class, 'exportTeamPdf'])
    ->name('manager.export_team_pdf')
    ->middleware(['auth', 'manager']);

Route::get('/manager/export-team-excel', [ManagerController::class, 'exportTeamExcel'])
    ->name('manager.export_team_excel')
    ->middleware(['auth', 'manager']);

Route::get('/manager/export-organization-pdf', [ManagerController::class, 'exportOrganizationPdf'])
    ->name('manager.export_organization_pdf')
    ->middleware(['auth', 'manager']);

Route::get('/manager/export-organization-excel', [ManagerController::class, 'exportOrganizationExcel'])
    ->name('manager.export_organization_excel')
    ->middleware(['auth', 'manager']);

Route::get('/manager/export-statistics-excel', [ManagerController::class, 'exportStatisticsExcel'])
    ->name('manager.export_statistics_excel')
    ->middleware(['auth', 'manager']);

Route::post('/manager/generate-comprehensive-report', [ManagerController::class, 'generateComprehensiveReport'])
    ->name('manager.generate_comprehensive_report')
    ->middleware(['auth', 'manager']);

Route::post('/manager/generate-department-summary', [ManagerController::class, 'generateDepartmentSummary'])
    ->name('manager.generate_department_summary')
    ->middleware(['auth', 'manager']);

Route::get('/department/export-analytics', [ManagerController::class, 'exportDepartmentAnalytics'])
    ->name('department.export_analytics')
    ->middleware(['auth', 'manager']);

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

Route::get('/form/edit/{uuid}', [SelfAssessmentController::class, 'edit'])->name('form.edit');


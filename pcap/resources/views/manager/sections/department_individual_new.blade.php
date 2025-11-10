@if($manager->role == 'head')
@php
    // Use the same view as individual but with department employees
    $employees = $departmentEmployees;
@endphp

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Dział - Indywidualne oceny</h2>
        <p class="card-description">Zarządzaj oceną kompetencji pracowników w Twoim dziale</p>
    </div>
    <div class="card-content">
        <!-- Employee Selection -->
        <div class="form-group">
            <label class="form-label" for="department-employee-select">
                <i class="fas fa-user-circle"></i> Wybierz pracownika z działu
            </label>
            <select id="department-employee-select" class="form-control select2" onchange="filterByDepartmentEmployee()">
                <option value="">-- Wybierz pracownika --</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ isset($employee) && $employee->id == $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }} - {{ $emp->job_title ?? 'Brak stanowiska' }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(isset($employee))
            @include('manager.sections.partials.individual_assessment_form')
        @else
            <!-- No Employee Selected -->
            <div class="no-data">
                <i class="fas fa-user-circle"></i>
                <h3>Wybierz pracownika z działu</h3>
                <p>Aby zobaczyć indywidualne oceny, wybierz pracownika z Twojego działu z listy rozwijanej powyżej.</p>
            </div>
        @endif
    </div>
</div>

@else
    <div class="no-data">
        <i class="fas fa-ban"></i>
        <h3>Brak dostępu</h3>
        <p>Ta sekcja jest dostępna tylko dla kierowników działów (head).</p>
    </div>
@endif

@push('scripts')
<script>
    function filterByDepartmentEmployee() {
        const employeeId = document.getElementById('department-employee-select').value;
        if (employeeId) {
            const cycleId = document.getElementById('cycle-select') ? document.getElementById('cycle-select').value : '';
            window.location.href = `?section=department_individual&employee=${employeeId}&cycle=${cycleId}`;
        }
    }
</script>
@endpush

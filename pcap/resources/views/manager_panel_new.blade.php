@extends('layouts.manager')

@section('breadcrumb')
    <div class="breadcrumb-item">
        <i class="fas fa-home"></i>
        Panel Managera
    </div>
    <span class="breadcrumb-separator">
        <i class="fas fa-chevron-right"></i>
    </span>
    <div class="breadcrumb-item" id="current-section">
        Dashboard
    </div>
@endsection

@section('header-actions')
    <div style="display: flex; align-items: center; gap: 12px;">
        <!-- Cycle Selector -->
        <div class="cycle-selector">
            <label for="cycle-select" style="font-weight: 500; margin-right: 8px;">Cykl:</label>
            <select id="cycle-select" onchange="onCycleChange()">
                @foreach(($cycles ?? []) as $c)
                    <option value="{{ $c->id }}" {{ (isset($selectedCycleId) && $selectedCycleId == $c->id) ? 'selected' : '' }}>
                        {{ $c->label }}
                    </option>
                @endforeach
            </select>
            @if(isset($selectedCycle))
                <span class="cycle-status {{ $isSelectedCycleActive ? 'active' : 'historical' }}">
                    {{ $isSelectedCycleActive ? 'Aktywny' : 'Historyczny' }}
                </span>
            @endif
        </div>
        
        <!-- Clear Cache Button -->
        <button id="clear-cache-btn" class="btn btn-sm" style="padding: 8px 12px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; cursor: pointer; transition: all 0.2s ease;" title="Wyczyść cache i odśwież dane">
            <i class="fas fa-sync-alt" style="color: var(--primary);"></i>
        </button>
    </div>
@endsection

@section('content')
<!-- Dynamic content container -->
<div id="manager-content">
    @php
        $currentSection = request('section', 'dashboard');
    @endphp

    @switch($currentSection)
        @case('dashboard')
            @include('manager.sections.dashboard')
            @break
        @case('individual')
            @include('manager.sections.individual')
            @break
        @case('team')
            @include('manager.sections.team')
            @break
        @case('codes')
            @include('manager.sections.codes')
            @break
        @case('hr_individual')
            @if($manager->role == 'supermanager')
                @include('manager.sections.hr_individual')
            @endif
            @break
        @case('hr')
            @if($manager->role == 'supermanager')
                @include('manager.sections.hr')
            @endif
            @break
        @case('department_individual')
            @if($manager->role == 'head')
                @include('manager.sections.department_individual')
            @endif
            @break
        @case('department')
            @if($manager->role == 'head')
                @include('manager.sections.department')
            @endif
            @break
        @default
            @include('manager.sections.individual')
    @endswitch
</div>

<!-- Definition Modal -->
<div id="definitionModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Definicja kompetencji</h3>
            <button class="btn btn-secondary btn-sm" onclick="closeDefinitionModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="definitionContent">
            <!-- Content loaded dynamically -->
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Additional specific styles for manager panel */
    
    /* Modal styles */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    
    .modal-content {
        background: var(--card);
        padding: 24px;
        border-radius: 12px;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    /* Employee details card */
    .employee-details {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .employee-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }

    .employee-detail-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .employee-detail-item i {
        color: var(--primary);
        width: 16px;
    }

    .employee-detail-item strong {
        color: var(--text);
        margin-right: 4px;
    }

    /* Stats cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin: 24px 0;
    }

    .stat-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        font-size: 24px;
        margin-bottom: 12px;
        padding: 12px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon.level-1 { background: #dbeafe; color: #1d4ed8; }
    .stat-icon.level-2 { background: #dcfce7; color: #166534; }
    .stat-icon.level-3 { background: #fef3c7; color: #92400e; }
    .stat-icon.level-4 { background: #f3e8ff; color: #7c3aed; }
    .stat-icon.level-5 { background: #fee2e2; color: #dc2626; }
    .stat-icon.filled { background: #ecfdf5; color: #059669; }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 14px;
        color: var(--muted);
        font-weight: 500;
    }

    /* Tables */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--card);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border);
    }

    .data-table th {
        background: var(--bg);
        padding: 16px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        color: var(--text);
        border-bottom: 1px solid var(--border);
    }

    .data-table td {
        padding: 16px;
        border-bottom: 1px solid var(--border);
        font-size: 14px;
        vertical-align: top;
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    .data-table tr:hover {
        background: rgba(59, 130, 246, 0.02);
    }

    /* Competency badges */
    .competency-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
    }

    .badge.level { background: #f3f4f6; color: #374151; }
    .badge.osobiste { background: #dbeafe; color: #1e40af; }
    .badge.spoleczne { background: #dcfce7; color: #166534; }
    .badge.liderskie { background: #f3e8ff; color: #7c3aed; }
    .badge.zawodowe-logistics { background: #fed7aa; color: #9a3412; }
    .badge.zawodowe-growth { background: #ccfbf1; color: #115e59; }
    .badge.zawodowe-inne { background: #fee2e2; color: #991b1b; }

    /* Form enhancements */
    .competency-value-container {
        position: relative;
        display: inline-block;
    }

    .competency-value-display {
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
        background: var(--bg);
    }

    .competency-value-overridden {
        color: var(--primary) !important;
        background: var(--ring) !important;
    }

    .icon-wrapper {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 4px;
        background: var(--bg);
        border: 1px solid var(--border);
        margin-left: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .icon-wrapper:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* Action buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .download-buttons {
        display: flex;
        gap: 8px;
        margin-top: 16px;
    }

    /* Search enhancement */
    .search-box {
        position: relative;
        margin-bottom: 20px;
    }

    .search-box input {
        padding-left: 40px;
    }

    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
    }

    /* Feedback textarea */
    .feedback-textarea {
        width: 100%;
        min-height: 80px;
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        resize: vertical;
        transition: border-color 0.2s ease;
    }

    .feedback-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--ring);
    }

    /* No data state */
    .no-data {
        text-align: center;
        padding: 60px 20px;
        color: var(--muted);
    }

    .no-data i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .no-data h3 {
        font-size: 18px;
        margin-bottom: 8px;
        color: var(--text);
    }

    .no-data p {
        font-size: 14px;
        max-width: 400px;
        margin: 0 auto;
        line-height: 1.5;
    }

    /* Alerts and notifications */
    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        font-weight: 500;
    }

    .alert-success {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .alert-info {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #93c5fd;
    }

    .alert-warning {
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    /* Cycle comparison */
    .cycle-comparison {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 16px;
        margin: 16px 0;
    }

    .cycle-comparison-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .cycle-comparison-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .cycle-data {
        padding: 12px;
        background: var(--card);
        border-radius: 6px;
        border: 1px solid var(--border);
    }

    .cycle-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .cycle-value {
        font-size: 16px;
        font-weight: 600;
        color: var(--text);
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Enhanced Select2 initialization
        $('#employee-select, #hr-employee-select, #department-employee-select, #cycle-select').select2({
            theme: 'default',
            width: '100%'
        });

        // Handle section changes
        $(document).on('sectionChanged', function(event, section) {
            updateBreadcrumb(section);
            // Load section content via AJAX if needed
            loadSectionContent(section);
        });

        // Handle cycle changes
        window.onCycleChange = function() {
            const cycleId = $('#cycle-select').val();
            const url = new URL(window.location);
            url.searchParams.set('cycle', cycleId);
            
            // Show brief loading with timeout
            showLoading();
            setTimeout(() => {
                window.location.href = url.toString();
            }, 150);
        };

        // Employee filtering functions
        window.filterByEmployee = function() {
            const employeeId = $('#employee-select').val();
            updateUrlWithEmployee(employeeId, 'individual');
        };

        window.filterByHREmployee = function() {
            const employeeId = $('#hr-employee-select').val();
            updateUrlWithEmployee(employeeId, 'hr_individual');
        };

        window.filterByDepartmentEmployee = function() {
            const employeeId = $('#department-employee-select').val();
            updateUrlWithEmployee(employeeId, 'department_individual');
        };

        function updateUrlWithEmployee(employeeId, section) {
            const url = new URL(window.location);
            if (employeeId) {
                url.searchParams.set('employee', employeeId);
            } else {
                url.searchParams.delete('employee');
            }
            url.searchParams.set('section', section);
            
            // Show brief loading with timeout
            showLoading();
            setTimeout(() => {
                window.location.href = url.toString();
            }, 150);
        }

        // Modal functions
        window.showDefinitionModal = function(competencyId) {
            showLoading();
            fetch('/competency-definition/' + competencyId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('definitionContent').innerHTML = data;
                    document.getElementById('definitionModal').style.display = 'flex';
                    hideLoading();
                })
                .catch(error => {
                    console.error('Error loading definition:', error);
                    hideLoading();
                });
        };

        window.closeDefinitionModal = function() {
            document.getElementById('definitionModal').style.display = 'none';
        };

        // Close modal on background click
        $('#definitionModal').on('click', function(e) {
            if (e.target === this) {
                closeDefinitionModal();
            }
        });

        // Copy text utility
        window.copyText = function(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast('Skopiowano do schowka', 'success');
                });
            } else {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                try {
                    document.execCommand('copy');
                    showToast('Skopiowano do schowka', 'success');
                } catch (err) {
                    console.error('Failed to copy text', err);
                }
                document.body.removeChild(textarea);
            }
        };

        // Enhanced search functionality
        $('#hr-search').on('input', function() {
            const query = $(this).val().toLowerCase();
            $('#hr-table-body tr').each(function() {
                const name = $(this).find('td').eq(0).text().toLowerCase();
                const job = $(this).find('td').eq(1).text().toLowerCase();
                const matches = name.includes(query) || job.includes(query);
                $(this).toggle(matches);
            });
        });

        // Competency value editing
        $(document).on('click', '.edit-competency-value-button', function() {
            handleEditCompetencyValue(this);
        });

        $(document).on('click', '.save-competency-value-button', function() {
            handleSaveCompetencyValue(this);
        });

        $(document).on('click', '.remove-overridden-value-button', function() {
            handleRemoveOverriddenValue(this);
        });
    });

    function updateBreadcrumb(section) {
        const sectionNames = {
            'dashboard': 'Dashboard',
            'individual': 'Indywidualne oceny',
            'team': 'Cały zespół',
            'codes': 'Kody dostępu',
            'hr_individual': 'HR - Indywidualne',
            'hr': 'HR - Organizacja',
            'department_individual': 'Dział - Indywidualne',
            'department': 'Dział - Zespół'
        };
        
        $('#current-section').text(sectionNames[section] || 'Panel Managera');
    }

    function loadSectionContent(section) {
        // Reload page with new section parameter
        const url = new URL(window.location);
        url.searchParams.set('section', section);
        
        // Keep current cycle if set
        const currentCycle = url.searchParams.get('cycle');
        if (currentCycle) {
            url.searchParams.set('cycle', currentCycle);
        }
        
        // Keep current employee if set
        const currentEmployee = url.searchParams.get('employee');
        if (currentEmployee) {
            url.searchParams.set('employee', currentEmployee);
        }
        
        // Reload the page
        window.location.href = url.toString();
    }

    // Alias for loadSectionContent for backward compatibility
    function loadSection(section) {
        loadSectionContent(section);
    }

    function showToast(message, type = 'info') {
        // Simple toast notification
        const toast = $(`
            <div class="alert alert-${type}" style="position: fixed; top: 20px; right: 20px; z-index: 10001; min-width: 300px;">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
                ${message}
            </div>
        `);
        
        $('body').append(toast);
        
        setTimeout(() => {
            toast.fadeOut(() => toast.remove());
        }, 3000);
    }

    // Competency value editing functions
    function handleEditCompetencyValue(icon) {
        const competencyId = icon.getAttribute('data-competency-id');
        const container = document.querySelector('.competency-value-container[data-competency-id="' + competencyId + '"]');
        const inputField = container.querySelector('input');
        const displaySpan = container.querySelector('.competency-value-display');

        displaySpan.style.display = 'none';
        inputField.style.display = 'inline-block';
        inputField.focus();

        icon.classList.remove('edit-competency-value-button', 'fa-pencil-alt');
        icon.classList.add('save-competency-value-button', 'fa-save');
    }

    function handleSaveCompetencyValue(icon) {
        const competencyId = icon.getAttribute('data-competency-id');
        const container = document.querySelector('.competency-value-container[data-competency-id="' + competencyId + '"]');
        const inputField = container.querySelector('input');
        const displaySpan = container.querySelector('.competency-value-display');

        const newValue = inputField.value;
        displaySpan.textContent = newValue;
        displaySpan.classList.add('competency-value-overridden');

        inputField.style.display = 'none';
        displaySpan.style.display = 'inline';

        inputField.setAttribute('name', 'competency_values[' + competencyId + ']');

        icon.classList.remove('save-competency-value-button', 'fa-save');
        icon.classList.add('remove-overridden-value-button', 'fa-trash');
    }

    function handleRemoveOverriddenValue(icon) {
        const competencyId = icon.getAttribute('data-competency-id');
        const container = document.querySelector('.competency-value-container[data-competency-id="' + competencyId + '"]');
        const inputField = container.querySelector('input');
        const displaySpan = container.querySelector('.competency-value-display');
        const teamValue = container.getAttribute('data-team-value');

        inputField.removeAttribute('name');
        displaySpan.textContent = teamValue;
        displaySpan.classList.remove('competency-value-overridden');

        inputField.style.display = 'none';
        displaySpan.style.display = 'inline';

        icon.classList.remove('remove-overridden-value-button', 'fa-trash');
        icon.classList.add('edit-competency-value-button', 'fa-pencil-alt');

        const deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = 'delete_competency_values[]';
        deleteInput.value = competencyId;
        container.appendChild(deleteInput);

        container.setAttribute('data-original-value', teamValue);
    }
</script>
@endpush
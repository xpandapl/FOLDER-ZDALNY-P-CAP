@extends('layouts.admin')

@section('page-title', 'Panel Administratora')

@section('breadcrumb')
    <div class="breadcrumb-item">
        <i class="fas fa-home"></i>
        Panel Admin
    </div>
    <span class="breadcrumb-separator">
        <i class="fas fa-chevron-right"></i>
    </span>
    <div class="breadcrumb-item">
        @switch(request('section', 'employees'))
            @case('employees')
                Pracownicy
                @break
            @case('managers')
                Managerowie
                @break
            @case('hierarchy')
                Hierarchia
                @break
            @case('dates')
                Zarządzanie Datami
                @break
            @case('competencies')
                Baza pytań
                @break
            @case('cycles')
                Cykl ocen
                @break
            @case('settings')
                Ustawienia
                @break
            @default
                Pracownicy
        @endswitch
    </div>
@endsection

@section('content')
<!-- Dynamic content container -->
<div id="admin-content">
    @php
        $currentSection = request('section', 'employees');
    @endphp

    @switch($currentSection)
        @case('employees')
            @include('admin.sections.employees')
            @break
        @case('managers')
            @include('admin.sections.managers')
            @break
        @case('hierarchy')
            @include('admin.sections.hierarchy')
            @break
        @case('dates')
            @include('admin.sections.dates')
            @break
        @case('competencies')
            @include('admin.sections.competencies')
            @break
        @case('cycles')
            @include('admin.sections.cycles')
            @break
        @case('settings')
            @include('admin.sections.settings')
            @break
        @default
            @include('admin.sections.employees')
    @endswitch
</div>

<!-- Loading overlay -->
<div id="section-loading" class="loading-overlay" style="display: none;">
    <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
        <span>Ładowanie...</span>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom alert styles */
    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    /* Search input styles */
    .search-container {
        position: relative;
        margin-bottom: 24px;
    }

    .search-input {
        padding-left: 40px;
    }

    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        font-size: 14px;
    }

    /* Filter card styles */
    .filter-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        align-items: end;
    }

    /* Action buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-group {
        display: flex;
        gap: 4px;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }

    .btn-outline {
        background: white;
        border: 1px solid var(--border);
        color: var(--muted);
    }

    .btn-outline:hover {
        color: var(--text);
        border-color: var(--muted);
    }

    .btn-outline.btn-warning:hover {
        background: var(--warning);
        border-color: var(--warning);
        color: white;
    }

    .btn-outline.btn-danger:hover {
        background: var(--danger);
        border-color: var(--danger);
        color: white;
    }

    /* UUID input styling */
    .uuid-container {
        display: flex;
        gap: 8px;
        max-width: 400px;
    }

    .uuid-input {
        flex: 1;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 12px;
        background: #f8fafc;
    }

    .copy-btn {
        background: var(--primary);
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .copy-btn:hover {
        background: var(--primary-600);
        transform: translateY(-1px);
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--muted);
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 18px;
        margin-bottom: 8px;
        color: var(--text);
    }

    .empty-state p {
        margin-bottom: 20px;
    }

    /* Modal backdrop */
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 2000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-content {
        background: var(--card);
        border-radius: 12px;
        padding: 24px;
        max-width: 500px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border);
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 20px;
        color: var(--muted);
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
    }

    .modal-close:hover {
        background: #f3f4f6;
        color: var(--text);
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
    }

    /* Loading state */
    .table-loading {
        text-align: center;
        padding: 40px;
        color: var(--muted);
    }

    /* Responsive table */
    .table-responsive {
        overflow-x: auto;
        border-radius: 12px;
        border: 1px solid var(--border);
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 12px;
        }
        
        .table th,
        .table td {
            padding: 12px 8px;
        }
        
        .uuid-container {
            max-width: none;
        }
        
        .filter-grid {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Global functions for admin panel
    
    // Copy to clipboard function
    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                showNotification('Link skopiowany do schowka', 'success');
            }).catch(function() {
                fallbackCopyTextToClipboard(text);
            });
        } else {
            fallbackCopyTextToClipboard(text);
        }
    }

    function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showNotification('Link skopiowany do schowka', 'success');
        } catch (err) {
            showNotification('Nie udało się skopiować linku', 'error');
        }
        
        document.body.removeChild(textArea);
    }

    // Show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Search functionality
    function setupSearch(inputId, tableBodyId) {
        const searchInput = document.getElementById(inputId);
        const tableBody = document.getElementById(tableBodyId);
        
        if (searchInput && tableBody) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                const rows = tableBody.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }
    }

    // Modal functions
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // Close modal on backdrop click
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            e.target.style.display = 'none';
            document.body.style.overflow = '';
        }
    });

    // Confirm delete function
    function confirmDelete(message, action) {
        if (confirm(message || 'Czy na pewno chcesz usunąć ten element?')) {
            action();
        }
    }

    // AJAX helper
    function makeRequest(url, options = {}) {
        const defaultOptions = {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };
        
        return fetch(url, { ...defaultOptions, ...options });
    }

    // Manager functions - made global to work with dynamic content
    window.editManager = function(managerId) {
        fetch(`/admin/manager/${managerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    // Populate edit form
                    document.getElementById('edit_user_id').value = data.id;
                    document.getElementById('edit_name').value = data.name || '';
                    document.getElementById('edit_username').value = data.username || '';
                    document.getElementById('edit_email').value = data.email || '';
                    document.getElementById('edit_role').value = data.role || '';
                    document.getElementById('edit_department').value = data.department || '';
                    
                    // Open modal
                    openModal('editManagerModal');
                } else {
                    showNotification('Nie udało się pobrać danych managera', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Wystąpił błąd podczas pobierania danych managera', 'error');
            });
    };
    
    window.resetPassword = function(managerId) {
        fetch(`/admin/manager/${managerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    document.getElementById('reset_user_id').value = data.id;
                    document.getElementById('reset_user_name').textContent = data.name;
                    document.getElementById('new_password').value = '';
                    document.getElementById('confirm_password').value = '';
                    
                    openModal('resetPasswordModal');
                } else {
                    showNotification('Nie udało się pobrać danych managera', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Wystąpił błąd podczas pobierania danych managera', 'error');
            });
    };
    
    window.deleteManager = function(managerId) {
        if (confirm('Czy na pewno chcesz usunąć tego managera? Ta operacja jest nieodwracalna.')) {
            fetch(`/admin/manager/${managerId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    // Reload the section
                    loadSection('managers');
                } else {
                    showNotification(data.message || 'Wystąpił błąd', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Wystąpił błąd podczas usuwania managera', 'error');
            });
        }
    };

    // Employee functions - made global to work with dynamic content  
    window.editEmployee = function(employeeId) {
        fetch(`/admin/employee/${employeeId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const employee = data.employee;
                    document.getElementById('employee_id_to_edit').value = employee.id;
                    document.getElementById('edit_first_name').value = employee.first_name || '';
                    document.getElementById('edit_last_name').value = employee.last_name || '';
                    document.getElementById('edit_job_title').value = employee.job_title;
                    
                    // Set hierarchy structure if exists
                    if (employee.hierarchy_structure_id) {
                        document.getElementById('edit_hierarchy_structure').value = employee.hierarchy_structure_id;
                    }
                    
                    openModal('editEmployeeModal');
                } else {
                    showNotification('Nie udało się pobrać danych pracownika', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Wystąpił błąd podczas pobierania danych', 'error');
            });
    };

    window.deleteEmployee = function(employeeId) {
        document.getElementById('employee_id_to_delete').value = employeeId;
        openModal('deleteEmployeeModal');
    };
</script>

<style>
    /* Notification styles */
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 3000;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .notification.show {
        transform: translateX(0);
    }

    .notification-success {
        background: var(--accent);
    }

    .notification-error {
        background: var(--danger);
    }

    .notification-info {
        background: var(--primary);
    }

    /* AJAX Section Loading */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2000;
    }

    .loading-spinner {
        text-align: center;
        color: var(--text-secondary);
    }

    .loading-spinner i {
        font-size: 2rem;
        margin-bottom: 1rem;
        display: block;
    }

    .loading-spinner span {
        font-size: 1rem;
        font-weight: 500;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if there are validation errors and reopen the reset password modal
    @if($errors->any() && session('_old_input.user_id'))
        setTimeout(function() {
            const userId = {{ session('_old_input.user_id') }};
            if (userId) {
                resetPassword(userId);
            }
        }, 100);
    @endif
    
    // AJAX Section Switching
    const sectionLinks = document.querySelectorAll('.section-link');
    const contentContainer = document.getElementById('admin-content');
    const loadingOverlay = document.getElementById('section-loading');
    
    sectionLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const section = this.getAttribute('data-section');
            if (!section) return;
            
            // Update active state
            sectionLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Show loading
            loadingOverlay.style.display = 'flex';
            
            // Load section content
            fetch(`/admin/section/${section}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        contentContainer.innerHTML = data.html;
                        
                        // Update URL without page reload
                        const newUrl = new URL(window.location);
                        newUrl.searchParams.set('section', section);
                        window.history.pushState({section: section}, '', newUrl);
                        
                        // Update breadcrumb
                        updateBreadcrumb(section);
                        
                        // Hide loading
                        loadingOverlay.style.display = 'none';
                        
                        // Trigger any section-specific initialization
                        initializeSection(section);
                        
                    } else {
                        console.error('Server error:', data);
                        showNotification(data.error || 'Nie udało się załadować sekcji', 'error');
                        loadingOverlay.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error loading section:', error);
                    showNotification(`Wystąpił błąd podczas ładowania sekcji: ${error.message}`, 'error');
                    loadingOverlay.style.display = 'none';
                });
        });
    });
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.section) {
            loadSection(e.state.section, false);
        }
    });
    
    function updateBreadcrumb(section) {
        const breadcrumbTexts = {
            'employees': 'Pracownicy',
            'managers': 'Managerowie', 
            'hierarchy': 'Hierarchia',
            'dates': 'Zarządzanie Datami',
            'competencies': 'Baza pytań',
            'cycles': 'Cykl ocen',
            'settings': 'Ustawienia'
        };
        
        const breadcrumbItem = document.querySelector('.breadcrumb-item:last-child');
        if (breadcrumbItem && breadcrumbTexts[section]) {
            breadcrumbItem.textContent = breadcrumbTexts[section];
        }
    }
    
    function initializeSection(section) {
        // Re-initialize any JavaScript that might be needed for specific sections
        if (section === 'competencies') {
            // Trigger competencies search initialization if needed
            const competencySearch = document.getElementById('competency-search');
            if (competencySearch) {
                // Trigger competencies loading
                const event = new Event('DOMContentLoaded');
                document.dispatchEvent(event);
            }
        } else if (section === 'employees') {
            // Initialize employee search
            setTimeout(() => {
                setupSearch('employee-search', 'employee-table-body');
            }, 100);
        } else if (section === 'managers') {
            // Initialize managers section JavaScript
            setTimeout(() => {
                // Re-attach event listeners for manager functions if needed
                console.log('Managers section initialized');
            }, 100);
        }
    }
});
</script>
</style>
@endpush
<!-- Section: Pracownicy -->
<div class="section-header">
    <div>
        <h2 class="section-title">Zarządzanie Pracownikami</h2>
        <p class="section-description">Przeglądaj i zarządzaj danymi pracowników oraz ich strukturą hierarchiczną</p>
    </div>
</div>

<!-- Search -->
<div class="search-container">
    <div class="search-icon">
        <i class="fas fa-search"></i>
    </div>
    <input type="text" id="employee-search" class="form-control search-input" placeholder="Wyszukaj pracownika po imieniu, nazwisku, dziale lub stanowisku...">
</div>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $employees->count() }}</h3>
            <p>Łącznie pracowników</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-sitemap"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $employees->whereNotNull('supervisor_username')->count() + $employees->whereNotNull('manager_username')->count() + $employees->whereNotNull('head_username')->count() }}</h3>
            <p>Przypisani do hierarchii</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-user-slash"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $employees->whereNull('supervisor_username')->whereNull('manager_username')->whereNull('head_username')->count() }}</h3>
            <p>Nieprzypisani</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-content">
            <h3>{{ $employees->groupBy('department')->count() }}</h3>
            <p>Działów</p>
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Pracownik</th>
                <th>Link do Edycji</th>
                <th>Dział</th>
                <th>Stanowisko</th>
                <th>Struktura Hierarchii</th>
                <th>Data utworzenia</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody id="employee-table-body">
            @foreach($employees as $employee)
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <div class="user-avatar">
                            {{ substr($employee->name ?? ($employee->first_name . ' ' . $employee->last_name), 0, 1) }}
                        </div>
                        <div>
                            <div class="user-name">{{ $employee->name ?? ($employee->first_name . ' ' . $employee->last_name) }}</div>
                            <div class="user-role">{{ $employee->email ?? 'Brak email' }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="uuid-container">
                        <input type="text" class="form-control uuid-input" value="{{ url('/form/edit/' . $employee->uuid) }}" readonly>
                        <button class="copy-btn" onclick="copyToClipboard('{{ url('/form/edit/' . $employee->uuid) }}')" type="button">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </td>
                <td>
                    <span class="badge badge-primary">{{ $employee->department }}</span>
                </td>
                <td>{{ $employee->job_title }}</td>
                <td>
                    @php
                        $structure = \App\Models\HierarchyStructure::where('department', $employee->department)
                            ->where(function($query) use ($employee) {
                                if ($employee->supervisor_username) {
                                    $query->where('supervisor_username', $employee->supervisor_username);
                                } else if ($employee->manager_username) {
                                    $query->where('manager_username', $employee->manager_username)
                                          ->whereNull('supervisor_username');
                                } else if ($employee->head_username) {
                                    $query->where('head_username', $employee->head_username)
                                          ->whereNull('supervisor_username')
                                          ->whereNull('manager_username');
                                }
                            })
                            ->first();
                    @endphp
                    @if($structure)
                        <div>
                            <strong class="text-primary">{{ $structure->team_name }}</strong>
                            <div class="text-muted" style="font-size: 12px;">
                                @if($employee->supervisor_username)
                                    <i class="fas fa-user"></i> {{ $employee->supervisor_username }}
                                @elseif($employee->manager_username)
                                    <i class="fas fa-user-tie"></i> {{ $employee->manager_username }}
                                @else
                                    <i class="fas fa-crown"></i> {{ $employee->head_username }}
                                @endif
                            </div>
                        </div>
                    @else
                        <span class="badge badge-secondary">Brak struktury</span>
                    @endif
                </td>
                <td>
                    <div style="font-size: 12px;">
                        {{ $employee->created_at->format('d.m.Y') }}
                        <div class="text-muted">{{ $employee->created_at->format('H:i') }}</div>
                    </div>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-outline btn-warning btn-icon edit-button" 
                                data-id="{{ $employee->id }}" 
                                title="Edytuj pracownika"
                                onclick="editEmployee({{ $employee->id }})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline btn-danger btn-icon delete-button" 
                                data-id="{{ $employee->id }}" 
                                title="Usuń pracownika"
                                onclick="deleteEmployee({{ $employee->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($employees->isEmpty())
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <h3>Brak pracowników</h3>
            <p>Nie znaleziono żadnych pracowników w systemie.</p>
        </div>
    @endif
</div>

<!-- Edit Employee Modal -->
<div id="editEmployeeModal" class="modal-backdrop" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edytuj Pracownika</h3>
            <button class="modal-close" onclick="closeModal('editEmployeeModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="editEmployeeForm" method="POST" action="{{ route('admin.update_employee') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="employee_id" id="employee_id_to_edit">
            
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_first_name" class="form-label">Imię</label>
                    <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_last_name" class="form-label">Nazwisko</label>
                    <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_job_title" class="form-label">Stanowisko</label>
                    <input type="text" class="form-control" id="edit_job_title" name="job_title" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_hierarchy_structure" class="form-label">Struktura hierarchii</label>
                    <select class="form-control" id="edit_hierarchy_structure" name="hierarchy_structure_id">
                        <option value="">— Wybierz strukturę —</option>
                        @foreach(\App\Models\HierarchyStructure::with(['supervisor', 'manager', 'head'])->orderBy('department')->orderBy('team_name')->get() as $structure)
                            <option value="{{ $structure->id }}" data-department="{{ $structure->department }}">
                                {{ $structure->department }} - {{ $structure->team_name }}
                                @if($structure->supervisor)
                                    (Supervisor: {{ $structure->supervisor->name }})
                                @elseif($structure->manager)
                                    (Manager: {{ $structure->manager->name }})
                                @else
                                    (Head: {{ $structure->head->name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted mt-2" style="display: block;">Wybierz strukturę hierarchii dla pracownika. To automatycznie ustawi przełożonych.</small>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editEmployeeModal')">Anuluj</button>
                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Employee Modal -->
<div id="deleteEmployeeModal" class="modal-backdrop" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Potwierdź usunięcie</h3>
            <button class="modal-close" onclick="closeModal('deleteEmployeeModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="deleteEmployeeForm" method="POST" action="{{ route('admin.delete_employee') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="employee_id" id="employee_id_to_delete">
            
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 48px; margin-bottom: 16px;"></i>
                    <p>Czy na pewno chcesz usunąć tego pracownika?</p>
                    <p class="text-muted">Ta operacja jest nieodwracalna.</p>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('deleteEmployeeModal')">Anuluj</button>
                <button type="submit" class="btn btn-danger">Usuń pracownika</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Setup search functionality
    setupSearch('employee-search', 'employee-table-body');
    
    // Edit employee function
    function editEmployee(employeeId) {
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
    }
    
    // Delete employee function
    function deleteEmployee(employeeId) {
        document.getElementById('employee_id_to_delete').value = employeeId;
        openModal('deleteEmployeeModal');
    }
</script>

<style>
    .section-header {
        margin-bottom: 32px;
    }
    
    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 8px 0;
    }
    
    .section-description {
        color: var(--muted);
        margin: 0;
        font-size: 16px;
    }
</style>
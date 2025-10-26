@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Panel Administratora</h1>
    <!-- Przycisk Wyloguj i Panel managera -->
    <div style="text-align: right; margin-bottom: 20px;">
        <!-- Formularz wylogowania -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <!-- Przycisk Panel managera -->
        <a href="{{ url('/manager-panel') }}">
            <button class="logout-button">
                Panel managera
            </button>
        </a>

        <!-- Przycisk Wyloguj -->
        <button class="logout-button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i> Wyloguj
        </button>
    </div>


    <!-- Nawigacja zakładek -->
    <ul class="nav nav-tabs" id="adminTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="employees-tab" data-toggle="tab" href="#employees" role="tab" aria-controls="employees" aria-selected="true">Pracownicy</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="managers-tab" data-toggle="tab" href="#managers" role="tab" aria-controls="managers" aria-selected="false">Managerowie</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="hierarchy-tab" data-toggle="tab" href="#hierarchy" role="tab" aria-controls="hierarchy" aria-selected="false">Hierarchia</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="dates-tab" data-toggle="tab" href="#dates" role="tab" aria-controls="dates" aria-selected="false">Zarządzanie Datami</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="competencies-tab" data-toggle="tab" href="#competencies" role="tab" aria-controls="competencies" aria-selected="false">Baza pytań</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cycles-tab" data-toggle="tab" href="#cycles" role="tab" aria-controls="cycles" aria-selected="false">Cykl ocen</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="settings-tab" data-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="false">Ustawienia</a>
        </li>
    </ul>

    <!-- Zawartość zakładek -->
    <div class="tab-content" id="adminTabContent">
        <!-- Zakładka Pracownicy -->
        <div class="tab-pane fade show active" id="employees" role="tabpanel" aria-labelledby="employees-tab">
            <!-- Wyszukiwarka -->
            <div class="mb-3 mt-3">
                <input type="text" id="employee-search" class="form-control" placeholder="Wyszukaj pracownika...">
            </div>

            <!-- Tabela z pracownikami -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Imię</th>
                        <th>Nazwisko</th>
                        <th>Link do Edycji</th>
                        <th>Dział</th>
                        <th>Stanowisko</th>
                        <th>Manager</th>
                        <th>Data przesłania</th>
                        <th>Ostatnia aktualizacja</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody id="employee-table-body">
                    @foreach($employees as $employee)
                    <tr>
                        <td>{{ $employee->first_name ?? $employee->name }}</td>
                        <td>{{ $employee->last_name ?? '' }}</td>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control uuid-input" value="{{ url('/form/edit/' . $employee->uuid) }}" readonly>
                                <button class="btn btn-outline-secondary copy-button" data-uuid="{{ url('/form/edit/' . $employee->uuid) }}" type="button">
                                <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </td>
                        <td>{{ $employee->department }}</td>
                        <td>{{ $employee->job_title }}</td>
                        <td>
                            @php
                                $mgr = $employee->manager_username;
                            @endphp
                            {{ $mgr && isset($managerNameByUsername[$mgr]) ? $managerNameByUsername[$mgr] : ($mgr ?? '-') }}
                        </td>
                        <td>{{ $employee->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $employee->updated_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <div class="action-buttons" style="display:flex;">
                                <button class="btn btn-outline-secondary btn-sm edit-button" style="margin-right:6px;" data-id="{{ $employee->id }}" type="button">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-danger btn-sm delete-button" data-id="{{ $employee->id }}" type="button">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>


                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.update_employee') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="employee_id" id="employee_id_to_edit">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edytuj Pracownika</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Zamknij">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Form fields -->
                                <div class="form-group">
                                    <label for="edit_name">Imię i Nazwisko:</label>
                                    <input type="text" class="form-control" id="edit_name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_job_title">Stanowisko:</label>
                                    <input type="text" class="form-control" id="edit_job_title" name="job_title" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_manager_username">Manager:</label>
                                    <select class="form-control" id="edit_manager_username" name="manager_username">
                                        <option value="">— Brak —</option>
                                        @foreach($users as $manager)
                                            <option value="{{ $manager->name }}" data-username="{{ $manager->username }}">{{ $manager->name }} ({{ $manager->username }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
                                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Modal potwierdzenia usunięcia -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.delete_employee') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="employee_id" id="employee_id_to_delete">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Potwierdzenie usunięcia</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Zamknij">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Czy na pewno chcesz usunąć ten formularz?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
                                <button type="submit" class="btn btn-danger">Usuń</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- Zamknięcie div dla zakładki employees -->

        <!-- Zakładka Managerowie -->
        <div class="tab-pane fade" id="managers" role="tabpanel" aria-labelledby="managers-tab">
            <div class="user-list mt-3">
                <h2>Lista Managerów / Użytkowników</h2>
                <!-- Dodaj formularz dodawania użytkownika -->
                <form method="POST" action="{{ route('admin.add_manager') }}" class="mb-4">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <input type="text" name="name" class="form-control" placeholder="Imię i Nazwisko" required>
                        </div>
                        <div class="col">
                            <input type="text" name="username" class="form-control" placeholder="Login" required>
                        </div>
                        <div class="col">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="col">
                            <input type="password" name="password" class="form-control" placeholder="Hasło" required>
                        </div>
                        <div class="col">
                            <select name="role" class="form-control" required>
                                <option value="">Wybierz rolę</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <select name="department" class="form-control" required>
                                <option value="">Wybierz dział</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team }}">{{ $team }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-success">Dodaj Użytkownika</button>
                        </div>
                    </div>
                </form>
                <!-- ...istniejąca tabela managerów... -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Imię i Nazwisko</th>
                            <th>Email</th>
                            <th>Rola</th>
                            <th>Dział</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->department }}</td>
                            <td>
                                <button class="btn btn-outline-secondary btn-sm edit-manager-button" data-id="{{ $user->id }}" type="button">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Manager Edit Modal -->
        <div class="modal fade" id="editManagerModal" tabindex="-1" aria-labelledby="editManagerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.update_manager') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="user_id" id="manager_id_to_edit">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edytuj Managera</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Zamknij">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="edit_manager_role">Rola:</label>
                                <select class="form-control" id="edit_manager_role" name="role" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_manager_department">Dział:</label>
                                <select class="form-control" id="edit_manager_department" name="department" required>
                                    @foreach($teams as $team)
                                        <option value="{{ $team }}">{{ $team }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
                            <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Zakładka Hierarchia -->
        <div class="tab-pane fade" id="hierarchy" role="tabpanel" aria-labelledby="hierarchy-tab">
            <div class="hierarchy-management mt-3">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Zarządzanie hierarchią organizacyjną</h2>
                    <a href="{{ route('admin.hierarchy.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Dodaj strukturę
                    </a>
                </div>

                <!-- Iframe z pełnym panelem hierarchii -->
                <div style="border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden;">
                    <iframe src="{{ route('admin.hierarchy.index') }}" 
                            style="width: 100%; height: 600px; border: none;">
                    </iframe>
                </div>

                <div class="mt-3">
                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Panel hierarchii pozwala na zarządzanie strukturą organizacyjną z 3 poziomami: Supervisor → Manager → Head.
                        Pracownicy automatycznie dziedziczą hierarchię po wyborze bezpośredniego supervisora.
                    </p>
                </div>
            </div>
        </div>

        <!-- Zakładka Zarządzanie Datami -->
        <div class="tab-pane fade" id="dates" role="tabpanel" aria-labelledby="dates-tab">
            <div class="date-management mt-3">
                <h2>Zarządzanie Datami</h2>
                <form method="POST" action="{{ route('admin.update_dates') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="block_date" class="form-label">Data blokady formularza:</label>
                        <input type="date" class="form-control" style="width:200px;" id="block_date" name="block_date"
    value="{{ $blockDate ? \Carbon\Carbon::parse($blockDate->block_date)->format('Y-m-d') : '' }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Zapisz</button>
                </form>
            </div>
        </div>

        <!-- Zakładka Baza pytań (lazy) -->
        <div class="tab-pane fade" id="competencies" role="tabpanel" aria-labelledby="competencies-tab">
            <div class="mt-3">
                <div class="d-flex" style="gap:10px; align-items:center; margin-bottom:10px;">
                    <a href="{{ route('upload.excel') }}" class="btn btn-primary">Aktualizacja bazy pytań</a>
                    <small class="text-muted">Dostęp tylko dla administracji</small>
                </div>
                <div class="d-flex align-items-end" style="gap:10px; flex-wrap:wrap;">
                    <div>
                        <label for="comp_department_filter" style="display:block; font-weight:600;">Filtr działu (opcjonalnie)</label>
                        <select id="comp_department_filter" class="form-control" style="min-width:220px;">
                            <option value="">— Wszystkie działy —</option>
                            @foreach($teams as $team)
                                <option value="{{ $team }}">{{ $team }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="comp_level_filter" style="display:block; font-weight:600;">Poziom</label>
                        <select id="comp_level_filter" class="form-control">
                            <option value="">— Wszystkie —</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div>
                        <label for="comp_type_filter" style="display:block; font-weight:600;">Typ</label>
                        <input id="comp_type_filter" class="form-control" placeholder="np. 1., 2., 3.G" />
                    </div>
                    <div>
                        <label for="comp_per_page" style="display:block; font-weight:600;">Na stronę</label>
                        <select id="comp_per_page" class="form-control">
                            <option value="10">10</option>
                            <option value="20" selected>20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <button id="comp_reload" class="btn btn-outline-secondary">Odśwież</button>
                    <div id="comp_status" style="margin-left:auto;"></div>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-sm" id="competencies_table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kompetencja</th>
                                <th>Poziom</th>
                                <th>Typ</th>
                                <th>Opis 0</th>
                                <th>Opis 0,25</th>
                                <th>Opis 0,5</th>
                                <th>Opis 0,75–1</th>
                                <th>Odpowiedzi</th>
                                <th>Śr. ocena</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="10" class="text-center">Kliknij zakładkę, aby załadować dane…</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-2" id="competencies_pagination" style="display:none;">
                    <button class="btn btn-sm btn-outline-secondary" id="comp_prev">Poprzednia</button>
                    <div id="comp_page_info"></div>
                    <button class="btn btn-sm btn-outline-secondary" id="comp_next">Następna</button>
                </div>
            </div>
        </div>

        <!-- Zakładka Cykl ocen -->
        <div class="tab-pane fade" id="cycles" role="tabpanel" aria-labelledby="cycles-tab">
            <div class="mt-3">
                <h2>Cykl ocen</h2>

                <h5 class="mt-3">Istniejące cykle</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Rok</th>
                                <th>Okres</th>
                                <th>Etykieta</th>
                                <th>Status</th>
                                <th>Zablokowano</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($cycles ?? collect()) as $c)
                                <tr>
                                    <td>{{ $c->id }}</td>
                                    <td>{{ $c->year }}</td>
                                    <td>{{ $c->period ?? '—' }}</td>
                                    <td>{{ $c->label }}</td>
                                    <td>
                                        @if($c->is_active)
                                            <span class="badge badge-success">aktywny</span>
                                        @else
                                            <span class="badge badge-secondary">historyczny</span>
                                        @endif
                                    </td>
                                    <td>{{ $c->locked_at ? \Carbon\Carbon::parse($c->locked_at)->format('Y-m-d H:i') : '—' }}</td>
                                    <td style="white-space:nowrap;">
                                        @if(!$c->is_active)
                                            <form method="POST" action="{{ route('admin.cycles.activate', ['id' => $c->id]) }}" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">Ustaw jako aktywny</button>
                                            </form>
                                        @endif
                                        @if(!$c->locked_at)
                                            <form method="POST" action="{{ route('admin.cycles.lock', ['id' => $c->id]) }}" style="display:inline-block; margin-left:6px;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning">Zablokuj</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">Brak cykli</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h5 class="mt-4">Nowy cykl</h5>
                <form method="POST" action="{{ route('admin.cycles.start') }}" class="row g-3" style="max-width:680px;">
                    @csrf
                    <div class="col-md-3">
                        <label for="cycle_year" class="form-label">Rok</label>
                        <input id="cycle_year" type="number" class="form-control" name="year" value="{{ now()->year }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="cycle_period" class="form-label">Okres</label>
                        <input id="cycle_period" type="text" class="form-control" name="period" placeholder="np. H1/H2" />
                    </div>
                    <div class="col-md-6">
                        <label for="cycle_label" class="form-label">Etykieta (opcjonalnie)</label>
                        <input id="cycle_label" type="text" class="form-control" name="label" placeholder="np. 2025 H1" />
                    </div>
                    <div class="col-12 form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="cycle_activate" name="activate" value="1">
                        <label class="form-check-label" for="cycle_activate">Ustaw jako aktywny (poprzedni aktywny zostanie zablokowany)</label>
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-success">Utwórz cykl</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Settings Tab -->
        <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
            <div class="card">
                <div class="card-header">
                    <h4>Ustawienia aplikacji</h4>
                    <p class="mb-0 text-muted">Zarządzaj treściami wyświetlanymi w aplikacji</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        
                        @foreach($appSettings as $setting)
                        <div class="form-group mb-4">
                            <label for="setting_{{ $setting->key }}" class="form-label">
                                <strong>{{ $setting->label }}</strong>
                            </label>
                            @if($setting->description)
                                <small class="form-text text-muted d-block mb-2">{{ $setting->description }}</small>
                            @endif
                            
                            @if($setting->type === 'textarea')
                                <textarea 
                                    name="settings[{{ $setting->key }}]" 
                                    id="setting_{{ $setting->key }}" 
                                    class="form-control" 
                                    rows="8"
                                >{{ $setting->value }}</textarea>
                                <small class="form-text text-muted">Możesz używać podstawowych tagów HTML (p, ul, li, strong, em, a).</small>
                            @elseif($setting->type === 'email')
                                <input 
                                    type="email" 
                                    name="settings[{{ $setting->key }}]" 
                                    id="setting_{{ $setting->key }}" 
                                    class="form-control" 
                                    value="{{ $setting->value }}"
                                >
                            @else
                                <input 
                                    type="text" 
                                    name="settings[{{ $setting->key }}]" 
                                    id="setting_{{ $setting->key }}" 
                                    class="form-control" 
                                    value="{{ $setting->value }}"
                                >
                            @endif
                        </div>
                        @endforeach
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Zapisz ustawienia
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div> <!-- Zamknięcie div dla tab-content -->
</div> <!-- Zamknięcie div dla container -->

@if(session('success'))
<div class="alert alert-success mt-3">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger mt-3">
    {{ session('error') }}
</div>
@endif


<!-- Skrypty JavaScript -->
@section('scripts')
<script>
$(document).ready(function() {
    // Dynamiczne filtrowanie tabeli
    $('#employee-search').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#employee-table-body tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Obsługa przycisku kopiowania UUID
    $('.copy-button').on('click', function() {
        var uuid = $(this).data('uuid');
        navigator.clipboard.writeText(uuid).then(function() {
            alert('Link skopiowany do schowka.');
        }, function(err) {
            alert('Błąd podczas kopiowania: ' + err);
        });
    });

    // Obsługa przycisku usuwania
    $('.delete-button').on('click', function() {
        var employeeId = $(this).data('id');
        $('#employee_id_to_delete').val(employeeId);
        $('#deleteModal').modal('show');
    });


    // Handle Edit button click
    $('.edit-button').on('click', function() {
        var employeeId = $(this).data('id');
        // Fetch employee data via AJAX
        $.ajax({
            url: '{{ url('/admin/employee') }}/' + employeeId,
            method: 'GET',
            success: function(data) {
                // Populate the modal fields
                $('#employee_id_to_edit').val(data.id);
                $('#edit_name').val(data.name);
                $('#edit_job_title').val(data.job_title);
                // Try to select by full name first
                var $mgrSelect = $('#edit_manager_username');
                var mgrValue = data.manager_username ?? '';
                $mgrSelect.val(mgrValue);
                // If no option with this full name, try mapping stored username to option's data-username
                if (!$mgrSelect.val() && mgrValue) {
                    var $optByUsername = $mgrSelect.find('option').filter(function(){
                        return $(this).data('username') === mgrValue;
                    }).first();
                    if ($optByUsername.length) {
                        $mgrSelect.val($optByUsername.val());
                    }
                }
                // Show the modal
                $('#editModal').modal('show');
            },
            error: function() {
                alert('Błąd podczas pobierania danych pracownika.');
            }
        });
    });

    // Handle Manager Edit button click
    $('.edit-manager-button').on('click', function() {
        var managerId = $(this).data('id');
        $.ajax({
            url: '{{ url('/admin/manager') }}/' + managerId,
            method: 'GET',
            success: function(data) {
                $('#manager_id_to_edit').val(data.id);
                $('#edit_manager_role').val(data.role);
                $('#edit_manager_department').val(data.department);
                $('#editManagerModal').modal('show');
            },
            error: function() {
                alert('Błąd podczas pobierania danych managera.');
            }
        });
    });

    // Lazy load for competencies tab
    var compLoaded = false;
    var compPage = 1;
    function loadCompetencies(resetPage) {
        if (resetPage) compPage = 1;
        var perPage = $('#comp_per_page').val() || 20;
        var department = $('#comp_department_filter').val() || '';
        var level = $('#comp_level_filter').val() || '';
        var ctype = $('#comp_type_filter').val() || '';
        $('#comp_status').text('Ładowanie…');
        $.ajax({
            url: '{{ route('admin.competencies_summary') }}',
            method: 'GET',
            data: { page: compPage, per_page: perPage, department: department, level: level, competency_type: ctype },
            success: function(resp){
                var $tbody = $('#competencies_table tbody');
                $tbody.empty();
                if (!resp.data || resp.data.length === 0) {
                    $tbody.append('<tr><td colspan="10" class="text-center">Brak danych</td></tr>');
                } else {
                    resp.data.forEach(function(row){
                        var avg = (row.avg_score !== null && row.avg_score !== undefined) ? parseFloat(row.avg_score).toFixed(2) : 'N/D';
                        var rcount = row.response_count ?? 0;
                        function safe(s){ return s ? $('<div>').text(s).html() : ''; }
                        $tbody.append(
                            '<tr>'+
                            '<td>'+row.id+'</td>'+
                            '<td>'+safe(row.competency_name)+'</td>'+
                            '<td>'+safe(row.level)+'</td>'+
                            '<td>'+safe(row.competency_type)+'</td>'+
                            '<td>'+safe('Nie dotyczy / brak oceny początkowo')+'</td>'+
                            '<td>'+safe(row.description_025)+'</td>'+
                            '<td>'+safe(row.description_0_to_05)+'</td>'+
                            '<td>'+safe(row.description_075_to_1)+'</td>'+
                            '<td>'+rcount+'</td>'+
                            '<td>'+avg+'</td>'+
                            '</tr>'
                        );
                    });
                }
                // Pagination controls
                var $pag = $('#competencies_pagination');
                if (resp.total > resp.per_page) {
                    $pag.show();
                    $('#comp_page_info').text('Strona '+resp.current_page+' z '+resp.last_page+' (łącznie: '+resp.total+')');
                    $('#comp_prev').prop('disabled', resp.current_page <= 1);
                    $('#comp_next').prop('disabled', resp.current_page >= resp.last_page);
                } else {
                    $pag.hide();
                }
                $('#comp_status').text('');
            },
            error: function(){
                $('#comp_status').text('Błąd ładowania');
            }
        });
    }

    // Bootstrap tab shown event
    $('a[data-toggle="tab"][href="#competencies"]').on('shown.bs.tab', function(){
        if (!compLoaded) {
            loadCompetencies(true);
            compLoaded = true;
        }
    });
    $('#comp_reload').on('click', function(){ loadCompetencies(true); });
    $('#comp_per_page, #comp_department_filter, #comp_level_filter').on('change', function(){ loadCompetencies(true); });
    var ctypeTimer = null;
    $('#comp_type_filter').on('input', function(){
        clearTimeout(ctypeTimer);
        ctypeTimer = setTimeout(function(){ loadCompetencies(true); }, 300);
    });
    $('#comp_prev').on('click', function(){ if (compPage>1){ compPage--; loadCompetencies(false);} });
    $('#comp_next').on('click', function(){ compPage++; loadCompetencies(false); });
});
</script>
@endsection

<!-- Style -->
@section('styles')
<style>
    .input-group .form-control.uuid-input {
        position: relative;
        z-index: 1;
    }

    .input-group .copy-button {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        z-index: 2;
        border: none;
        background: transparent;
    }

    .input-group .copy-button i {
        font-size: 1.2em;
    }

    .logout-button {
        background-color: grey;
        padding: 5px 10px;
        font-size: 12px;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 5px;
    }

    .logout-button:hover {
        background-color: darkgrey;
    }

    .action-buttons {
        display: flex!important;
        align-items: center; /* Wyrównuje elementy w pionie */
        justify-content: flex-start;
    }

    .action-buttons .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .action-buttons .btn i {
        font-size: 1.2em;
    }

    .action-buttons .btn.btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        color: #fff;
    }

    .action-buttons .btn.btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    td {
        white-space: nowrap; /* Zapobiega łamaniu się przycisków w kolejne wiersze */
    }

</style>
@endsection

@endsection

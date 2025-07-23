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
            <a class="nav-link" id="dates-tab" data-toggle="tab" href="#dates" role="tab" aria-controls="dates" aria-selected="false">Zarządzanie Datami</a>
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
                        <th>Imię i Nazwisko</th>
                        <th>Link do Edycji</th>
                        <th>Dział</th>
                        <th>Stanowisko</th>
                        <th>Data przesłania</th>
                        <th>Ostatnia aktualizacja</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody id="employee-table-body">
                    @foreach($employees as $employee)
                    <tr>
                        <td>{{ $employee->name }}</td>
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
                <h2>Lista Managerów</h2>
                <!-- Dodaj formularz dodawania managera -->
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
                            <select name="department" class="form-control" required>
                                <option value="">Wybierz dział</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team }}">{{ $team }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-success">Dodaj Managera</button>
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->department }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
                // Show the modal
                $('#editModal').modal('show');
            },
            error: function() {
                alert('Błąd podczas pobierania danych pracownika.');
            }
        });
    });
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

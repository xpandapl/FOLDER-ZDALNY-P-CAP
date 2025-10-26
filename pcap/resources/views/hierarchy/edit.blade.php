<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj strukturę hierarchii</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body style="background: #f8f9fa;">
    <div class="container-fluid p-3">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.hierarchy.index') }}" class="text-decoration-none">
                        <i class="fas fa-sitemap"></i> Hierarchie
                    </a>
                </li>
                <li class="breadcrumb-item active">Edytuj strukturę</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit text-warning"></i>
                            Edytuj strukturę hierarchii - {{ $hierarchy->department }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Wystąpiły błędy:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('admin.hierarchy.update', $hierarchy) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Dział -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="department" class="form-label">
                                        <i class="fas fa-building text-primary"></i> Dział
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('department') is-invalid @enderror" 
                                            id="department" name="department" required>
                                        <option value="">Wybierz dział</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept }}" {{ old('department', $hierarchy->department) === $dept ? 'selected' : '' }}>
                                                {{ $dept }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Wybierz dział z dostępnej listy</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="team_name" class="form-label">
                                        <i class="fas fa-users text-info"></i> Nazwa zespołu
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('team_name') is-invalid @enderror" 
                                           id="team_name" name="team_name" 
                                           value="{{ old('team_name', $hierarchy->team_name) }}" 
                                           placeholder="np. SEO, E-commerce Performance, Brand Marketing" required>
                                    @error('team_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Nazwa konkretnego zespołu w dziale</div>
                                </div>
                            </div>

                            <!-- Supervisor -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="supervisor_username" class="form-label">
                                        <i class="fas fa-user text-info"></i> Supervisor
                                        <span class="text-muted">(opcjonalny)</span>
                                    </label>
                                    <select class="form-select @error('supervisor_username') is-invalid @enderror" 
                                            id="supervisor_username" name="supervisor_username">
                                        <option value="">Brak supervisora (bezpośrednio pod managerem)</option>
                                        @foreach($users->where('role', 'supervisor') as $user)
                                            <option value="{{ $user->username }}" 
                                                {{ old('supervisor_username', $hierarchy->supervisor_username) === $user->username ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->username }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supervisor_username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Zostaw puste jeśli pracownicy mają pracować bezpośrednio pod managerem</div>
                                </div>
                            </div>

                            <!-- Manager -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="manager_username" class="form-label">
                                        <i class="fas fa-user-tie text-success"></i> Manager
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('manager_username') is-invalid @enderror" 
                                            id="manager_username" name="manager_username" required>
                                        <option value="">Wybierz managera</option>
                                        @foreach($users->where('role', 'manager') as $user)
                                            <option value="{{ $user->username }}" 
                                                {{ old('manager_username', $hierarchy->manager_username) === $user->username ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->username }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('manager_username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Head -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="head_username" class="form-label">
                                        <i class="fas fa-crown text-warning"></i> Head
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('head_username') is-invalid @enderror" 
                                            id="head_username" name="head_username" required>
                                        <option value="">Wybierz head</option>
                                        @foreach($users->where('role', 'head') as $user)
                                            <option value="{{ $user->username }}" 
                                                {{ old('head_username', $hierarchy->head_username) === $user->username ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->username }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('head_username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr>

                            <!-- Przyciski -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Zapisz zmiany
                                </button>
                                <a href="{{ route('admin.hierarchy.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Anuluj
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Podgląd struktury -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-eye text-info"></i> Podgląd struktury
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="hierarchy-preview text-center">
                            <div class="level">
                                <div class="badge bg-warning p-2 mb-2">
                                    <i class="fas fa-crown"></i>
                                    <span id="head-preview">{{ $hierarchy->head->name ?? $hierarchy->head_username }}</span>
                                </div>
                            </div>
                            <div class="text-muted">↓</div>
                            <div class="level">
                                <div class="badge bg-success p-2 mb-2">
                                    <i class="fas fa-user-tie"></i>
                                    <span id="manager-preview">{{ $hierarchy->manager->name ?? $hierarchy->manager_username }}</span>
                                </div>
                            </div>
                            <div class="text-muted">↓</div>
                            <div class="level">
                                <div class="badge bg-info p-2 mb-2">
                                    <i class="fas fa-user"></i>
                                    <span id="supervisor-preview">{{ $hierarchy->supervisor->name ?? $hierarchy->supervisor_username }}</span>
                                </div>
                            </div>
                            <div class="text-muted">↓</div>
                            <div class="level">
                                <div class="badge bg-secondary p-2">
                                    <i class="fas fa-users"></i>
                                    Pracownicy działu <span id="department-preview">{{ $hierarchy->department }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Przypisani pracownicy -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-users text-success"></i> 
                            Przypisani pracownicy ({{ $assignedEmployees->count() }})
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($assignedEmployees->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Pracownik</th>
                                        <th>Dział</th>
                                        <th>Stanowisko</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignedEmployees as $employee)
                                    <tr>
                                        <td>
                                            <strong>{{ $employee->last_name }} {{ $employee->first_name }}</strong>
                                            <br><small class="text-muted">{{ $employee->employee_code }}</small>
                                        </td>
                                        <td>{{ $employee->department }}</td>
                                        <td>{{ $employee->position }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                            <p>Brak przypisanych pracowników do tej struktury</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const departmentInput = document.getElementById('department');
        const supervisorSelect = document.getElementById('supervisor_username');
        const managerSelect = document.getElementById('manager_username');
        const headSelect = document.getElementById('head_username');

        function updatePreview() {
            const department = departmentInput.value;
            const supervisor = supervisorSelect.options[supervisorSelect.selectedIndex]?.text;
            const manager = managerSelect.options[managerSelect.selectedIndex]?.text;
            const head = headSelect.options[headSelect.selectedIndex]?.text;

            if (department) {
                document.getElementById('department-preview').textContent = department;
            }
            if (supervisor && supervisor !== 'Wybierz supervisora') {
                document.getElementById('supervisor-preview').textContent = supervisor;
            }
            if (manager && manager !== 'Wybierz managera') {
                document.getElementById('manager-preview').textContent = manager;
            }
            if (head && head !== 'Wybierz head') {
                document.getElementById('head-preview').textContent = head;
            }
        }

        [departmentInput, supervisorSelect, managerSelect, headSelect].forEach(element => {
            element.addEventListener('change', updatePreview);
        });
    });
    </script>
</body>
</html>
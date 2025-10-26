<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj strukturę hierarchii</title>
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
                <li class="breadcrumb-item active">Dodaj strukturę</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus text-primary"></i>
                            Dodaj nową strukturę hierarchii
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

                        <form action="{{ route('admin.hierarchy.store') }}" method="POST">
                            @csrf
                            
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
                                            <option value="{{ $dept }}" {{ old('department') === $dept ? 'selected' : '' }}>
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
                                           id="team_name" name="team_name" value="{{ old('team_name') }}" 
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
                                            <option value="{{ $user->username }}" {{ old('supervisor_username') === $user->username ? 'selected' : '' }}>
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
                                            <option value="{{ $user->username }}" {{ old('manager_username') === $user->username ? 'selected' : '' }}>
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
                                            <option value="{{ $user->username }}" {{ old('head_username') === $user->username ? 'selected' : '' }}>
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

                            <!-- Automatyczne przypisanie -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="auto_assign" name="auto_assign" value="1" 
                                           {{ old('auto_assign') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_assign">
                                        <i class="fas fa-magic text-primary"></i>
                                        Automatycznie przypisz pracowników z tego działu do struktury
                                    </label>
                                </div>
                                <div class="form-text">
                                    Jeśli zaznaczone, wszyscy pracownicy z wybranego działu zostaną automatycznie 
                                    przypisani do tej struktury hierarchii.
                                </div>
                            </div>

                            <!-- Przyciski -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Zapisz strukturę
                                </button>
                                <a href="{{ route('admin.hierarchy.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Anuluj
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Podgląd struktury -->
                <div class="card mt-3" id="preview" style="display: none;">
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
                                    <span id="head-preview">Head</span>
                                </div>
                            </div>
                            <div class="text-muted">↓</div>
                            <div class="level">
                                <div class="badge bg-success p-2 mb-2">
                                    <i class="fas fa-user-tie"></i>
                                    <span id="manager-preview">Manager</span>
                                </div>
                            </div>
                            <div class="text-muted">↓</div>
                            <div class="level">
                                <div class="badge bg-info p-2 mb-2">
                                    <i class="fas fa-user"></i>
                                    <span id="supervisor-preview">Supervisor</span>
                                </div>
                            </div>
                            <div class="text-muted">↓</div>
                            <div class="level">
                                <div class="badge bg-secondary p-2">
                                    <i class="fas fa-users"></i>
                                    Pracownicy działu <span id="department-preview"></span>
                                </div>
                            </div>
                        </div>
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
        const preview = document.getElementById('preview');

        function updatePreview() {
            const department = departmentInput.value;
            const supervisor = supervisorSelect.options[supervisorSelect.selectedIndex]?.text;
            const manager = managerSelect.options[managerSelect.selectedIndex]?.text;
            const head = headSelect.options[headSelect.selectedIndex]?.text;

            if (department && supervisor && manager && head) {
                document.getElementById('department-preview').textContent = department;
                document.getElementById('supervisor-preview').textContent = supervisor;
                document.getElementById('manager-preview').textContent = manager;
                document.getElementById('head-preview').textContent = head;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        [departmentInput, supervisorSelect, managerSelect, headSelect].forEach(element => {
            element.addEventListener('change', updatePreview);
        });
    });
    </script>
</body>
</html>
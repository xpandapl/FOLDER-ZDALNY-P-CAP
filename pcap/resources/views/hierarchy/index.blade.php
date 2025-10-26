<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie hierarchią</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body style="background: #f8f9fa;">
    <div class="container-fluid p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Struktury hierarchiczne</h4>
            <a href="{{ route('admin.hierarchy.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Dodaj strukturę
            </a>
        </div>

        <!-- Statystyki -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-sitemap fa-lg me-2"></i>
                            <div>
                                <h6 class="card-title mb-0">{{ $stats['total_structures'] }}</h6>
                                <small>Struktur hierarchii</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users fa-lg me-2"></i>
                            <div>
                                <h6 class="card-title mb-0">{{ $stats['employees_assigned'] }}</h6>
                                <small>Przypisanych pracowników</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-slash fa-lg me-2"></i>
                            <div>
                                <h6 class="card-title mb-0">{{ $stats['employees_unassigned'] }}</h6>
                                <small>Nieprzypisanych</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-building fa-lg me-2"></i>
                            <div>
                                <h6 class="card-title mb-0">{{ $stats['departments_count'] }}</h6>
                                <small>Działów z hierarchią</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtry -->
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label for="department" class="form-label mb-1">Dział</label>
                        <select name="department" id="department" class="form-select form-select-sm">
                            <option value="">Wszystkie działy</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="search" class="form-label mb-1">Szukaj</label>
                        <input type="text" name="search" id="search" class="form-select form-select-sm" 
                               value="{{ request('search') }}" placeholder="Szukaj po dziale, nazwisku...">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-search"></i> Filtruj
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela hierarchii -->
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Dział</th>
                        <th>Zespół</th>
                        <th>Supervisor</th>
                        <th>Manager</th>
                        <th>Head</th>
                        <th>Pracownicy</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hierarchies as $hierarchy)
                    <tr>
                        <td>
                            <span class="badge bg-primary">{{ $hierarchy->department }}</span>
                        </td>
                        <td>
                            <strong>{{ $hierarchy->team_name ?? 'Brak nazwy' }}</strong>
                        </td>
                        <td>
                            @if($hierarchy->supervisor_username)
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user text-primary me-1"></i>
                                    <div>
                                        <strong>{{ $hierarchy->supervisor->name ?? 'N/A' }}</strong>
                                        <br><small class="text-muted">{{ $hierarchy->supervisor_username }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted"><i class="fas fa-minus"></i> Brak supervisora</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-tie text-success me-1"></i>
                                <div>
                                    <strong>{{ $hierarchy->manager->name ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $hierarchy->manager_username }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-crown text-warning me-1"></i>
                                <div>
                                    <strong>{{ $hierarchy->head->name ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $hierarchy->head_username }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $employeeCount = \App\Models\Employee::where('supervisor_username', $hierarchy->supervisor_username)->count();
                            @endphp
                            <span class="badge bg-{{ $employeeCount > 0 ? 'success' : 'secondary' }}">
                                {{ $employeeCount }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.hierarchy.edit', $hierarchy) }}" 
                                   class="btn btn-outline-warning btn-sm" title="Edytuj">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                        onclick="confirmDelete({{ $hierarchy->id }})" title="Usuń">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-2">Brak struktur hierarchicznych</p>
                            <a href="{{ route('admin.hierarchy.create') }}" class="btn btn-primary btn-sm">
                                Dodaj pierwszą strukturę
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($hierarchies->hasPages())
        <div class="d-flex justify-content-center">
            {{ $hierarchies->links() }}
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function confirmDelete(id) {
        if (confirm('Czy na pewno chcesz usunąć tę strukturę hierarchii?')) {
            fetch(`/admin/hierarchy/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Błąd podczas usuwania struktury.');
                }
            });
        }
    }
    </script>
</body>
</html>
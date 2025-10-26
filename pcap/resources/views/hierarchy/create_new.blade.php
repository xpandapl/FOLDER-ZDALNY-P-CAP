@extends('layouts.admin')

@section('page-title', 'Dodaj strukturę hierarchii')

@section('breadcrumb')
    <div class="breadcrumb-item">
        <i class="fas fa-home"></i>
        Panel Admin
    </div>
    <span class="breadcrumb-separator">
        <i class="fas fa-chevron-right"></i>
    </span>
    <div class="breadcrumb-item">
        <a href="{{ route('admin.panel') }}?section=hierarchy">Hierarchia</a>
    </div>
    <span class="breadcrumb-separator">
        <i class="fas fa-chevron-right"></i>
    </span>
    <div class="breadcrumb-item">
        Dodaj strukturę
    </div>
@endsection

@section('header-actions')
    <a href="{{ route('admin.panel') }}?section=hierarchy" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Powrót do listy
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Nowa struktura hierarchii</h3>
    </div>
    <div class="card-body">
        @if ($errors->any())
        <div class="alert alert-error mb-6">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Wystąpiły błędy:</strong>
                <ul style="margin: 8px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <form action="{{ route('admin.hierarchy.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="department" class="form-label">
                    <i class="fas fa-building text-primary"></i>
                    Dział
                </label>
                <select name="department" id="department" class="form-control" required>
                    <option value="">Wybierz dział</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ old('department') === $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="team_name" class="form-label">
                    <i class="fas fa-users text-success"></i>
                    Nazwa zespołu
                </label>
                <input type="text" 
                       name="team_name" 
                       id="team_name" 
                       class="form-control" 
                       value="{{ old('team_name') }}" 
                       placeholder="Np. Zespół Logistyki, Dział IT..."
                       required>
                <small class="text-muted mt-2" style="display: block;">
                    Podaj opisową nazwę dla tego zespołu/struktury
                </small>
            </div>

            <div class="form-group">
                <label for="supervisor_username" class="form-label">
                    <i class="fas fa-user text-primary"></i>
                    Supervisor (opcjonalnie)
                </label>
                <select name="supervisor_username" id="supervisor_username" class="form-control">
                    <option value="">Brak supervisora</option>
                    @foreach($users->where('role', 'supervisor') as $user)
                        <option value="{{ $user->username }}" 
                                {{ old('supervisor_username') === $user->username ? 'selected' : '' }}
                                data-department="{{ $user->department }}">
                            {{ $user->name }} ({{ $user->username }})
                            @if($user->department) - {{ $user->department }} @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="manager_username" class="form-label">
                    <i class="fas fa-user-tie text-warning"></i>
                    Manager (opcjonalnie)
                </label>
                <select name="manager_username" id="manager_username" class="form-control">
                    <option value="">Brak managera</option>
                    @foreach($users->where('role', 'manager') as $user)
                        <option value="{{ $user->username }}" 
                                {{ old('manager_username') === $user->username ? 'selected' : '' }}
                                data-department="{{ $user->department }}">
                            {{ $user->name }} ({{ $user->username }})
                            @if($user->department) - {{ $user->department }} @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="head_username" class="form-label">
                    <i class="fas fa-crown text-danger"></i>
                    Head (wymagany)
                </label>
                <select name="head_username" id="head_username" class="form-control" required>
                    <option value="">Wybierz head'a</option>
                    @foreach($users->where('role', 'head') as $user)
                        <option value="{{ $user->username }}" 
                                {{ old('head_username') === $user->username ? 'selected' : '' }}
                                data-department="{{ $user->department }}">
                            {{ $user->name }} ({{ $user->username }})
                            @if($user->department) - {{ $user->department }} @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="card mt-6" style="background: #f8fafc; border: 1px solid #e5e7eb;">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-info-circle text-primary" style="font-size: 20px;"></i>
                        <div>
                            <h4 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 600;">Ważne informacje</h4>
                            <ul style="margin: 0; color: var(--muted); font-size: 13px; padding-left: 16px;">
                                <li>Head jest wymagany - to główny przełożony dla tej struktury</li>
                                <li>Manager i Supervisor są opcjonalni</li>
                                <li>Możesz utworzyć strukturę tylko z Head'em (bez Manager/Supervisor)</li>
                                <li>Pracownicy będą automatycznie przypisywani na podstawie tej hierarchii</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-6">
                <a href="{{ route('admin.panel') }}?section=hierarchy" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Anuluj
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Zapisz strukturę
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-filter users by department when department is selected
    document.getElementById('department').addEventListener('change', function() {
        const selectedDept = this.value;
        const userSelects = ['supervisor_username', 'manager_username', 'head_username'];
        
        userSelects.forEach(selectId => {
            const select = document.getElementById(selectId);
            const options = select.querySelectorAll('option[data-department]');
            
            options.forEach(option => {
                const userDept = option.getAttribute('data-department');
                if (!selectedDept || !userDept || userDept === selectedDept) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                    if (option.selected) {
                        option.selected = false;
                    }
                }
            });
        });
    });

    // Trigger the filter on page load if department is pre-selected
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('department').dispatchEvent(new Event('change'));
    });
</script>
@endpush
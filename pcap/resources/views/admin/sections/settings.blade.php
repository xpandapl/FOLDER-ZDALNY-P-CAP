<!-- Section: Ustawienia -->
<div class="section-header">
    <div>
        <h2 class="section-title">Ustawienia Systemu</h2>
        <p class="section-description">Konfiguruj globalne ustawienia aplikacji i parametry systemu</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Konfiguracja Aplikacji</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            
            @if($appSettings->count() > 0)
                @foreach($appSettings as $setting)
                <div class="form-group">
                    <label for="setting_{{ $setting->id }}" class="form-label">
                        {{ $setting->label }}
                    </label>
                    
                    @if($setting->type === 'boolean')
                        <select class="form-control" id="setting_{{ $setting->id }}" name="settings[{{ $setting->key }}]">
                            <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>Nie</option>
                            <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Tak</option>
                        </select>
                    @elseif($setting->type === 'number')
                        <input type="number" 
                               class="form-control" 
                               id="setting_{{ $setting->id }}" 
                               name="settings[{{ $setting->key }}]" 
                               value="{{ $setting->value }}"
                               step="0.01">
                    @elseif($setting->type === 'textarea')
                        <textarea class="form-control" 
                                  id="setting_{{ $setting->id }}" 
                                  name="settings[{{ $setting->key }}]" 
                                  rows="5">{{ $setting->value }}</textarea>
                    @elseif($setting->type === 'email')
                        <input type="email" 
                               class="form-control" 
                               id="setting_{{ $setting->id }}" 
                               name="settings[{{ $setting->key }}]" 
                               value="{{ $setting->value }}">
                    @else
                        <input type="text" 
                               class="form-control" 
                               id="setting_{{ $setting->id }}" 
                               name="settings[{{ $setting->key }}]" 
                               value="{{ $setting->value }}">
                    @endif
                    
                    @if($setting->description)
                        <small class="text-muted mt-2" style="display: block;">
                            {{ $setting->description }}
                        </small>
                    @endif
                </div>
                @endforeach
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Zapisz ustawienia
                    </button>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-cog"></i>
                    <h3>Brak ustawień</h3>
                    <p>Nie znaleziono żadnych ustawień do konfiguracji.</p>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- System info -->
<div class="card mt-6">
    <div class="card-header">
        <h3 class="card-title">Informacje o systemie</h3>
    </div>
    <div class="card-body">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-server"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ PHP_VERSION }}</h3>
                    <p>Wersja PHP</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fab fa-laravel"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ app()->version() }}</h3>
                    <p>Wersja Laravel</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-database"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ \App\Models\Employee::count() }}</h3>
                    <p>Rekordów w bazie</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon danger">
                    <i class="fas fa-hdd"></i>
                </div>
                <div class="stat-content">
                    <h3>
                        @php
                            $freeSpace = 'N/A';
                            try {
                                $bytes = disk_free_space(base_path());
                                if ($bytes !== false) {
                                    $freeSpace = round($bytes / 1024 / 1024 / 1024, 1) . 'GB';
                                }
                            } catch (Exception $e) {
                                // Ignore disk space errors
                            }
                        @endphp
                        {{ $freeSpace }}
                    </h3>
                    <p>Wolne miejsce</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance mode -->
<div class="card mt-6">
    <div class="card-header">
        <h3 class="card-title">Tryb konserwacji</h3>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 style="margin: 0 0 8px 0; color: var(--text);">Status systemu</h4>
                <p style="margin: 0; color: var(--muted);">
                    @if(config('app.maintenance'))
                        <span class="badge badge-danger">Tryb konserwacji WŁĄCZONY</span>
                        <br><small>System jest niedostępny dla użytkowników</small>
                    @else
                        <span class="badge badge-success">System działa normalnie</span>
                        <br><small>Wszyscy użytkownicy mają dostęp do aplikacji</small>
                    @endif
                </p>
            </div>
            <div>
                @if(config('app.maintenance'))
                    <button class="btn btn-success" onclick="toggleMaintenance(false)">
                        <i class="fas fa-play"></i>
                        Wyłącz konserwację
                    </button>
                @else
                    <button class="btn btn-warning" onclick="toggleMaintenance(true)">
                        <i class="fas fa-pause"></i>
                        Włącz konserwację
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function toggleMaintenance(enable) {
        const action = enable ? 'włączyć' : 'wyłączyć';
        if (confirm(`Czy na pewno chcesz ${action} tryb konserwacji?`)) {
            showNotification('Funkcja trybu konserwacji w rozwoju', 'info');
        }
    }
</script>
<!-- Section: Zarządzanie Datami -->
<div class="section-header">
    <div>
        <h2 class="section-title">Zarządzanie Datami</h2>
        <p class="section-description">Konfiguruj daty blokady formularzy i inne ustawienia czasowe</p>
    </div>
</div>

<div class="dates-container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ustawienia dat blokady</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.update_dates') }}">
                @csrf
                
                <div class="form-group">
                    <label for="block_new_submissions_date" class="form-label">
                        <i class="fas fa-user-plus"></i> Data blokady dla nowych formularzy (świeżacy)
                    </label>
                    <input type="datetime-local" 
                           class="form-control date-input" 
                           id="block_new_submissions_date" 
                           name="block_new_submissions_date" 
                           value="{{ $blockDate->block_new_submissions_date ? $blockDate->block_new_submissions_date->format('Y-m-d\TH:i') : '' }}"
                           required>
                    <small class="text-muted">
                        Po tej dacie nowi pracownicy (świeżacy) nie będą mogli wypełnić nowego formularza.
                    </small>
                </div>
                
                <div class="form-group mt-4">
                    <label for="block_edits_date" class="form-label">
                        <i class="fas fa-user-edit"></i> Data blokady dla edycji formularzy (weterani)
                    </label>
                    <input type="datetime-local" 
                           class="form-control date-input" 
                           id="block_edits_date" 
                           name="block_edits_date" 
                           value="{{ $blockDate->block_edits_date ? $blockDate->block_edits_date->format('Y-m-d\TH:i') : '' }}"
                           required>
                    <small class="text-muted">
                        Po tej dacie pracownicy z istniejącymi formularzami (weterani) nie będą mogli edytować swoich odpowiedzi.
                    </small>
                </div>
                
                <div class="current-date-info">
                    <strong>Aktualna data:</strong> {{ now()->format('d.m.Y H:i') }}
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Zapisz zmiany
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.dates-container {
    max-width: 600px;
}

.date-input {
    max-width: 300px;
}

.current-date-info {
    margin: 1rem 0;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 6px;
    border-left: 4px solid var(--primary-color);
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 1.5rem;
}

@media (max-width: 768px) {
    .dates-container {
        max-width: none;
    }
    
    .date-input {
        max-width: none;
    }
}
</style>

<div class="card mt-6">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3">
            <i class="fas fa-info-circle text-primary" style="font-size: 24px;"></i>
            <div>
                <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">Informacje o datach</h4>
                <ul style="margin: 0; color: var(--muted); font-size: 14px; padding-left: 20px;">
                    <li><strong>Nowi pracownicy (świeżacy):</strong> Blokada dotyczy wypełnienia nowego formularza po raz pierwszy</li>
                    <li><strong>Weterani:</strong> Blokada dotyczy edycji już istniejących formularzy z bieżącego cyklu</li>
                    <li>Możesz ustawić różne daty dla każdej grupy (np. nowi do 30 listopada, edycje do 10 grudnia)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
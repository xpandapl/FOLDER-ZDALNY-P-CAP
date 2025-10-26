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
            <h3 class="card-title">Ustawienia dat</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.update_dates') }}">
                @csrf
                <div class="form-group">
                    <label for="block_date" class="form-label">Data blokady formularzy</label>
                    <input type="datetime-local" 
                           class="form-control date-input" 
                           id="block_date" 
                           name="block_date" 
                           value="{{ $blockDate->format('Y-m-d\TH:i') }}"
                           required>
                    <small class="text-muted">
                        Po tej dacie użytkownicy nie będą mogli edytować swoich formularzy samooceny.
                    </small>
                </div>
                
                <div class="current-date-info">
                    <strong>Aktualna data:</strong> {{ now()->format('d.m.Y H:i') }}
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Zapisz zmiany
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
                <p style="margin: 0; color: var(--muted); font-size: 14px;">
                    Data blokady określa moment, po którym użytkownicy nie będą mogli już edytować swoich formularzy samooceny.
                    Upewnij się, że data jest ustawiona z odpowiednim wyprzedzeniem.
                </p>
            </div>
        </div>
    </div>
</div>
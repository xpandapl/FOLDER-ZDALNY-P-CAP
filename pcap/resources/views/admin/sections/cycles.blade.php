<!-- Section: Cykl ocen -->
<div class="section-header">
    <div>
        <h2 class="section-title">Zarządzanie Cyklem Ocen</h2>
        <p class="section-description">Kontroluj aktywne cykle oceniania i zarządzaj ich statusem</p>
    </div>
    <div class="actions">
        <button type="button" class="btn btn-primary" onclick="showNewCycleModal()">
            Rozpocznij nowy cykl
        </button>
    </div>
</div>

@php
    $activeCycle = \App\Models\AssessmentCycle::where('is_active', true)->first();
    $allCycles = \App\Models\AssessmentCycle::orderBy('created_at', 'desc')->get();
@endphp

<!-- Current active cycle -->
@if($activeCycle)
<div class="card mb-6">
    <div class="card-header">
        <h3 class="card-title">Aktywny Cykl</h3>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 style="margin: 0 0 8px 0; color: var(--text);">{{ $activeCycle->label }}</h4>
                <p style="margin: 0; color: var(--muted);">
                    Rozpoczęty: {{ $activeCycle->created_at->format('d.m.Y H:i') }}
                </p>
            </div>
            <div class="d-flex gap-3">
                <form method="POST" action="{{ route('admin.cycles.lock', $activeCycle->id) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning" 
                            onclick="return confirm('Czy na pewno chcesz zablokować aktywny cykl? Ta operacja jest nieodwracalna.')">
                        <i class="fas fa-lock"></i>
                        Zablokuj cykl
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@else
<div class="card mb-6">
    <div class="card-body text-center">
        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 48px; margin-bottom: 16px;"></i>
        <h3>Brak aktywnego cyklu</h3>
        <p class="text-muted mb-4">Nie ma obecnie aktywnego cyklu oceniania. Użytkownicy nie mogą wypełniać formularzy.</p>
        <form method="POST" action="{{ route('admin.cycles.start') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-play"></i>
                Rozpocznij nowy cykl
            </button>
        </form>
    </div>
</div>
@endif

<!-- All cycles history -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Historia Cykli</h3>
        @if(!$activeCycle)
        <form method="POST" action="{{ route('admin.cycles.start') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                Nowy cykl
            </button>
        </form>
        @endif
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table" style="margin: 0;">
                <thead>
                    <tr>
                        <th>Nazwa cyklu</th>
                        <th>Status</th>
                        <th>Data rozpoczęcia</th>
                        <th>Data zakończenia</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allCycles as $cycle)
                    <tr>
                        <td>
                            <strong>{{ $cycle->label }}</strong>
                        </td>
                        <td>
                            @if($cycle->is_active)
                                <span class="badge badge-success">Aktywny</span>
                            @else
                                <span class="badge badge-secondary">Zakończony</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-size: 12px;">
                                {{ $cycle->created_at->format('d.m.Y') }}
                                <div class="text-muted">{{ $cycle->created_at->format('H:i') }}</div>
                            </div>
                        </td>
                        <td>
                            @if($cycle->locked_at)
                                <div style="font-size: 12px;">
                                    {{ $cycle->locked_at->format('d.m.Y') }}
                                    <div class="text-muted">{{ $cycle->locked_at->format('H:i') }}</div>
                                </div>
                            @else
                                <span class="text-muted">W trakcie</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                @if(!$cycle->is_active && !$activeCycle)
                                    <form method="POST" action="{{ route('admin.cycles.activate', $cycle->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-success btn-icon" 
                                                title="Aktywuj cykl"
                                                onclick="return confirm('Czy na pewno chcesz aktywować ten cykl?')">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                @if($cycle->is_active)
                                    <form method="POST" action="{{ route('admin.cycles.lock', $cycle->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-warning btn-icon" 
                                                title="Zablokuj cykl"
                                                onclick="return confirm('Czy na pewno chcesz zablokować ten cykl? Ta operacja jest nieodwracalna.')">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-history"></i>
                                <h3>Brak cykli</h3>
                                <p>Nie ma jeszcze żadnych cykli oceniania w systemie.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Info -->
<div class="card mt-6">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3">
            <i class="fas fa-info-circle text-primary" style="font-size: 24px;"></i>
            <div>
                <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600;">Jak działają cykle?</h4>
                <p style="margin: 0; color: var(--muted); font-size: 14px;">
                    Cykle oceniania pozwalają na zarządzanie okresami, w których użytkownicy mogą wypełniać formularze samooceny.
                    Tylko jeden cykl może być aktywny w danym czasie. Po zablokowaniu cyklu dane są zachowane, ale niemożliwa jest ich edycja.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal dla nowego cyklu -->
<div id="newCycleModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Rozpocznij nowy cykl oceniania</h3>
            <button type="button" class="modal-close" onclick="hideNewCycleModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="newCycleForm" method="POST" action="{{ route('admin.cycles.start') }}">
                @csrf
                <div class="form-group">
                    <label for="cycle_year" class="form-label">Rok</label>
                    <input type="number" id="cycle_year" name="year" class="form-control" 
                           value="{{ date('Y') }}" min="2000" max="2100" required>
                </div>
                <div class="form-group">
                    <label for="cycle_period" class="form-label">Okres (opcjonalnie)</label>
                    <input type="text" id="cycle_period" name="period" class="form-control" 
                           placeholder="np. Q1, H1, Roczna" maxlength="20">
                    <small class="text-muted">Pozostaw puste dla standardowego cyklu rocznego</small>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" name="mark_active" value="1" checked> 
                        Ustaw jako aktywny cykl
                    </label>
                    <small class="text-muted">Automatycznie dezaktywuje poprzedni cykl</small>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="hideNewCycleModal()">Anuluj</button>
            <button type="submit" form="newCycleForm" class="btn btn-primary">Rozpocznij cykl</button>
        </div>
    </div>
</div>

<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
    color: var(--text-primary);
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    color: #333;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e0e0e0;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}
</style>

<script>
function showNewCycleModal() {
    document.getElementById('newCycleModal').style.display = 'flex';
}

function hideNewCycleModal() {
    document.getElementById('newCycleModal').style.display = 'none';
}

// Zamknij modal przy kliknięciu poza nim
document.addEventListener('click', function(e) {
    const modal = document.getElementById('newCycleModal');
    if (e.target === modal) {
        hideNewCycleModal();
    }
});
</script>
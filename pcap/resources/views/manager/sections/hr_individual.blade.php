@if($manager->role == 'supermanager')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">HR - Indywidualne oceny</h2>
        <p class="card-description">Przeglądaj oceny wszystkich pracowników w organizacji</p>
    </div>
    <div class="card-content">
        <!-- Employee Selection -->
        <div class="form-group">
            <label class="form-label" for="hr-employee-select">
                <i class="fas fa-user-circle"></i> Wybierz pracownika
            </label>
            <select id="hr-employee-select" class="form-control select2" onchange="filterByHREmployee()">
                <option value="">-- Wybierz pracownika --</option>
                @foreach($allEmployees as $emp)
                    <option value="{{ $emp->id }}" {{ isset($employee) && $employee->id == $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }} - {{ $emp->department ?? 'Brak działu' }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(isset($employee))
            <!-- Action Buttons -->
            <div class="download-buttons">
                <a href="{{ route('manager.generate_pdf', ['employeeId' => $employee->id]) }}?cycle={{ $selectedCycleId }}" target="_blank" class="btn btn-secondary btn-sm">
                    <i class="fas fa-file-pdf"></i> Pobierz PDF
                </a>
                <a href="{{ route('manager.generate_xls', ['employeeId' => $employee->id]) }}?cycle={{ $selectedCycleId }}" target="_blank" class="btn btn-secondary btn-sm">
                    <i class="fas fa-file-excel"></i> Pobierz XLS
                </a>
            </div>

            <!-- Employee Details with Extended Info -->
            <div class="employee-details">
                <h3 style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-id-card"></i> Informacje o pracowniku
                </h3>
                <div class="employee-details-grid">
                    <div class="employee-detail-item">
                        <i class="fas fa-user"></i>
                        <strong>Imię i nazwisko:</strong> {{ $employee->name }}
                    </div>
                    <div class="employee-detail-item">
                        <i class="fas fa-calendar-alt"></i>
                        <strong>Data przesłania:</strong> {{ $employee->created_at->format('Y-m-d H:i') }}
                    </div>
                    <div class="employee-detail-item">
                        <i class="fas fa-building"></i>
                        <strong>Dział:</strong> {{ $employee->department }}
                    </div>
                    <div class="employee-detail-item">
                        <i class="fas fa-sync-alt"></i>
                        <strong>Data aktualizacji:</strong> {{ $employee->updated_at->format('Y-m-d H:i') }}
                    </div>
                    <div class="employee-detail-item">
                        <i class="fas fa-user-tie"></i>
                        <strong>Przełożony:</strong> {{ $employee->manager_username }}
                    </div>
                    <div class="employee-detail-item">
                        <i class="fas fa-clipboard"></i>
                        <strong>Stanowisko:</strong> {{ $employee->job_title }}
                    </div>
                    <!-- Additional HR info -->
                    <div class="employee-detail-item">
                        <i class="fas fa-sitemap"></i>
                        <strong>Struktura:</strong> 
                        @php
                            $structure = \App\Models\HierarchyStructure::where('department', $employee->department)
                                ->where(function($query) use ($employee) {
                                    if ($employee->supervisor_username) {
                                        $query->where('supervisor_username', $employee->supervisor_username);
                                    } else if ($employee->manager_username) {
                                        $query->where('manager_username', $employee->manager_username)
                                              ->whereNull('supervisor_username');
                                    } else if ($employee->head_username) {
                                        $query->where('head_username', $employee->head_username)
                                              ->whereNull('supervisor_username')
                                              ->whereNull('manager_username');
                                    }
                                })
                                ->first();
                        @endphp
                        {{ $structure->team_name ?? 'Brak struktury' }}
                    </div>
                    <div class="employee-detail-item">
                        <i class="fas fa-clock"></i>
                        <strong>Status oceny:</strong> 
                        @if($results->where('self_assessment', '!=', null)->count() > 0)
                            <span style="color: var(--accent); font-weight: 600;">Wypełniona</span>
                        @else
                            <span style="color: var(--danger); font-weight: 600;">Niewypełniona</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Assessment Form (Read-only for HR) -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Ocena kompetencji (widok HR)</h3>
                    <p class="card-description">Przegląd ocen pracownika - możliwość edycji feedbacku</p>
                </div>
                <div class="card-content">
                    <form action="{{ route('manager.panel.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="width: 25%;">Kompetencja</th>
                                        <th style="width: 12%;">Samoocena</th>
                                        <th style="width: 12%;">Ocena managera</th>
                                        <th style="width: 12%;">Wartość zespołu</th>
                                        <th style="width: 14%;">Komentarz managera</th>
                                        <th style="width: 25%;">Komentarz HR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $currentLevel = null;
                                    @endphp
                                    @foreach($results->sortBy('competency.level') as $result)
                                        @php
                                            $level = $result->competency->level ?? 'Brak poziomu';
                                            $competency = $result->competency;
                                            
                                            // Determine competency category for badge
                                            $category = 'zawodowe-inne';
                                            if (str_contains(strtolower($competency->category), 'osobiste')) {
                                                $category = 'osobiste';
                                            } elseif (str_contains(strtolower($competency->category), 'społeczne')) {
                                                $category = 'spoleczne';
                                            } elseif (str_contains(strtolower($competency->category), 'liderskie')) {
                                                $category = 'liderskie';
                                            } elseif (str_contains(strtolower($competency->category), 'logistics')) {
                                                $category = 'zawodowe-logistics';
                                            } elseif (str_contains(strtolower($competency->category), 'growth')) {
                                                $category = 'zawodowe-growth';
                                            }
                                        @endphp
                                        
                                        @if($currentLevel !== $level)
                                            @if($currentLevel !== null)
                                                <tr style="background: var(--bg); font-weight: 600;">
                                                    <td colspan="6" style="padding: 12px 16px; border-top: 2px solid var(--border);">
                                                        <i class="fas fa-chart-bar"></i> Koniec poziomu: {{ $currentLevel }}
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr style="background: var(--primary); color: white; font-weight: 600;">
                                                <td colspan="6" style="padding: 12px 16px;">
                                                    <i class="fas fa-layer-group"></i> {{ $level }}
                                                </td>
                                            </tr>
                                            @php $currentLevel = $level; @endphp
                                        @endif
                                        
                                        <tr>
                                            <td>
                                                <div style="margin-bottom: 8px;">
                                                    <strong>{{ $competency->name }}</strong>
                                                    <button type="button" class="btn btn-sm" onclick="showDefinitionModal({{ $competency->id }})" 
                                                            style="padding: 2px 6px; margin-left: 8px; font-size: 11px;">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                </div>
                                                <div class="competency-badges">
                                                    <span class="badge level">{{ $level }}</span>
                                                    <span class="badge {{ $category }}">{{ $competency->category }}</span>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <div style="font-weight: 600; font-size: 16px;">
                                                    {{ $result->self_assessment ?? '—' }}
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                <div style="font-weight: 600; font-size: 16px; color: {{ $result->manager_assessment ? 'var(--primary)' : 'var(--muted)' }};">
                                                    {{ $result->manager_assessment ?? '—' }}
                                                </div>
                                            </td>
                                            <td style="text-align: center;">
                                                @php
                                                    $teamValue = $result->competency_team_value->value ?? '—';
                                                    $overriddenValue = $result->overridden_competency_value;
                                                    $displayValue = $overriddenValue !== null ? $overriddenValue : $teamValue;
                                                @endphp
                                                <div style="font-weight: 600; color: {{ $overriddenValue !== null ? 'var(--primary)' : 'var(--text)' }};">
                                                    {{ $displayValue }}
                                                    @if($overriddenValue !== null)
                                                        <i class="fas fa-edit" style="font-size: 12px; margin-left: 4px;" title="Wartość została nadpisana przez managera"></i>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-size: 13px; max-width: 200px; line-height: 1.4;">
                                                    {{ $result->manager_feedback ?: '—' }}
                                                </div>
                                            </td>
                                            <td>
                                                <textarea name="hr_feedback[{{ $result->id }}]" class="feedback-textarea" 
                                                          placeholder="Komentarz HR..." rows="2" style="font-size: 13px;">{{ $result->hr_feedback ?? '' }}</textarea>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Form Actions -->
                        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border); display: flex; gap: 16px; align-items: center;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Zapisz komentarze HR
                            </button>
                            
                            <div class="alert alert-info" style="margin: 0; flex: 1; padding: 12px 16px;">
                                <i class="fas fa-info-circle"></i>
                                Jako HR możesz dodawać własne komentarze, ale nie edytować ocen
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Employee History (if multiple cycles exist) -->
            @if(count($cycles) > 1)
                <div class="card" style="margin-top: 24px;">
                    <div class="card-header">
                        <h3 class="card-title">Historia ocen pracownika</h3>
                        <p class="card-description">Porównanie wyników z poprzednich cykli</p>
                    </div>
                    <div class="card-content">
                        <div style="display: flex; gap: 16px; margin-bottom: 20px;">
                            <select id="history-cycle" class="form-control" style="width: 250px;">
                                <option value="">-- Wybierz cykl do porównania --</option>
                                @foreach($cycles as $cycle)
                                    @if($cycle->id != $selectedCycleId)
                                        <option value="{{ $cycle->id }}">{{ $cycle->label }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <button class="btn btn-secondary" onclick="loadEmployeeHistory()">
                                <i class="fas fa-history"></i> Pokaż historię
                            </button>
                        </div>
                        <div id="employee-history-content">
                            <div style="text-align: center; color: var(--muted); padding: 40px;">
                                Wybierz cykl aby zobaczyć historię ocen
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- No Employee Selected -->
            <div class="no-data">
                <i class="fas fa-user-circle"></i>
                <h3>Wybierz pracownika</h3>
                <p>Aby zobaczyć indywidualne oceny, wybierz pracownika z listy rozwijanej powyżej.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function loadEmployeeHistory() {
        const historyCycleId = $('#history-cycle').val();
        const currentEmployeeId = {{ isset($employee) ? $employee->id : 'null' }};
        
        if (!historyCycleId || !currentEmployeeId) {
            showToast('Wybierz cykl do porównania', 'warning');
            return;
        }
        
        $('#employee-history-content').html('<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Ładowanie historii...</div>');
        
        fetch(`/manager/employee-history?employee=${currentEmployeeId}&cycle=${historyCycleId}&current={{ $selectedCycleId }}`)
            .then(response => response.json())
            .then(data => {
                displayEmployeeHistory(data);
            })
            .catch(error => {
                $('#employee-history-content').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Nie udało się załadować historii</div>');
                console.error('Error:', error);
            });
    }
    
    function displayEmployeeHistory(data) {
        if (!data.success) {
            $('#employee-history-content').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Brak danych dla wybranego cyklu</div>');
            return;
        }
        
        const html = `
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="cycle-data">
                    <div class="cycle-label">Obecny cykl (${data.current.cycle_name})</div>
                    <div style="margin-top: 12px;">
                        <div><strong>Średnia samoocena:</strong> ${data.current.self_avg || '—'}</div>
                        <div><strong>Średnia ocena managera:</strong> ${data.current.manager_avg || '—'}</div>
                        <div><strong>Wypełnione kompetencje:</strong> ${data.current.completed || 0}</div>
                    </div>
                </div>
                <div class="cycle-data">
                    <div class="cycle-label">Porównywany cykl (${data.previous.cycle_name})</div>
                    <div style="margin-top: 12px;">
                        <div><strong>Średnia samoocena:</strong> ${data.previous.self_avg || '—'}</div>
                        <div><strong>Średnia ocena managera:</strong> ${data.previous.manager_avg || '—'}</div>
                        <div><strong>Wypełnione kompetencje:</strong> ${data.previous.completed || 0}</div>
                    </div>
                </div>
            </div>
            <div style="margin-top: 20px; padding: 16px; background: var(--bg); border-radius: 8px;">
                <h4><i class="fas fa-chart-line"></i> Zmiana</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-top: 12px;">
                    <div>Samoocena: <strong style="color: ${data.change.self_change >= 0 ? 'var(--accent)' : 'var(--danger)'};">${data.change.self_change > 0 ? '+' : ''}${data.change.self_change || 0}</strong></div>
                    <div>Ocena managera: <strong style="color: ${data.change.manager_change >= 0 ? 'var(--accent)' : 'var(--danger)'};">${data.change.manager_change > 0 ? '+' : ''}${data.change.manager_change || 0}</strong></div>
                </div>
            </div>
        `;
        
        $('#employee-history-content').html(html);
    }
</script>
@endpush

@else
    <div class="no-data">
        <i class="fas fa-ban"></i>
        <h3>Brak dostępu</h3>
        <p>Ta sekcja jest dostępna tylko dla supermanagerów.</p>
    </div>
@endif
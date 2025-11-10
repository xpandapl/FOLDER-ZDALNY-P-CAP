<div class="card">
    <div class="card-header">
        <h2 class="card-title">Indywidualne oceny pracowników</h2>
        <p class="card-description">Zarządzaj oceną kompetencji indywidualnych pracowników z Twojego zespołu</p>
    </div>
    <div class="card-content">
        <!-- Employee Selection -->
        <div class="form-group">
            <label class="form-label" for="employee-select">
                <i class="fas fa-user"></i> Wybierz pracownika
            </label>
            <select id="employee-select" class="form-control select2" onchange="filterByEmployee()">
                <option value="">-- Wybierz pracownika --</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ isset($employee) && $employee->id == $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }}
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

            <!-- Employee Details -->
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
                        <strong>Przełożony:</strong> 
                        @if($employee->supervisor)
                            {{ $employee->supervisor->name ?? $employee->supervisor->username }}
                        @elseif($employee->manager && $employee->manager->id !== auth()->id())
                            {{ $employee->manager->name ?? $employee->manager->username }}
                        @elseif($employee->head && $employee->head->id !== auth()->id())
                            {{ $employee->head->name ?? $employee->head->username }} (Head)
                        @else
                            Brak przypisanego przełożonego
                        @endif
                    </div>
                    <div class="employee-detail-item">
                        <i class="fas fa-clipboard"></i>
                        <strong>Stanowisko:</strong> {{ $employee->job_title }}
                    </div>
                </div>
            </div>

            <!-- Cycle Comparison Feature -->
            @if(count($cycles) > 1)
                <div class="cycle-comparison">
                    <div class="cycle-comparison-header">
                        <i class="fas fa-chart-line"></i>
                        <h4>Porównanie z poprzednimi cyklami</h4>
                        <select id="comparison-cycle" class="form-control" style="width: 200px; margin-left: auto;">
                            <option value="">-- Wybierz cykl do porównania --</option>
                            @foreach($cycles as $cycle)
                                @if($cycle->id != $selectedCycleId)
                                    <option value="{{ $cycle->id }}">{{ $cycle->label }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div id="comparison-content" style="display: none;">
                        <!-- Comparison data will be loaded here -->
                    </div>
                </div>
            @endif

            <!-- New Code Generation Alert -->
            @if(session('generated_code') && session('generated_code_employee_id') == $employee->id)
                <div class="alert alert-success">
                    <i class="fas fa-key"></i>
                    <div>
                        <strong>Nowy kod dostępu wygenerowany:</strong>
                        <code style="font-family: monospace; background: rgba(0,0,0,0.1); padding: 2px 6px; border-radius: 4px; margin: 0 8px;">{{ session('generated_code') }}</code>
                        <button class="btn btn-sm btn-secondary" onclick="copyText('{{ session('generated_code') }}')">
                            <i class="fas fa-copy"></i> Kopiuj
                        </button>
                        <br><small style="color: #666; margin-top: 4px; display: block;">Zapisz teraz – nie będzie już widoczny w całości</small>
                    </div>
                </div>
            @endif

            <!-- Assessment Form -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Ocena kompetencji</h3>
                    <p class="card-description">Oceń poziom kompetencji pracownika w różnych obszarach</p>
                </div>
                
                <!-- Level Summaries - Top -->
                @if(isset($levelSummaries) && !empty($levelSummaries))
                    <div style="margin: 20px 24px;">
                        <h4 style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px; font-size: 16px;">
                            <i class="fas fa-chart-pie"></i> Podsumowanie poziomów
                        </h4>
                        <div class="stats-grid">
                            @foreach($levelSummaries as $level => $summary)
                                @php
                                    $levelNumber = (int)substr($level, 0, 1);
                                    $iconClass = 'level-' . $levelNumber;
                                    if ($levelNumber == 1) $icon = 'fa-user-graduate';
                                    elseif ($levelNumber == 2) $icon = 'fa-user';
                                    elseif ($levelNumber == 3) $icon = 'fa-user-tie';
                                    elseif ($levelNumber == 4) $icon = 'fa-chalkboard-teacher';
                                    elseif ($levelNumber == 5) $icon = 'fa-user-cog';
                                    else $icon = 'fa-briefcase';
                                @endphp
                                <div class="stat-card">
                                    <div class="stat-icon {{ $iconClass }}">
                                        <i class="fas {{ $icon }}"></i>
                                    </div>
                                    <div class="stat-value">
                                        @if(isset($summary['percentageManager']) && is_numeric($summary['percentageManager']))
                                            {{ number_format($summary['percentageManager'], 1) }}%
                                        @elseif(isset($summary['percentageManager']))
                                            {{ $summary['percentageManager'] }}
                                        @else
                                            N/D
                                        @endif
                                    </div>
                                    <div class="stat-label">{{ $level }}</div>
                                    <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                                        {{ $summary['count'] ?? 0 }} kompetencji
                                    </div>
                                    <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">
                                        {{ $summary['earnedPointsManager'] ?? 0 }} / {{ $summary['maxPoints'] ?? 0 }} pkt.
                                    </div>
                                    <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">
                                        @if(isset($summary['percentageEmployee']) && is_numeric($summary['percentageEmployee']))
                                            Samoocena: {{ number_format($summary['percentageEmployee'], 1) }}%
                                        @elseif(isset($summary['percentageEmployee']))
                                            Samoocena: {{ $summary['percentageEmployee'] }}
                                        @else
                                            Samoocena: N/D
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <div class="card-content">
                    <form action="{{ route('manager.panel.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="width: 20%;">Kompetencja</th>
                                        <th style="width: 10%;">Ocena użytkownika</th>
                                        <th style="width: 10%;">Powyżej oczekiwań</th>
                                        <th style="width: 20%;">Argumentacja użytkownika</th>
                                        <th style="width: 10%;">Wartość zespołu</th>
                                        <th style="width: 10%;">Ocena managera</th>
                                        <th style="width: 20%;">Feedback od managera</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $currentLevel = null;
                                        $levelCounts = [];
                                        $levelTotals = [];
                                    @endphp
                                    @foreach($results->sortBy('competency.level') as $result)
                                        @php
                                            $level = $result->competency->level ?? 'Brak poziomu';
                                            $competency = $result->competency;
                                            
                                            if (!isset($levelCounts[$level])) {
                                                $levelCounts[$level] = 0;
                                                $levelTotals[$level] = 0;
                                            }
                                            
                                            if ($result->score_manager) {
                                                $levelCounts[$level]++;
                                                $levelTotals[$level] += $result->score_manager;
                                            }
                                            
                                            // Determine competency category for badge
                                            $category = 'zawodowe-inne';
                                            if (str_contains(strtolower($competency->competency_type), 'osobiste')) {
                                                $category = 'osobiste';
                                            } elseif (str_contains(strtolower($competency->competency_type), 'społeczne')) {
                                                $category = 'spoleczne';
                                            } elseif (str_contains(strtolower($competency->competency_type), 'liderskie')) {
                                                $category = 'liderskie';
                                            } elseif (str_contains(strtolower($competency->competency_type), 'logistics')) {
                                                $category = 'zawodowe-logistics';
                                            } elseif (str_contains(strtolower($competency->competency_type), 'growth')) {
                                                $category = 'zawodowe-growth';
                                            }
                                        @endphp
                                        
                                        @if($currentLevel !== $level)
                                            @if($currentLevel !== null)
                                                <tr style="background: var(--bg); font-weight: 600;">
                                                    <td colspan="7" style="padding: 12px 16px; border-top: 2px solid var(--border);">
                                                        <i class="fas fa-chart-bar"></i> Koniec poziomu: {{ $currentLevel }}
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr style="background: var(--primary-600) !important; color: white !important; font-weight: 600;">
                                                <td colspan="7" style="padding: 12px 16px; color: white !important; background: var(--primary-600) !important;">
                                                    <i class="fas fa-layer-group"></i> {{ $level }}
                                                </td>
                                            </tr>
                                            @php $currentLevel = $level; @endphp
                                        @endif
                                        
                                        <tr>
                                            <td>
                                                <div style="margin-bottom: 8px;">
                                                    <strong>{{ $competency->competency_name }}</strong>
                                                    <button type="button" class="btn btn-sm" onclick="showDefinitionModal({{ $competency->id }})" 
                                                            style="padding: 2px 6px; margin-left: 8px; font-size: 11px;">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                </div>
                                                <div class="competency-badges">
                                                    <span class="badge level">{{ $level }}</span>
                                                    <span class="badge {{ $category }}">{{ $competency->competency_type }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="text-align: center; font-weight: 600; font-size: 16px;">
                                                    {{ $result->score > 0 ? $result->score : 'N/D' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="text-align: center;">
                                                    {{ $result->above_expectations ? 'Tak' : 'Nie' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-size: 14px; line-height: 1.4;">
                                                    {{ $result->comments ?? '—' }}
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $competencyValue = $employee->getCompetencyValue($result->competency_id) ?? 0;
                                                    $overriddenValue = isset($overriddenValues[$result->competency_id]) ? $overriddenValues[$result->competency_id] : null;
                                                    $displayValue = $overriddenValue !== null ? $overriddenValue : $competencyValue;
                                                    $canEditWeight = isset($manager) && $manager->role === 'head';
                                                @endphp
                                                <div class="competency-value-container" data-competency-id="{{ $competency->id }}" data-team-value="{{ $competencyValue }}">
                                                    <span class="competency-value-display {{ $overriddenValue !== null ? 'competency-value-overridden' : '' }}">
                                                        {{ $displayValue }}
                                                    </span>
                                                    @if($canEditWeight)
                                                        <input type="number" step="0.01" min="0" max="5" value="{{ $displayValue }}" 
                                                               style="display: none; width: 80px;"
                                                               {{ $overriddenValue !== null ? 'name=competency_values[' . $competency->id . ']' : '' }}>
                                                        <div class="icon-wrapper">
                                                            <i class="fas {{ $overriddenValue !== null ? 'fa-trash remove-overridden-value-button' : 'fa-pencil-alt edit-competency-value-button' }}" 
                                                               data-competency-id="{{ $competency->id }}"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <select name="score_manager[{{ $result->id }}]" class="form-control" 
                                                        {{ (!$isSelectedCycleActive) ? 'disabled' : '' }}>
                                                    <option value="" {{ is_null($result->score_manager) ? 'selected' : '' }}>Ok</option>
                                                    <option value="0" {{ $result->score_manager === 0.0 && !$result->above_expectations_manager ? 'selected' : '' }}>N/D</option>
                                                    <option value="0.25" {{ $result->score_manager === 0.25 ? 'selected' : '' }}>0.25</option>
                                                    <option value="0.5" {{ $result->score_manager === 0.5 ? 'selected' : '' }}>0.5</option>
                                                    <option value="0.75" {{ $result->score_manager === 0.75 ? 'selected' : '' }}>0.75</option>
                                                    <option value="1" {{ $result->score_manager === 1.0 && !$result->above_expectations_manager ? 'selected' : '' }}>1</option>
                                                </select>
                                            </td>
                                            <td>
                                                <textarea name="feedback_manager[{{ $result->id }}]" class="feedback-textarea" 
                                                          placeholder="Wpisz komentarz..." rows="2"
                                                          {{ (!$isSelectedCycleActive) ? 'disabled' : '' }}>{{ $result->feedback_manager }}</textarea>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Level Summaries -->
                        @if(isset($levelSummaries))
                            @if(!empty($levelSummaries))
                                <div style="margin-top: 32px;">
                                    <h4 style="margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-chart-pie"></i> Podsumowanie poziomów
                                    </h4>
                                    <div class="stats-grid">
                                        @foreach($levelSummaries as $level => $summary)
                                            @php
                                                $levelNumber = (int)substr($level, 0, 1);
                                                $iconClass = 'level-' . $levelNumber;
                                                if ($levelNumber == 1) $icon = 'fa-user-graduate';
                                                elseif ($levelNumber == 2) $icon = 'fa-user';
                                                elseif ($levelNumber == 3) $icon = 'fa-user-tie';
                                                elseif ($levelNumber == 4) $icon = 'fa-chalkboard-teacher';
                                                elseif ($levelNumber == 5) $icon = 'fa-user-cog';
                                                else $icon = 'fa-briefcase';
                                            @endphp
                                            <div class="stat-card">
                                                <div class="stat-icon {{ $iconClass }}">
                                                    <i class="fas {{ $icon }}"></i>
                                                </div>
                                                <div class="stat-value">
                                                    @if(isset($summary['percentageManager']) && is_numeric($summary['percentageManager']))
                                                        {{ number_format($summary['percentageManager'], 1) }}%
                                                    @elseif(isset($summary['percentageManager']))
                                                        {{ $summary['percentageManager'] }}
                                                    @else
                                                        N/D
                                                    @endif
                                                </div>
                                                <div class="stat-label">{{ $level }}</div>
                                                <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                                                    {{ $summary['count'] ?? 0 }} kompetencji
                                                </div>
                                                <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">
                                                    {{ $summary['earnedPointsManager'] ?? 0 }} / {{ $summary['maxPoints'] ?? 0 }} pkt.
                                                </div>
                                                <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">
                                                    @if(isset($summary['percentageEmployee']) && is_numeric($summary['percentageEmployee']))
                                                        Samoocena: {{ number_format($summary['percentageEmployee'], 1) }}%
                                                    @elseif(isset($summary['percentageEmployee']))
                                                        Samoocena: {{ $summary['percentageEmployee'] }}
                                                    @else
                                                        Samoocena: N/D
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div style="margin-top: 32px; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: center; color: #666;">
                                    <i class="fas fa-info-circle"></i> Brak danych do podsumowania poziomów (levelSummaries jest puste)
                                </div>
                            @endif
                        @else
                            <div style="margin-top: 32px; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: center; color: #666;">
                                <i class="fas fa-exclamation-triangle"></i> Zmienna levelSummaries nie została przekazana do widoku
                            </div>
                        @endif

                        <!-- Form Actions -->
                        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border); display: flex; gap: 16px; align-items: center;">
                            <button type="submit" class="btn btn-primary" 
                                    {{ (!$isSelectedCycleActive) ? 'disabled' : '' }}>
                                <i class="fas fa-save"></i>
                                Zapisz zmiany
                            </button>
                            
                            @if($isSelectedCycleActive)
                                <form action="{{ route('manager.generate_access_code', ['employeeId' => $employee->id]) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary" title="Wygeneruj kod dla aktywnego cyklu">
                                        <i class="fas fa-key"></i>
                                        Wygeneruj kod dostępu
                                    </button>
                                </form>
                            @endif
                            
                            @if(!$isSelectedCycleActive)
                                <div class="alert alert-warning" style="margin: 0; flex: 1;">
                                    <i class="fas fa-lock"></i>
                                    Edycja zablokowana dla cyklu historycznego
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        @else
            <!-- No Employee Selected -->
            <div class="no-data">
                <i class="fas fa-user-plus"></i>
                <h3>Wybierz pracownika</h3>
                <p>Aby zobaczyć indywidualne oceny, wybierz pracownika z listy rozwijanej powyżej.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Cycle comparison functionality
        $('#comparison-cycle').on('change', function() {
            const comparisonCycleId = $(this).val();
            const currentEmployeeId = {{ isset($employee) ? $employee->id : 'null' }};
            
            if (comparisonCycleId && currentEmployeeId) {
                loadCycleComparison(currentEmployeeId, comparisonCycleId);
            } else {
                $('#comparison-content').hide();
            }
        });
    });
    
    function loadCycleComparison(employeeId, comparisonCycleId) {
        $('#comparison-content').html('<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Ładowanie porównania...</div>').show();
        
        // This would make an AJAX call to get comparison data
        fetch(`/manager/cycle-comparison?employee=${employeeId}&cycle=${comparisonCycleId}&current={{ $selectedCycleId }}`)
            .then(response => response.json())
            .then(data => {
                displayCycleComparison(data);
            })
            .catch(error => {
                $('#comparison-content').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Nie udało się załadować danych porównania</div>');
            });
    }
    
    function displayCycleComparison(data) {
        // This would render the comparison data
        const html = `
            <div class="cycle-comparison-content">
                <div class="cycle-data">
                    <div class="cycle-label">Obecny cykl</div>
                    <div class="cycle-value">Kompetencje wypełnione: ${data.current.completed || 0}</div>
                </div>
                <div class="cycle-data">
                    <div class="cycle-label">Poprzedni cykl</div>
                    <div class="cycle-value">Kompetencje wypełnione: ${data.previous.completed || 0}</div>
                </div>
                <div class="cycle-data">
                    <div class="cycle-label">Różnica</div>
                    <div class="cycle-value">Postęp: ${data.difference > 0 ? '+' : ''}${data.difference || 0}</div>
                </div>
            </div>
        `;
        $('#comparison-content').html(html);
    }
</script>
@endpush
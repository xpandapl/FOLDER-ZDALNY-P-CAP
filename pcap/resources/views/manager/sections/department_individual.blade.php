@if($manager->role == 'head')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Dział - Indywidualne oceny</h2>
        <p class="card-description">Zarządzaj oceną kompetencji pracowników w Twoim dziale</p>
    </div>
    <div class="card-content">
        <!-- Employee Selection -->
        <div class="form-group">
            <label class="form-label" for="department-employee-select">
                <i class="fas fa-user-circle"></i> Wybierz pracownika z działu
            </label>
            <select id="department-employee-select" class="form-control select2" onchange="filterByDepartmentEmployee()">
                <option value="">-- Wybierz pracownika --</option>
                @foreach($departmentEmployees as $emp)
                    <option value="{{ $emp->id }}" {{ isset($employee) && $employee->id == $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }} - {{ $emp->job_title ?? 'Brak stanowiska' }}
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

            <!-- Employee Details with Department Context -->
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
                    <!-- Additional department context -->
                    <div class="employee-detail-item">
                        <i class="fas fa-sitemap"></i>
                        <strong>Zespół:</strong> 
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

            <!-- Assessment Form with Department Head Capabilities -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Ocena kompetencji (widok kierownika działu)</h3>
                    <p class="card-description">Edytuj oceny i dodawaj komentarze jako kierownik działu</p>
                </div>
                <div class="card-content">
                    <form action="{{ route('manager.panel.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="width: 22%;">Kompetencja</th>
                                        <th style="width: 10%;">Samoocena</th>
                                        <th style="width: 12%;">Ocena managera</th>
                                        <th style="width: 12%;">Ocena kierownika</th>
                                        <th style="width: 12%;">Wartość zespołu</th>
                                        <th style="width: 32%;">Komentarz kierownika</th>
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
                                            <td>
                                                <select name="head_assessment[{{ $result->id }}]" class="form-control" 
                                                        {{ (!$isSelectedCycleActive) ? 'disabled' : '' }}>
                                                    <option value="">—</option>
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <option value="{{ $i }}" {{ $result->head_assessment == $i ? 'selected' : '' }}>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
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
                                                        <i class="fas fa-edit" style="font-size: 12px; margin-left: 4px;" title="Wartość została nadpisana"></i>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <textarea name="head_feedback[{{ $result->id }}]" class="feedback-textarea" 
                                                          placeholder="Komentarz kierownika działu..." rows="2"
                                                          {{ (!$isSelectedCycleActive) ? 'disabled' : '' }}>{{ $result->head_feedback ?? '' }}</textarea>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Form Actions -->
                        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border); display: flex; gap: 16px; align-items: center;">
                            <button type="submit" class="btn btn-primary" 
                                    {{ (!$isSelectedCycleActive) ? 'disabled' : '' }}>
                                <i class="fas fa-save"></i>
                                Zapisz oceny kierownika
                            </button>
                            
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

            <!-- Manager vs Head Assessment Comparison -->
            @php
                $managerAssessments = $results->pluck('manager_assessment', 'competency.name')->filter();
                $headAssessments = $results->pluck('head_assessment', 'competency.name')->filter();
                $differences = [];
                
                foreach($managerAssessments as $competencyName => $managerScore) {
                    $headScore = $headAssessments[$competencyName] ?? null;
                    if ($headScore !== null) {
                        $diff = $headScore - $managerScore;
                        if (abs($diff) >= 1) { // Show only significant differences
                            $differences[$competencyName] = [
                                'manager' => $managerScore,
                                'head' => $headScore,
                                'difference' => $diff
                            ];
                        }
                    }
                }
            @endphp
            
            @if(!empty($differences))
                <div class="card" style="margin-top: 24px;">
                    <div class="card-header">
                        <h3 class="card-title">Różnice w ocenach</h3>
                        <p class="card-description">Porównanie ocen managera i kierownika działu</p>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Kompetencja</th>
                                        <th style="text-align: center;">Ocena managera</th>
                                        <th style="text-align: center;">Ocena kierownika</th>
                                        <th style="text-align: center;">Różnica</th>
                                        <th style="text-align: center;">Interpretacja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($differences as $competencyName => $data)
                                        <tr>
                                            <td><strong>{{ $competencyName }}</strong></td>
                                            <td style="text-align: center; font-weight: 600;">{{ $data['manager'] }}</td>
                                            <td style="text-align: center; font-weight: 600;">{{ $data['head'] }}</td>
                                            <td style="text-align: center;">
                                                <span style="color: {{ $data['difference'] > 0 ? 'var(--accent)' : 'var(--danger)' }}; font-weight: 600;">
                                                    {{ $data['difference'] > 0 ? '+' : '' }}{{ $data['difference'] }}
                                                </span>
                                            </td>
                                            <td style="text-align: center;">
                                                @if($data['difference'] > 0)
                                                    <span class="badge" style="background: #dcfce7; color: #166534;">Kierownik wyżej</span>
                                                @else
                                                    <span class="badge" style="background: #fee2e2; color: #991b1b;">Manager wyżej</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- No Employee Selected -->
            <div class="no-data">
                <i class="fas fa-user-circle"></i>
                <h3>Wybierz pracownika z działu</h3>
                <p>Aby zobaczyć indywidualne oceny, wybierz pracownika z Twojego działu z listy rozwijanej powyżej.</p>
            </div>
        @endif
    </div>
</div>

@else
    <div class="no-data">
        <i class="fas fa-ban"></i>
        <h3>Brak dostępu</h3>
        <p>Ta sekcja jest dostępna tylko dla kierowników działów (head).</p>
    </div>
@endif
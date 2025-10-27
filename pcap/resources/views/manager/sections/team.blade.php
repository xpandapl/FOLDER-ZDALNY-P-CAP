<div class="card">
    <div class="card-header">
        <h2 class="card-title">Podsumowanie zespołu</h2>
        <p class="card-description">Przegląd kompetencji wszystkich pracowników w Twoim zespole</p>
    </div>
    <div class="card-content">
        @if($employees->isEmpty())
            <!-- No Team Members -->
            <div class="no-data">
                <i class="fas fa-users"></i>
                <h3>Brak przypisanych pracowników</h3>
                <p>
                    @if($manager->role === 'supermanager')
                        Aby widzieć pracowników w zakładce "Twój zespół", musisz być przypisany do struktury hierarchii jako supervisor, manager lub head.
                        <br><br>
                        <strong>Wszyscy pracownicy są dostępni w zakładkach HR.</strong>
                    @else
                        Nie masz jeszcze przypisanych pracowników w systemie hierarchii.
                    @endif
                </p>
                @if($manager->role === 'supermanager')
                    <div style="margin-top: 20px;">
                        <a href="/admin" class="btn btn-primary">
                            <i class="fas fa-sitemap"></i> Przejdź do panelu administracyjnego
                        </a>
                    </div>
                @endif
            </div>
        @else
            <!-- Team Overview Stats -->
            @php
                $totalEmployees = count($teamEmployeesData ?? []);
                $filledSurveyCount = 0;
                $levelCounts = [];
                
                // Initialize level counts
                foreach($levelNames as $levelName) {
                    $levelCounts[$levelName] = 0;
                }
                
                // Count filled surveys and levels
                foreach($teamEmployeesData as $emp) {
                    if (!empty($emp['levelPercentagesManager'])) {
                        $filledSurveyCount++;
                    }
                    
                    $highestLevel = $emp['highestLevelManager'];
                    // Map 3/4. Senior/Supervisor to 4. Supervisor for summaries
                    if ($highestLevel === "3/4. Senior/Supervisor") {
                        $highestLevel = "4. Supervisor";
                    }
                    
                    if (isset($highestLevel) && isset($levelCounts[$highestLevel])) {
                        $levelCounts[$highestLevel]++;
                    }
                }
            @endphp
            
            <!-- Summary Statistics -->
            <div class="stats-grid">
                @foreach($levelNames as $levelName)
                    @php
                        $count = $levelCounts[$levelName] ?? 0;
                        $levelNumber = (int)substr($levelName, 0, 1);
                        $iconClass = 'level-' . $levelNumber;
                        
                        if (str_contains($levelName, 'Junior')) {
                            $icon = 'fa-user-graduate';
                            $iconClass = 'level-1';
                        } elseif (str_contains($levelName, 'Specjalista')) {
                            $icon = 'fa-user';
                            $iconClass = 'level-2';
                        } elseif (str_contains($levelName, 'Senior/Supervisor') || str_contains($levelName, 'Senior')) {
                            $icon = 'fa-user-tie';
                            $iconClass = 'level-3';
                        } elseif (str_contains($levelName, 'Supervisor')) {
                            $icon = 'fa-chalkboard-teacher';
                            $iconClass = 'level-4';
                        } elseif (str_contains($levelName, 'Manager')) {
                            $icon = 'fa-user-cog';
                            $iconClass = 'level-5';
                        } else {
                            $icon = 'fa-briefcase';
                            $iconClass = 'level-1';
                        }
                        
                        if (!function_exists('pluralForm')) {
                            function pluralForm($number, $forms) {
                                $number = abs($number);
                                if ($number == 1) {
                                    return $forms[0];
                                } elseif ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 10 || $number % 100 >= 20)) {
                                    return $forms[1];
                                } else {
                                    return $forms[2];
                                }
                            }
                        }
                    @endphp
                    
                    <div class="stat-card">
                        <div class="stat-icon {{ $iconClass }}">
                            <i class="fas {{ $icon }}"></i>
                        </div>
                        <div class="stat-value">{{ $count }}</div>
                        <div class="stat-label">{{ $levelName }}</div>
                        <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                            {{ pluralForm($count, ['osoba', 'osoby', 'osób']) }}
                        </div>
                    </div>
                @endforeach
                
                <!-- Filled Surveys Card -->
                <div class="stat-card">
                    <div class="stat-icon filled">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="stat-value">{{ $filledSurveyCount }}</div>
                    <div class="stat-label">Przesłało</div>
                    <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                        z {{ $totalEmployees }} {{ pluralForm($totalEmployees, ['osoby', 'osób', 'osób']) }}
                    </div>
                </div>
            </div>
            
            <!-- Team Details Table -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Szczegółowe dane zespołu</h3>
                    <div style="display: flex; gap: 12px;">
                        <div class="search-box" style="margin: 0;">
                            <i class="fas fa-search"></i>
                            <input type="text" id="team-search" class="form-control" placeholder="Wyszukaj w zespole..." style="width: 250px;">
                        </div>
                    </div>
                </div>
                <div class="card-content" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Pracownik</th>
                                    <th>Stanowisko</th>
                                    @foreach($levelNames as $levelName)
                                        <th style="text-align: center; white-space: nowrap;">{{ $levelName }}</th>
                                    @endforeach
                                    <th style="text-align: center;">Poziom</th>
                                    <th style="text-align: center;">Akcje</th>
                                </tr>
                            </thead>
                            <tbody id="team-table-body">
                                @foreach($teamEmployeesData as $emp)
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div class="user-avatar" style="width: 32px; height: 32px; font-size: 14px;">
                                                    {{ strtoupper(substr($emp['name'], 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div style="font-weight: 600;">{{ $emp['name'] }}</div>
                                                    <div style="font-size: 12px; color: var(--muted);">
                                                        {{ $emp['department'] ?? 'Brak działu' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $emp['job_title'] ?? 'Brak stanowiska' }}</td>
                                        
                                        @foreach($levelNames as $levelName)
                                            <td style="text-align: center;">
                                                @php
                                                    $percentage = (float)($emp['levelPercentagesManager'][$levelName] ?? 0);
                                                @endphp
                                                <div style="position: relative;">
                                                    @if($percentage > 0)
                                                        <span style="font-weight: 600; color: {{ $percentage >= 70 ? 'var(--accent)' : ($percentage >= 50 ? 'var(--warning)' : 'var(--text)') }};">
                                                            {{ number_format($percentage, 0) }}%
                                                        </span>
                                                        <!-- Progress bar -->
                                                        <div style="width: 100%; height: 4px; background: var(--border); border-radius: 2px; margin-top: 4px; overflow: hidden;">
                                                            <div style="height: 100%; background: {{ $percentage >= 70 ? 'var(--accent)' : ($percentage >= 50 ? 'var(--warning)' : 'var(--muted)') }}; width: {{ $percentage }}%; transition: width 0.3s ease;"></div>
                                                        </div>
                                                    @else
                                                        <span style="color: var(--muted);">—</span>
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach
                                        
                                        <td style="text-align: center;">
                                            @if(!empty($emp['highestLevelManager']))
                                                <span class="badge level" style="font-size: 12px; padding: 6px 12px;">
                                                    {{ $emp['highestLevelManager'] }}
                                                </span>
                                            @else
                                                <span style="color: var(--muted);">Brak oceny</span>
                                            @endif
                                        </td>
                                        
                                        <td style="text-align: center;">
                                            <div class="action-buttons">
                                                <a href="?section=individual&employee={{ $emp['id'] }}&cycle={{ $selectedCycleId }}" 
                                                   class="btn btn-sm btn-secondary loading-btn" title="Zobacz szczegóły">
                                                    <i class="fas fa-eye"></i>
                                                    <span class="loading-text" style="display: none;">
                                                        <i class="fas fa-spinner fa-spin"></i>
                                                    </span>
                                                </a>
                                                <a href="{{ route('manager.generate_pdf', ['employeeId' => $emp['id']]) }}?cycle={{ $selectedCycleId }}" 
                                                   target="_blank" class="btn btn-sm btn-secondary" title="Pobierz PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Team Export Options -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Eksport danych zespołu</h3>
                    <p class="card-description">Pobierz raporty dla całego zespołu</p>
                </div>
                <div class="card-content">
                    <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                        <form action="{{ route('manager.export_team_pdf') }}" method="GET" style="display: inline;">
                            <input type="hidden" name="cycle" value="{{ $selectedCycleId }}">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-file-pdf"></i> Eksport zespołu PDF
                            </button>
                        </form>
                        
                        <form action="{{ route('manager.export_team_excel') }}" method="GET" style="display: inline;">
                            <input type="hidden" name="cycle" value="{{ $selectedCycleId }}">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-file-excel"></i> Eksport zespołu Excel
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Team search functionality
        $('#team-search').on('input', function() {
            const query = $(this).val().toLowerCase();
            $('#team-table-body tr').each(function() {
                const name = $(this).find('td').eq(0).text().toLowerCase();
                const position = $(this).find('td').eq(1).text().toLowerCase();
                const matches = name.includes(query) || position.includes(query);
                $(this).toggle(matches);
            });
            
            // Update visible count
            const visibleRows = $('#team-table-body tr:visible').length;
            const totalRows = $('#team-table-body tr').length;
            
            if (query && visibleRows === 0) {
                if ($('#no-results').length === 0) {
                    $('#team-table-body').append(`
                        <tr id="no-results">
                            <td colspan="100%" style="text-align: center; padding: 40px; color: var(--muted);">
                                <i class="fas fa-search"></i><br>
                                Brak wyników dla "${query}"
                            </td>
                        </tr>
                    `);
                }
            } else {
                $('#no-results').remove();
            }
        });
        
        // Clear search when empty
        $('#team-search').on('blur', function() {
            if (!$(this).val()) {
                $('#team-table-body tr').show();
                $('#no-results').remove();
            }
        });
    });
</script>
@endpush
@if($manager->role == 'supermanager')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">HR - Podsumowanie organizacji</h2>
        <p class="card-description">Kompleksowy przegląd kompetencji w całej organizacji</p>
    </div>
    <div class="card-content">
        <!-- Organization Summary Stats -->
        @if(!isset($organizationEmployeesData))
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Brak danych organizacyjnych. Zmienna $organizationEmployeesData nie została przekazana.
            </div>
        @elseif(empty($organizationEmployeesData))
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i>
                Brak danych pracowników w organizacji.
            </div>
        @endif
        
        @php
            $totalEmployees = count($organizationEmployeesData ?? []);
            $filledSurveyCount = 0;
            $levelCounts = [];
            
            // Initialize level counts
            foreach($levelNames as $levelName) {
                $levelCounts[$levelName] = 0;
            }
            
            // Count filled surveys and levels
            foreach($organizationEmployeesData as $emp) {
                // Check if employee has any non-null percentage values (meaning they filled the survey)
                $hasFilledSurvey = false;
                if (!empty($emp['levelPercentagesManager'])) {
                    foreach ($emp['levelPercentagesManager'] as $percentage) {
                        if ($percentage !== null && $percentage > 0) {
                            $hasFilledSurvey = true;
                            break;
                        }
                    }
                }
                
                if ($hasFilledSurvey) {
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
            
            $completionRate = $totalEmployees > 0 ? round(($filledSurveyCount / $totalEmployees) * 100, 1) : 0;
            
            // Debug info
            \Log::info('HR Stats Debug', [
                'total_employees' => $totalEmployees,
                'filled_count' => $filledSurveyCount,
                'completion_rate' => $completionRate,
                'level_counts' => $levelCounts,
                'organization_data_exists' => isset($organizationEmployeesData),
                'organization_data_count' => count($organizationEmployeesData ?? [])
            ]);
        @endphp
        
        <!-- Key Metrics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon level-1">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">{{ $totalEmployees }}</div>
                <div class="stat-label">Łącznie pracowników</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon filled">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value">{{ $filledSurveyCount }}</div>
                <div class="stat-label">Wypełniło samoocenę</div>
                <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                    {{ $completionRate }}% ukończenia
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #fef3c7; color: #92400e;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value">{{ $totalEmployees - $filledSurveyCount }}</div>
                <div class="stat-label">Oczekuje na wypełnienie</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #ede9fe; color: #7c3aed;">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stat-value">{{ count($hrData ?? []) }}</div>
                <div class="stat-label">Zespołów</div>
            </div>
        </div>

        <!-- Level Distribution -->
        <div class="card" style="margin-top: 24px;">
            <div class="card-header">
                <h3 class="card-title">Rozkład poziomów w organizacji</h3>
            </div>
            <div class="card-content">
                <div class="stats-grid">
                    @foreach($levelNames as $levelName)
                        @php
                            $count = $levelCounts[$levelName] ?? 0;
                            $percentage = $totalEmployees > 0 ? round(($count / $totalEmployees) * 100, 1) : 0;
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
                        @endphp
                        
                        <div class="stat-card">
                            <div class="stat-icon {{ $iconClass }}">
                                <i class="fas {{ $icon }}"></i>
                            </div>
                            <div class="stat-value">{{ $count }}</div>
                            <div class="stat-label">{{ $levelName }}</div>
                            <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                                {{ $percentage }}% organizacji
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Team Summary -->
        <div class="card" style="margin-top: 24px;">
            <div class="card-header">
                <h3 class="card-title">Podsumowanie zespołów</h3>
                <p class="card-description">Wypełnienie samoocen w poszczególnych zespołach</p>
            </div>
            <div class="card-content" style="padding: 0;">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Zespół</th>
                                <th style="text-align: center;">Wypełnione samooceny</th>
                                <th style="text-align: center;">% ukończenia</th>
                                <th style="text-align: center;">Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hrData as $data)
                                @php
                                    $teamCompletionRate = isset($data['total_count']) && $data['total_count'] > 0 
                                        ? round(($data['completed_count'] / $data['total_count']) * 100, 1) 
                                        : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div class="stat-icon level-2" style="width: 32px; height: 32px; font-size: 14px;">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600;">{{ $data['team_name'] }}</div>
                                                <div style="font-size: 12px; color: var(--muted);">
                                                    {{ $data['total_count'] ?? 0 }} 
                                                    @if(($data['total_count'] ?? 0) == 1)
                                                        pracownik
                                                    @elseif(($data['total_count'] ?? 0) == 2 || ($data['total_count'] ?? 0) == 3 || ($data['total_count'] ?? 0) == 4)
                                                        pracowników
                                                    @else
                                                        pracowników
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span style="font-weight: 600; font-size: 16px;">
                                            {{ $data['completed_count'] }}
                                        </span>
                                        <span style="color: var(--muted);"> / {{ $data['total_count'] ?? 0 }}</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                            <span style="font-weight: 600; color: {{ $teamCompletionRate >= 80 ? 'var(--accent)' : ($teamCompletionRate >= 50 ? 'var(--warning)' : 'var(--danger)') }};">
                                                {{ $teamCompletionRate }}%
                                            </span>
                                            <!-- Progress bar -->
                                            <div style="width: 60px; height: 6px; background: var(--border); border-radius: 3px; overflow: hidden;">
                                                <div style="height: 100%; background: {{ $teamCompletionRate >= 80 ? 'var(--accent)' : ($teamCompletionRate >= 50 ? 'var(--warning)' : 'var(--danger)') }}; width: {{ $teamCompletionRate }}%; transition: width 0.3s ease;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-buttons">
                                            <form action="{{ route('manager.export_team_pdf') }}" method="GET" style="display: inline;">
                                                <input type="hidden" name="team" value="{{ $data['team_name'] }}">
                                                <input type="hidden" name="cycle" value="{{ $selectedCycleId }}">
                                                <button type="submit" class="btn btn-sm btn-secondary" title="Pobierz raport PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('manager.export_team_excel') }}" method="GET" style="display: inline;">
                                                <input type="hidden" name="team" value="{{ $data['team_name'] }}">
                                                <input type="hidden" name="cycle" value="{{ $selectedCycleId }}">
                                                <button type="submit" class="btn btn-sm btn-secondary" title="Pobierz raport Excel">
                                                    <i class="fas fa-file-excel"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detailed Employee Data -->
        <div class="card" style="margin-top: 24px;">
            <div class="card-header">
                <h3 class="card-title">Wszyscy pracownicy organizacji</h3>
                <div style="display: flex; gap: 12px;">
                    <div class="search-box" style="margin: 0;">
                        <i class="fas fa-search"></i>
                        <input type="text" id="hr-search" class="form-control" placeholder="Wyszukaj po imieniu, nazwisku lub stanowisku..." style="width: 350px;">
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
                                    <th style="text-align: center; white-space: nowrap; font-size: 12px;">{{ $levelName }}</th>
                                @endforeach
                                <th style="text-align: center;">Poziom</th>
                                <th style="text-align: center;">Akcje</th>
                            </tr>
                        </thead>
                        <tbody id="hr-table-body">
                            @foreach($organizationEmployeesData as $emp)
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
                                            @if($percentage > 0)
                                                <div style="position: relative;">
                                                    <span style="font-weight: 600; font-size: 13px; color: {{ $percentage >= 70 ? 'var(--accent)' : ($percentage >= 50 ? 'var(--warning)' : 'var(--text)') }};">
                                                        {{ number_format($percentage, 0) }}%
                                                    </span>
                                                    <!-- Mini progress bar -->
                                                    <div style="width: 100%; height: 3px; background: var(--border); border-radius: 2px; margin-top: 2px; overflow: hidden;">
                                                        <div style="height: 100%; background: {{ $percentage >= 70 ? 'var(--accent)' : ($percentage >= 50 ? 'var(--warning)' : 'var(--muted)') }}; width: {{ $percentage }}%; transition: width 0.3s ease;"></div>
                                                    </div>
                                                </div>
                                            @else
                                                <span style="color: var(--muted); font-size: 13px;">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    
                                    <td style="text-align: center;">
                                        @if(!empty($emp['highestLevelManager']))
                                            <span class="badge level" style="font-size: 11px; padding: 4px 8px;">
                                                {{ $emp['highestLevelManager'] }}
                                            </span>
                                        @else
                                            <span style="color: var(--muted); font-size: 13px;">Brak oceny</span>
                                        @endif
                                    </td>
                                    
                                    <td style="text-align: center;">
                                        <div class="action-buttons">
                                            <a href="?section=hr_individual&employee={{ $emp['id'] }}&cycle={{ $selectedCycleId }}" 
                                               class="btn btn-sm btn-secondary" title="Zobacz szczegóły">
                                                <i class="fas fa-eye"></i>
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

        <!-- Export Options -->
        <div class="card" style="margin-top: 24px;">
            <div class="card-header">
                <h3 class="card-title">Eksport danych organizacji</h3>
                <p class="card-description">Pobierz kompleksowe raporty dla całej organizacji</p>
            </div>
            <div class="card-content">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                    <form action="{{ route('manager.export_organization_pdf') }}" method="GET">
                        <input type="hidden" name="cycle" value="{{ $selectedCycleId }}">
                        <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: flex-start;">
                            <i class="fas fa-file-pdf"></i> 
                            <span>Raport organizacji PDF</span>
                        </button>
                    </form>
                    
                    <form action="{{ route('manager.export_organization_excel') }}" method="GET">
                        <input type="hidden" name="cycle" value="{{ $selectedCycleId }}">
                        <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: flex-start;">
                            <i class="fas fa-file-excel"></i> 
                            <span>Raport organizacji Excel</span>
                        </button>
                    </form>
                    
                    <form action="{{ route('manager.export_statistics_excel') }}" method="GET">
                        <input type="hidden" name="cycle" value="{{ $selectedCycleId }}">
                        <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: flex-start;">
                            <i class="fas fa-chart-bar"></i> 
                            <span>Statystyki Excel</span>
                        </button>
                    </form>
                    
                    <button class="btn btn-primary" onclick="generateComprehensiveReport()" style="width: 100%; justify-content: flex-start;">
                        <i class="fas fa-clipboard-list"></i> 
                        <span>Raport kompletny</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Enhanced HR search functionality
        $('#hr-search').on('input', function() {
            const query = $(this).val().toLowerCase();
            let visibleCount = 0;
            
            $('#hr-table-body tr').each(function() {
                const name = $(this).find('td').eq(0).text().toLowerCase();
                const position = $(this).find('td').eq(1).text().toLowerCase();
                const matches = name.includes(query) || position.includes(query);
                $(this).toggle(matches);
                if (matches) visibleCount++;
            });
            
            // Update search results indicator
            updateSearchResultsIndicator(query, visibleCount, {{ count($organizationEmployeesData) }});
        });
        
        // Auto-refresh data every 5 minutes for live updates
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                refreshOrganizationStats();
            }
        }, 300000); // 5 minutes
    });
    
    function updateSearchResultsIndicator(query, visibleCount, totalCount) {
        $('#search-results-indicator').remove();
        
        if (query) {
            const indicator = $(`
                <div id="search-results-indicator" style="padding: 12px 16px; background: var(--bg); border-bottom: 1px solid var(--border); font-size: 14px; color: var(--muted);">
                    <i class="fas fa-search"></i> 
                    Znaleziono ${visibleCount} z ${totalCount} pracowników dla "${query}"
                    ${visibleCount === 0 ? '<span style="color: var(--danger); margin-left: 8px;"><i class="fas fa-exclamation-triangle"></i> Brak wyników</span>' : ''}
                </div>
            `);
            $('.table-responsive').prepend(indicator);
        }
    }
    
    function refreshOrganizationStats() {
        // This would make an AJAX call to refresh statistics
        console.log('Refreshing organization stats...');
    }
    
    function generateComprehensiveReport() {
        showLoading();
        
        // This would generate a comprehensive report with all data
        fetch('/manager/generate-comprehensive-report', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cycle_id: {{ $selectedCycleId }},
                include_teams: true,
                include_individuals: true,
                include_statistics: true
            })
        })
        .then(response => {
            if (response.ok) {
                return response.blob();
            }
            throw new Error('Network response was not ok');
        })
        .then(blob => {
            hideLoading();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `Raport_kompletny_${new Date().toISOString().slice(0, 10)}.xlsx`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            showToast('Raport został wygenerowany', 'success');
        })
        .catch(error => {
            hideLoading();
            showToast('Wystąpił błąd podczas generowania raportu', 'danger');
            console.error('Error:', error);
        });
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
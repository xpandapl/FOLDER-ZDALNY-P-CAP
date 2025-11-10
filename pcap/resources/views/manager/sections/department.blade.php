@if($manager->role == 'head')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Dział - Podsumowanie zespołu</h2>
        <p class="card-description">Przegląd kompetencji wszystkich pracowników w Twoim dziale</p>
    </div>
    <div class="card-content">
        @if(empty($departmentEmployeesData))
            <!-- No Department Members -->
            <div class="no-data">
                <i class="fas fa-sitemap"></i>
                <h3>Brak pracowników w dziale</h3>
                <p>Nie masz przypisanych pracowników w strukturze działu lub dział jest pusty.</p>
            </div>
        @else
            <!-- Department Summary Stats -->
            @php
                $totalEmployees = count($departmentEmployeesData);
                $filledSurveyCount = 0;
                $levelCounts = [];
                
                // Initialize level counts
                foreach($levelNames as $levelName) {
                    $levelCounts[$levelName] = 0;
                }
                
                // Count filled surveys and levels
                foreach($departmentEmployeesData as $emp) {
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
            @endphp
            
            <!-- Key Department Metrics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon level-1">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-value">{{ $totalEmployees }}</div>
                    <div class="stat-label">Pracowników w dziale</div>
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
                    <div class="stat-label">Oczekuje wypełnienia</div>
                </div>
            </div>

            <!-- Level Distribution in Department -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Rozkład poziomów w dziale</h3>
                    <div style="display: flex; gap: 16px;">
                        <form action="{{ route('department.export') }}" method="GET" style="display: inline;">
                            @csrf
                            <input type="hidden" name="cycle" value="{{ $selectedCycleId }}">
                            <button type="submit" class="btn btn-secondary btn-sm">
                                <i class="fas fa-download"></i> Eksport działu XLS
                            </button>
                        </form>
                    </div>
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
                                    {{ $percentage }}% działu
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Department Team Details -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Pracownicy działu</h3>
                    <div style="display: flex; gap: 12px;">
                        <div class="search-box" style="margin: 0;">
                            <i class="fas fa-search"></i>
                            <input type="text" id="department-search" class="form-control" placeholder="Wyszukaj w dziale..." style="width: 250px;">
                        </div>
                        
                        <select id="level-filter" class="form-control" style="width: 200px;">
                            <option value="">Wszystkie poziomy</option>
                            @foreach($levelNames as $levelName)
                                <option value="{{ $levelName }}">{{ $levelName }}</option>
                            @endforeach
                        </select>
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
                            <tbody id="department-table-body">
                                @foreach($departmentEmployeesData as $emp)
                                    <tr data-level="{{ $emp['highestLevelManager'] ?? '' }}">
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div class="user-avatar" style="width: 32px; height: 32px; font-size: 14px;">
                                                    {{ strtoupper(substr($emp['name'], 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div style="font-weight: 600;">{{ $emp['name'] }}</div>
                                                    <div style="font-size: 12px; color: var(--muted);">
                                                        @php
                                                            $hasFilledSurvey = false;
                                                            if (!empty($emp['levelPercentagesManager'])) {
                                                                foreach ($emp['levelPercentagesManager'] as $percentage) {
                                                                    if ($percentage !== null && $percentage > 0) {
                                                                        $hasFilledSurvey = true;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                                                        @if($hasFilledSurvey)
                                                            <i class="fas fa-check" style="color: var(--accent);"></i> Wypełnione
                                                        @else
                                                            <i class="fas fa-clock" style="color: var(--warning);"></i> Oczekuje
                                                        @endif
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
                                                <a href="?section=department_individual&employee={{ $emp['id'] }}&cycle={{ $selectedCycleId }}" 
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
                    <h3 class="card-title">Eksport danych działu</h3>
                    <p class="card-description">Pobierz szczegółowe raporty dla działu</p>
                </div>
                <div class="card-content">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                        <form action="{{ route('department.export') }}" method="GET">
                            @csrf
                            <input type="hidden" name="cycle" value="{{ $selectedCycleId }}">
                            <input type="hidden" name="format" value="pdf">
                            <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: flex-start;">
                                <i class="fas fa-file-pdf"></i> 
                                <span>Raport działu PDF</span>
                            </button>
                        </form>
                        
                        <form action="{{ route('department.export') }}" method="GET">
                            @csrf
                            <input type="hidden" name="cycle" value="{{ $selectedCycleId }}">
                            <input type="hidden" name="format" value="excel">
                            <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: flex-start;">
                                <i class="fas fa-file-excel"></i> 
                                <span>Raport działu Excel</span>
                            </button>
                        </form>
                        
                        <form action="{{ route('department.export_analytics') }}" method="GET">
                            @csrf
                            <input type="hidden" name="cycle" value="{{ $selectedCycleId }}">
                            <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: flex-start;">
                                <i class="fas fa-chart-pie"></i> 
                                <span>Analityka Excel</span>
                            </button>
                        </form>
                        
                        <button class="btn btn-primary" onclick="generateDepartmentSummary()" style="width: 100%; justify-content: flex-start;">
                            <i class="fas fa-clipboard-list"></i> 
                            <span>Podsumowanie</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Department search functionality
        $('#department-search').on('input', function() {
            const query = $(this).val().toLowerCase();
            filterDepartmentTable();
        });
        
        // Level filter functionality
        $('#level-filter').on('change', function() {
            filterDepartmentTable();
        });
    });
    
    function filterDepartmentTable() {
        const searchQuery = $('#department-search').val().toLowerCase();
        const levelFilter = $('#level-filter').val();
        let visibleCount = 0;
        
        $('#department-table-body tr').each(function() {
            const name = $(this).find('td').eq(0).text().toLowerCase();
            const position = $(this).find('td').eq(1).text().toLowerCase();
            const team = $(this).find('td').eq(2).text().toLowerCase();
            const level = $(this).data('level');
            
            const matchesSearch = !searchQuery || name.includes(searchQuery) || position.includes(searchQuery) || team.includes(searchQuery);
            const matchesLevel = !levelFilter || level === levelFilter;
            
            const shouldShow = matchesSearch && matchesLevel;
            $(this).toggle(shouldShow);
            if (shouldShow) visibleCount++;
        });
        
        // Update results indicator
        updateDepartmentSearchResults(searchQuery, levelFilter, visibleCount, {{ count($departmentEmployeesData) }});
    }
    
    function updateDepartmentSearchResults(searchQuery, levelFilter, visibleCount, totalCount) {
        $('#department-search-results').remove();
        
        if (searchQuery || levelFilter) {
            let message = `Wyświetlono ${visibleCount} z ${totalCount} pracowników`;
            if (searchQuery) message += ` dla "${searchQuery}"`;
            if (levelFilter) message += ` (poziom: ${levelFilter})`;
            
            const indicator = $(`
                <div id="department-search-results" style="padding: 12px 16px; background: var(--bg); border-bottom: 1px solid var(--border); font-size: 14px; color: var(--muted);">
                    <i class="fas fa-filter"></i> ${message}
                    ${visibleCount === 0 ? '<span style="color: var(--danger); margin-left: 8px;"><i class="fas fa-exclamation-triangle"></i> Brak wyników</span>' : ''}
                </div>
            `);
            $('.table-responsive').prepend(indicator);
        }
    }
    
    function generateDepartmentSummary() {
        showLoading();
        
        fetch('/manager/generate-department-summary', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cycle_id: {{ $selectedCycleId }},
                department: '{{ $manager->department ?? "" }}'
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
            a.download = `Podsumowanie_dzialu_${new Date().toISOString().slice(0, 10)}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            showToast('Podsumowanie zostało wygenerowane', 'success');
        })
        .catch(error => {
            hideLoading();
            showToast('Wystąpił błąd podczas generowania podsumowania', 'danger');
            console.error('Error:', error);
        });
    }
</script>
@endpush

@else
    <div class="no-data">
        <i class="fas fa-ban"></i>
        <h3>Brak dostępu</h3>
        <p>Ta sekcja jest dostępna tylko dla kierowników działów (head).</p>
    </div>
@endif
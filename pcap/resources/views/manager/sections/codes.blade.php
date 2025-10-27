<div class="card">
    <div class="card-header">
        <h2 class="card-title">Kody dostępu dla zespołu</h2>
        <p class="card-description">Zarządzaj kodami dostępu do systemu dla członków zespołu</p>
    </div>
    <div class="card-content">
        @php
            // Determine which employees to show based on manager role
            $codesEmployees = collect();
            if ($manager->role == 'supermanager') {
                $codesEmployees = $allEmployees ?? collect();
            } elseif ($manager->role == 'head') {
                $codesEmployees = $departmentEmployees ?? collect();
            } else {
                $codesEmployees = $employees ?? collect();
            }
        @endphp

        @if($codesEmployees->isEmpty())
            <div class="no-data">
                <i class="fas fa-key"></i>
                <h3>Brak pracowników w zespole</h3>
                <p>Nie masz przypisanych pracowników do zarządzania kodami dostępu.</p>
            </div>
        @else
            <!-- Cycle Info -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>Aktualny cykl:</strong> {{ $selectedCycle->label ?? 'Brak wybranego cyklu' }}
                    <br>
                    <small>
                        @if(isset($isSelectedCycleActive) && $isSelectedCycleActive)
                            Generowanie kodów dostępne dla aktywnego cyklu
                        @else
                            Przeglądanie kodów dostępne - generowanie zablokowane dla cyklu historycznego
                        @endif
                    </small>
                </div>
            </div>

            <!-- New Code Alert -->
            @if(session('generated_code'))
                <div class="alert alert-success">
                    <i class="fas fa-key"></i>
                    <div>
                        <strong>Nowy kod wygenerowany (jednorazowy podgląd):</strong>
                        <div style="margin: 8px 0;">
                            <code id="full-code-display" style="font-family: monospace; background: rgba(0,0,0,0.1); padding: 8px 12px; border-radius: 6px; font-size: 16px; letter-spacing: 1px;">{{ session('generated_code') }}</code>
                            <button class="btn btn-sm btn-primary" onclick="copyFullCode('{{ session('generated_code') }}')" style="margin-left: 12px;">
                                <i class="fas fa-copy"></i> Kopiuj pełny kod
                            </button>
                        </div>
                        <small style="color: #666;">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>WAŻNE:</strong> To jedyna okazja do skopiowania pełnego kodu! Po odświeżeniu strony będą widoczne tylko ostatnie 4 cyfry.
                        </small>
                    </div>
                </div>
            @endif

            <!-- Search and Filters -->
            <div style="display: flex; gap: 16px; margin-bottom: 24px; align-items: center; flex-wrap: wrap;">
                <div class="search-box" style="margin: 0; flex: 1; min-width: 250px;">
                    <i class="fas fa-search"></i>
                    <input type="text" id="codes-search" class="form-control" placeholder="Wyszukaj pracownika...">
                </div>
                
                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-secondary btn-sm" onclick="filterCodes('all')" id="filter-all">
                        <i class="fas fa-users"></i> Wszyscy
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="filterCodes('active')" id="filter-active">
                        <i class="fas fa-check-circle"></i> Z kodami
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="filterCodes('expired')" id="filter-expired">
                        <i class="fas fa-clock"></i> Wygasłe
                    </button>
                </div>
            </div>

            <!-- Access Codes Table -->
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Pracownik</th>
                            <th>Stanowisko</th>
                            <th>Dział/Zespół</th>
                            <th style="text-align: center;">Status kodu</th>
                            <th style="text-align: center;">Ostatnie 4 cyfry</th>
                            <th style="text-align: center;">Data wygaśnięcia</th>
                            <th style="text-align: center;">Akcje</th>
                        </tr>
                    </thead>
                    <tbody id="codes-table-body">
                        @foreach($codesEmployees as $emp)
                            @php
                                $code = ($employeeAccessCodes ?? collect())->get($emp->id);
                                $hasActiveCode = false;
                                $isExpired = false;
                                
                                if ($code) {
                                    if ($code->expires_at) {
                                        // Kod ma datę wygaśnięcia
                                        $hasActiveCode = $code->expires_at->isFuture();
                                        $isExpired = $code->expires_at->isPast();
                                    } else {
                                        // Kod bez daty wygaśnięcia - traktuj jako aktywny
                                        $hasActiveCode = true;
                                        $isExpired = false;
                                    }
                                }
                            @endphp
                            <tr data-filter-status="{{ $hasActiveCode ? 'active' : ($isExpired ? 'expired' : 'none') }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div class="user-avatar" style="width: 32px; height: 32px; font-size: 14px;">
                                            {{ strtoupper(substr($emp->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 600;">{{ $emp->name }}</div>
                                            <div style="font-size: 12px; color: var(--muted);">
                                                ID: {{ $emp->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $emp->job_title ?? 'Brak stanowiska' }}</td>
                                <td>{{ $emp->department ?? 'Brak działu' }}</td>
                                <td style="text-align: center;">
                                    @if($hasActiveCode)
                                        <span class="badge" style="background: #dcfce7; color: #166534; padding: 6px 12px;">
                                            <i class="fas fa-check-circle"></i> Aktywny
                                        </span>
                                    @elseif($isExpired)
                                        <span class="badge" style="background: #fee2e2; color: #991b1b; padding: 6px 12px;">
                                            <i class="fas fa-clock"></i> Wygasły
                                        </span>
                                    @else
                                        <span class="badge" style="background: #f3f4f6; color: #6b7280; padding: 6px 12px;">
                                            <i class="fas fa-minus-circle"></i> Brak kodu
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($code && $code->raw_last4)
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                            <code style="font-family: monospace; background: var(--bg); padding: 4px 8px; border-radius: 4px; letter-spacing: 1px;">
                                                ****{{ $code->raw_last4 }}
                                            </code>
                                            <button class="btn-icon" onclick="showFullCode({{ $emp->id }})" title="Pokaż pełny kod">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    @else
                                        <span style="color: var(--muted);">—</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($code && $code->expires_at)
                                        @php
                                            $expiresAt = $code->expires_at;
                                            $isExpiring = $expiresAt->diffInDays(now()) <= 7 && $expiresAt->isFuture();
                                        @endphp
                                        <div style="color: {{ $isExpired ? 'var(--danger)' : ($isExpiring ? 'var(--warning)' : 'var(--text)') }};">
                                            <div style="font-weight: 500;">{{ $expiresAt->format('Y-m-d') }}</div>
                                            <div style="font-size: 12px; color: var(--muted);">{{ $expiresAt->format('H:i') }}</div>
                                            @if($isExpiring && !$isExpired)
                                                <div style="font-size: 11px; color: var(--warning); margin-top: 2px;">
                                                    <i class="fas fa-exclamation-triangle"></i> Wygasa za {{ $expiresAt->diffInDays(now()) }} dni
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color: var(--muted);">bez terminu</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div class="action-buttons">
                                        @if(isset($isSelectedCycleActive) && $isSelectedCycleActive)
                                            <form action="{{ route('manager.generate_access_code', ['employeeId' => $emp->id]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary" title="Wygeneruj nowy kod dostępu">
                                                    <i class="fas fa-key"></i>
                                                    {{ $code ? 'Regeneruj' : 'Generuj' }}
                                                </button>
                                            </form>
                                            
                                            @if($code)
                                                <button class="btn btn-sm btn-danger" onclick="revokeCode({{ $emp->id }})" title="Unieważnij kod">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @endif
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled title="Generowanie zablokowane dla cyklu historycznego">
                                                <i class="fas fa-lock"></i>
                                                {{ $code ? 'Regeneruj' : 'Generuj' }}
                                            </button>
                                            
                                            @if($code)
                                                <button class="btn btn-sm btn-secondary" disabled title="Unieważnienie zablokowane dla cyklu historycznego">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Security Info and Help -->
            <div class="card" style="margin-top: 24px; border-left: 4px solid var(--primary);">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt"></i> Informacje o bezpieczeństwie kodów dostępu
                    </h3>
                </div>
                <div class="card-content">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                        <div>
                            <h4 style="color: var(--primary); margin-bottom: 8px;">
                                <i class="fas fa-eye"></i> Pełny kod dostępu
                            </h4>
                            <ul style="margin: 0; padding-left: 20px; color: var(--text);">
                                <li>Dostępny poprzez przycisk <strong>"Pokaż pełny kod"</strong></li>
                                <li>Przechowywany w formie zaszyfrowanej</li>
                                <li>Można kopiować w dowolnym momencie</li>
                                <li>Bezpieczne wyświetlanie w modalnym oknie</li>
                            </ul>
                        </div>
                        
                        <div>
                            <h4 style="color: var(--success); margin-bottom: 8px;">
                                <i class="fas fa-download"></i> Eksport kodów
                            </h4>
                            <ul style="margin: 0; padding-left: 20px; color: var(--text);">
                                <li><strong>Eksportuj podsumowanie:</strong> Tylko ostatnie 4 cyfry</li>
                                <li><strong>Eksportuj z pełnymi kodami:</strong> Zawiera pełne kody</li>
                                <li>Format CSV kompatybilny z Excel</li>
                                <li>Kodowanie UTF-8 dla polskich znaków</li>
                            </ul>
                        </div>
                        
                        <div>
                            <h4 style="color: var(--warning); margin-bottom: 8px;">
                                <i class="fas fa-lightbulb"></i> Najlepsze praktyki
                            </h4>
                            <ul style="margin: 0; padding-left: 20px; color: var(--text);">
                                <li>Eksportuj pełne kody tylko gdy potrzeba</li>
                                <li>Usuń pobraną listę po użyciu</li>
                                <li>Nie udostępniaj kodów przez e-mail</li>
                                <li>Używaj funkcji "Pokaż kod" dla pojedynczych przypadków</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Akcje grupowe</h3>
                    <p class="card-description">Operacje dla całego zespołu</p>
                </div>
                <div class="card-content">
                    @if(isset($isSelectedCycleActive) && $isSelectedCycleActive)
                        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                            <button class="btn btn-primary" onclick="generateAllCodes()" id="generate-all-btn">
                                <i class="fas fa-users"></i> Wygeneruj kody dla wszystkich
                            </button>
                            
                            <button class="btn btn-danger" onclick="revokeAllCodes()" id="revoke-all-btn">
                                <i class="fas fa-ban"></i> Unieważnij wszystkie kody
                            </button>
                            
                            <button class="btn btn-secondary" onclick="exportCodes()">
                                <i class="fas fa-download"></i> Eksportuj podsumowanie
                            </button>
                            
                            <button class="btn btn-success" onclick="exportFullCodes()">
                                <i class="fas fa-file-excel"></i> Eksportuj z pełnymi kodami
                            </button>
                        </div>
                    @else
                        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                            <button class="btn btn-secondary" disabled title="Generowanie zablokowane dla cyklu historycznego">
                                <i class="fas fa-lock"></i> Wygeneruj kody dla wszystkich
                            </button>
                            
                            <button class="btn btn-secondary" disabled title="Unieważnienie zablokowane dla cyklu historycznego">
                                <i class="fas fa-ban"></i> Unieważnij wszystkie kody
                            </button>
                            
                            <button class="btn btn-secondary" onclick="exportCodes()">
                                <i class="fas fa-download"></i> Eksportuj podsumowanie
                            </button>
                            
                            <button class="btn btn-success" onclick="exportFullCodes()">
                                <i class="fas fa-file-excel"></i> Eksportuj z pełnymi kodami
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats-grid" style="margin-top: 24px;">
                @php
                    $totalEmployees = $codesEmployees->count();
                    $activeCodes = 0;
                    $expiredCodes = 0;
                    $noCodes = 0;
                    
                    foreach($codesEmployees as $emp) {
                        $code = ($employeeAccessCodes ?? collect())->get($emp->id);
                        if ($code) {
                            if ($code->expires_at) {
                                // Kod ma datę wygaśnięcia
                                if ($code->expires_at->isFuture()) {
                                    $activeCodes++;
                                } else {
                                    $expiredCodes++;
                                }
                            } else {
                                // Kod bez daty wygaśnięcia - traktuj jako aktywny
                                $activeCodes++;
                            }
                        } else {
                            $noCodes++;
                        }
                    }
                @endphp
                
                <div class="stat-card">
                    <div class="stat-icon level-1">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value">{{ $totalEmployees }}</div>
                    <div class="stat-label">Łącznie pracowników</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #dcfce7; color: #166534;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value">{{ $activeCodes }}</div>
                    <div class="stat-label">Aktywne kody</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fee2e2; color: #991b1b;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value">{{ $expiredCodes }}</div>
                    <div class="stat-label">Wygasłe kody</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #f3f4f6; color: #6b7280;">
                        <i class="fas fa-minus-circle"></i>
                    </div>
                    <div class="stat-value">{{ $noCodes }}</div>
                    <div class="stat-label">Bez kodów</div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Modal styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-content {
        background: var(--card);
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        color: var(--primary);
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--muted);
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close:hover {
        color: var(--danger);
    }

    .modal-body {
        padding: 20px;
    }

    /* Icon button styles */
    .btn-icon {
        background: none;
        border: none;
        padding: 6px;
        border-radius: 4px;
        cursor: pointer;
        color: var(--muted);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
    }
    
    .btn-icon:hover {
        background: var(--hover);
        color: var(--primary);
        transform: scale(1.1);
    }
    
    .btn-icon:active {
        transform: scale(0.95);
    }

    /* Existing styles... */
</style>
@endpush
<script>
    $(document).ready(function() {
        // Initialize with "all" filter active
        filterCodes('all');
        
        // Search functionality
        $('#codes-search').on('input', function() {
            const query = $(this).val().toLowerCase();
            $('#codes-table-body tr').each(function() {
                const name = $(this).find('td').eq(0).text().toLowerCase();
                const position = $(this).find('td').eq(1).text().toLowerCase();
                const department = $(this).find('td').eq(2).text().toLowerCase();
                const matches = name.includes(query) || position.includes(query) || department.includes(query);
                
                // Only show if matches search AND current filter
                const currentFilter = $('.btn-primary[onclick^="filterCodes"]').attr('onclick').match(/'([^']+)'/)[1];
                const filterStatus = $(this).data('filter-status');
                const matchesFilter = currentFilter === 'all' || filterStatus === currentFilter;
                
                $(this).toggle(matches && matchesFilter);
            });
        });
    });
    
    function copyFullCode(code) {
        copyText(code);
        showToast('Pełny kod został skopiowany do schowka!', 'success');
    }
    
    function showFullCode(employeeId) {
        showLoading();
        
        fetch(`/manager/get-full-code/${employeeId}?cycle=${$('#cycle-select').val()}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                // Pokaż modal z pełnym kodem
                showFullCodeModal(data.code, data.employee_name, data.last4);
            } else {
                showToast(data.message || 'Nie można pobrać pełnego kodu', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showToast('Wystąpił błąd podczas pobierania kodu', 'danger');
            console.error('Error:', error);
        });
    }
    
    function showFullCodeModal(fullCode, employeeName, last4) {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content" style="max-width: 500px;">
                <div class="modal-header">
                    <h3><i class="fas fa-key"></i> Pełny kod dostępu</h3>
                    <button class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <strong>Pracownik:</strong> ${employeeName}
                    </div>
                    <div style="text-align: center; margin-bottom: 20px;">
                        <code style="font-family: monospace; background: var(--bg); padding: 12px 16px; border-radius: 8px; font-size: 18px; letter-spacing: 2px; border: 2px solid var(--primary);">
                            ${fullCode}
                        </code>
                    </div>
                    <div style="text-align: center; margin-bottom: 20px;">
                        <button class="btn btn-primary" onclick="copyFullCodeAndClose('${fullCode}')">
                            <i class="fas fa-copy"></i> Kopiuj kod
                        </button>
                        <button class="btn btn-secondary" onclick="closeModal()" style="margin-left: 10px;">
                            Zamknij
                        </button>
                    </div>
                    <div style="font-size: 12px; color: var(--muted); text-align: center;">
                        <i class="fas fa-info-circle"></i> Ostatnie 4 cyfry: ${last4}
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Auto-focus na przycisku kopiowania
        setTimeout(() => {
            modal.querySelector('.btn-primary').focus();
        }, 100);
    }
    
    function copyFullCodeAndClose(code) {
        copyText(code);
        showToast('Pełny kod został skopiowany do schowka!', 'success');
        closeModal();
    }
    
    function closeModal() {
        const modal = document.querySelector('.modal-overlay');
        if (modal) {
            modal.remove();
        }
    }
    
    function exportFullCodes() {
        showLoading();
        window.location.href = `/manager/export-access-codes?cycle=${$('#cycle-select').val()}`;
        setTimeout(hideLoading, 2000);
    }
    
    function regenerateForCopy(employeeId) {
        if (!confirm('Czy chcesz regenerować kod dostępu aby móc skopiować pełną wersję? Stary kod przestanie działać.')) {
            return;
        }
        
        showLoading();
        
        // Submit the regeneration form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/manager/generate-access-code/${employeeId}`;
        form.innerHTML = `<input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">`;
        document.body.appendChild(form);
        form.submit();
    }
    
    function filterCodes(status) {
        // Update button states
        $('.btn[onclick^="filterCodes"]').removeClass('btn-primary').addClass('btn-secondary');
        $(`#filter-${status}`).removeClass('btn-secondary').addClass('btn-primary');
        
        // Filter table rows
        $('#codes-table-body tr').each(function() {
            const filterStatus = $(this).data('filter-status');
            const shouldShow = status === 'all' || filterStatus === status;
            $(this).toggle(shouldShow);
        });
        
        // Re-apply search filter
        const searchQuery = $('#codes-search').val();
        if (searchQuery) {
            $('#codes-search').trigger('input');
        }
    }
    
    function generateAllCodes() {
        if (!confirm('Czy na pewno chcesz wygenerować kody dostępu dla wszystkich pracowników? Istniejące kody zostaną zastąpione.')) {
            return;
        }
        
        showLoading();
        
        // This would make an AJAX call to generate codes for all employees
        fetch('/manager/generate-all-codes', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cycle_id: {{ $selectedCycleId }}
            })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showToast('Kody zostały wygenerowane dla wszystkich pracowników', 'success');
                setTimeout(() => window.location.reload(), 2000);
            } else {
                showToast('Wystąpił błąd podczas generowania kodów', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showToast('Wystąpił błąd podczas generowania kodów', 'danger');
            console.error('Error:', error);
        });
    }
    
    function revokeCode(employeeId) {
        if (!confirm('Czy na pewno chcesz unieważnić kod dostępu dla tego pracownika?')) {
            return;
        }
        
        showLoading();
        
        fetch(`/manager/revoke-code/${employeeId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showToast('Kod dostępu został unieważniony', 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast('Wystąpił błąd podczas unieważniania kodu', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showToast('Wystąpił błąd podczas unieważniania kodu', 'danger');
            console.error('Error:', error);
        });
    }
    
    function revokeAllCodes() {
        if (!confirm('Czy na pewno chcesz unieważnić WSZYSTKIE kody dostępu w zespole? Ta akcja jest nieodwracalna.')) {
            return;
        }
        
        showLoading();
        
        fetch('/manager/revoke-all-codes', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showToast('Wszystkie kody zostały unieważnione', 'success');
                setTimeout(() => window.location.reload(), 2000);
            } else {
                showToast('Wystąpił błąd podczas unieważniania kodów', 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            showToast('Wystąpił błąd podczas unieważniania kodów', 'danger');
            console.error('Error:', error);
        });
    }
    
    function exportCodes() {
        showLoading();
        window.location.href = `/manager/export-codes?cycle={{ $selectedCycleId }}`;
        setTimeout(hideLoading, 2000);
    }
</script>
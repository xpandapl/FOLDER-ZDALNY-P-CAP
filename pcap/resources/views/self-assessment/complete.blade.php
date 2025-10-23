@extends('layouts.self-assessment')

@section('content')
<style>
/* Fixed button layout - direct targeting without nesting dependency */
div.actions.main-actions { 
    display: flex !important; 
    flex-direction: row !important;
    gap: 12px !important; 
    justify-content: center !important; 
    flex-wrap: wrap !important;
    margin: 20px 0 !important;
}

/* Ensure all .actions containers use horizontal layout */
.actions { 
    display: flex !important; 
    flex-direction: row !important;
    gap: 12px !important; 
    justify-content: center !important; 
    flex-wrap: wrap !important;
}

/* Scoped styles for complete view - consistent with other assessment pages */
.sa-complete { max-width: 800px; margin: 0 auto; }
.sa-complete .success-icon { color: #059669; font-size: 28px; margin-right: 8px; }
.sa-complete .lead { color:#4b5563; margin-top:6px; }
.sa-complete .actions { display:flex; gap:12px; align-items:center; justify-content:center; margin: 20px 0; flex-wrap:wrap; }

/* Main Actions - Primary button group */
.sa-complete .main-actions { 
    margin-bottom: 20px; 
    display: flex !important; 
    gap: 12px !important; 
    justify-content: center !important; 
    flex-wrap: wrap !important; 
}
.sa-complete .main-actions .btn { 
    min-width: 120px !important; 
    flex: 0 1 auto !important; 
    max-width: 180px !important; 
    white-space: nowrap !important;
}

/* Button styling with proper colors */
.main-actions .btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-width: 120px !important;
    padding: 12px 20px !important;
}

/* Additional options section styling */
.additional-options {
    text-align: center !important;
    margin: 24px 0 !important;
}

.btn-link {
    background: transparent !important;
    color: #6b7280 !important;
    border: none !important;
    padding: 8px 12px !important;
    cursor: pointer !important;
    font-size: 14px !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    transition: color 0.2s ease !important;
}

.btn-link:hover {
    color: #374151 !important;
    background: #f3f4f6 !important;
    border-radius: 6px !important;
}

/* Navigation section styling */
.navigation-section {
    text-align: center !important;
    margin-top: 32px !important;
    padding-top: 20px !important;
    border-top: 1px solid #e5e7eb !important;
}

.navigation-section .btn-text {
    color: #6b7280 !important;
    font-size: 14px !important;
}

/* Override global button styles with higher specificity */
.sa-complete .main-actions .btn-primary {
    background: #3b82f6 !important;
    color: white !important;
    border-color: #3b82f6 !important;
}
.sa-complete .main-actions .btn-secondary {
    background: white !important;
    color: #374151 !important;
    border-color: #d1d5db !important;
}
.sa-complete .dropdown-container { 
    position: relative; 
    display: inline-block; 
    flex: 0 1 auto; 
}
.sa-complete .dropdown-trigger { 
    display: flex; 
    align-items: center; 
    gap: 8px; 
    position: relative;
}
.sa-complete .dropdown-arrow { 
    font-size: 0.8em; 
    transition: transform 0.2s ease;
}
.sa-complete .dropdown-trigger.active .dropdown-arrow { 
    transform: rotate(180deg); 
}
.sa-complete .dropdown-menu { 
    position: absolute; 
    top: 100%; 
    left: 0; 
    right: 0; 
    background: white; 
    border: 1px solid #e5e7eb; 
    border-radius: 8px; 
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); 
    z-index: 1000; 
    margin-top: 4px;
    overflow: hidden;
}
.sa-complete .dropdown-item { 
    display: flex; 
    align-items: center; 
    gap: 8px; 
    padding: 12px 16px; 
    color: #374151; 
    text-decoration: none; 
    transition: background-color 0.2s; 
    font-size: 0.9em;
}
.sa-complete .dropdown-item:hover { 
    background: #f3f4f6; 
}
.sa-complete .dropdown-item i { 
    width: 16px; 
    text-align: center; 
}

/* Additional Options - Subtle link toggle */
.sa-complete .additional-options { 
    margin-bottom: 20px; 
    text-align: center; 
}
.sa-complete .btn-link { 
    background: none; 
    border: none; 
    color: #6b7280; 
    font-size: 0.9em; 
    cursor: pointer; 
    padding: 8px 12px;
    border-radius: 6px;
    transition: all 0.2s;
}
.sa-complete .btn-link:hover { 
    background: #f3f4f6; 
    color: #374151; 
}

/* Navigation - Minimal footer */
.sa-complete .navigation-section { 
    margin-top: 32px; 
    padding-top: 20px; 
    border-top: 1px solid #e5e7eb; 
    text-align: center; 
}

/* Link Section Styling */
.sa-complete .link-section { margin: 16px auto; max-width: 500px; }
.sa-complete .link-section .edit-section p { margin-bottom: 12px; color: #6b7280; font-size: 0.9em; }
.sa-complete .card { border:1px solid #e5e7eb; border-radius:14px; padding:20px; background:#fff; box-shadow:0 4px 14px rgba(15,23,42,.06); margin: 16px 0; }
.sa-complete .edit-section { text-align:center; }
.sa-complete .edit-section p { color:#6b7280; margin-bottom:12px; }
.sa-complete .link-row { display:flex; gap:8px; align-items:center; justify-content:center; margin-bottom:16px; }
.sa-complete .link-input { flex:1; max-width:480px; border:1px solid #d1d5db; border-radius:10px; padding:10px 12px; font-size:14px; background:#f9fafb; }
.sa-complete .home-section { text-align:center; margin-top:20px; padding-top:20px; border-top:1px solid #e5e7eb; }
/* Button System - Consistent Hierarchy */
.sa-complete .btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    min-height: 44px;
    justify-content: center;
}

/* Primary Button - Main actions (Submit, Save, Confirm) */
.sa-complete .btn-primary {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}
.sa-complete .btn-primary:hover {
    background: #2563eb;
    border-color: #2563eb;
    color: white;
    text-decoration: none;
}

/* Secondary Button - Supporting actions (Copy, Cancel) */
.sa-complete .btn-secondary {
    background: white;
    color: #374151;
    border-color: #d1d5db;
}
.sa-complete .btn-secondary:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    color: #374151;
    text-decoration: none;
}

/* Text Button - Low priority actions (Back, Links) */
.sa-complete .btn-text {
    background: transparent;
    color: #6b7280;
    border-color: transparent;
    padding: 8px 12px;
}
.sa-complete .btn-text:hover {
    background: #f3f4f6;
    color: #374151;
    text-decoration: none;
}

/* Assessment Summary Styles */
.sa-complete .assessment-summary-section { margin: 24px 0; }
.sa-complete .assessment-results-card { border: 1px solid #e0e7ff; background: linear-gradient(135deg, #f8faff 0%, #ffffff 100%); }
.sa-complete .card-header { text-align: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #e0e7ff; }
.sa-complete .card-header h2 { color: #3730a3; font-size: 1.5em; margin: 0; font-weight: 600; }
.sa-complete .card-header i { margin-right: 8px; color: #6366f1; }

/* Overall Results */
.sa-complete .competency-status { 
    text-align: center; 
    margin: 32px 0; 
    padding: 24px; 
    background: #f8fafc; 
    border-radius: 12px; 
    border-left: 4px solid #059669; 
}
.sa-complete .status-content { 
    display: flex; 
    flex-direction: column; 
    align-items: center; 
    gap: 8px; 
}
.sa-complete .status-text { 
    font-size: 1em; 
    color: #6b7280; 
    font-weight: 500; 
}
.sa-complete .level-name { 
    font-size: 1.3em; 
    font-weight: 600; 
    color: #059669; 
    background: #f0fdf4; 
    padding: 8px 20px; 
    border-radius: 8px; 
    border: 1px solid #dcfce7; 
}

.sa-complete .percentage-badge { display: inline-block; padding: 8px 16px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border-radius: 20px; font-weight: 600; font-size: 1.1em; }

/* Level Breakdown */
.sa-complete .level-breakdown-section { margin-top: 32px; }
.sa-complete .breakdown-title { text-align: center; color: #374151; font-size: 1.2em; margin-bottom: 20px; font-weight: 600; }
.sa-complete .breakdown-title i { margin-right: 8px; color: #6366f1; }
.sa-complete .levels-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }

/* Level Cards */
.sa-complete .level-card { background: #ffffff; border-radius: 12px; padding: 20px; transition: all 0.3s ease; position: relative; overflow: hidden; }
.sa-complete .level-card.achieved { border: 2px solid #10b981; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15); }
.sa-complete .level-card.development-focus { border: 2px solid #f59e0b; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15); }
.sa-complete .level-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; }
.sa-complete .level-card.achieved::before { background: linear-gradient(90deg, #10b981, #059669); }
.sa-complete .level-card.development-focus::before { background: linear-gradient(90deg, #f59e0b, #d97706); }

.sa-complete .level-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.sa-complete .level-name { margin: 0; color: #374151; font-size: 1.1em; font-weight: 600; }


.sa-complete .level-metrics { text-align: center; }
.sa-complete .percentage-display { margin-bottom: 12px; }
.sa-complete .percentage-value { font-size: 2em; font-weight: 700; }
.sa-complete .level-card.achieved .percentage-value { color: #059669; }
.sa-complete .level-card.development-focus .percentage-value { color: #d97706; }

.sa-complete .points-display { margin-bottom: 16px; color: #6b7280; font-size: 1em; }
.sa-complete .points-earned { font-weight: 600; color: #374151; }
.sa-complete .points-separator { margin: 0 4px; }
.sa-complete .points-total { font-weight: 500; }
.sa-complete .points-label { margin-left: 4px; font-size: 0.9em; }

.sa-complete .level-status { padding: 8px 12px; border-radius: 6px; font-size: 0.85em; font-weight: 500; }
.sa-complete .level-status.achieved { background: #f0f9f4; color: #065f46; }
.sa-complete .level-status.development { background: #fffbeb; color: #92400e; }

@media (max-width:600px){ 
  .sa-complete { padding: 0 6px; } 
  .sa-complete .actions { flex-direction:column; gap:8px; }
  .sa-complete .main-actions { flex-direction: column; }
  .sa-complete .main-actions .btn { width: 100%; max-width: none; }
  .sa-complete .dropdown-container { width: 100%; }
  .sa-complete .dropdown-trigger { width: 100%; justify-content: center; }
  .sa-complete .link-row { flex-direction:column; gap:8px; }
  .sa-complete .link-input { max-width:100%; }
  .sa-complete .levels-grid { grid-template-columns: 1fr; }
  .sa-complete .competency-status { padding: 16px; }
  .sa-complete .percentage-value { font-size: 1.6em; }
}

/* Debug: Force all button styles to be visible */
.sa-complete .main-actions .btn {
  background: red !important;
  color: white !important;
  border: 2px solid red !important;
  padding: 12px 20px !important;
  text-decoration: none !important;
  display: inline-flex !important;
  align-items: center !important;
  border-radius: 8px !important;
}

.sa-complete .btn-secondary {
  background: #6b7280 !important;
  color: white !important;
  border-color: #6b7280 !important;
}
</style>

    <div class="sa-complete">
        <input type="hidden" name="uuid" value="{{ $uuid }}">
        
        <div class="center" style="margin-bottom: 12px;">
            <h1><i class="fas fa-check-circle success-icon"></i>Ocena zakończona</h1>
            <p class="lead">Dziękujemy za wypełnienie formularza samooceny.</p>
        </div>

        @if(isset($assessmentSummary))
        <div class="assessment-summary-section">
            <div class="card assessment-results-card">
                <div class="card-body">
                    <!-- Current competency status -->
                    <div class="competency-status">
                        <div class="status-content">
                            <span class="status-text">Obecny poziom kompetencji:</span>
                            <span class="level-name">{{ $assessmentSummary['achievedLevel'] }}</span>
                        </div>
                    </div>

                    <!-- Level details -->
                        
                        @php
                            // Define thresholds for each level
                            $thresholds = [
                                1 => 80, // Junior ≥80%
                                2 => 85, // Specjalista ≥85%
                                3 => 85, // Senior ≥85%
                                4 => 80, // Supervisor ≥80%
                                5 => 80, // Manager ≥80%
                            ];
                        @endphp
                        
                        <div class="levels-grid">
                            @foreach($assessmentSummary['displayLevels'] as $levelNumber => $summary)
                            @php
                                // Extract numeric level from levelNumber
                                preg_match('/^(\d+)/', $levelNumber, $matches);
                                $numericLevel = isset($matches[1]) ? (int)$matches[1] : 1;
                                $requiredThreshold = $thresholds[$numericLevel] ?? 50;
                                $isAchieved = $summary['percentage'] >= $requiredThreshold;
                            @endphp
                            <div class="level-card {{ $isAchieved ? 'achieved' : 'development-focus' }}">
                                <div class="level-header">
                                    <h4 class="level-name">{{ $summary['levelName'] }}</h4>
                                </div>
                                
                                <div class="level-metrics">
                                    <div class="percentage-display">
                                        <span class="percentage-value">{{ $summary['percentage'] }}%</span>
                                    </div>
                                    
                                    <div class="points-display">
                                        <span class="points-earned">{{ $summary['earnedPoints'] }}</span>
                                        <span class="points-separator">/</span>
                                        <span class="points-total">{{ $summary['maxPoints'] }}</span>
                                        <span class="points-label">pkt</span>
                                    </div>
                                    
                                    @if($isAchieved)
                                        <div class="level-status achieved">
                                            Obecny poziom kompetencji
                                        </div>
                                    @else
                                        <div class="level-status development">
                                            Obszar rozwoju - wymagane {{ $requiredThreshold }}%
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Actions - Clean and focused -->
        <div class="actions main-actions">
            <div class="dropdown-container">
                <button onclick="toggleDownload()" class="btn btn-primary dropdown-trigger" id="downloadBtn">
                    <i class="fas fa-download"></i> Pobierz
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </button>
                <div class="dropdown-menu" id="downloadMenu" style="display: none;">
                    <a href="{{ route('self.assessment.generate_pdf', ['uuid' => $uuid]) }}" target="_blank" class="dropdown-item">
                        <i class="fas fa-file-pdf"></i> Pobierz w PDF
                    </a>
                    <a href="{{ route('self.assessment.generate_xls', ['uuid' => $uuid]) }}" target="_blank" class="dropdown-item">
                        <i class="fas fa-file-excel"></i> Pobierz w XLS
                    </a>
                </div>
            </div>
            <a href="{{ route('form.edit', ['uuid' => $uuid]) }}" class="btn btn-secondary">
                <i class="fas fa-edit"></i> Edytuj odpowiedzi
            </a>
        </div>

        <!-- Additional Options - Clean toggle for link -->
        <div class="additional-options">
            <button onclick="toggleLinkSection()" class="btn-link" id="linkToggleBtn">
                <i class="fas fa-link"></i> Kopiuj link do edycji
            </button>
        </div>

        <!-- Collapsible Link Section -->
        <div class="card link-section" id="linkSection" style="display: none;">
            <div class="edit-section">
                <p><i class="fas fa-info-circle"></i> Link umożliwia edycję odpowiedzi do czasu zamknięcia okna samooceny.</p>
                <div class="link-row">
                    <input type="text" id="editLink" value="{{ route('form.edit', ['uuid' => $uuid]) }}" readonly class="link-input">
                    <button onclick="copyToClipboard()" class="btn btn-secondary">
                        <i class="fas fa-copy"></i> Kopiuj
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="navigation-section">
            <a href="{{ url('/') }}" class="btn btn-text">
                <i class="fas fa-home"></i> Powrót do strony głównej
            </a>
        </div>
        
    </div> <!-- End sa-complete -->
@endsection

@section('scripts')
<script>
function copyToClipboard(){
    var el = document.getElementById('editLink');
    el.select();
    el.setSelectionRange(0, 99999);
    
    // Modern clipboard API with fallback
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(el.value).then(function() {
            showCopyFeedback();
        });
    } else {
        // Fallback for older browsers
        document.execCommand('copy');
        showCopyFeedback();
    }
}

function showCopyFeedback() {
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i> Skopiowano';
    btn.style.background = '#10b981';
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.style.background = '';
    }, 2000);
}

function toggleDownload() {
    const downloadMenu = document.getElementById('downloadMenu');
    const downloadBtn = document.getElementById('downloadBtn');
    
    if (downloadMenu.style.display === 'none' || downloadMenu.style.display === '') {
        downloadMenu.style.display = 'block';
        downloadBtn.classList.add('active');
    } else {
        downloadMenu.style.display = 'none';
        downloadBtn.classList.remove('active');
    }
}

function toggleLinkSection() {
    const linkSection = document.getElementById('linkSection');
    const toggleBtn = document.getElementById('linkToggleBtn');
    
    if (linkSection.style.display === 'none' || linkSection.style.display === '') {
        linkSection.style.display = 'block';
        toggleBtn.innerHTML = '<i class="fas fa-link"></i> Ukryj link';
    } else {
        linkSection.style.display = 'none';
        toggleBtn.innerHTML = '<i class="fas fa-link"></i> Kopiuj link do edycji';
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.dropdown-container');
    const downloadMenu = document.getElementById('downloadMenu');
    const downloadBtn = document.getElementById('downloadBtn');
    
    if (dropdown && !dropdown.contains(event.target)) {
        downloadMenu.style.display = 'none';
        downloadBtn.classList.remove('active');
    }
});
</script>
@endsection

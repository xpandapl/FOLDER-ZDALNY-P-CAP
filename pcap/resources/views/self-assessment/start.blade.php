@extends('layouts.self-assessment')

@section('content')
<style>
/* Scoped styles for start view */
.sa-start { max-width: 960px; margin: 0 auto; }
.sa-start .lead { color:#4b5563; margin-top:6px; }
.sa-start .choices { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:16px; margin: 18px 0 26px; }
.sa-start .card { border:1px solid #e5e7eb; border-radius:14px; padding:16px; background:#fff; box-shadow:0 4px 14px rgba(15,23,42,.06); transition: box-shadow .15s, border-color .15s; }
.sa-start .card:hover { box-shadow:0 8px 24px rgba(15,23,42,.10); border-color:#d1d5db; }
.sa-start .card h3 { margin:0 0 6px; font-size:18px; color:#111827; }
.sa-start .card p { margin:0; color:#374151; }
.sa-start .card .btn { margin-top:10px; }

/* Legend */
.sa-start .legend { margin-top:8px; }
.sa-start .legend h2 { font-size:16px; margin:0 0 8px; color:#111827; }
.sa-start .legend-grid { display:grid; grid-template-columns: repeat(6, minmax(0,1fr)); gap:10px; }
.sa-start .legend-item { border:1px solid #e5e7eb; border-radius:10px; padding:10px; background:#fff; text-align:center; }
.sa-start .legend-item .num { font-weight:700; color:#111827; }
.sa-start .legend-item .lbl { color:#4b5563; font-size:12px; margin-top:4px; }

@media (max-width: 760px){
  .sa-start .choices { grid-template-columns: 1fr; }
  .sa-start .legend-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
}

@media (max-width: 500px){
  .sa-start .legend-grid { grid-template-columns: repeat(3, minmax(0,1fr)); gap: 8px; }
  .sa-start .legend-item { padding: 8px; }
  .sa-start .legend-item .num { font-size: 14px; }
  .sa-start .legend-item .lbl { font-size: 10px; }
}

/* Onboarding Demo Styles */
.onboarding-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.onboarding-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.onboarding-header h2 {
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.onboarding-header p {
    opacity: 0.9;
    margin-bottom: 1rem;
}

.demo-toggle {
    background: rgba(255,255,255,0.2);
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    transition: all 0.3s ease;
}

        .demo-toggle:hover:not(:disabled) {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-2px);
            color: white;
        }
        
        .demo-toggle:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.2);
        }
        
        .wip-notice {
            margin-top: 0.5rem;
            text-align: center;
        }
        
        .wip-notice small {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }.demo-container {
    margin-top: 2rem;
    background: white;
    color: #333;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
}

.demo-question {
    position: relative;
}

.question-demo {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    border: 2px solid #e9ecef;
    position: relative;
}

.highlight-badge {
    animation: pulse-glow 2s infinite;
    position: relative;
    z-index: 10;
}

@keyframes pulse-glow {
    0%, 100% { 
        box-shadow: 0 0 5px rgba(102, 126, 234, 0.5);
        transform: scale(1);
    }
    50% { 
        box-shadow: 0 0 20px rgba(102, 126, 234, 0.8);
        transform: scale(1.05);
    }
}

.demo-rating .rating-option {
    transition: all 0.3s ease;
}

.demo-rating .rating-option.highlight {
    animation: rating-highlight 1.5s infinite;
}

@keyframes rating-highlight {
    0%, 100% { 
        background: transparent;
        transform: scale(1);
    }
    50% { 
        background: rgba(40, 167, 69, 0.1);
        transform: scale(1.02);
    }
}

        .demo-explanation {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }
        
        /* Global overlay for settings button highlight */
        .demo-global-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 9999;
            pointer-events: none;
            display: none;
        }
        
        .demo-global-overlay.active {
            display: block;
        }
        
        .settings-highlight {
            position: absolute;
            background: rgba(102, 126, 234, 0.2);
            border: 3px solid #667eea;
            border-radius: 15px;
            animation: settings-pulse 2s infinite;
        }
        
        @keyframes settings-pulse {
            0%, 100% { 
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
                transform: scale(1);
            }
            50% { 
                box-shadow: 0 0 0 15px rgba(102, 126, 234, 0);
                transform: scale(1.05);
            }
        }.explanation-box {
    position: relative;
    height: 100%;
}

        .pulse-dot {
            position: absolute;
            width: 15px;
            height: 15px;
            background: #dc3545;
            border-radius: 50%;
            animation: pulse-dot 2s infinite;
            z-index: 20;
            transform: translate(-50%, -50%);
        }@keyframes pulse-dot {
    0% {
        transform: scale(0.8);
        opacity: 1;
    }
    50% {
        transform: scale(1.2);
        opacity: 0.7;
    }
    100% {
        transform: scale(0.8);
        opacity: 1;
    }
}

        .tooltip {
            position: absolute;
            background: #333;
            color: white;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.9rem;
            min-width: 180px;
            max-width: 280px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 25;
            opacity: 0;
            transform: translateY(10px);
            animation: fade-in-up 0.5s ease forwards;
            white-space: nowrap;
        }
        
        .tooltip::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid #333;
        }

@keyframes fade-in-up {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.demo-controls {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.demo-btn {
    transition: all 0.3s ease;
}

.demo-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.demo-step {
    display: none;
}

.demo-step.active {
    display: block;
    animation: slide-in 0.5s ease;
}

@keyframes slide-in {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Demo question specific styles - skopiowane z form.blade.php */
.question-header {
    margin-bottom: 1rem;
}

.badge-container {
    margin-bottom: 0.5rem;
}

.badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-right: 0.5rem;
    display: inline-block;
}

.badge.competency.osobiste {
    background: #e3f2fd;
    color: #1976d2;
}

.badge.level {
    background: #f3e5f5;
    color: #7b1fa2;
}

.assessment-subheader {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.competency-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
    display: block;
    margin-bottom: 1rem;
}

/* Poprzednia ocena badge */
.prev-badge {
    color: #1976d2;
    font-size: 13px;
    font-weight: 600;
    background: rgba(25, 118, 210, 0.1);
    padding: 4px 8px;
    border-radius: 12px;
    display: inline-block;
    margin-bottom: 10px;
}

/* Rating dots - dokładnie jak w form.blade.php */
.rating-dots {
    padding-top: 8px;
    padding-bottom: 10px;
    margin-top: 6px;
    margin-bottom: 8px;
}

.rating-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 10px 0;
    width: 100%;
    padding: 4px 8px;
}

.rating-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.rating-label {
    font-size: 12px;
    color: #666;
    text-align: center;
    line-height: 1.2;
    min-height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.rating-label.active {
    color: #1976d2;
    font-weight: 600;
}

.rating-label.prev {
    color: #1976d2;
    font-weight: 500;
    background: rgba(25, 118, 210, 0.1);
    padding: 2px 4px;
    border-radius: 4px;
}

.dot {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #ddd;
    border: 2px solid #bdbdbd;
    cursor: pointer;
    position: relative;
    transition: all 0.2s ease;
}

.dot.selected {
    background: #1976d2;
    border-color: #1565c0;
}

.dot.prev {
    outline: 2px dashed #1976d2;
}

.dot.star {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    line-height: 1;
    border: 2px solid #bdbdbd;
    background: #ddd;
    color: #9e9e9e;
    position: relative;
}

.dot.star:not(.selected)::after {
    content: '⭐';
    opacity: 0.3;
    font-size: 14px;
}

.dot.star.selected {
    background: #ffd54f;
    border-color: #f9a825;
    color: #7a5900;
}

.dot.star.selected::after {
    content: '⭐';
    opacity: 1;
    font-size: 16px;
}

/* Settings preview panel */
.demo-settings-preview {
    animation: slide-in 0.5s ease;
}

.settings-preview-container {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 12px 32px rgba(15,23,42,.16);
    padding: 15px;
    max-width: 320px;
    margin: 0 auto;
}

.preview-header {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 12px;
    font-size: 14px;
}

.preview-header i {
    color: #6b7280;
}

.preview-content {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.preview-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.preview-label {
    font-size: 12px;
    color: #6b7280;
    font-weight: 600;
}

.preview-flags {
    display: flex;
    gap: 6px;
}

.preview-flag {
    width: 24px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.preview-flag.active {
    background: rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

.preview-flag:hover {
    background: rgba(156, 163, 175, 0.1);
}

.preview-sizes {
    display: flex;
    gap: 4px;
}

.preview-size {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s ease;
    border: 1px solid #e5e7eb;
    background: white;
}

.preview-size:nth-child(1) { font-size: 12px; }
.preview-size:nth-child(2) { font-size: 14px; }
.preview-size:nth-child(3) { font-size: 16px; }

.preview-size.active {
    background: #3b82f6;
    color: white;
    border-color: #2563eb;
}

.preview-size:hover:not(.active) {
    background: #f3f4f6;
}

.preview-divider {
    width: 100%;
    height: 1px;
    background: #e5e7eb;
}

/* Chat messages - skopiowane z form.blade.php */
.chat-message {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
    max-width: 85%;
}

.chat-message.manager-message {
    margin-right: auto;
}

.message-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.manager-message .message-avatar {
    background: #ecfdf5;
    color: #059669;
}

.message-content {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 10px 12px;
    flex: 1;
    min-width: 0;
    max-width: 100%;
}

.manager-message .message-content {
    background: #ecfdf5;
    border-color: #bbf7d0;
}

.message-header {
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 4px;
    opacity: 0.8;
}

.message-text {
    font-size: 14px;
    line-height: 1.5;
    white-space: pre-wrap;
}

.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .onboarding-section {
        padding: 1.5rem;
    }
    
    .demo-container {
        padding: 1rem;
    }
    
    .demo-controls {
        flex-direction: column;
        align-items: center;
    }
    
    .tooltip {
        min-width: 150px;
        font-size: 0.8rem;
        position: fixed !important;
        top: 10px !important;
        left: 10px !important;
        right: 10px !important;
        width: auto !important;
        max-width: calc(100vw - 20px);
        white-space: normal !important;
        text-align: center;
    }
    
    .tooltip::before {
        display: none;
    }
    
    .pulse-dot {
        display: none !important;
    }
    
    .rating-grid {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 4px 4px;
    }
    
    .rating-col {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    
    .rating-label {
        min-height: auto;
        margin: 0;
        flex: 1;
        text-align: left;
        justify-content: flex-start;
    }
    
    .chat-message {
        max-width: 95%;
    }
    
    .message-avatar {
        width: 32px;
        height: 32px;
    }
}
</style>

    <div class="sa-start">
        <div class="center" style="margin-bottom: 6px;">
            <h1>Start samooceny @if($cycle) <span style="color: var(--primary)">({{ $cycle->label }})</span> @endif</h1>
            <p class="lead">Wybierz odpowiednią ścieżkę. Jeśli wypełniałeś/aś samoocenę w poprzednim cyklu – skorzystaj z opcji <strong>Weteran</strong>. Nowe osoby wybierają <strong>Świeżak</strong>.</p>
        </div>

        <div class="choices">
            <div class="card">
                <h3>Świeżak</h3>
                <p>Pierwsza samoocena lub nowa rola. Zaczynasz od krótkiego formularza startowego, a następnie przechodzisz przez pytania poziom po poziomie.</p>
                <a href="{{ route('self.assessment.step1') }}" class="btn btn-primary">Rozpocznij jako Świeżak</a>
            </div>
            <div class="card">
                <h3>Weteran</h3>
                <p>Masz już wyniki z poprzedniego cyklu. Wpisz kod dostępu, aby wczytać poprzednie odpowiedzi i dokończyć aktualną samoocenę.</p>
                <a href="{{ route('start.veteran.form') }}" class="btn btn-secondary">Mam kod – przejdź</a>
            </div>
        </div>

        <!-- Interactive Onboarding Demo -->
        <div class="onboarding-section">
            <div class="onboarding-header">
                <h2>💡 Jak wypełniać formularz? - Interaktywna demonstracja</h2>
                <p>Kliknij <strong>"Zobacz demo"</strong> aby zobaczyć jak wygląda przykładowe pytanie i jak je wypełnić.</p>
                <button class="btn btn-secondary demo-toggle" onclick="toggleDemo()" disabled>
                    <i class="fas fa-play"></i> Zobacz demo
                </button>
                <div class="wip-notice">
                    <small style="color: rgba(255,255,255,0.7); font-style: italic;">
                        <i class="fas fa-wrench"></i> Work in progress - dopracowujemy funkcjonalność
                    </small>
                </div>
            </div>
            
            <div class="demo-container" id="demoContainer" style="display: none;">
                <!-- Global overlay for highlighting elements outside demo container -->
                <div class="demo-global-overlay" id="globalOverlay">
                    <div class="settings-highlight" id="settingsHighlight"></div>
                    <div class="tooltip" id="globalTooltip" style="position: fixed; z-index: 10001;"></div>
                </div>
                
                <div class="demo-question">
                    <div class="demo-step" id="step1">
                        <div class="question-demo">
                            <div class="question-header">
                                <div class="badge-container">
                                    <span class="badge competency osobiste highlight-badge">Osobiste</span>
                                    <span class="badge level">Junior</span>
                                </div>
                            </div>
                            <div class="assessment-subheader">Jak oceniasz swoją kompetencję/cechę:</div>
                            <label class="competency-name">Komunikacja pisemna</label>
                            
                            <!-- Informacja o poprzedniej ocenie (przykład) -->
                            <span class="prev-badge"><i class="fa fa-history"></i> Poprzednio: 0.5 (Wymaga rozwoju)</span>
                            
                            <div class="rating-dots demo-rating" role="radiogroup" aria-label="Ocena">
                                <div class="rating-grid">
                                    <div class="rating-col">
                                        <div class="rating-label" data-value="0">Nie dotyczy</div>
                                        <button type="button" class="dot" data-value="0" title="Nie dotyczy">
                                            <span class="sr-only">Nie dotyczy</span>
                                        </button>
                                    </div>
                                    <div class="rating-col">
                                        <div class="rating-label" data-value="0.25">Poniżej oczekiwań</div>
                                        <button type="button" class="dot" data-value="0.25" title="Poniżej oczekiwań">
                                            <span class="sr-only">Poniżej oczekiwań</span>
                                        </button>
                                    </div>
                                    <div class="rating-col">
                                        <div class="rating-label prev" data-value="0.5">Wymaga rozwoju</div>
                                        <button type="button" class="dot prev" data-value="0.5" title="Wymaga rozwoju">
                                            <span class="sr-only">Wymaga rozwoju</span>
                                        </button>
                                    </div>
                                    <div class="rating-col">
                                        <div class="rating-label" data-value="0.75">Blisko oczekiwań</div>
                                        <button type="button" class="dot" data-value="0.75" title="Blisko oczekiwań">
                                            <span class="sr-only">Blisko oczekiwań</span>
                                        </button>
                                    </div>
                                    <div class="rating-col">
                                        <div class="rating-label" data-value="1">Spełnia oczekiwania</div>
                                        <button type="button" class="dot" data-value="1" title="Spełnia oczekiwania">
                                            <span class="sr-only">Spełnia oczekiwania</span>
                                        </button>
                                    </div>
                                    <div class="rating-col">
                                        <div class="rating-label" data-above="1">Powyżej oczekiwań</div>
                                        <button type="button" class="dot star" data-value="1" data-above="1" title="Powyżej oczekiwań">
                                            <span class="sr-only">Powyżej oczekiwań</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Przykład feedbacku managera -->
                            <div class="demo-feedback" style="margin-top: 15px;">
                                <div class="chat-message manager-message">
                                    <div class="message-avatar">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="message-content">
                                        <div class="message-header">Feedback od twojego przełożonego:</div>
                                        <div class="message-text">Widzę postęp w tej kompetencji. Kontynuuj pracę nad komunikacją pisemną, szczególnie w zakresie strukturyzowania treści.</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Podgląd panelu ustawień -->
                            <div class="demo-settings-preview" id="settingsPreview" style="display: none; margin-top: 20px;">
                                <div class="settings-preview-container">
                                    <div class="preview-header">
                                        <i class="fas fa-cog"></i>
                                        <span>Panel ustawień (podgląd)</span>
                                    </div>
                                    <div class="preview-content">
                                        <div class="preview-section">
                                            <span class="preview-label">Język:</span>
                                            <div class="preview-flags">
                                                <span class="preview-flag active">🇵🇱</span>
                                                <span class="preview-flag">🇬🇧</span>
                                                <span class="preview-flag">🇺🇦</span>
                                                <span class="preview-flag">🇪🇸</span>
                                                <span class="preview-flag">🇵🇹</span>
                                                <span class="preview-flag">🇫🇷</span>
                                            </div>
                                        </div>
                                        <div class="preview-divider"></div>
                                        <div class="preview-section">
                                            <span class="preview-label">Wielkość tekstu:</span>
                                            <div class="preview-sizes">
                                                <span class="preview-size active">A</span>
                                                <span class="preview-size">A</span>
                                                <span class="preview-size">A</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="demo-explanation" id="demoExplanation">
                                <div class="explanation-box">
                                    <div class="pulse-dot" id="pulseDot1"></div>
                                    <div class="tooltip" id="tooltip1">
                                        <strong>Kategoria kompetencji</strong><br>
                                        Osobiste, Społeczne, Zawodowe, Liderskie
                                    </div>
                                </div>
                            </div>
                            
                            <div class="demo-controls">
                                <button class="btn btn-text demo-btn" onclick="prevStep()" id="prevBtn" style="opacity: 0.5;" disabled>Poprzedni</button>
                                <button class="btn btn-primary demo-btn" onclick="nextStep()" id="nextBtn">Następny</button>
                                <button class="btn btn-secondary demo-btn" onclick="resetDemo()" id="resetBtn">Resetuj demo</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="legend">
            <h2>Skala ocen</h2>
            <div class="legend-grid">
                <div class="legend-item">
                    <div class="num">0</div>
                    <div class="lbl">Nie dotyczy</div>
                </div>
                <div class="legend-item">
                    <div class="num">0.25</div>
                    <div class="lbl">Poniżej oczekiwań</div>
                </div>
                <div class="legend-item">
                    <div class="num">0.5</div>
                    <div class="lbl">Wymaga rozwoju</div>
                </div>
                <div class="legend-item">
                    <div class="num">0.75</div>
                    <div class="lbl">Blisko oczekiwań</div>
                </div>
                <div class="legend-item">
                    <div class="num">1.0</div>
                    <div class="lbl">Spełnia oczekiwania</div>
                </div>
                <div class="legend-item">
                    <div class="num">1+</div>
                    <div class="lbl">Powyżej oczekiwań</div>
                </div>
            </div>
        </div>
    </div>

<script>
let currentStep = 0;
let demoVisible = false;

const demoSteps = [
    {
        title: "Przycisk ustawień",
        explanation: "W prawym górnym rogu znajdziesz przycisk ustawień (ikonka zębatki). Po kliknięciu otwiera się panel z opcjami zmiany języka (🇵🇱🇬🇧🇺🇦🇪🇸🇵🇹🇫🇷) oraz rozmiar tekstu (mały/średni/duży).",
        highlight: "#settingsBtn",
        tooltip: { element: "#settingsBtn", text: "Przycisk ustawień<br>• Zmień język (6 opcji)<br>• Dostosuj rozmiar tekstu<br>• Ustawienia są zapamiętywane", position: { top: "80px", right: "10px" } },
        action: () => {
            // Dodaj dodatkowe animacje dla lepszego wyróżnienia
            setTimeout(() => {
                const settingsBtn = document.querySelector('#settingsBtn');
                const settingsContainer = document.querySelector('.settings-container');
                if (settingsBtn && settingsContainer) {
                    settingsBtn.style.animation = 'pulse-glow 2s infinite';
                    settingsContainer.style.position = 'relative';
                    settingsContainer.style.zIndex = '10000';
                }
            }, 500);
        }
    },
    {
        title: "Kategorie kompetencji",
        explanation: "Każda kompetencja jest przypisana do kategorii: Osobiste, Społeczne, Zawodowe lub Liderskie",
        highlight: ".badge-container",
        tooltip: { element: ".badge-container", text: "Kategoria kompetencji<br>Określa obszar rozwoju" }
    },
    {
        title: "Opcje w ustawieniach",
        explanation: "W panelu ustawień możesz wybrać jeden z 6 języków oraz dostosować rozmiar tekstu do swoich potrzeb. Ustawienia są automatycznie zapisywane.",
        highlight: ".demo-settings-preview",
        tooltip: { element: ".demo-settings-preview", text: "Tak wygląda panel ustawień<br>Kliknij 🇵🇱 aby zmienić język" },
        action: () => {
            setTimeout(() => {
                const preview = document.getElementById('settingsPreview');
                if (preview) {
                    preview.style.display = 'block';
                }
            }, 500);
        }
    },
    {
        title: "Nazwa kompetencji",
        explanation: "To jest konkretna umiejętność lub cecha, którą oceniasz",
        highlight: ".competency-name",
        tooltip: { element: ".competency-name", text: "Konkretna kompetencja<br>do samooceny" },
        action: () => {
            const preview = document.getElementById('settingsPreview');
            if (preview) {
                preview.style.display = 'none';
            }
        }
    },
    {
        title: "Poprzednia ocena",
        explanation: "Jeśli wypełniałeś już ocenę wcześniej, zobaczysz swoją poprzednią odpowiedź",
        highlight: ".prev-badge",
        tooltip: { element: ".prev-badge", text: "Twoja poprzednia ocena<br>z ostatniego cyklu" }
    },
    {
        title: "Skala oceny",
        explanation: "Wybierz wartość od 0 (nie dotyczy) do 1.0 (spełnia oczekiwania). Poprzednia ocena jest podświetlona.",
        highlight: ".rating-grid",
        tooltip: { element: ".rating-grid", text: "Skala oceny 0-1.0<br>Niebieska ramka = poprzednia ocena" }
    },
    {
        title: "Ocena powyżej oczekiwań",
        explanation: "Jeśli uważasz, że twoja kompetencja jest wyjątkowo wysoka, możesz wybrać ostatnią opcję",
        highlight: ".dot.star",
        tooltip: { element: ".dot.star", text: "Powyżej oczekiwań<br>Dla wyjątkowych kompetencji" }
    },
    {
        title: "Feedback managera",
        explanation: "Jeśli manager dodał komentarz do poprzedniej oceny, zobaczysz go w zielonym dymku",
        highlight: ".chat-message.manager-message",
        tooltip: { element: ".chat-message.manager-message", text: "Feedback od przełożonego<br>z poprzedniego cyklu" }
    },
    {
        title: "Jak wypełnić?",
        explanation: "Kliknij na kropkę aby wybrać nową ocenę. Możesz też dodać komentarz uzasadniający swoją ocenę.",
        highlight: ".dot[data-value='0.75']",
        tooltip: { element: ".dot[data-value='0.75']", text: "Kliknij aby wybrać<br>nową ocenę" },
        action: () => {
            setTimeout(() => {
                const dot = document.querySelector('.dot[data-value="0.75"]');
                const label = document.querySelector('.rating-label[data-value="0.75"]');
                if (dot && label) {
                    dot.classList.add('selected');
                    label.classList.add('active');
                    dot.classList.add('highlight');
                }
            }, 1000);
        }
    },
    {
        title: "Gotowe!",
        explanation: "W prawdziwym formularzu wypełnisz wszystkie kompetencje i będziesz mógł dodać komentarze. Po zakończeniu zobaczysz podsumowanie swojego poziomu.",
        highlight: null,
        tooltip: null
    }
];

function toggleDemo() {
    const container = document.getElementById('demoContainer');
    const button = document.querySelector('.demo-toggle');
    
    demoVisible = !demoVisible;
    
    if (demoVisible) {
        container.style.display = 'block';
        button.innerHTML = '<i class="fas fa-times"></i> Zamknij demo';
        currentStep = 0;
        showStep(0);
    } else {
        container.style.display = 'none';
        button.innerHTML = '<i class="fas fa-play"></i> Zobacz demo';
        resetDemo();
    }
}

function showStep(stepIndex) {
    const step = demoSteps[stepIndex];
    if (!step) return;
    
    // Clear previous highlights and tooltips
    clearHighlights();
    clearTooltips();
    
    // Update buttons
    updateButtons();
    
    // Handle global elements (like settings button)
    if (step.highlight === "#settingsBtn") {
        showGlobalHighlight(step);
    } else {
        // Hide global overlay
        const overlay = document.getElementById('globalOverlay');
        if (overlay) overlay.classList.remove('active');
        
        // Add highlight if specified
        if (step.highlight) {
            const element = document.querySelector(step.highlight);
            if (element) {
                element.classList.add('highlight');
            }
        }
        
        // Add tooltip if specified
        if (step.tooltip) {
            showTooltip(step.tooltip);
        }
    }
    
    // Execute action if specified
    if (step.action) {
        step.action();
    }
    
    // Update step indicator (if you want to add one later)
    updateStepInfo(step);
}

function showGlobalHighlight(step) {
    const element = document.querySelector(step.highlight);
    const overlay = document.getElementById('globalOverlay');
    const highlight = document.getElementById('settingsHighlight');
    const tooltip = document.getElementById('globalTooltip');
    
    if (!element || !overlay || !highlight || !tooltip) return;
    
    // Show overlay
    overlay.classList.add('active');
    
    // Position highlight over the target element
    const rect = element.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
    
    highlight.style.top = (rect.top + scrollTop - 5) + 'px';
    highlight.style.left = (rect.left + scrollLeft - 5) + 'px';
    highlight.style.width = (rect.width + 10) + 'px';
    highlight.style.height = (rect.height + 10) + 'px';
    
    // Position and show tooltip
    if (step.tooltip) {
        tooltip.innerHTML = step.tooltip.text;
        tooltip.style.display = 'block';
        
        // Position tooltip below and to the left of the settings button
        const tooltipLeft = rect.left + scrollLeft - 200; // Move left by 200px
        const tooltipTop = rect.bottom + scrollTop + 10; // Below the button with 10px gap
        
        tooltip.style.left = Math.max(10, tooltipLeft) + 'px'; // Don't go beyond left edge
        tooltip.style.top = tooltipTop + 'px';
        tooltip.style.right = 'auto';
    }
}

function showTooltip(tooltipConfig) {
    const tooltip = document.getElementById('tooltip1');
    const pulseDot = document.getElementById('pulseDot1');
    const targetElement = document.querySelector(tooltipConfig.element);
    
    if (!tooltip || !pulseDot || !targetElement) {
        console.log('Missing elements:', { tooltip: !!tooltip, pulseDot: !!pulseDot, targetElement: !!targetElement, selector: tooltipConfig.element });
        return;
    }
    
    // Set tooltip content
    tooltip.innerHTML = tooltipConfig.text;
    tooltip.style.display = 'block';
    
    // Get the demo explanation container (our positioning context)
    const explanationBox = document.querySelector('.explanation-box');
    if (!explanationBox) return;
    
    // Get positions relative to the demo container
    const demoContainer = document.querySelector('.question-demo');
    const targetRect = targetElement.getBoundingClientRect();
    const containerRect = demoContainer.getBoundingClientRect();
    
    // Calculate center point of target element
    const targetCenterX = targetRect.left - containerRect.left + targetRect.width / 2;
    const targetCenterY = targetRect.top - containerRect.top + targetRect.height / 2;
    
    // Position pulse dot at the center of target element
    pulseDot.style.display = 'block';
    pulseDot.style.left = targetCenterX + 'px';
    pulseDot.style.top = targetCenterY + 'px';
    
    // Position tooltip based on target location
    let tooltipLeft = targetCenterX;
    let tooltipTop = targetCenterY + 30; // Below the target
    
    // Adjust for different elements
    if (tooltipConfig.element.includes('badge')) {
        tooltipTop = targetCenterY - 50; // Above badges
        tooltipLeft = targetCenterX - 90;
    } else if (tooltipConfig.element.includes('competency-name')) {
        tooltipTop = targetCenterY + 40;
        tooltipLeft = targetCenterX - 90;
    } else if (tooltipConfig.element.includes('prev-badge')) {
        tooltipTop = targetCenterY - 50;
        tooltipLeft = targetCenterX - 90;
    } else if (tooltipConfig.element.includes('rating-grid')) {
        tooltipTop = targetCenterY - 30;
        tooltipLeft = Math.max(10, targetCenterX - 100);
    } else if (tooltipConfig.element.includes('dot')) {
        tooltipTop = targetCenterY - 50;
        tooltipLeft = targetCenterX - 80;
    } else if (tooltipConfig.element.includes('chat-message')) {
        tooltipTop = targetCenterY + 60;
        tooltipLeft = Math.max(10, targetCenterX - 100);
    }
    
    // Make sure tooltip doesn't go outside container
    tooltipLeft = Math.max(10, Math.min(tooltipLeft, containerRect.width - 200));
    tooltipTop = Math.max(10, tooltipTop);
    
    tooltip.style.left = tooltipLeft + 'px';
    tooltip.style.top = tooltipTop + 'px';
}

function clearHighlights() {
    const highlighted = document.querySelectorAll('.highlight');
    highlighted.forEach(el => el.classList.remove('highlight'));
}

function clearTooltips() {
    const tooltip = document.getElementById('tooltip1');
    const pulseDot = document.getElementById('pulseDot1');
    const globalTooltip = document.getElementById('globalTooltip');
    const globalOverlay = document.getElementById('globalOverlay');
    
    if (tooltip) tooltip.style.display = 'none';
    if (pulseDot) pulseDot.style.display = 'none';
    if (globalTooltip) globalTooltip.style.display = 'none';
    if (globalOverlay) globalOverlay.classList.remove('active');
}

function updateButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (prevBtn) {
        prevBtn.disabled = currentStep === 0;
        prevBtn.style.opacity = currentStep === 0 ? '0.5' : '1';
    }
    
    if (nextBtn) {
        nextBtn.textContent = currentStep === demoSteps.length - 1 ? 'Zakończ demo' : 'Następny';
    }
}

function updateStepInfo(step) {
    // You could add a step counter or title display here
    console.log(`Step ${currentStep + 1}: ${step.title}`);
}

function nextStep() {
    if (currentStep < demoSteps.length - 1) {
        currentStep++;
        showStep(currentStep);
    } else {
        // End demo
        toggleDemo();
    }
}

function prevStep() {
    if (currentStep > 0) {
        currentStep--;
        showStep(currentStep);
    }
}

function resetDemo() {
    currentStep = 0;
    clearHighlights();
    clearTooltips();
    
    // Reset form
    const dots = document.querySelectorAll('.demo-rating .dot');
    const labels = document.querySelectorAll('.demo-rating .rating-label');
    dots.forEach(dot => dot.classList.remove('selected'));
    labels.forEach(label => label.classList.remove('active'));
    
    // Hide settings preview
    const preview = document.getElementById('settingsPreview');
    if (preview) {
        preview.style.display = 'none';
    }
    
    // Reset settings button animation
    const settingsBtn = document.querySelector('#settingsBtn');
    if (settingsBtn) {
        settingsBtn.style.animation = '';
    }
    
    if (demoVisible) {
        showStep(0);
    }
}

// Demo dot click handler
function handleDotClick(event) {
    if (!demoVisible) return;
    
    const dot = event.target.closest('.dot');
    if (!dot) return;
    
    // Clear previous selections
    document.querySelectorAll('.demo-rating .dot').forEach(d => d.classList.remove('selected'));
    document.querySelectorAll('.demo-rating .rating-label').forEach(l => l.classList.remove('active'));
    
    // Select clicked dot
    dot.classList.add('selected');
    const value = dot.getAttribute('data-value');
    const label = document.querySelector(`.demo-rating .rating-label[data-value="${value}"]`);
    if (label) {
        label.classList.add('active');
    }
}

// Demo settings preview click handlers
function handleSettingsPreview(event) {
    if (!demoVisible) return;
    
    const flag = event.target.closest('.preview-flag');
    const size = event.target.closest('.preview-size');
    
    if (flag) {
        // Change active flag
        document.querySelectorAll('.preview-flag').forEach(f => f.classList.remove('active'));
        flag.classList.add('active');
    }
    
    if (size) {
        // Change active size
        document.querySelectorAll('.preview-size').forEach(s => s.classList.remove('active'));
        size.classList.add('active');
    }
}

// Initialize demo when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Set initial step
    document.getElementById('step1').classList.add('active');
    
    // Add click handlers
    document.addEventListener('click', handleDotClick);
    document.addEventListener('click', handleSettingsPreview);
});
</script>

@endsection

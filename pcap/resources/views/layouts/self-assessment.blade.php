<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'P-CAP') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --gt-offset: 0px; --topbar-h: 64px; --bg:#f8fafc; --card:#fff; --text:#1f2937; --muted:#6b7280; --border:#e5e7eb; --primary:#2563eb; --primary-600:#1d4ed8; }
        * { box-sizing: border-box; }
        body { margin:0; background:var(--bg); color:var(--text); font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji", sans-serif; }
        .assessment-topbar { position: fixed; top: var(--gt-offset, 0px); left:0; right:0; background:#ffffffcc; backdrop-filter:saturate(150%) blur(6px); border-bottom:1px solid var(--border); z-index:1200; }
        .assessment-topbar .inner { max-width: 1100px; margin: 0 auto; padding: 10px 16px; display:flex; align-items:center; justify-content:space-between; gap:12px; }
        .brand { display:flex; align-items:center; gap:10px; min-width:0; }
        .brand .logo { width:32px; height:32px; border-radius:8px; background:#eef2ff; display:flex; align-items:center; justify-content:center; color:#1e40af; border:1px solid #e5e7eb; }
        .brand .title { font-weight:700; }
        .brand .subtitle { font-size:12px; color:var(--muted); }
        .topbar-controls { display:flex; align-items:center; gap:10px; }
        .settings-container { position:relative; }
        .settings-button { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:10px; border:1px solid var(--border); background:#fff; color:#374151; cursor:pointer; }
        .settings-button:hover { background:#f3f4f6; }
        .settings-button i { font-size:16px; color:#6b7280; }
        .settings-panel { position:absolute; right:0; top:42px; background:#fff; border:1px solid var(--border); border-radius:12px; box-shadow:0 12px 32px rgba(15,23,42,.16); padding:10px 12px; width:320px; display:none; z-index:1300; }
        .settings-panel.open { display:block; }
        .settings-toolbar { display:flex; align-items:center; gap:10px; justify-content:space-between; flex-wrap:wrap; }
        .settings-label { font-size:12px; color:#6b7280; font-weight:600; }
        .lang-flags { display:inline-flex; align-items:center; gap:8px; }
        .lang-flag { display:inline-flex; align-items:center; justify-content:center; width:36px; height:32px; border-radius:8px; border:1px solid var(--border); background:#fff; cursor:pointer; font-size:16px; line-height:1; }
        .lang-flag:hover { background:#f3f4f6; }
        .lang-flag.active { background:#eef2ff; border-color:#c7d2fe; }
        .settings-divider { width:1px; height:22px; background:#e5e7eb; }
        .textsize-switch { display:inline-flex; gap:6px; border:0; padding:0; background:transparent; }
        .ts-btn { min-width:42px; height:32px; border-radius:8px; border:1px solid var(--border); background:#fff; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; }
        .ts-btn:hover { background:#f3f4f6; }
        .ts-icon { width:18px; height:18px; }
        .container-wrapper { padding: calc(var(--gt-offset, 0px) + var(--topbar-h, 64px) + 16px) 12px 24px; display:flex; justify-content:center; width:100%; }
        .container { background:var(--card); padding: 20px 24px; border-radius:12px; border:1px solid var(--border); box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08); max-width: 900px; width:100%; }
        h1,h2,h3 { margin: 0 0 10px; }
        p { margin: 0 0 12px; color:#374151; }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:10px 14px; border-radius:10px; border:1px solid var(--border); background:#f3f4f6; color:#0b1b33; font-weight:600; text-decoration:none; cursor:pointer; transition: background-color .15s ease, color .15s ease, border-color .15s ease; }
        .btn:hover { background:#e5e7eb; }
        .btn-primary { background: var(--primary); border-color: var(--primary); color:#fff; }
        .btn-primary:hover { background: var(--primary-600); border-color: var(--primary-600); }
        .btn-success { background:#10b981; border-color:#10b981; color:#fff; }
        .btn-success:hover { background:#059669; border-color:#059669; }
        .btn-outline { background:#fff; color:#374151; }
        .btn-outline:hover { background:#f3f4f6; }
        .grid { display:grid; gap:16px; }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0,1fr)); }
        @media (max-width: 700px){ .grid-cols-2 { grid-template-columns: 1fr; } .assessment-topbar .inner { padding:8px 10px; } .brand .title { font-size:14px; } }
        label { display:block; font-weight:600; margin-bottom:6px; color:#374151; }
        input[type="text"], select, input[type="email"], textarea { width:100%; padding:10px 12px; border-radius:8px; border:1px solid #d1d5db; background:#fff; font-size:14px; }
        input:focus, select:focus, textarea:focus { outline:none; border-color:#93c5fd; box-shadow: 0 0 0 3px rgba(59,130,246,.2); }
        .form-row { display:flex; gap:16px; flex-wrap:wrap; }
        .form-group { flex:1 1 260px; }
        .muted { color:#6b7280; }
        .center { text-align:center; }
    </style>
</head>
<body class="assessment-fix">
    <header class="assessment-topbar" role="banner">
        <div class="inner">
            <div class="brand">
                <div class="logo" aria-hidden="true"><i class="fas fa-check"></i></div>
                <div>
                    <div class="title">P-CAP</div>
                    <div class="subtitle">Samoocena pracownika</div>
                </div>
            </div>
            <div class="topbar-controls" aria-label="Ustawienia">
                <div class="settings-container">
                    <button type="button" id="settingsBtn" class="settings-button" aria-haspopup="true" aria-expanded="false" title="Ustawienia">
                        <i class="fas fa-cog" aria-hidden="true"></i>
                    </button>
                    <div id="settingsPanel" class="settings-panel" role="dialog" aria-label="Ustawienia">
                        <div class="settings-toolbar">
                            <span class="settings-label">Język:</span>
                            <div class="lang-flags" role="group" aria-label="Język">
                                <button type="button" class="lang-flag" data-lang="pl" title="Polski">🇵🇱</button>
                                <button type="button" class="lang-flag" data-lang="en" title="English">🇬🇧</button>
                                <button type="button" class="lang-flag" data-lang="uk" title="Українська">🇺🇦</button>
                                <button type="button" class="lang-flag" data-lang="es" title="Español">🇪🇸</button>
                                <button type="button" class="lang-flag" data-lang="pt" title="Português">🇵🇹</button>
                                <button type="button" class="lang-flag" data-lang="fr" title="Français">🇫🇷</button>
                            </div>
                            <div class="settings-divider" aria-hidden="true"></div>
                            <span class="settings-label">Wielkość tekstu:</span>
                            <div class="textsize-switch" role="group" aria-label="Wielkość tekstu">
                                <button type="button" class="ts-btn notranslate" data-size="base" title="Normalny tekst" aria-label="Normalny tekst">
                                    <svg class="ts-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M4 17h2.5l1-3h9l1 3H20L13.5 5h-3L4 17Zm4.5-5L12 6.5 15.5 12h-7Z" fill="#374151"/>
                                    </svg>
                                </button>
                                <button type="button" class="ts-btn notranslate" data-size="lg" title="Duży tekst" aria-label="Duży tekst">
                                    <svg class="ts-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M4 18h3l1.2-3.6h7.6L17 18h3L14.5 4h-5L4 18Zm5.2-6L12 6.8 14.8 12h-5.6Z" fill="#374151"/>
                                    </svg>
                                </button>
                                <button type="button" class="ts-btn notranslate" data-size="xl" title="Bardzo duży tekst" aria-label="Bardzo duży tekst">
                                    <svg class="ts-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M4 19h3.5l1.4-4.2h6.2L16.5 19H20L14.5 3h-5L4 19Zm6.4-7L12 7.2 13.6 12h-3.2Z" fill="#374151"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div id="google_translate_element" style="display:none"></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container-wrapper">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <script>
        // Topbar height var
        function setTopbarHeightVar(){
            var header = document.querySelector('.assessment-topbar');
            if (!header) return;
            var h = header.getBoundingClientRect().height;
            document.documentElement.style.setProperty('--topbar-h', h + 'px');
        }
        setTopbarHeightVar();
        window.addEventListener('resize', function(){ setTimeout(setTopbarHeightVar, 150); });

        // Settings dropdown
        (function(){
            const btn = document.getElementById('settingsBtn');
            const panel = document.getElementById('settingsPanel');
            if (!btn || !panel) return;
            function closePanel(){ panel.classList.remove('open'); btn.setAttribute('aria-expanded','false'); }
            function openPanel(){ panel.classList.add('open'); btn.setAttribute('aria-expanded','true'); }
            btn.addEventListener('click', function(e){ e.stopPropagation(); panel.classList.contains('open') ? closePanel() : openPanel(); });
            document.addEventListener('click', function(e){ if (!panel.contains(e.target) && e.target !== btn){ closePanel(); } });
            document.addEventListener('keydown', function(e){ if (e.key === 'Escape'){ closePanel(); } });
        })();

        // Text size
        (function(){
            const root = document.documentElement; 
            function applySize(size){
                root.style.setProperty('--base-font-size', size==='xl'?'18px': size==='lg'?'16px':'14px');
                document.body.style.fontSize = getComputedStyle(root).getPropertyValue('--base-font-size') || '14px';
                try { localStorage.setItem('ui_text_size', size); } catch(e){}
            }
            const saved = (function(){ try { return localStorage.getItem('ui_text_size'); } catch(e){ return null; } })();
            if (saved) applySize(saved); else applySize('base');
            document.querySelectorAll('.ts-btn').forEach(function(b){ b.addEventListener('click', function(){ applySize(this.dataset.size); }); });
        })();

        // Google Translate
        function googleTranslateElementInit(){ new google.translate.TranslateElement({pageLanguage:'pl', includedLanguages:'en,uk,es,pt,fr,pl', autoDisplay:false}, 'google_translate_element'); }
        (function loadGTranslate(){ var s = document.createElement('script'); s.src='//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit'; document.head.appendChild(s); })();
        function setCookie(name, value, days, domain){ var d = new Date(); d.setTime(d.getTime() + (days*24*60*60*1000)); var expires = 'expires='+ d.toUTCString(); var cookie = name + '=' + value + ';' + expires + ';path=/'; if (domain) cookie += ';domain=' + domain; document.cookie = cookie; }
        function getCookie(name){ var n = name + '='; var ca = document.cookie.split(';'); for (var i=0; i<ca.length; i++){ var c = ca[i].trim(); if (c.indexOf(n)===0) return c.substring(n.length,c.length); } return null; }
        function setGoogTransCookie(lang){ var host = location.hostname; var val = '/pl/' + lang; setCookie('googtrans', val, 365); setCookie('googtrans', val, 365, '.' + host); }
        function reinitTranslateGadget(cb){ var container = document.getElementById('google_translate_element'); if (!container) return; container.innerHTML=''; try { googleTranslateElementInit(); } catch(e){} setTimeout(function(){ var sel = document.querySelector('select.goog-te-combo'); cb && cb(sel); }, 600); }
        function setLanguage(lang){ var select = document.querySelector('select.goog-te-combo'); setGoogTransCookie(lang); if (select){ select.value = lang; select.dispatchEvent(new Event('change')); } else { reinitTranslateGadget(function(sel){ if(sel){ sel.value=lang; sel.dispatchEvent(new Event('change')); } else { location.reload(); } }); } try { localStorage.setItem('ui_language', lang); } catch(e){} }
        (function hookLangFlags(){ var saved = (function(){ try { return localStorage.getItem('ui_language'); } catch(e){ return null; } })(); if (saved){ setTimeout(function(){ setLanguage(saved); }, 800); } document.querySelectorAll('.lang-flag').forEach(function(b){ b.addEventListener('click', function(){ setLanguage(this.dataset.lang); document.querySelectorAll('.lang-flag').forEach(f=>f.classList.remove('active')); this.classList.add('active'); }); }); })();

        // Compute and set CSS var for Google Translate banner height
        function updateGtOffset(){
            var htmlTop = parseInt(getComputedStyle(document.body).top || '0');
            var frame = document.querySelector('.goog-te-banner-frame');
            var h = 0; if (!isNaN(htmlTop) && htmlTop>0) h = htmlTop; if (frame){ try { h = Math.max(h, frame.getBoundingClientRect().height || 0); } catch(e){} }
            document.documentElement.style.setProperty('--gt-offset', (h||0) + 'px');
        }
        updateGtOffset();
        var mo = new MutationObserver(function(){ updateGtOffset(); setTopbarHeightVar(); });
        mo.observe(document.documentElement, { childList:true, subtree:true, attributes:true, attributeFilter:['style','class'] });
    </script>
    @yield('scripts')
</body>
</html>

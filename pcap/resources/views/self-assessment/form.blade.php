<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularz samooceny P-CAP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
    :root {
        --bg: #f8fafc;
        --card: #ffffff;
        --text: #1f2937; /* gray-800 */
        --muted: #6b7280; /* gray-500 */
        --primary: #2563eb; /* blue-600 */
        --primary-600: #1d4ed8; /* blue-700 */
        --ring: rgba(37, 99, 235, 0.15);
        --accent: #10b981; /* emerald-500 */
        --warning: #f59e0b; /* amber-500 */
        --danger: #ef4444; /* red-500 */
        --border: #e5e7eb; /* gray-200 */
    }

    /* Podstawowe style zostały przeniesione do body.assessment-fix */

.level-header {
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.question {
    background-color: white;
    padding: 20px;
    margin-bottom: 30px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    box-sizing: border-box;
}
                    .rating-grid {
                        display: grid;
                        grid-template-columns: repeat(6, 1fr);
                        gap: 10px 0;
                        width: 100%;
                        padding: 8px 8px 12px 8px;
                    }
                    .rating-col {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        gap: 8px;
                    }
                    .rating-label {
                        font-size: 14px;
                        color: #333;
                        text-align: center;
                        margin-bottom: 4px;
                        line-height: 1.35;
                        min-height: 52px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        padding: 0 2px;
                    }
                    .rating-label.active { color: #1976d2; font-weight: 600; }
                            margin: 0;
                            padding: 20px;
                    .rating-dots { padding-top: 8px; padding-bottom: 10px; margin-top: 6px; margin-bottom: 8px; }
                    .question { margin-bottom: 28px; }
                    .definition-bubble, .prev-definition-bubble { margin-top: 12px; }
                    .add-description-container { margin-top: 10px; }
                    .textarea-description { margin-top: 10px !important; }
                    .legend-item {
                        font-size: 14px;
                        color: #333;
                        word-break: break-word;
                        white-space: normal;
                        text-align: center;
                        padding: 0 2px;
                            --bg: #f8fafc;
                            --card: #ffffff;
                            --text: #1f2937; /* gray-800 */
                            --muted: #6b7280; /* gray-500 */
                            --primary: #2563eb; /* blue-600 */
                            --primary-600: #1d4ed8; /* blue-700 */
                            --ring: rgba(37, 99, 235, 0.15);
                            --accent: #10b981; /* emerald-500 */
                            --warning: #f59e0b; /* amber-500 */
                            --danger: #ef4444; /* red-500 */
                            --border: #e5e7eb; /* gray-200 */
                        border: 2px solid #1976d2;
                        background: #fff;
                        margin: 0 auto;
                            background-color: var(--card);
                            padding: 20px 24px;
                            border-radius: 12px;
                            border: 1px solid var(--border);
                            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
                            max-width: 900px;
                            width: 100%;
                            display: flex;
                            flex-direction: column;
                            align-items: stretch;
                            gap: 20px;
                        color: #fff;
                        box-shadow: 0 0 0 2px #1976d233;
                    }
                            font-size: 22px;
                            font-weight: 700;
                            text-align: center;
                            margin-bottom: 8px;
                            color: var(--text);
                        background: #fbc02d;
                        color: #fff;
                    }
                            background-color: var(--card);
                            padding: 18px 16px;
                            margin-bottom: 22px;
                            border-radius: 12px;
                            border: 1px solid var(--border);
                            box-shadow: 0 3px 8px rgba(15, 23, 42, 0.06);
                            width: 100%;
                            box-sizing: border-box;
                            position: relative;
                        }
                        /* removed legacy .modal rule to avoid conflicting sizing */
                        .rating-label { min-height: 58px; line-height: 1.38; }
                    }
                    .dot {
                        width: 32px;
                        height: 32px;
                        border-radius: 50%;
                        border: 2px solid #1976d2;
                        background: #fff;
                        margin: 0 auto;
                            font-size: 16px;
                            font-weight: 700;
                            margin-bottom: 10px;
                            color: var(--text);
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                    }
                    .dot.selected {
                        background: #1976d2;
                            display: inline-block;
                            padding: 4px 10px;
                            border-radius: 999px;
                            color: #0b1b33;
                            font-size: 12px;
                            border: 1px solid var(--border);
                            background: #f3f4f6;
                        color: #fbc02d;
                    }
                    .dot.star.selected {
                            background-color: #e8f0fe;
                            border-color: #dbeafe;
                            color: #1e40af;
                        color: #fff;
                    }
                    @media (max-width: 700px) {
                            background-color: #eef2ff;
                            border-color: #e5e7eb;
                            color:#312e81;
                            max-width: 60px;
                        }
                        .legend-item {
                            width: 28px;
                            height: 28px;
                            border-radius: 50%;
                            border: 2px solid var(--primary);
                            background: #fff;
                            margin: 0 auto;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 18px;
                            cursor: pointer;
                            transition: box-shadow 0.2s, transform .05s ease-in-out;
                            position: relative;
        outline: none;
        transition: opacity .2s;
                            background: var(--primary);
                            color: #fff;
                            box-shadow: 0 0 0 3px var(--ring);
    opacity: 1;
}
                            border-color: var(--warning);
                            color: var(--warning);
    display: flex;
    justify-content: space-between;
                            background: var(--warning);
                            color: #fff;
    font-size: 14px;
    margin-top: 10px;
                            border-radius: 10px;
                            border: 1px solid var(--border);
                            padding: 10px 16px;
                            font-weight: 600;
                            transition: background-color .15s ease, color .15s ease, border-color .15s ease, box-shadow .15s ease;
                        }
                        .skip-button { background-color: #fff; color: #b91c1c; border-color: #fecaca; }
                        .skip-button:hover { background-color: #fee2e2; }
                        /* Updated Button System */
                        .back-button { background: transparent; color: #6b7280; border-color: transparent; padding: 8px 12px; }
                        .back-button:hover { background: #f3f4f6; color: #374151; }
                        .save-button { background: #3b82f6; color: white; border-color: #3b82f6; }
                        .save-button:hover { background: #2563eb; border-color: #2563eb; }
                        .cancel-button { background: white; color: #374151; border-color: #d1d5db; }
                        .cancel-button:hover { background: #f9fafb; border-color: #9ca3af; }
                        .copy-button { background-color: #fff; color: var(--text); }
                        .copy-button:hover { background-color: #f3f4f6; }
                        .modal-content {
                            background-color: var(--card);
                            padding: 24px;
                            border-radius: 14px;
                            width: min(520px, 92vw);
                            text-align: center;
                            border: 1px solid var(--border);
                            box-shadow: 0 16px 40px rgba(15,23,42,.18);

.custom-toggle {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: #d3d3d3;
    position: relative;
    cursor: pointer;
    display: inline-block;
    transition: background-color 0.2s;
}

.toggle-checkbox input[type="checkbox"] {
    display: none;
}

.toggle-checkbox input[type="checkbox"]:checked + .custom-toggle {
    background-color: #2196F3;
}

.label-text {
    font-size: 14px;
    display: inline-block;
    line-height: 1.2;
}

.textarea-description {
    display: none;
    margin-top: 10px;
}

.textarea-description textarea {
    width: 100%;
    height: 80px;
    padding: 10px;
    font-size: 14px;
    border-radius: 4px;
    border: 1px solid #ccc;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    resize: none;
}

.checkbox-container {
    display: flex;
    flex-direction: row;
    gap: 10px;
    align-items: center;
    margin-top: 10px;
}

.checkbox-container input[type="checkbox"] {
    margin-right: 5px;
}

.checkbox-container label {
    font-size: 14px;
    color: #333;
}

.above-expectations-container {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-top: 10px;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000; /* Dodaj tę linię */
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: white;
    padding: 25px;
    border-radius: 8px;
    width: 500px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.modal-content p {
    font-size: 16px;
    font-weight: 500;
    color: #444;
    margin-bottom: 20px;
}

.modal-content button {
    background-color: #2196F3;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    border: none;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin: 0 10px;
}

.modal-content button:hover {
    background-color: #1976d2;
}

button {
    font-size: 16px;
    font-weight: 600;
    border: none;
    cursor: pointer;
}
.badge.osobiste {
    background-color: #2196F3; /* niebieski */
}

.badge.spoleczne {
    background-color: #4CAF50; /* zielony */
}

.badge.liderskie {
    background-color: #9C27B0; /* fioletowy */
}

.badge.zawodowe-logistics {
    background-color: #FF9800; /* pomarańczowy */
}

.badge.zawodowe-growth {
    background-color: #009688; /* turkusowy */
}

.badge.zawodowe-inne {
    background-color: #F44336; /* czerwony */
}
.skip-button {
    background-color: #f44336;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    align-self: flex-start;
}

.skip-button:hover {
    background-color: #d32f2f;
}
/* Button definitions moved to consolidated button system */

.user-info {
    position: fixed;
    top: 10px;
    right: 10px;
    color: grey;
    font-size: 12px;
    text-align: right;
}

.edit-link-wrapper {
    display: flex;
    align-items: center;
    margin-top: 5px;
}

.edit-link-wrapper input {
    width: 300px;
    font-size: 10px;
}

.copy-button {
    background-color: #4CAF50;
    border: none;
    font-size: 10px;
    margin-left: 5px;
    scale: 0.7;
}

.copy-button:hover {
    background-color: #45a049;
}

.cancel-button {
    background-color: #f44336;
    color: white;
    padding: 8px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.cancel-button:hover {
    background-color: #d32f2f;
}

.user-card {
    position: fixed;
    top: 10px;
    right: 10px;
    background-color: #fff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    width: 200px;
    z-index: 1000;
}

.user-icon {
    text-align: center;
    font-size: 40px;
    color: #2196F3;
}

.user-details {
    text-align: center;
    margin-top: 10px;
}

.user-details p {
    margin: 5px 0;
    color: #333;
    font-size: 14px;
}

/* Default: keep neutral; scope special look to header and user-card separately */
.user-card .save-and-exit-button {
    background-color: #2196F3;
    color: white;
    padding: 8px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    margin-top: 10px;
    width: 100%;
}

.user-card .save-and-exit-button:hover {
    background-color: #1976d2;
}

/* Rating dots UI */
.rating-dots .dots-wrap{display:flex;gap:14px;align-items:center;margin:10px 0}
.rating-dots .dot{width:28px;height:28px;border-radius:50%;background:#ddd;border:2px solid #bdbdbd;cursor:pointer;position:relative}
.rating-dots .dot.selected{background:#1976d2;border-color:#1565c0}
.rating-dots .dot.prev::after{display:none}
.show-prev .rating-dots .dot.prev::after{display:none}
.rating-dots .dot.star{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;line-height:1;border:2px solid #bdbdbd;background:#ddd;color:#9e9e9e}
.rating-dots .dot.star.selected{background:#ffd54f;border-color:#f9a825;color:#7a5900}
.rating-dots .dot.star.prev{outline:2px dashed #f57f17}
.rating-dots .dots-legend{display:flex;gap:28px;flex-wrap:wrap;color:#666;font-size:13px;margin-bottom:8px;margin-top:0}
.rating-dots .dots-legend .legend-item.active{color:#1976d2;font-weight:600}
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}

.definition-bubble{background:#e7f3ff;border-left:none;border-radius:6px;padding:12px;margin-top:10px}
.definition-bubble .def-content{font-size:14px;color:#0d47a1}

/* Previous-year definition bubble */
/* Show star on last-year selected dot (instead of star button) */
.rating-dots .dot.prev.prev-star::before{content:'\2605'; /* ★ */ position:absolute; right:-8px; top:-10px; font-size:14px; color:#f9a825; display:none}
.show-prev .rating-dots .dot.prev.prev-star::before{display:block}

/* Mobile-specific styles */
@media only screen and (max-width: 768px) {
    .user-card {
        position: fixed;
        bottom: 10px;
        right: 50px;
        background-color: #fff;
        padding: 10px;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        width: 50px;
        height: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .user-card.expanded {
        width: 200px;
        height: 140px;
        border-radius: 8px;
        padding: 15px;
        display: block;
    }

    .user-card .user-icon {
        font-size: 24px;
    }

    .user-card .user-details {
        display: none;
    }

    .user-card.expanded .user-details {
        display: block;
        text-align: center;
        margin-top: 10px;
    }

    .user-card.expanded .save-and-exit-button {
        display: block;
    }

    .user-card .save-and-exit-button {
        display: none;
    }
}




</style>

<!-- Scoped modern/fixed styles to override any broken rules above without touching them -->
<style>
body.assessment-fix {
    font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji", sans-serif;
    background-color: #f8fafc;
    color: #1f2937;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    min-height: 100vh;
}
/* Make outer container airy; individual question cards carry borders/shadows */
body.assessment-fix .container {
    background-color: transparent;
    border: none;
    border-radius: 0;
    box-shadow: none;
    width: 100%;
    padding: 0;
    box-sizing: border-box;
}
body.assessment-fix .level-header { color: #1f2937; font-weight: 700; margin-bottom: 8px; }
body.assessment-fix .form-header { display:flex; justify-content: space-between; align-items:center; gap:12px; margin-bottom:16px; }
body.assessment-fix .assessment-flow { display:flex; justify-content: center; align-items:center; gap:16px; width:100%; }
body.assessment-fix .assessment-flow .flow-left { display:flex; flex-direction:column; gap:8px; min-width:0; align-items:center; }
body.assessment-fix .assessment-flow .flow-right { flex:0 0 auto; display:flex; align-items:center; gap:10px; }
body.assessment-fix .flow-info { color:#1f2937; font-weight:600; font-size:14px; }
body.assessment-fix .steps { display:flex; align-items:center; gap:10px; justify-content:center; }
body.assessment-fix .step { position:relative; display:flex; align-items:center; gap:10px; }
body.assessment-fix .step .ring { width:30px; height:30px; border-radius:50%; background: conic-gradient(#e5e7eb 0 360deg); display:grid; place-items:center; box-shadow: inset 0 0 0 3px #fff, 0 0 0 1px #e5e7eb; }
body.assessment-fix .step.current .ring { background: conic-gradient(#2563eb 0deg, #e5e7eb 0 360deg); box-shadow: inset 0 0 0 3px #fff, 0 0 0 1px #2563eb; }
body.assessment-fix .step.done .ring { background:#2563eb; box-shadow: inset 0 0 0 3px #fff, 0 0 0 1px #2563eb; }
body.assessment-fix .ring-text { font-size:10px; font-weight:700; color:#111827; mix-blend-mode:normal; }
body.assessment-fix .step.done .ring-text { color:#fff; }
body.assessment-fix .step-label { font-size:11px; color:#374151; font-weight:600; }
body.assessment-fix .step.current .step-label { color:#1f2937; }
body.assessment-fix .connector { width:32px; height:2px; background:#e5e7eb; border-radius:2px; }
body.assessment-fix .step.done + .connector { background:#2563eb; }
@media (max-width: 820px) {
    body.assessment-fix .flow-info { display:none; }
    /* tighter spacing for mobile */
    body.assessment-fix .steps { gap:3px; }
    body.assessment-fix .step { gap:3px; }
    body.assessment-fix .connector { width:10px; }
    body.assessment-fix .step .ring { width:20px; height:20px; }
    body.assessment-fix .ring-text { font-size:8px; }
    /* On very small screens show numbers 1..5 instead of labels */
    body.assessment-fix .step-label { position: relative; color: transparent !important; max-width: 22px; }
    /* Ensure the current step label also hides (override stronger selector above) */
    body.assessment-fix .step.current .step-label { color: transparent !important; }
    body.assessment-fix .step-label::after { content: attr(data-num); position:absolute; inset:0; color:#374151; font-weight:700; text-align:center; font-size:11px; }
}

/* Fixed top header */
body.assessment-fix .assessment-topbar { position: fixed; top: var(--gt-offset, 0px); left: 0; right: 0; background: #ffffffcc; backdrop-filter: saturate(150%) blur(6px); border-bottom:1px solid #e5e7eb; z-index: 1200; }
body.assessment-fix .assessment-topbar .inner { max-width: 1100px; margin: 0 auto; padding: 10px 16px; }
@media (max-width: 700px){
    body.assessment-fix .assessment-topbar .topbar-row{ flex-wrap: wrap; gap:8px; }
    body.assessment-fix .assessment-topbar .topbar-row > .user-summary{ flex:1 1 60%; min-width:220px; }
    body.assessment-fix .assessment-topbar .topbar-row > div:last-child{ flex:1 1 40%; display:flex; justify-content:flex-end; flex-wrap:wrap; gap:8px; }
}
@media (max-width: 480px){
    body.assessment-fix #skipButton{ display:none !important; }
}
/* Dynamic topbar height variable (updated via JS) */
:root { --topbar-h: 66px; }
/* Offset main content to avoid overlap with fixed header */
body.assessment-fix .container-wrapper { 
    padding-top: calc(var(--topbar-h, 66px) + var(--gt-offset, 0px) + 20px); 
    display: block;
    max-width: 840px;
    width: 100%;
    margin: 0 auto;
    padding-left: 16px;
    padding-right: 16px;
    padding-bottom: 20px;
    box-sizing: border-box;
}
/* Remove previously reserved space for floating user card */
@media (min-width: 1200px){ body.assessment-fix .container { padding-right: 0; } }
/* Step label ellipsis for long names */
body.assessment-fix .step-label { max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* Language selector and text size switcher (legacy inline in header; now inside settings panel) */
body.assessment-fix .topbar-controls { display:flex; align-items:center; gap:10px; margin-right:10px; }
body.assessment-fix .lang-select { appearance:none; border:1px solid #e5e7eb; background:#fff; border-radius:8px; padding:6px 8px; font-size:12px; color:#111827; }
body.assessment-fix .textsize-switch { display:flex; align-items:center; gap:6px; border:1px solid #e5e7eb; border-radius:999px; padding:4px 6px; background:#fff; }
body.assessment-fix .ts-btn { border:0; background:transparent; color:#374151; font-weight:700; padding:4px 6px; border-radius:6px; cursor:pointer; }
body.assessment-fix .ts-btn[aria-pressed="true"], body.assessment-fix .ts-btn.active { background:#eef2ff; color:#1f2937; }
/* Save and exit button in header */
body.assessment-fix .save-and-exit-button { background-color: var(--primary); border: 1px solid var(--primary); color:#fff; border-radius:10px; padding:8px 12px; font-weight:600; cursor:pointer; }
/* Ensure header Save button is always visible */
body.assessment-fix .assessment-topbar .save-and-exit-button { display: inline-flex !important; align-items: center; }
body.assessment-fix .save-and-exit-button:hover { background-color: var(--primary-600); border-color: var(--primary-600); }
/* Settings dropdown */
body.assessment-fix .settings-container { position: relative; }
body.assessment-fix .settings-button { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:10px; border:1px solid #e5e7eb; background:#fff; color:#374151; cursor:pointer; }
body.assessment-fix .settings-button:hover { background:#f3f4f6; }
body.assessment-fix .settings-button i { font-size:16px; color:#6b7280; }
body.assessment-fix .settings-panel { position:absolute; right:0; top:42px; background:#fff; border:1px solid #e5e7eb; border-radius:12px; box-shadow:0 12px 32px rgba(15,23,42,.16); padding:10px 12px; width:320px; display:none; z-index:1300; }
body.assessment-fix .settings-panel.open { display:block; }
/* Settings toolbar (horizontal) */
body.assessment-fix .settings-toolbar { display:flex; align-items:center; gap:10px; justify-content:space-between; flex-wrap:wrap; }
body.assessment-fix .settings-toolbar .settings-label { font-size:12px; color:#6b7280; font-weight:600; }
/* Language flags group */
body.assessment-fix .settings-toolbar .lang-flags { display:inline-flex; align-items:center; gap:8px; }
body.assessment-fix .settings-toolbar .lang-flag { display:inline-flex; align-items:center; justify-content:center; width:36px; height:32px; border-radius:8px; border:1px solid #e5e7eb; background:#fff; cursor:pointer; font-size:16px; line-height:1; }
body.assessment-fix .settings-toolbar .lang-flag:hover { background:#f3f4f6; }
body.assessment-fix .settings-toolbar .lang-flag.active { background:#eef2ff; border-color:#c7d2fe; }
/* Hidden select kept for integration */
body.assessment-fix .settings-toolbar .lang-select { display:none; }
body.assessment-fix .settings-toolbar .settings-divider { width:1px; height:22px; background:#e5e7eb; }
body.assessment-fix .settings-toolbar .textsize-switch { display:inline-flex; gap:6px; border:0; padding:0; background:transparent; }
body.assessment-fix .settings-toolbar .ts-btn { min-width:42px; height:32px; border-radius:8px; border:1px solid #e5e7eb; background:#fff; cursor:pointer; white-space:nowrap; display:inline-flex; align-items:center; justify-content:center; }
body.assessment-fix .settings-toolbar .ts-btn:hover { background:#f3f4f6; }
/* Text size icons (SVG, not translatable) */
body.assessment-fix .ts-icon { display:block; color:#374151; }
body.assessment-fix .ts-icon-sm { width:16px; height:16px; }
body.assessment-fix .ts-icon-md { width:18px; height:18px; }
body.assessment-fix .ts-icon-lg { width:20px; height:20px; }
/* Text scaling */
body.assessment-fix.text-lg { font-size: 17px; }
body.assessment-fix.text-xl { font-size: 19px; }
/* Ensure form elements scale comfortably */
body.assessment-fix.text-lg .textarea-description textarea { min-height: 150px; }
body.assessment-fix.text-xl .textarea-description textarea { min-height: 160px; }

/* Accessibility: scale key text blocks when user chooses larger text */
/* Competency name (question label) overrides inline px */
body.assessment-fix.text-lg .question > label { font-size: 20px !important; line-height: 1.35 !important; }
body.assessment-fix.text-xl .question > label { font-size: 22px !important; line-height: 1.35 !important; }
/* Subheaders and assessment labels */
body.assessment-fix.text-lg .assessment-subheader { font-size: 15px !important; }
body.assessment-fix.text-xl .assessment-subheader { font-size: 16px !important; }
body.assessment-fix.text-lg .assessment-label { font-size: 15px !important; }
body.assessment-fix.text-xl .assessment-label { font-size: 16px !important; }
/* Rating labels */
body.assessment-fix.text-lg .rating-label { font-size: 14px !important; min-height:52px !important; align-items:flex-end !important; }
body.assessment-fix.text-xl .rating-label { font-size: 15px !important; min-height:56px !important; align-items:flex-end !important; }
/* Definition bubble */
body.assessment-fix.text-lg .definition-bubble > div:first-child { font-size: 15px !important; }
body.assessment-fix.text-xl .definition-bubble > div:first-child { font-size: 16px !important; }
body.assessment-fix.text-lg .definition-bubble .def-content { font-size: 16px !important; line-height: 1.6 !important; }
body.assessment-fix.text-xl .definition-bubble .def-content { font-size: 18px !important; line-height: 1.65 !important; }
/* Chat-style previous cycle messages */
body.assessment-fix.text-lg .message-header { font-size: 13px !important; }
body.assessment-fix.text-xl .message-header { font-size: 14px !important; }
body.assessment-fix.text-lg .message-text { font-size: 16px !important; line-height: 1.6 !important; }
body.assessment-fix.text-xl .message-text { font-size: 18px !important; line-height: 1.65 !important; }
body.assessment-fix.text-lg .prev-conversation-toggle { font-size: 14px !important; }
body.assessment-fix.text-xl .prev-conversation-toggle { font-size: 15px !important; }
/* Badges */
body.assessment-fix.text-lg .badge { font-size: 13px !important; }
body.assessment-fix.text-xl .badge { font-size: 14px !important; }
body.assessment-fix.text-lg .prev-badge { font-size: 13px !important; }
body.assessment-fix.text-xl .prev-badge { font-size: 14px !important; }
/* Topbar text */
body.assessment-fix.text-lg .flow-info { font-size: 15px !important; }
body.assessment-fix.text-xl .flow-info { font-size: 16px !important; }
body.assessment-fix.text-lg .step-label { font-size: 12px !important; }
body.assessment-fix.text-xl .step-label { font-size: 13px !important; }

/* Note: nie ukrywamy na siłę banera Google Translate, zamiast tego mądrze odsuwamy header i content dynamicznie JS-em. */
body.assessment-fix .question {
    background-color: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 3px 8px rgba(15, 23, 42, 0.06);
    padding: 18px 16px;
    margin-bottom: 24px;
    position: relative;
}
body.assessment-fix .question::before { display: none !important; }
body.assessment-fix .question-header { color:#1f2937; font-weight:700; margin-bottom:10px; }
body.assessment-fix .assessment-subheader { color:#6b7280; font-weight:500; margin:6px 0 4px; font-size:13px; text-align:left; }
body.assessment-fix .assessment-label { color:#111827; font-weight:500; font-size:14px; }
/* Left-align primary texts for readability */
body.assessment-fix .question > label { display:block; font-weight:600; color:#111827; text-align:left; margin-bottom:6px; }
body.assessment-fix .definition-bubble, body.assessment-fix .prev-definition-bubble { text-align:left; }
body.assessment-fix .badge { display:inline-block; padding:4px 10px; border-radius:999px; border:1px solid #e5e7eb; background:#f3f4f6; font-size:12px; color:#0b1b33; }
body.assessment-fix .badge.competency { background:#e8f0fe; border-color:#dbeafe; color:#1e40af; }
body.assessment-fix .badge.level { background:#eef2ff; border-color:#e5e7eb; color:#312e81; }
/* Gentle left color bar per category */
body.assessment-fix .question{ border-left:4px solid #e5e7eb; padding-left:20px; }
body.assessment-fix .question .badge-container{ display:flex; gap:8px; align-items:center; justify-content:flex-start; }
body.assessment-fix .assessment-subheader{ text-align:left; }
body.assessment-fix label{ text-align:left; display:block; }
/* Balance inner padding in cards */
body.assessment-fix .question{ padding-right:20px; }
body.assessment-fix .question.osobiste{ border-left-color:#bfdbfe; }
body.assessment-fix .question.spoleczne{ border-left-color:#bbf7d0; }
body.assessment-fix .question.liderskie{ border-left-color:#e9d5ff; }
body.assessment-fix .question.zawodowe-logistics{ border-left-color:#fed7aa; }
body.assessment-fix .question.zawodowe-growth{ border-left-color:#a7f3d0; }
body.assessment-fix .question.zawodowe-inne{ border-left-color:#fecaca; }

/* Rating grid */
body.assessment-fix .rating-dots { padding-top: 8px; padding-bottom: 8px; }
body.assessment-fix .rating-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:10px 0; width:100%; padding: 4px 8px; }
body.assessment-fix .rating-col { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; }
/* Bottom-align labels so 1-line and 2-line look even */
body.assessment-fix .rating-label { font-size:13px; line-height:1.3; min-height:48px; text-align:center; display:flex; align-items:flex-end; justify-content:center; color:#374151; padding:0 4px; font-weight:400; }
body.assessment-fix .rating-label.active { color:#2563eb; font-weight:500; }
body.assessment-fix .dot { width:28px; height:28px; border-radius:50%; border:2px solid #2563eb; background:#fff; display:flex; align-items:center; justify-content:center; font-size:18px; cursor:pointer; transition: box-shadow .2s; }
body.assessment-fix .dot.selected { background:#2563eb; color:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.15); }
body.assessment-fix .dot.star { border-color:#f59e0b; color:#f59e0b; }
body.assessment-fix .dot.star.selected { background:#f59e0b; color:#fff; }

/* Text areas and bubbles */
body.assessment-fix .definition-bubble { background:#eef2ff; border:1px solid #c7d2fe; color:#111827; border-radius:10px; padding:12px 14px; }
body.assessment-fix .definition-bubble > div:first-child { font-size:13px; font-weight:700; color:#1d4ed8; }
body.assessment-fix .definition-bubble .def-content { font-size:14px; font-weight:400; line-height:1.55; color:#111827; }

/* Chat-style previous cycle messages */
body.assessment-fix .chat-message { display:flex; gap:8px; margin-bottom:12px; max-width:85%; }
body.assessment-fix .chat-message.user-message { margin-left:auto; }
body.assessment-fix .chat-message.manager-message { margin-right:auto; }

body.assessment-fix .message-avatar { width:36px; height:36px; border-radius:50%; background:#f3f4f6; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
body.assessment-fix .user-message .message-avatar { background:#dbeafe; color:#1d4ed8; }
body.assessment-fix .manager-message .message-avatar { background:#ecfdf5; color:#059669; }
body.assessment-fix .user-avatar-text { font-size:11px; font-weight:700; }

body.assessment-fix .message-content { background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:10px 12px; flex:1; min-width:0; max-width:100%; }
body.assessment-fix .user-message .message-content { background:#dbeafe; border-color:#c7d2fe; }
body.assessment-fix .manager-message .message-content { background:#ecfdf5; border-color:#bbf7d0; }

body.assessment-fix .message-header { font-size:12px; font-weight:600; margin-bottom:4px; opacity:0.8; word-wrap:break-word; overflow-wrap:break-word; }
body.assessment-fix .message-text { font-size:14px; line-height:1.5; white-space:pre-wrap; word-wrap:break-word; overflow-wrap:break-word; word-break:break-word; hyphens:auto; min-width:0; }

/* Previous conversation toggle */
body.assessment-fix .prev-conversation-toggle { background:none; border:1px solid #e5e7eb; border-radius:6px; padding:8px 12px; cursor:pointer; display:flex; align-items:center; gap:8px; font-size:13px; color:#6b7280; transition:all 0.2s ease; width:100%; text-align:left; }
body.assessment-fix .prev-conversation-toggle:hover { border-color:#c7d2fe; background:#f8faff; color:#1d4ed8; }
body.assessment-fix .conversation-chevron { transition:transform 0.2s ease; font-size:11px; }
body.assessment-fix .prev-conversation-toggle.expanded .conversation-chevron { transform:rotate(90deg); }

body.assessment-fix .textarea-description textarea { border:1px solid #e5e7eb; border-radius:8px; padding:12px; outline:none; min-height:140px; resize: vertical; width:100%; box-sizing:border-box; font-size:15px; line-height:1.55; font-family: 'Segoe UI', Roboto, Inter, sans-serif; color:#111827; }
body.assessment-fix .textarea-description textarea:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.15); }
body.assessment-fix .textarea-description textarea::placeholder { color:#6b7280; font-style:italic; }
body.assessment-fix .textarea-description { margin-top: 10px; }

/* Buttons */
/* Updated Button System */
body.assessment-fix .skip-button, body.assessment-fix .back-button, body.assessment-fix .save-button, body.assessment-fix .cancel-button, body.assessment-fix .copy-button { 
    border-radius:8px; 
    border:1px solid transparent; 
    padding:12px 20px; 
    font-weight:600; 
    cursor: pointer;
    transition: all 0.2s ease;
    min-height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
/* Primary buttons */
body.assessment-fix .save-button, body.assessment-fix .confirm-button { background:#3b82f6; color:#fff; border-color:#3b82f6; }
body.assessment-fix .save-button:hover, body.assessment-fix .confirm-button:hover { background:#2563eb; border-color:#2563eb; }
/* Secondary buttons */
body.assessment-fix .cancel-button { background:white; color:#374151; border-color:#d1d5db; }
body.assessment-fix .cancel-button:hover { background:#f9fafb; border-color:#9ca3af; }
/* Text buttons */
body.assessment-fix .back-button { background:transparent; color:#6b7280; border-color:transparent; padding: 8px 12px; }
body.assessment-fix .back-button:hover { background:#f3f4f6; color:#374151; }
body.assessment-fix .skip-button { background:#fff; color:#991b1b; border-color:#fecaca; }
body.assessment-fix .skip-button:hover { background:#fee2e2; }
/* Prevent skip button from stretching full height in any flex context */
body.assessment-fix .skip-button { display:inline-flex; flex:0 0 auto; align-self:center; height:auto; max-height:none; width:auto; }

/* Modal */
body.assessment-fix .modal-content { background:#fff; border:1px solid #e5e7eb; border-radius:14px; width:min(520px,92vw); box-shadow: 0 16px 40px rgba(15,23,42,.18); }
/* Strong, centered modal overlay to ensure correct positioning */
body.assessment-fix .modal { position: fixed !important; inset: 0 !important; width: 100% !important; height: 100% !important; display: none; align-items: center; justify-content: center; background: rgba(17,24,39,.55); z-index: 1400; padding: 16px; }
body.assessment-fix .modal[style*="display:flex"] { align-items: center; justify-content: center; }
/* Stable content size */
body.assessment-fix .modal .modal-content { width: min(520px, 92vw); max-height: 80vh; min-height: 200px; overflow: auto; background:#fff; color:#111827; }
body.assessment-fix .modal .modal-content h3 { font-size: 22px; font-weight: 700; color:#111827; margin: 0 0 8px; }
body.assessment-fix .modal .modal-content p { color:#111827; font-weight: 400 !important; font-size: 16px; line-height: 1.5; margin: 8px 0 16px; }
/* Primary confirm button in modal */
/* Confirm button already defined above in primary buttons section */

/* --- Submit/loading state --- */
.btn-spinner{display:inline-block;width:16px;height:16px;border:2px solid #fff;border-top-color:transparent;border-right-color:transparent;border-radius:50%;vertical-align:-3px;margin-right:8px;animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.is-loading{opacity:.7; pointer-events:none}
.submit-overlay{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(255,255,255,.55);z-index:2000}
.submit-overlay .box{background:#111827;color:#fff;padding:12px 16px;border-radius:10px;display:flex;align-items:center;gap:10px;box-shadow:0 8px 24px rgba(0,0,0,.2)}
.submit-overlay .box .spinner{width:18px;height:18px;border:2px solid #fff;border-top-color:transparent;border-right-color:transparent;border-radius:50%;animation:spin .8s linear infinite}

/* Restore floating user card on desktop */
body.assessment-fix .user-card { position: fixed; top: calc(var(--gt-offset, 0px) + var(--topbar-h, 66px) + 10px); right: 10px; background: #fff; border:1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 8px 24px rgba(15,23,42,.12); width: 220px; z-index: 1000; padding:12px; }
body.assessment-fix .user-card .save-and-exit-button { display:block; width:100%; margin-top:8px; background:#2563eb; color:#fff; border-color:#2563eb; border-radius:10px; padding:8px 10px; font-size:14px; }
body.assessment-fix .user-card .save-and-exit-button:hover { background:#1d4ed8; }
/* Align add-description checkbox to the left */
body.assessment-fix .add-description-container { display:flex; align-items:center; justify-content:flex-start; gap:8px; margin-top:8px; }

@media (max-width: 700px){
  body.assessment-fix .rating-label { min-height:56px; }
  body.assessment-fix .dot { width:24px; height:24px; font-size:16px; }
}
/* Remove legacy right offset for floating user card to keep container centered */
/* (user card is fixed and shouldn't influence container centering) */

    /* Mobile layout: stack rating options vertically with labels to the right */
    @media (max-width: 640px){
        body.assessment-fix .rating-grid { display:flex; flex-direction:column; gap:10px; padding: 4px 4px; }
        body.assessment-fix .rating-col { flex-direction:row; align-items:center; justify-content:flex-start; gap:12px; padding:10px 8px; border-bottom:1px solid #e5e7eb; cursor:pointer; border-radius:8px; }
        body.assessment-fix .rating-col:last-child { border-bottom:none; }
        body.assessment-fix .rating-col:hover { background:#f9fafb; }
        body.assessment-fix .rating-col.active { background:#f3f4f6; }
        body.assessment-fix .rating-col .dot { order:1; width:30px; height:30px; font-size:16px; }
        body.assessment-fix .rating-col .rating-label { order:2; min-height:auto; text-align:left; justify-content:flex-start; padding:0; font-size:14px; }
        /* Slightly increase touch targets */
        body.assessment-fix.text-lg .rating-col .dot { width:32px; height:32px; }
        body.assessment-fix.text-xl .rating-col .dot { width:34px; height:34px; }
    }
</style>

<script>
    /*
    let completedCount = 0;
    let totalQuestions = {{ $competencies->count() }};
    let osobisteCompleted = 0;
    let spoleczneCompleted = 0;
    let liderskieCompleted = 0;
    let zawodoweCompleted = 0;
*/
    /*function updateProgress() {
        completedCount = 0;
        osobisteCompleted = 0;
        spoleczneCompleted = 0;
        liderskieCompleted = 0;
        zawodoweCompleted = 0;

        document.querySelectorAll('.slider').forEach(slider => {
            if (slider.value > 0) {
                completedCount++;
                const competencyType = slider.closest('.question').dataset.competencyType;
                if (competencyType.includes('1. Osobiste')) osobisteCompleted++;
                if (competencyType.includes('2. Społeczne')) spoleczneCompleted++;
                if (competencyType.includes('4. Liderskie')) liderskieCompleted++;
                if (competencyType.includes('3.')) zawodoweCompleted++;
            }
        });

        document.getElementById('completed').innerText = completedCount;
        document.getElementById('osobiste-completed').innerText = osobisteCompleted;
        document.getElementById('spoleczne-completed').innerText = spoleczneCompleted;
        document.getElementById('liderskie-completed').innerText = liderskieCompleted;
        document.getElementById('zawodowe-completed').innerText = zawodoweCompleted;
    }*/

    function toggleAboveExpectations(checkbox) {
    const question = checkbox.closest('.question');
    const slider = question.querySelector('.slider');
    const descriptionCheckbox = question.querySelector('.add-description-container input[type="checkbox"]');
    const commentContainer = question.querySelector('.textarea-description');
    const descriptionDiv = question.querySelector('.slider-description');

    if (checkbox.checked) {
        // Ustawienie suwaka na 1
        slider.value = 1;
        // Uniemożliwienie interakcji użytkownika
        slider.style.pointerEvents = 'none';
        slider.style.backgroundColor = '#e0e0e0'; // Opcjonalnie: zmień kolor, aby wskazać, że suwak jest nieaktywny

        // Wyświetlenie opisu "Powyżej oczekiwań"
        descriptionDiv.textContent = question.dataset.descriptionAboveExpectations;
        descriptionDiv.style.display = 'block';

        // Zaznacz i zablokuj checkbox "Dodaj opis/argumentację"
        descriptionCheckbox.checked = true;
        descriptionCheckbox.disabled = true;
        commentContainer.style.display = 'block';
        commentContainer.querySelector('textarea').required = true;
    } else {
        // Przywróć interakcję użytkownika
        slider.style.pointerEvents = 'auto';
        slider.style.backgroundColor = ''; // Przywróć domyślny kolor

        // Odblokuj i odznacz checkbox "Dodaj opis/argumentację"
        descriptionCheckbox.disabled = false;
        descriptionCheckbox.checked = false;
        commentContainer.style.display = 'none';
        commentContainer.querySelector('textarea').required = false;

        // Aktualizacja opisu na podstawie wartości suwaka
        updateSliderValue(slider);
    }
}




    function updateSliderValue(slider) {
    const value = parseFloat(slider.value);
    const question = slider.closest('.question');
    const descriptionDiv = question.querySelector('.slider-description');
    const touched = slider.dataset.touched === "true";

    if (value === 0) {
        if (touched) {
            descriptionDiv.textContent = "Nie dotyczy mnie ta kompetencja";
            descriptionDiv.style.display = 'block';
        } else {
            descriptionDiv.style.display = 'none';
            descriptionDiv.textContent = '';
        }
    } else if (value === 0.25) {
        descriptionDiv.textContent = question.dataset.description025;
        descriptionDiv.style.display = 'block';
    } else if (value === 0.5) {
        descriptionDiv.textContent = question.dataset.description0to05;
        descriptionDiv.style.display = 'block';
    } else if (value === 0.75 || value === 1) {
        descriptionDiv.textContent = question.dataset.description075to1;
        descriptionDiv.style.display = 'block';
    } else {
        descriptionDiv.style.display = 'none';
        descriptionDiv.textContent = '';
    }
}


// Funkcja, która pokazuje pole tekstowe przy zaznaczeniu checkboxa "Dodaj opis/argumentację"
    function toggleDescriptionInput(checkbox, isInitializing = false) {
        // Only mark user changes and trigger autosave if this is not part of form initialization
        if (!isInitializing) {
            markUserChanges();
        }
        
        const question = checkbox.closest('.question');
        const commentContainer = question.querySelector('.textarea-description');

        if (checkbox.checked) {
            commentContainer.style.display = 'block';
            commentContainer.querySelector('textarea').required = true;
        } else {
            commentContainer.style.display = 'none';
            commentContainer.querySelector('textarea').required = false;
        }
        
        // Only trigger autosave if this is not part of form initialization
        if (!isInitializing) {
            try { triggerAutosaveDebounced(); } catch(e) {}
        }
    }

    function copyLink() {
            var copyText = document.getElementById("editLinkModal");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices

            document.execCommand("copy");

            alert("Link został skopiowany do schowka.");
        }

    console.log('JAVASCRIPT STARTED - DOM Content loading...');
    document.addEventListener("DOMContentLoaded", function() {
        console.log('DOM CONTENT LOADED EVENT FIRED');
        // Compute dynamic topbar height and set CSS var
        function setTopbarHeightVar(){
            var header = document.querySelector('.assessment-topbar');
            if (header){
                var h = header.offsetHeight || 66;
                document.documentElement.style.setProperty('--topbar-h', h + 'px');
            }
        }
        setTopbarHeightVar();
        window.addEventListener('resize', function(){ setTimeout(setTopbarHeightVar, 150); });

        // User card expand/collapse
        const userCard = document.querySelector(".user-card");
        if (userCard) {
            const userIcon = userCard.querySelector(".user-icon");
            userIcon.addEventListener("click", function () {
                userCard.classList.toggle("expanded");
            });
        }

        // Settings menu (gear icon)
        const settingsBtn = document.getElementById('settingsBtn');
        const settingsPanel = document.getElementById('settingsPanel');
        if (settingsBtn && settingsPanel){
            function closeSettings(){
                settingsPanel.classList.remove('open');
                settingsBtn.setAttribute('aria-expanded','false');
            }
            function openSettings(){
                settingsPanel.classList.add('open');
                settingsBtn.setAttribute('aria-expanded','true');
            }
            settingsBtn.addEventListener('click', function(e){
                e.stopPropagation();
                if (settingsPanel.classList.contains('open')) closeSettings(); else openSettings();
            });
            document.addEventListener('click', function(e){
                if (!settingsPanel.contains(e.target) && e.target !== settingsBtn){ closeSettings(); }
            });
            document.addEventListener('keydown', function(e){ if (e.key === 'Escape'){ closeSettings(); } });
        }

        // Language flags hookup
        (function(){
            var langSelect = document.getElementById('langSelect');
            var flagButtons = Array.prototype.slice.call(document.querySelectorAll('.lang-flag'));
            function setActiveFlag(code){
                flagButtons.forEach(function(b){ b.classList.toggle('active', b.getAttribute('data-lang') === code); });
            }
            flagButtons.forEach(function(btn){
                btn.addEventListener('click', function(){
                    var code = this.getAttribute('data-lang');
                    if (langSelect){ langSelect.value = code; var ev = new Event('change'); langSelect.dispatchEvent(ev); }
                    try { if (typeof setLanguage === 'function'){ setLanguage(code); } } catch(e){}
                    setActiveFlag(code);
                });
            });
            // Initialize active based on saved or current select
            var savedLang = (function(){ try { return localStorage.getItem('ui_language'); } catch(e){ return null; } })();
            var initial = savedLang || (langSelect ? langSelect.value : 'pl');
            setActiveFlag(initial);
        })();

        // Modal logic
        var skipButton = document.getElementById("skipButton");
        if (skipButton) {
            var skipModal = document.getElementById("skipModal");
            var confirmSkip = document.getElementById("confirmSkip");
            var cancelSkip = document.getElementById("cancelSkip");
            skipButton.addEventListener("click", function() {
                skipModal.style.display = "flex";
                // focus the dialog for accessibility
                var mc = skipModal.querySelector('.modal-content');
                if (mc) mc.focus();
            });
            var uuid = "{{ $uuid }}";
            confirmSkip.addEventListener("click", function() {
                window.location.href = "/self-assessment/complete/" + uuid;
            });
            cancelSkip.addEventListener("click", function() {
                skipModal.style.display = "none";
            });
            // close when clicking backdrop
            skipModal.addEventListener('click', function(e){
                if (e.target === skipModal) { skipModal.style.display = 'none'; }
            });
        }
        var closeModalButton = document.getElementById("closeModal");
        if (closeModalButton) {
            closeModalButton.addEventListener("click", function() {
                document.getElementById("saveModal").style.display = "none";
            });
        }

        // Checkbox "Dodaj uzasadnienie" logic
        document.querySelectorAll('input[name^="add_description"]').forEach(checkbox => {
            toggleDescriptionInput(checkbox, true); // isInitializing = true
        });
    });




// --- Autosave helpers ---
let hasUserMadeChanges = false; // Flag to track if user made any changes since page load
console.log('AUTOSAVE PROTECTION LOADED: hasUserMadeChanges = false');

function autosaveNow(){
    // Don't autosave if user hasn't made any changes since page load
    if (!hasUserMadeChanges) {
        console.log('Skipping autosave - no user changes detected since page load');
        return Promise.resolve();
    }
    
    console.log('Autosave triggered - user has made changes');
    const form = document.getElementById('assessmentForm');
    if (!form) return Promise.resolve();
    const formData = new FormData(form);
    return fetch('{{ route('self_assessment.autosave') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    }).then(r=>{ try { return r.json(); } catch(e){ return null; } }).catch(()=>null);
}
let __autosaveTimer;
function triggerAutosaveDebounced(delay){
    clearTimeout(__autosaveTimer);
    __autosaveTimer = setTimeout(function(){ autosaveNow(); }, typeof delay==='number'? delay : 1000);
}

// Mark that user made changes - this will be called on form interactions
function markUserChanges() {
    console.log('USER MADE CHANGES: Setting hasUserMadeChanges = true');
    hasUserMadeChanges = true;
}

// Safety net: periodic autosave every 60s - but only start after 60s delay
let autosaveInterval = setTimeout(() => {
    autosaveInterval = setInterval(autosaveNow, 60000);
}, 60000);
</script>

</head>
<body class="assessment-fix">
    @php
        // Opcjonalnie: przekaż z backendu tablicę $levelNames[1..6] z pełnymi nazwami poziomów.
        // Jeśli brak, użyj prostych fallbacków "Poziom {i}".
        $hasLevelNames = isset($levelNames) && is_array($levelNames);
    @endphp
    <!-- Stały nagłówek strony z przepływem poziomów i przyciskiem Pomiń -->
    <header class="assessment-topbar" role="banner">
        <div class="inner">
            <!-- Row 1: User summary + actions + settings -->
            <div class="topbar-row" style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                <div class="user-summary" style="display:flex; align-items:center; gap:10px; min-width:0;">
                    <div class="user-chip" style="display:flex; align-items:center; gap:8px; padding:6px 10px; border:1px solid #e5e7eb; border-radius:999px; background:#fff;">
                        <i class="fas fa-user" aria-hidden="true" style="color:#6b7280"></i>
                        <span style="font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:240px;">{{ $employee->name }}</span>
                        <span class="muted" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:160px;">{{ $employee->department }}</span>
                        @if(!empty($employee->manager_username))
                            <span class="muted" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px;">
                                <i class="fas fa-user-tie" aria-hidden="true"></i> {{ $employee->manager_username }}
                            </span>
                        @endif
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    @if ($currentLevel > 1)
                        <button type="button" id="skipButton" class="skip-button">Pomiń resztę samooceny</button>
                    @endif
                    <button type="submit" form="assessmentForm" name="save_and_exit" class="save-and-exit-button" style="margin:0;">Zapisz i dokończ później</button>
                    <div class="topbar-controls" aria-label="Ustawienia">
                        <div class="settings-container">
                            <button type="button" id="settingsBtn" class="settings-button" aria-haspopup="true" aria-expanded="false" title="Ustawienia">
                                <i class="fas fa-cog" aria-hidden="true"></i>
                            </button>
                            <div id="settingsPanel" class="settings-panel" role="dialog" aria-label="Ustawienia">
                                <div class="settings-toolbar">
                                    <span class="settings-label">Język:</span>
                                    <div class="lang-flags notranslate" role="group" aria-label="Język" translate="no">
                                        <button type="button" class="lang-flag notranslate" data-lang="pl" title="Polski" aria-label="Polski">🇵🇱</button>
                                        <button type="button" class="lang-flag notranslate" data-lang="en" title="English" aria-label="English">🇬🇧</button>
                                        <button type="button" class="lang-flag notranslate" data-lang="uk" title="Українська" aria-label="Українська">🇺🇦</button>
                                        <button type="button" class="lang-flag notranslate" data-lang="es" title="Español" aria-label="Español">🇪🇸</button>
                                        <button type="button" class="lang-flag notranslate" data-lang="pt" title="Português" aria-label="Português">🇵🇹</button>
                                        <button type="button" class="lang-flag notranslate" data-lang="fr" title="Français" aria-label="Français">🇫🇷</button>
                                    </div>
                                    <select id="langSelect" class="lang-select" title="Język" aria-hidden="true">
                                        <option value="pl">PL</option>
                                        <option value="en">EN</option>
                                        <option value="uk">UK</option>
                                        <option value="es">ES</option>
                                        <option value="pt">PT</option>
                                        <option value="fr">FR</option>
                                    </select>
                                    <div class="settings-divider" aria-hidden="true"></div>
                                    <span class="settings-label">Wielkość tekstu:</span>
                                    <div class="textsize-switch" role="group" aria-label="Wielkość tekstu">
                                        <button type="button" class="ts-btn notranslate" data-size="base" title="Normalny tekst" aria-label="Normalny tekst">
                                            <svg class="ts-icon ts-icon-sm" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M4 17h2.5l1-3h9l1 3H20L13.5 5h-3L4 17Zm4.5-5L12 6.5 15.5 12h-7Z" fill="#374151"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="ts-btn notranslate" data-size="lg" title="Duży tekst" aria-label="Duży tekst">
                                            <svg class="ts-icon ts-icon-md" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M4 18h3l1.2-3.6h7.6L17 18h3L14.5 4h-5L4 18Zm5.2-6L12 6.8 14.8 12h-5.6Z" fill="#374151"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="ts-btn notranslate" data-size="xl" title="Bardzo duży tekst" aria-label="Bardzo duży tekst">
                                            <svg class="ts-icon ts-icon-lg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M4 19h3.5l1.4-4.2h6.2L16.5 19H20L14.5 3h-5L4 19Zm6.4-7L12 7.2 13.6 12h-3.2Z" fill="#374151"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <!-- Ukryty kontener Google Translate (wymagany przez skrypt) -->
                                <div id="google_translate_element" style="display:none"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Level flow centered -->
            <div class="assessment-flow" aria-label="Postęp poziomów" style="margin-top:6px;">
                <div class="flow-left" style="width:100%;">
                    @php $totalLevels = $hasLevelNames ? count($levelNames) : 5; @endphp
                    <div class="flow-info">Poziom {{ min($currentLevel, $totalLevels) }} z {{ $totalLevels }} • {{ $currentLevelName }}</div>
                    <div class="steps" role="list">
                        @for ($i = 1; $i <= $totalLevels; $i++)
                            @php
                                $isCurrent = ($i == $currentLevel);
                                $isDone = ($i < $currentLevel);
                                $fullName = $hasLevelNames && !empty($levelNames[$i]) ? $levelNames[$i] : 'Poziom ' . $i;
                            @endphp
                            <div 
                                class="step {{ $isCurrent ? 'current' : '' }} {{ $isDone ? 'done' : '' }}" 
                                role="listitem" 
                                aria-current="{{ $isCurrent ? 'step' : 'false' }}"
                            >
                                <div class="ring" data-step="{{ $i }}" title="{{ $isCurrent ? '0% uzupełnione' : ($isDone ? '100% uzupełnione' : '0% uzupełnione') }}">
                                    <span class="ring-text">{{ $isCurrent ? '0' : ($isDone ? '100' : '') }}</span>
                                </div>
                                @php
                                    $short = match($i){
                                        1 => 'Jr',
                                        2 => 'Spec',
                                        3 => 'Sr',
                                        4 => 'Sup',
                                        5 => 'Mgr',
                                        default => 'L'.$i,
                                    };
                                @endphp
                                <div class="step-label" title="{{ $fullName }}" data-short="{{ $short }}" data-num="{{ $i }}">{{ $fullName }}</div>
                            </div>
                            @if ($i < $totalLevels)
                                <div class="connector" aria-hidden="true"></div>
                            @endif
                        @endfor
                    </div>
                </div>
                <div class="flow-right" style="display:none"></div>
            </div>
        </div>
    </header>
    <div class="container-wrapper">
        <!-- Global submit overlay -->
        <div id="submitOverlay" class="submit-overlay" role="status" aria-live="polite" aria-label="Trwa zapisywanie">
            <div class="box"><div class="spinner" aria-hidden="true"></div><div id="submitOverlayText">Zapisywanie…</div></div>
        </div>
        <div class="container">

            <!-- Toggle poprzedniego cyklu usunięty - dane wyświetlają się domyślnie -->
                <style>
                    .prev-badge{display:inline-block;color:#555;font-size:12px;margin-left:8px}
                    .prev-value{display:block;color:#777;font-size:12px;margin-top:6px}
                    /* Improved rating dots and labels layout */
                    .rating-dots {
                        display: flex;
                        flex-direction: column;
                        gap: 8px;
                        margin-bottom: 10px;
                    }
                    .dots-wrap {
                        display: flex;
                        flex-direction: row;
                        justify-content: space-between;
                        gap: 24px;
                        margin-top: 6px;
                        margin-bottom: 4px;
                    }
                    .dot {
                        width: 32px;
                        height: 32px;
                        border-radius: 50%;
                        border: 2px solid #1976d2;
                        background: #fff;
                        margin: 0 auto;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 20px;
                        cursor: pointer;
                        transition: box-shadow 0.2s;
                        position: relative;
                    }
                    .dot.selected {
                        background: #1976d2;
                        color: #fff;
                        box-shadow: 0 0 0 2px #1976d233;
                    }
                    .dot.star {
                        border-color: #fbc02d;
                        color: #fbc02d;
                    }
                    .dot.star.selected {
                        background: #fbc02d;
                        color: #fff;
                    }
                    .dots-legend {
                        display: flex;
                        flex-direction: row;
                        justify-content: space-between;
                        gap: 24px;
                        margin-bottom: 2px;
                        margin-top: 2px;
                    }
                    .legend-item {
                        font-size: 14px;
                        color: #333;
                        text-align: center;
                        width: 70px;
                        min-width: 60px;
                        max-width: 90px;
                        word-break: break-word;
                        white-space: normal;
                        display: inline-block;
                    }
                    @media (max-width: 700px) {
                        .dots-wrap, .dots-legend {
                            gap: 8px;
                        }
                        .legend-item {
                            font-size: 12px;
                            width: 48px;
                            min-width: 40px;
                            max-width: 60px;
                        }
                        .dot {
                            width: 24px;
                            height: 24px;
                            font-size: 16px;
                        }
                    }
                </style>
            </div>
            

            <!-- Modal -->
            <div id="skipModal" class="modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="skipModalTitle">
                <div class="modal-content" tabindex="-1">
                    <h3 id="skipModalTitle" style="margin-top:0;margin-bottom:10px;">Potwierdź pominięcie</h3>
                    <p>Pominięcie dalszej oceny oznacza, że nie chcesz oceniać się na tym oraz wyższych poziomach i to jest w porządku. Potwierdź tylko, czy to jest to, co miałeś na myśli.</p>
                    <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-top:10px;">
                        <button id="confirmSkip" class="confirm-button" type="button">Tak, pomiń pozostałe pytania</button>
                        <button id="cancelSkip" class="cancel-button" type="button">Wróć</button>
                    </div>
                </div>
            </div>

            @if(session('show_modal'))
            <!-- Modal -->
            <div id="saveModal" class="modal" style="display:flex;" role="dialog" aria-modal="true" aria-labelledby="saveModalTitle">
                <div class="modal-content" tabindex="-1">
                    <h3 id="saveModalTitle" style="margin-top:0;margin-bottom:10px;">Zapisano postęp</h3>
                    <p>Twoja dotychczasowa samoocena została zapisana. Aby wrócić później do edycji tego formularza, użyj tego linku:</p>
                    <div class="edit-link-wrapper">
                        <input type="text" id="editLinkModal" value="{{ route('form.edit', ['uuid' => $uuid]) }}" readonly>
                        <button onclick="copyLink()" class="button copy-button">
                            <i class="fas fa-copy"></i> Kopiuj link
                        </button>
                    </div>
                    <button id="closeModal" class="cancel-button">Zamknij</button>
                </div>
            </div>
            @endif



            <!-- Główny formularz -->
            <form action="{{ route('save_results') }}" method="POST" id="assessmentForm">
                @csrf

                <!-- Ukryte pola z danymi użytkownika -->
                <input type="hidden" name="name" value="{{ session('name') }}">
                <input type="hidden" name="email" value="{{ session('email') }}">
                <input type="hidden" name="department" value="{{ session('department') }}">
                <input type="hidden" name="current_level" value="{{ $currentLevel }}">
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <input type="hidden" name="uuid" value="{{ $uuid }}">
                <input type="hidden" name="action" id="formAction" value="">



                @php
                    function getCompetencyClass($competencyType) {
                        if (strpos($competencyType, '1. Osobiste') !== false) {
                            return 'osobiste';
                        } elseif (strpos($competencyType, '2. Społeczne') !== false) {
                            return 'spoleczne';
                        } elseif (strpos($competencyType, '3.L.') !== false) {
                            return 'zawodowe-logistics';
                        } elseif (strpos($competencyType, '3.G.') !== false) {
                            return 'zawodowe-growth';
                        } elseif (strpos($competencyType, '3.') !== false) {
                            return 'zawodowe-inne';
                        } elseif (strpos($competencyType, '4. Liderskie') !== false) {
                            return 'liderskie';
                        } else {
                            return '';
                        }
                    }
                @endphp


                <!-- Pętla wyświetlająca pytania -->
                @foreach($competencies as $competency)
                @php
                    // Zbuduj dymek z opisem z zeszłego roku zgodnie z mapowaniem wartości -> tekst opisu
                    $prevValGlobal = $prevAnswers['score'][$competency->id] ?? null;
                    $prevTextGlobal = null;
                    if ($prevValGlobal !== null) {
                        if ((float)$prevValGlobal == 0) { $prevTextGlobal = 'Nie dotyczy / brak oceny początkowo'; }
                        elseif ((float)$prevValGlobal == 0.25) { $prevTextGlobal = $competency->description_025; }
                        elseif ((float)$prevValGlobal == 0.5) { $prevTextGlobal = $competency->description_0_to_05; }
                        elseif ((float)$prevValGlobal >= 0.75) { $prevTextGlobal = $competency->description_075_to_1; }
                    }
                    if (!empty($prevAnswers['above_expectations'][$competency->id])) {
                        $prevTextGlobal = $competency->description_above_expectations ?: $prevTextGlobal;
                    }
                @endphp
            @php
                // Precompute values for initial state and data attributes
                $current = $savedAnswers['score'][$competency->id] ?? 0;
                $prev = $prevAnswers['score'][$competency->id] ?? null;
                $hasCommentInit = !empty($savedAnswers['comments'][$competency->id]);
                $isAboveSelectedInit = !empty($savedAnswers['above_expectations'][$competency->id]);
                
                // DEBUG: Log what we're setting for each competency
                if ($competency->id == 3) {
                    \Log::info("DEBUG: Blade template for competency 3", [
                        'competency_id' => $competency->id,
                        'current' => $current,
                        'savedAnswers_for_3' => $savedAnswers['score'][$competency->id] ?? 'NOT_SET',
                        'all_savedAnswers_scores' => array_slice($savedAnswers['score'] ?? [], 0, 5, true)
                    ]);
                }
            @endphp
             <div class="question"
                    data-competency-id="{{ $competency->id }}"
                    data-description0to05="{{ $competency->description_0_to_05 }}"
                    data-description025="{{ $competency->description_025 }}"
                    data-description075to1="{{ $competency->description_075_to_1 }}"
                    data-description-above-expectations="{{ $competency->description_above_expectations }}"
                data-competency-type="{{ $competency->competency_type }}"
                data-level="{{ $currentLevel }}"
                data-score="{{ $current }}"
                data-star="{{ $isAboveSelectedInit ? 1 : 0 }}"
                data-comment="{{ $hasCommentInit ? 1 : 0 }}">

                        <div class="question-header">
                            <div class="badge-container">
                                <span class="badge competency {{ getCompetencyClass($competency->competency_type) }}">{{ preg_replace('/^(\d+\.|3\.(L\.|G\.))\s*/', '', $competency->competency_type) }}</span>
                                <span class="badge level">{{ preg_replace('/^(Poziom\s+\d+\.\s*|\d+\.\s*)/','', $currentLevelName) }}</span>
                            </div>
                        </div>
                        <div class="assessment-subheader">Jak oceniasz swoją kompetencję/cechę:</div>
                        <label style="display:block;font-size:18px;font-weight:700;margin-bottom:8px;">{{ $competency->competency_name }}
                            @if(!empty($prevAnswers['score'][$competency->id]))
                                @php
                                    $prevScore = $prevAnswers['score'][$competency->id];
                                    $isAboveExpectations = !empty($prevAnswers['above_expectations'][$competency->id]);
                                    
                                    // Mapowanie score na definicje
                                    if ($isAboveExpectations) {
                                        $prevDefinition = 'Powyżej oczekiwań';
                                    } elseif ((float)$prevScore == 0) {
                                        $prevDefinition = 'Nie dotyczy';
                                    } elseif ((float)$prevScore == 0.25) {
                                        $prevDefinition = 'Poniżej oczekiwań';
                                    } elseif ((float)$prevScore == 0.5) {
                                        $prevDefinition = 'Wymaga rozwoju';
                                    } elseif ((float)$prevScore == 0.75) {
                                        $prevDefinition = 'Blisko oczekiwań';
                                    } elseif ((float)$prevScore == 1) {
                                        $prevDefinition = 'Spełnia oczekiwania';
                                    } else {
                                        $prevDefinition = '';
                                    }
                                @endphp
                                <span class="prev-badge"><i class="fa fa-history"></i> Poprzednio: {{ $prevScore }}@if($prevDefinition) ({{ $prevDefinition }})@endif @if($isAboveExpectations)⭐@endif</span>
                            @endif
                        </label>
                        <div class="rating-dots" role="radiogroup" aria-label="Ocena">
                            <input type="hidden" name="competency_id[]" value="{{ $competency->id }}">
                            @php
                                $options = [
                                    ['v'=>0, 'label'=>'Nie dotyczy'],
                                    ['v'=>0.25, 'label'=>'Poniżej oczekiwań'],
                                    ['v'=>0.5, 'label'=>'Wymaga rozwoju'],
                                    ['v'=>0.75, 'label'=>'Blisko oczekiwań'],
                                    ['v'=>1, 'label'=>'Spełnia oczekiwania'],
                                ];
                            @endphp
                            <div class="rating-grid">
                                @foreach($options as $opt)
                                    @php
                                        // Preselect from saved score; on a new form $current = 0, so 'Nie dotyczy' will be selected by default
                                        $isSelected = (!$isAboveSelectedInit) && ((string)$current === (string)$opt['v']);
                                        $isPrev = ((string)$prev === (string)$opt['v']);
                                        $prevHadStar = !empty($prevAnswers['above_expectations'][$competency->id]);
                                        $prevStarOnThisDot = $isPrev && $prevHadStar;
                                    @endphp
                                    <div class="rating-col">
                                        <div class="rating-label {{ $isSelected ? 'active' : '' }}" data-value="{{ $opt['v'] }}">{{ $opt['label'] }}</div>
                                        <button type="button" class="dot {{ $isSelected ? 'selected' : '' }} {{ $isPrev ? 'prev' : '' }} {{ $prevStarOnThisDot ? 'prev-star' : '' }}" data-value="{{ $opt['v'] }}" aria-pressed="{{ $isSelected ? 'true' : 'false' }}" title="{{ $opt['label'] }}">
                                            <span class="sr-only">{{ $opt['label'] }}</span>
                                        </button>
                                    </div>
                                @endforeach
                                @php $isAboveSelected = $isAboveSelectedInit; @endphp
                                <div class="rating-col">
                                    <div class="rating-label {{ $isAboveSelected ? 'active' : '' }}" data-above="1">Powyżej oczekiwań</div>
                                    <button type="button" class="dot {{ $isAboveSelected ? 'selected' : '' }}" data-value="1" data-above="1" aria-pressed="{{ $isAboveSelected ? 'true' : 'false' }}" title="Powyżej oczekiwań">
                                        <span class="sr-only">Powyżej oczekiwań</span>
                                    </button>
                                </div>
                                <input type="hidden" name="score[{{ $competency->id }}]" value="{{ $current }}" class="score-input">
                                <input type="hidden" name="above_expectations[{{ $competency->id }}]" value="{{ $isAboveSelected ? 1 : 0 }}" class="star-input">
                            </div>
                        </div>

                        <!-- Opis suwaka -->
                        <div class="slider-description" style="display:none; margin-top:15px;"></div>

                        <!-- Definicja wybranego poziomu (dymek) -->
                        <div class="definition-bubble" style="display:none;">
                            <div style="font-weight:600;color:#1976d2;margin-bottom:6px;">Definicja wybranego poziomu kompetencji:</div>
                            <div class="def-content"></div>
                        </div>

                        <!-- Checkbox "Dodaj uzasadnienie" -->
                        <div class="add-description-container">
                            <input id="adddesc-{{ $competency->id }}" type="checkbox" name="add_description[{{ $competency->id }}]" onchange="toggleDescriptionInput(this)" {{ isset($savedAnswers['add_description'][$competency->id]) ? 'checked' : '' }}>
                            <label for="adddesc-{{ $competency->id }}" class="assessment-label">Dodaj uzasadnienie</label>
                        </div>

                        <!-- DEBUG okienko usunięte - dane działają poprawnie -->
                        <!-- Dymek z definicją poziomu (prevTextGlobal) usunięty - niepotrzebny -->

                        @php $hasPrev = !empty($prevAnswers['comments'][$competency->id]) || !empty($prevAnswers['manager_feedback'][$competency->id]); @endphp

                        <!-- Pole tekstowe na opis -->
                        <div class="textarea-description" style="display:none;">
                            <textarea name="comments[{{ $competency->id }}]" placeholder="Wpisz opis/argumentację...">{{ $savedAnswers['comments'][$competency->id] ?? '' }}</textarea>
                        </div>
                        <!-- DEBUG okienko 2 usunięte - dane działają poprawnie -->
                        <!-- Duplikat "Jak opisaliśmy to poprzednio" usunięty -->
                        <!-- Poprzedni komentarz (read-only box below textarea) -->
                        @if($hasPrev && (!empty($prevAnswers['comments'][$competency->id]) || !empty($prevAnswers['manager_feedback'][$competency->id])))
                        <div class="prev-conversation-section" style="margin-top:10px;">
                            <button type="button" class="prev-conversation-toggle" onclick="togglePrevConversation(this)">
                                <i class="fas fa-chevron-right conversation-chevron"></i>
                                <span>Zobacz poprzednie uzasadnienia i feedback</span>
                            </button>
                            <div class="prev-conversation-content" style="display:none; margin-top:8px;">
                                @if(!empty($prevAnswers['comments'][$competency->id]))
                                    <!-- Chat-style dymek użytkownika (po prawej) -->
                                    <div class="chat-message user-message" style="margin-top:8px;">
                                        <div class="message-content">
                                            <div class="message-header">Co napisałeś/aś poprzednim razem:</div>
                                            <div class="message-text">{{ $prevAnswers['comments'][$competency->id] }}</div>
                                        </div>
                                        <div class="message-avatar user-avatar-text">
                                            JA
                                        </div>
                                    </div>
                                @endif
                                @if(!empty($prevAnswers['manager_feedback'][$competency->id]))
                                    <!-- Chat-style dymek managera (po lewej) -->
                                    <div class="chat-message manager-message" style="margin-top:8px;">
                                        <div class="message-avatar">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <div class="message-content">
                                            <div class="message-header">Feedback od twojego przełożonego:</div>
                                            <div class="message-text">{{ $prevAnswers['manager_feedback'][$competency->id] }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                @endforeach

                <!-- Przycisk Submit -->
                <div style="display: flex; justify-content: space-between;">
                    <button type="submit" name="back" class="back-button">Wróć</button>
                    @if ($currentLevel == 6)
                        <button type="submit" name="submit" class="save-button">Wyślij</button>
                    @else
                        <button type="submit" name="next" class="save-button">Przejdź dalej</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
    <script>
    function copyToClipboard() {
        var copyText = document.getElementById("editLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999); // Dla urządzeń mobilnych

        document.execCommand("copy");

        alert("Link został skopiowany do schowka.");
    }

    // Apply category class to question containers and clean labels
    document.querySelectorAll('.question').forEach(function(q){
        var type = (q.getAttribute('data-competency-type')||'')+'';
        var cls = '';
        if (type.indexOf('1. Osobiste')!==-1) cls = 'osobiste';
        else if (type.indexOf('2. Społeczne')!==-1) cls = 'spoleczne';
        else if (type.indexOf('4. Liderskie')!==-1) cls = 'liderskie';
        else if (type.indexOf('3.L.')!==-1) cls = 'zawodowe-logistics';
        else if (type.indexOf('3.G.')!==-1) cls = 'zawodowe-growth';
        else if (type.indexOf('3.')!==-1) cls = 'zawodowe-inne';
        if (cls) q.classList.add(cls);
        var badge = q.querySelector('.badge.competency');
        if (badge){ badge.textContent = type.replace(/^\d+\.?\s*/,'').replace(/^3\.(L\.|G\.)\s*/,''); }
        var levelBadge = q.querySelector('.badge.level');
        if (levelBadge){
            levelBadge.textContent = levelBadge.textContent
                .replace(/Poziom\s+\d+\.\s*/,'Poziom ')
                .replace(/^Poziom\s+\d+\.?\s*/,'Poziom ');
        }
    });
    </script>
<script>
// Handle rating dots interactions
document.querySelectorAll('.question').forEach(function(q){
    const wrap = q.querySelector('.rating-dots'); if(!wrap) return;
    const scoreInput = q.querySelector('.score-input');
    const starInput = q.querySelector('.star-input');
    const defBubble = q.querySelector('.definition-bubble');
    const defContent = q.querySelector('.definition-bubble .def-content');
    const addDescCheckbox = q.querySelector('input[name^="add_description"]');

    function updateDefinition(val){
        const d0 = q.getAttribute('data-description0to05') || '';
        const d025 = q.getAttribute('data-description025') || '';
        const d075 = q.getAttribute('data-description075to1') || '';
        const dAbove = q.getAttribute('data-description-above-expectations') || '';
        let text = '';
        
        // Poprawna kolejność zgodnie z wartościami:
        // 0, 0.25 -> description_025
        // 0.5 -> description_0_to_05
        // 0.75, 1 -> description_075_to_1
        const numVal = parseFloat(val);
        if (numVal >= 0.75) {
            text = d075;
        } else if (numVal >= 0.5) {
            text = d0;
        } else {
            text = d025;
        }
        
        if (parseInt(starInput.value)) text = dAbove || text;
        if (text){ defBubble.style.display='block'; defContent.textContent = text; } else { defBubble.style.display='none'; }
    }

    wrap.querySelectorAll('.dot').forEach(function(dot){
        dot.addEventListener('click', function(){
            const value = this.getAttribute('data-value') || '0';
            const isAbove = this.getAttribute('data-above') === '1';
            scoreInput.value = value;
            // deselect all dots
            wrap.querySelectorAll('.dot').forEach(d=>{ d.classList.remove('selected'); d.setAttribute('aria-pressed','false'); });
            this.classList.add('selected');
            this.setAttribute('aria-pressed','true');
            // set above_expectations flag
            starInput.value = isAbove ? '1' : '0';
            // Mark that user made changes
            markUserChanges();
            // Update label active
            const legends = q.querySelectorAll('.rating-label');
            legends.forEach(l=>{
                const v = l.getAttribute('data-value');
                const lAbove = l.getAttribute('data-above') === '1';
                l.classList.toggle('active', (isAbove && lAbove) || (!isAbove && v === value));
            });
            // Handle 'Dodaj uzasadnienie' behavior
            const textarea = q.querySelector('.textarea-description textarea');
            if (addDescCheckbox) {
                if (isAbove) {
                    // Force-check and disable when 'Powyżej oczekiwań'
                    if (!addDescCheckbox.checked) {
                        addDescCheckbox.checked = true;
                        toggleDescriptionInput(addDescCheckbox);
                    }
                    addDescCheckbox.disabled = true;
                } else {
                    // Re-enable checkbox; auto-uncheck if textarea empty
                    addDescCheckbox.disabled = false;
                    const isEmpty = !textarea || textarea.value.trim() === '';
                    if (isEmpty && addDescCheckbox.checked) {
                        addDescCheckbox.checked = false;
                        toggleDescriptionInput(addDescCheckbox);
                    }
                }
            }
            updateDefinition(value);
            // Update active row highlight
            wrap.querySelectorAll('.rating-col').forEach(function(c){ c.classList.remove('active'); });
            this.closest('.rating-col')?.classList.add('active');
            try { updateHeaderProgress(); } catch(e) {}
            // autosave after selection
            markUserChanges();
            try { triggerAutosaveDebounced(); } catch(e) {}
        });
    });

    // Make entire rating row clickable on mobile and highlight active row
    wrap.querySelectorAll('.rating-col').forEach(function(col){
        col.addEventListener('click', function(e){
            // Avoid double-handling when actual dot was clicked
            if (e.target && e.target.classList && e.target.classList.contains('dot')) return;
            const dot = col.querySelector('.dot');
            if (dot) dot.click();
        });
    });

    // Autosave on textarea input (debounced)
    const textarea = q.querySelector('.textarea-description textarea');
    if (textarea){ textarea.addEventListener('input', function(){ markUserChanges(); try { triggerAutosaveDebounced(1200); } catch(e) {} }); }

    // Init bubble and selection state
    // Initialize from data attributes first to avoid any accidental defaults
    const initScore = (q.getAttribute('data-score') || scoreInput.value || '0').toString();
    // Only trust data-star from server, and only if a comment existed (since star requires justification)
    const initStar = (q.getAttribute('data-star') === '1') && (q.getAttribute('data-comment') === '1');
    console.log('DEBUG: Init values - competency ID:', q.getAttribute('data-competency-id'), 'data-score:', q.getAttribute('data-score'), 'initScore:', initScore, 'initStar:', initStar);
    scoreInput.value = initScore;
    starInput.value = initStar ? '1' : '0';
    // Only show definition once a selection exists
    function clearSelection(){
        wrap.querySelectorAll('.dot').forEach(d=>{ d.classList.remove('selected'); d.setAttribute('aria-pressed','false'); });
        const legends = q.querySelectorAll('.rating-label');
        legends.forEach(l=>l.classList.remove('active'));
        defBubble.style.display='none';
    }
    if (initStar){
        // Explicitly select the "Powyżej oczekiwań" dot
        const aboveBtn = wrap.querySelector('.dot[data-above="1"]');
        if (aboveBtn){
            // Clear any other accidental selections first
            wrap.querySelectorAll('.dot').forEach(d=>{ d.classList.remove('selected'); d.setAttribute('aria-pressed','false'); });
            aboveBtn.classList.add('selected');
            aboveBtn.setAttribute('aria-pressed','true');
            const legends = q.querySelectorAll('.rating-label');
            legends.forEach(l=>{
                const lAbove = l.getAttribute('data-above') === '1';
                l.classList.toggle('active', lAbove);
            });
            if (addDescCheckbox) {
                if (!addDescCheckbox.checked) {
                    addDescCheckbox.checked = true;
                    toggleDescriptionInput(addDescCheckbox, true); // isInitializing = true
                }
                // Disable checkbox while 'Powyżej oczekiwań' is active
                addDescCheckbox.disabled = true;
            }
            // Show definition for "Powyżej oczekiwań"
            updateDefinition(initScore);
        }
    } else {
        // Not above-expectations: ensure star is NOT selected and select saved score if > 0
        const aboveBtn = wrap.querySelector('.dot[data-above="1"]');
        if (aboveBtn){
            aboveBtn.classList.remove('selected');
            aboveBtn.setAttribute('aria-pressed','false');
        }
        // Select the dot matching saved score; for a new form it's '0' (Nie dotyczy)
        const targetVal = (scoreInput.value || '0').toString();
        console.log('DEBUG: Initializing dots - competency ID:', q.getAttribute('data-competency-id'), 'targetVal:', targetVal, 'scoreInput.value:', scoreInput.value);
        if (parseFloat(targetVal) >= 0){
            // Normalize values: "1.00" should match "1", "0.75" should match "0.75"
            const normalizedTarget = parseFloat(targetVal).toString();
            let targetDot = wrap.querySelector(`.dot[data-value="${normalizedTarget}"]:not([data-above="1"])`);
            console.log('DEBUG: Found targetDot for', normalizedTarget, '(from', targetVal, '):', targetDot);
            if (targetDot) {
                wrap.querySelectorAll('.dot').forEach(d=>{ d.classList.remove('selected'); d.setAttribute('aria-pressed','false'); });
                targetDot.classList.add('selected');
                targetDot.setAttribute('aria-pressed','true');
                // Sync active legend labels
                const legends = q.querySelectorAll('.rating-label');
                legends.forEach(l=>{
                    const v = l.getAttribute('data-value');
                    const lAbove = l.getAttribute('data-above') === '1';
                    l.classList.toggle('active', (!lAbove && v === (scoreInput.value || '0')));
                });
                // Show definitions for all levels including 0 (Nie dotyczy) if they exist
                updateDefinition(targetVal);
            } else {
                clearSelection();
            }
        }
        // Ensure checkbox is enabled when not 'Powyżej oczekiwań'
        if (addDescCheckbox) addDescCheckbox.disabled = false;
    }
    // After initializing selections, update header once in case of pre-filled values
    try { updateHeaderProgress(); } catch(e) {}
    // Highlight active row initially for this question
    (function(){
        const selectedDot = wrap.querySelector('.dot.selected');
        wrap.querySelectorAll('.rating-col').forEach(function(c){ c.classList.remove('active'); });
        if (selectedDot){ selectedDot.closest('.rating-col')?.classList.add('active'); }
    })();

    // Final safety: if nothing is selected (edge cases), default to 'Nie dotyczy' (0)
    // BUT ONLY if scoreInput is also empty/zero - respect saved values!
    if (!wrap.querySelector('.dot.selected') && (!scoreInput.value || scoreInput.value === '0')){
        const zeroDot = wrap.querySelector('.dot[data-value="0"]:not([data-above="1"])');
        if (zeroDot){
            zeroDot.classList.add('selected');
            zeroDot.setAttribute('aria-pressed','true');
            scoreInput.value = '0';
            // activate corresponding label and show definition if exists
            const legends = q.querySelectorAll('.rating-label');
            legends.forEach(l=>{
                const v = l.getAttribute('data-value');
                const lAbove = l.getAttribute('data-above') === '1';
                l.classList.toggle('active', (!lAbove && v === '0'));
            });
            updateDefinition('0');
            zeroDot.closest('.rating-col')?.classList.add('active');
        }
    }
    try { updateHeaderProgress(); } catch(e) {}
});

// Header progress computation: current level ring shows percentage of answered questions
function updateHeaderProgress() {
    var hiddenLevel = document.querySelector('input[name="current_level"]');
    var currentLevel = hiddenLevel ? parseInt(hiddenLevel.value || '1', 10) : 1;
    var questions = document.querySelectorAll('.question');
    var total = 0, answered = 0;
    questions.forEach(function(q){
        // Count only questions for current level using data-level to avoid parsing text
        var qLevel = parseInt(q.getAttribute('data-level') || '0', 10);
        if (qLevel !== currentLevel) return;
        total++;
        var selected = q.querySelector('.rating-dots .dot.selected');
        if (selected) {
            var isAbove = selected.getAttribute('data-above') === '1';
            var val = parseFloat(selected.getAttribute('data-value') || '0');
            // Count only answers other than 'Nie dotyczy' (0) or explicitly 'Powyżej oczekiwań'
            if (isAbove || val > 0) answered++;
        }
    });
    var pct = total > 0 ? Math.round((answered/total)*100) : 0;
    var currentStep = document.querySelector('.steps .step.current');
    if (currentStep){
        var ring = currentStep.querySelector('.ring');
        var text = currentStep.querySelector('.ring-text');
        if (ring){
            // Use degrees for reliable rendering across browsers (0% edge case)
            var deg = Math.max(0, Math.min(360, pct * 3.6));
            ring.style.background = 'conic-gradient(#2563eb ' + deg + 'deg, #e5e7eb 0 360deg)';
            ring.title = pct + '% uzupełnione';
        }
        if (text){ text.textContent = String(pct); }
    }
}

// Initial header update after DOM ready (in case no interactions yet)
document.addEventListener('DOMContentLoaded', function(){
    try { updateHeaderProgress(); } catch(e) {}
    // Safety: if for any reason ring text is empty on current step, set to 0
    var cur = document.querySelector('.steps .step.current');
    if (cur){
        var t = cur.querySelector('.ring-text');
        var ring = cur.querySelector('.ring');
        if (t && (t.textContent === '' || isNaN(parseInt(t.textContent,10)))) t.textContent = '0';
        if (ring && (!ring.style.background || ring.style.background.indexOf('conic-gradient') === -1)){
            ring.style.background = 'conic-gradient(#2563eb 0deg, #e5e7eb 0 360deg)';
        }
    }

    // Submit loading state: disable buttons, show overlay and spinner on the clicked button
    var form = document.getElementById('assessmentForm');
    var lastClickedBtn = null;
    document.querySelectorAll('button[type="submit"][form="assessmentForm"], #assessmentForm button[type="submit"]').forEach(function(btn){
        btn.addEventListener('click', function(){
            lastClickedBtn = this;
            var act = document.getElementById('formAction');
            if (act){ act.value = (this.getAttribute('name')||'').toLowerCase(); }
        });
    });
    function setButtonLoading(btn, text){
        if (!btn) return;
        btn.classList.add('is-loading');
        btn.disabled = true;
        try { btn.dataset._original = btn.innerHTML; } catch(e) {}
        btn.innerHTML = '<span class="btn-spinner" aria-hidden="true"></span>' + (text||btn.textContent||'...');
    }

    function setAllDisabled(){
        document.querySelectorAll('#assessmentForm button[type="submit"], button[form="assessmentForm"]').forEach(function(b){ b.disabled = true; b.classList.add('is-loading'); });
    }
    function openOverlay(msg){
        var ov = document.getElementById('submitOverlay');
        var txt = document.getElementById('submitOverlayText');
        if (txt) txt.textContent = msg || 'Zapisywanie…';
        if (ov) ov.style.display = 'flex';
    }
    if (form){
        form.addEventListener('submit', function(){
            var msg = 'Zapisywanie…';
            var label = 'Zapisywanie…';
            if (lastClickedBtn){
                var n = (lastClickedBtn.getAttribute('name')||'').toLowerCase();
                if (n === 'next') { msg = 'Przechodzę dalej…'; label = 'Przechodzę dalej…'; }
                else if (n === 'back') { msg = 'Wracam…'; label = 'Wracam…'; }
                else if (n === 'save_and_exit') { msg = 'Zapisuję…'; label = 'Zapisuję…'; }
                else if (n === 'submit') { msg = 'Wysyłam…'; label = 'Wysyłam…'; }
                setButtonLoading(lastClickedBtn, label);
            }
            setAllDisabled();
            openOverlay(msg);
            
            // Reset user changes flag when submitting (navigating to next level)
            hasUserMadeChanges = false;
            console.log('FORM SUBMITTED: Reset hasUserMadeChanges = false');
        });
    }
});

// --- Language: Google Translate integration with custom selector ---
function googleTranslateElementInit(){
    new google.translate.TranslateElement({pageLanguage:'pl', includedLanguages:'en,uk,es,pt,fr,pl', autoDisplay:false}, 'google_translate_element');
}
(function loadGTranslate(){
    var s = document.createElement('script');
    s.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    document.head.appendChild(s);
})();

// Cookie helpers for Google Translate
function setCookie(name, value, days, domain){
    var d = new Date();
    d.setTime(d.getTime() + ((days||365)*24*60*60*1000));
    var expires = '; expires=' + d.toUTCString();
    var path = '; path=/';
    var dm = domain ? '; domain=' + domain : '';
    document.cookie = name + '=' + encodeURIComponent(value) + expires + path + dm;
}
function getCookie(name){
    var m = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\\/\+^])/g,'\\$1') + '=([^;]*)'));
    return m ? decodeURIComponent(m[1]) : null;
}
function setGoogTransCookie(lang){
    var val = '/pl/' + lang;
    var host = location.hostname;
    setCookie('googtrans', val, 365);
    setCookie('googtrans', val, 365, '.' + host);
}
function translationAppliedTo(lang){
    var c = getCookie('googtrans');
    var htmlCls = document.documentElement.className || '';
    return (c && c.indexOf('/pl/' + lang) !== -1) || /translated-/.test(htmlCls);
}
function reinitTranslateGadget(cb){
    try {
        var container = document.getElementById('google_translate_element');
        if (container) container.innerHTML = '';
        googleTranslateElementInit();
        var tries = 0;
        (function wait(){
            var sel = document.querySelector('select.goog-te-combo');
            if (sel || tries++ > 20) return cb && cb(sel);
            setTimeout(wait, 100);
        })();
    } catch(e){ cb && cb(null); }
}
// Helper: programmatically set Google Translate language by simulating change with fallbacks
function setLanguage(lang){
    // Try direct select change first
    var select = document.querySelector('select.goog-te-combo');
    if (select){
        select.value = lang;
        select.dispatchEvent(new Event('change'));
    }
    // Also set the googtrans cookie to enforce state
    setGoogTransCookie(lang);
    // After a short delay, verify application; if not applied, re-init gadget and retry; final fallback reload
    setTimeout(function(){
        if (!translationAppliedTo(lang)){
            reinitTranslateGadget(function(sel){
                if (sel){
                    sel.value = lang;
                    sel.dispatchEvent(new Event('change'));
                }
                setTimeout(function(){
                    if (!translationAppliedTo(lang)){
                        // Last resort: reload to let Google apply cookie state
                        location.reload();
                    }
                }, 900);
            });
        }
    }, 700);
}

// Hook custom language selector
document.addEventListener('DOMContentLoaded', function(){
    var langSelect = document.getElementById('langSelect');
    if (langSelect){
        // Load saved language
        var savedLang = localStorage.getItem('ui_lang');
        if (savedLang){ langSelect.value = savedLang; setTimeout(function(){ setLanguage(savedLang); }, 800); }
        langSelect.addEventListener('change', function(){
            localStorage.setItem('ui_lang', this.value);
            setLanguage(this.value);
            setTimeout(updateGtOffset, 800);
        });
    }
});

// --- Text size switcher ---
function applyTextSize(size){
    document.body.classList.remove('text-lg','text-xl');
    if (size === 'lg') document.body.classList.add('text-lg');
    if (size === 'xl') document.body.classList.add('text-xl');
    // Toggle pressed states
    document.querySelectorAll('.ts-btn').forEach(function(b){
        b.setAttribute('aria-pressed', b.getAttribute('data-size') === size ? 'true' : 'false');
    });
}
document.addEventListener('DOMContentLoaded', function(){
    var savedSize = localStorage.getItem('ui_text_size') || 'base';
    applyTextSize(savedSize);
    document.querySelectorAll('.ts-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            var size = this.getAttribute('data-size');
            localStorage.setItem('ui_text_size', size);
            applyTextSize(size);
        });
    });
    // Initial offset fix (if Google banner is injected late)
    setTimeout(updateGtOffset, 800);
    window.addEventListener('resize', function(){ setTimeout(updateGtOffset, 200); });
});

// Compute and set CSS var for Google Translate banner height
function updateGtOffset(){
    try {
        var h = 0;
        // 1) Google często daje body { top: Xpx } – zbierz to jako źródło prawdy
        var bodyTop = parseInt(getComputedStyle(document.body).top || '0', 10);
        if (!isNaN(bodyTop) && bodyTop > 0) { h = bodyTop; }
        // 2) Dodatkowo sprawdź iframe banera (gdy obecny)
        var frame = document.querySelector('.goog-te-banner-frame');
        if (frame && frame.offsetHeight) {
            h = Math.max(h, frame.offsetHeight);
        }
        document.documentElement.style.setProperty('--gt-offset', (h||0) + 'px');
    } catch(e) {
        document.documentElement.style.setProperty('--gt-offset', '0px');
    }
}

// Obserwuj zmiany w DOM (Google wstrzykuje/usuwa baner dynamicznie)
(function observeGt(){
    var timeout;
    function debounced(){ clearTimeout(timeout); timeout = setTimeout(updateGtOffset, 100); }
    var mo = new MutationObserver(debounced);
    mo.observe(document.documentElement, { childList: true, subtree: true, attributes: true, attributeFilter: ['style', 'class'] });
})();

// Global function for toggling previous conversation
function togglePrevConversation(button) {
    const content = button.nextElementSibling;
    const isExpanded = content.style.display !== 'none';
    
    if (isExpanded) {
        content.style.display = 'none';
        button.classList.remove('expanded');
    } else {
        content.style.display = 'block';
        button.classList.add('expanded');
    }
}
</script>
</body>



</html>

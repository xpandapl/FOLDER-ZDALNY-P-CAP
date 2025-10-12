@extends('layouts.self-assessment')

@section('content')
    <div class="center" style="margin-bottom: 12px;">
        <h1>Start samooceny @if($cycle) <span style="color: var(--primary)">({{ $cycle->label }})</span> @endif</h1>
        <p class="muted">Wybierz odpowiednią ścieżkę. Jeśli wypełniałeś/aś samoocenę w poprzednim roku – skorzystaj z opcji <strong>Weteran</strong>. Nowe osoby wybierają <strong>Świeżak</strong>.</p>
    </div>

    <div class="grid grid-cols-2" style="margin-bottom:24px;">
        <div class="container" style="padding:16px; border:1px solid #d1fae5; box-shadow:none;">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:10px;">
                <div style="width:56px;height:56px; border-radius:50%; background:#10b981; color:#fff; display:flex; align-items:center; justify-content:center; font-size:26px;">🌱</div>
                <h3 style="margin:0;">Świeżak</h3>
            </div>
            <p>Pierwsza samoocena lub nowa rola. Zaczynasz od wprowadzenia danych i przechodzisz przez pytania krok po kroku.</p>
            <a href="{{ route('self.assessment.step1') }}" class="btn btn-success" style="margin-top:6px;">Rozpocznij jako Świeżak</a>
        </div>
        <div class="container" style="padding:16px; border:1px solid #bfdbfe; box-shadow:none;">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:10px;">
                <div style="width:56px;height:56px; border-radius:50%; background:#2563eb; color:#fff; display:flex; align-items:center; justify-content:center; font-size:26px;">🧭</div>
                <h3 style="margin:0;">Weteran</h3>
            </div>
            <p>Masz już wyniki z poprzedniego cyklu. Wpisz kod dostępu aby zobaczyć poprzednie odpowiedzi i dodać nowe.</p>
            <a href="{{ route('start.veteran.form') }}" class="btn btn-primary" style="margin-top:6px;">Mam kod – przejdź</a>
        </div>
    </div>

    <div>
        <h2 style="font-size:16px; margin-bottom:8px;">Legenda ocen (planowana nowa skala)</h2>
        <div class="grid" style="grid-template-columns: repeat(5,minmax(0,1fr)); gap:12px; font-size:12px;">
            <div class="container" style="padding:12px; text-align:center; box-shadow:none;">
                <div style="font-weight:700;">0</div>
                <div class="muted">Brak / jeszcze nie</div>
            </div>
            <div class="container" style="padding:12px; text-align:center; box-shadow:none;">
                <div style="font-weight:700;">0.25</div>
                <div class="muted">Pierwsze kroki</div>
            </div>
            <div class="container" style="padding:12px; text-align:center; box-shadow:none;">
                <div style="font-weight:700;">0.5</div>
                <div class="muted">Częściowo</div>
            </div>
            <div class="container" style="padding:12px; text-align:center; box-shadow:none;">
                <div style="font-weight:700;">0.75</div>
                <div class="muted">Prawie stabilnie</div>
            </div>
            <div class="container" style="padding:12px; text-align:center; box-shadow:none;">
                <div style="font-weight:700;">1.0 ⭐</div>
                <div class="muted">Stabilnie</div>
            </div>
        </div>
        <div class="muted" style="margin-top:8px; font-size:12px;">Dodatkowo: "Powyżej oczekiwań" = specjalny znacznik (ikonka gwiazdki), który nadpisze ocenę 1.0.</div>
    </div>
@endsection

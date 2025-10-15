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
.sa-start .legend-grid { display:grid; grid-template-columns: repeat(5, minmax(0,1fr)); gap:10px; }
.sa-start .legend-item { border:1px solid #e5e7eb; border-radius:10px; padding:10px; background:#fff; text-align:center; }
.sa-start .legend-item .num { font-weight:700; color:#111827; }
.sa-start .legend-item .lbl { color:#4b5563; font-size:12px; margin-top:4px; }

@media (max-width: 760px){
  .sa-start .choices { grid-template-columns: 1fr; }
  .sa-start .legend-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
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
                <a href="{{ route('self.assessment.step1') }}" class="btn btn-success">Rozpocznij jako Świeżak</a>
            </div>
            <div class="card">
                <h3>Weteran</h3>
                <p>Masz już wyniki z poprzedniego cyklu. Wpisz kod dostępu, aby wczytać poprzednie odpowiedzi i dokończyć aktualną samoocenę.</p>
                <a href="{{ route('start.veteran.form') }}" class="btn btn-primary">Mam kod – przejdź</a>
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
            </div>
        </div>
    </div>
@endsection

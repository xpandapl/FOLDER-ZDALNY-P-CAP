@extends('layouts.self-assessment')

@section('content')
<style>
/* Scoped styles for veteran view, aligned with start */
.sa-veteran { max-width: 640px; margin: 0 auto; }
.sa-veteran .lead { color:#4b5563; margin-top:6px; }
.sa-veteran .alert { border:1px solid; border-radius:12px; padding:10px 12px; margin-bottom:12px; }
.sa-veteran .alert-error { border-color:#fecaca; background:#fef2f2; color:#991b1b; }
.sa-veteran .alert-info { border-color:#bfdbfe; background:#eff6ff; color:#1e40af; }
.sa-veteran .card { border:1px solid #e5e7eb; border-radius:14px; padding:16px; background:#fff; box-shadow:0 4px 14px rgba(15,23,42,.06); }
.sa-veteran label { display:block; font-weight:600; color:#111827; margin-bottom:6px; }
.sa-veteran input[type="text"] { width:100%; border:1px solid #d1d5db; border-radius:10px; padding:10px 12px; font-size:16px; }
.sa-veteran input[type="text"]:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.15); }
.sa-veteran .hint { color:#6b7280; font-size:12px; margin-top:6px; }
.sa-veteran .actions { display:flex; gap:8px; align-items:center; justify-content:flex-end; margin-top:12px; }
.sa-veteran .footnote { color:#6b7280; font-size:12px; margin-top:12px; }
@media (max-width:760px){ .sa-veteran { padding: 0 6px; } }
</style>

    <div class="sa-veteran">
        <div class="center" style="margin-bottom: 8px;">
            <h1>Dostęp Weterana @if($cycle)<span style="color: var(--primary)">({{ $cycle->label }})</span>@endif</h1>
            <p class="lead">Wpisz otrzymany kod. Po weryfikacji zobaczysz poprzednie wyniki i przejdziesz do aktualizacji.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif
        @if($status = $status ?? null)
            <div class="alert alert-info">{{ $status }}</div>
        @endif

        <div class="card">
            <form method="POST" action="{{ route('start.veteran.submit') }}">
                @csrf
                <div class="form-group">
                    <label for="access_code">Kod dostępu</label>
                    <input type="text" name="access_code" id="access_code" maxlength="50" required autofocus>
                    <div class="hint">Przykład formatu: AB7K-X9P2-LM (docelowo 10–12 znaków)</div>
                </div>
                <div class="actions">
                    <a href="{{ route('start.landing') }}" class="btn btn-text">Powrót</a>
                    <button type="submit" class="btn btn-primary">Kontynuuj</button>
                </div>
            </form>
        </div>
        <div class="footnote">Jeśli nie masz kodu – skontaktuj się ze swoim przełożonym / HR.</div>
    </div>
@endsection

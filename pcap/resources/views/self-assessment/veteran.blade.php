@extends('layouts.self-assessment')

@section('content')
    <div class="center" style="margin-bottom: 12px;">
        <h1>Dostęp Weterana @if($cycle)<span style="color: var(--primary)">({{ $cycle->label }})</span>@endif</h1>
        <p class="muted">Wpisz otrzymany kod. Po weryfikacji zobaczysz poprzednie wyniki i przejdziesz do aktualizacji.</p>
    </div>

    @if($errors->any())
        <div class="container" style="border:1px solid #fecaca; background:#fef2f2; color:#991b1b; margin-bottom:12px;">
            {{ $errors->first() }}
        </div>
    @endif
    @if($status = $status ?? null)
        <div class="container" style="border:1px solid #bfdbfe; background:#eff6ff; color:#1e40af; margin-bottom:12px;">
            {{ $status }}
        </div>
    @endif

    <div class="container" style="max-width:640px;">
        <form method="POST" action="{{ route('start.veteran.submit') }}">
            @csrf
            <div class="form-group">
                <label for="access_code">Kod dostępu</label>
                <input type="text" name="access_code" id="access_code" maxlength="50" required autofocus>
                <div class="muted" style="font-size:12px;">Przykład formatu: AB7K-X9P2-LM (docelowo 10–12 znaków)</div>
            </div>
            <div style="display:flex; gap:8px; align-items:center; justify-content:flex-end;">
                <a href="{{ route('start.landing') }}" class="btn btn-outline">Powrót</a>
                <button type="submit" class="btn btn-primary">Kontynuuj</button>
            </div>
        </form>
        <div class="muted" style="margin-top:12px; font-size:12px;">Jeśli nie masz kodu – skontaktuj się ze swoim przełożonym / HR.</div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center mb-4">
        <div class="col-lg-7 text-center">
            <h1 class="fw-bold mb-3">Dostęp Weterana @if($cycle)<span class="text-primary">({{ $cycle->label }})</span>@endif</h1>
            <p class="text-muted">Wpisz otrzymany kod. Po weryfikacji zobaczysz poprzednie wyniki i przejdziesz do aktualizacji. (Logika walidacji zostanie dodana w kolejnym kroku.)</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            @if($status = $status ?? null)
                <div class="alert alert-info">{{ $status }}</div>
            @endif
            <div class="card shadow-sm">
                <form method="POST" action="{{ route('start.veteran.submit') }}" class="card-body">
                    @csrf
                    <div class="mb-3">
                        <label for="access_code" class="form-label">Kod dostępu</label>
                        <input type="text" name="access_code" id="access_code" class="form-control" maxlength="50" required autofocus>
                        <div class="form-text">Przykład formatu: AB7K-X9P2-LM (docelowo 10–12 znaków)</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('start.landing') }}" class="btn btn-outline-secondary">Powrót</a>
                        <button type="submit" class="btn btn-primary ms-auto">Kontynuuj</button>
                    </div>
                </form>
            </div>
            <div class="mt-4 small text-muted">Jeśli nie masz kodu – skontaktuj się ze swoim przełożonym / HR.</div>
        </div>
    </div>
</div>
@endsection

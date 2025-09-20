@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8 text-center">
            <h1 class="fw-bold mb-3">Start samooceny @if($cycle) <span class="text-primary">({{ $cycle->label }})</span> @endif</h1>
            <p class="text-muted">Wybierz odpowiednią ścieżkę. Jeśli wypełniałeś/aś samoocenę w poprzednim roku – skorzystaj z opcji <strong>Weteran</strong>, aby odblokować dane historyczne. Nowe osoby wybierają <strong>Świeżak</strong>.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-success">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:56px;height:56px;font-size:26px;">🌱</div>
                        <h3 class="h4 mb-0">Świeżak</h3>
                    </div>
                    <p class="flex-grow-1">Pierwsza samoocena lub nowa rola. Zaczynasz od wprowadzenia danych i przechodzisz przez pytania krok po kroku.</p>
                    <a href="{{ route('self.assessment.step1') }}" class="btn btn-success mt-auto">Rozpocznij jako Świeżak</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-primary">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:56px;height:56px;font-size:26px;">🧭</div>
                        <h3 class="h4 mb-0">Weteran</h3>
                    </div>
                    <p class="flex-grow-1">Masz już wyniki z poprzedniego cyklu. Wpisz kod dostępu aby zobaczyć poprzednie odpowiedzi i dodać nowe.</p>
                    <a href="{{ route('start.veteran.form') }}" class="btn btn-primary mt-auto">Mam kod – przejdź</a>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5"/>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h2 class="h4 mb-3">Legenda ocen (planowana nowa skala)</h2>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-5 g-3 small">
                <div class="col">
                    <div class="border rounded p-3 h-100 text-center">
                        <div class="fw-bold">0</div>
                        <div class="text-muted">Brak / jeszcze nie</div>
                    </div>
                </div>
                <div class="col">
                    <div class="border rounded p-3 h-100 text-center">
                        <div class="fw-bold">0.25</div>
                        <div class="text-muted">Pierwsze kroki</div>
                    </div>
                </div>
                <div class="col">
                    <div class="border rounded p-3 h-100 text-center">
                        <div class="fw-bold">0.5</div>
                        <div class="text-muted">Częściowo</div>
                    </div>
                </div>
                <div class="col">
                    <div class="border rounded p-3 h-100 text-center">
                        <div class="fw-bold">0.75</div>
                        <div class="text-muted">Prawie stabilnie</div>
                    </div>
                </div>
                <div class="col">
                    <div class="border rounded p-3 h-100 text-center">
                        <div class="fw-bold">1.0 ⭐</div>
                        <div class="text-muted">Stabilnie</div>
                    </div>
                </div>
            </div>
            <div class="mt-3 small text-muted">Dodatkowo: "Powyżej oczekiwań" = specjalny znacznik (ikonka gwiazdki), który nadpisze ocenę 1.0. Implementacja w kolejnym kroku.</div>
        </div>
    </div>
</div>
@endsection

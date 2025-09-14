@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Aktualizacja bazy pytań (Excel)</h1>
        <div class="d-flex align-items-center" style="gap:8px;">
            <a href="{{ route('upload.excel.template') }}" class="btn btn-outline-primary btn-sm">Pobierz przykładowy szablon</a>
            <a href="{{ route('admin.panel') }}" class="btn btn-link btn-sm">← Powrót do panelu</a>
        </div>
    </div>

    @if(session('message'))
        <div class="alert alert-info">{{ session('message') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Wystąpiły błędy:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Wgraj plik Excel</h5>
                    <p class="text-muted small">Załaduj plik zgodny z szablonem. Obsługiwane formaty: .xlsx, .xls</p>
                    <form action="{{ route('upload.excel.post') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file" class="font-weight-semibold">Plik Excel</label>
                            <input type="file" name="file" id="file" class="form-control-file" accept=".xlsx,.xls" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Prześlij</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Struktura pliku</h5>
                    <p class="mb-2 text-muted">Nagłówki w wierszu 1. Wiersze bez nazwy kompetencji (kolumna C) są pomijane.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><code>A</code>: Poziom (1..5)</li>
                        <li class="list-group-item"><code>B</code>: Rodzaj kompetencji (np. 1., 2., 3.G/3.L)</li>
                        <li class="list-group-item"><code>C</code>: Nazwa kompetencji</li>
                        <li class="list-group-item"><code>D</code>: Opis 0,75–1</li>
                        <li class="list-group-item"><code>E</code>: Opis 0–0,5</li>
                        <li class="list-group-item"><code>F</code>: Opis 0,25</li>
                        <li class="list-group-item"><code>G</code>: Powyżej oczekiwań</li>
                        <li class="list-group-item"><code>H</code>: Rezerwowe (pomijane)</li>
                        <li class="list-group-item"><code>I</code>: Produkcja (wartość)</li>
                        <li class="list-group-item"><code>J</code>: Sprzedaż (wartość)</li>
                        <li class="list-group-item"><code>K</code>: Growth (wartość)</li>
                        <li class="list-group-item"><code>L</code>: Logistyka (wartość)</li>
                        <li class="list-group-item"><code>M</code>: People & Culture (wartość)</li>
                        <li class="list-group-item"><code>N</code>: Zarząd (wartość)</li>
                        <li class="list-group-item"><code>O</code>: Order Care (wartość)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .font-weight-semibold { font-weight: 600; }
</style>
@endsection

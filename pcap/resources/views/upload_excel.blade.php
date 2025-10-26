@extends('layouts.admin')

@section('title', 'Import Excel - Admin Panel')

@section('content')
<div class="section-header">
    <div>
        <h2 class="section-title">Aktualizacja bazy pytań (Excel)</h2>
        <p class="section-description">Importuj kompetencje i pytania do systemu z pliku Excel</p>
    </div>
    <div class="actions">
        <a href="{{ route('upload.excel.template') }}" class="btn btn-outline">Pobierz szablon</a>
        <a href="{{ route('admin.panel') }}" class="btn btn-secondary">Powrót do panelu</a>
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

<div class="upload-container">
    <div class="upload-section">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Wgraj plik Excel</h3>
                <p class="text-muted">Załaduj plik zgodny z szablonem. Obsługiwane formaty: .xlsx, .xls</p>
                <form action="{{ route('upload.excel.post') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="file" class="form-label">Plik Excel</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Prześlij</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="info-section">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Struktura pliku</h3>
                <p class="text-muted">Nagłówki w wierszu 1. Wiersze bez nazwy kompetencji (kolumna C) są pomijane.</p>
                <div class="columns-grid">
                    <div class="column-item"><code>A</code>: Poziom (1..5)</div>
                    <div class="column-item"><code>B</code>: Rodzaj kompetencji (np. 1., 2., 3.G/3.L)</div>
                    <div class="column-item"><code>C</code>: Nazwa kompetencji</div>
                    <div class="column-item"><code>D</code>: Opis 0,75–1</div>
                    <div class="column-item"><code>E</code>: Opis 0–0,5</div>
                    <div class="column-item"><code>F</code>: Opis 0,25</div>
                    <div class="column-item"><code>G</code>: Powyżej oczekiwań</div>
                    <div class="column-item"><code>H</code>: Rezerwowe (pomijane)</div>
                    <div class="column-item"><code>I</code>: Produkcja (wartość)</div>
                    <div class="column-item"><code>J</code>: Sprzedaż (wartość)</div>
                    <div class="column-item"><code>K</code>: Growth (wartość)</div>
                    <div class="column-item"><code>L</code>: Logistyka (wartość)</div>
                    <div class="column-item"><code>M</code>: People & Culture (wartość)</div>
                    <div class="column-item"><code>N</code>: Zarząd (wartość)</div>
                    <div class="column-item"><code>O</code>: Order Care (wartość)</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.upload-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    max-width: 1200px;
}

.upload-section, .info-section {
    display: flex;
    flex-direction: column;
}

.form-actions {
    margin-top: 1.5rem;
}

.columns-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    margin-top: 1rem;
}

.column-item {
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 4px;
    font-size: 0.875rem;
}

.column-item code {
    background: var(--primary-color);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 3px;
    margin-right: 0.5rem;
    font-weight: 600;
}

@media (max-width: 768px) {
    .upload-container {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .columns-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush
@endsection

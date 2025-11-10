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
                    <div class="column-item"><code>B</code>: Rodzaj kompetencji (np. 1., 2., 3.G/3.L/3.PS/3.N/3.Ex/3.PM)</div>
                    <div class="column-item"><code>C</code>: Nazwa kompetencji</div>
                    <div class="column-item"><code>D</code>: Opis 0,75–1</div>
                    <div class="column-item"><code>E</code>: Opis 0–0,5</div>
                    <div class="column-item"><code>F</code>: Opis 0,25</div>
                    <div class="column-item"><code>G</code>: Powyżej oczekiwań</div>
                    <div class="column-item"><code>H</code>: Rezerwowe (pomijane)</div>
                    <div class="column-item"><code>I</code>: Rezerwowe (pomijane)</div>
                    <div class="column-item"><code>J</code>: Rezerwowe (pomijane)</div>
                    <div class="column-item"><code>K</code>: Produkcja (wartość)</div>
                    <div class="column-item"><code>L</code>: Sprzedaż (wartość)</div>
                    <div class="column-item"><code>M</code>: Growth (wartość)</div>
                    <div class="column-item"><code>N</code>: Logistyka (wartość)</div>
                    <div class="column-item"><code>O</code>: People & Culture (wartość)</div>
                    <div class="column-item"><code>P</code>: Zarząd (wartość)</div>
                    <div class="column-item"><code>Q</code>: Order Care (wartość)</div>
                    <div class="column-item"><code>R</code>: Finanse i Kadry (wartość)</div>
                    <div class="column-item"><code>S</code>: Expo Designer (wartość)</div>
                    <div class="column-item"><code>T</code>: Sales Innovation PM (wartość)</div>
                    <div class="column-item"><code>U</code>: NPI (wartość)</div>
                    <div class="column-item"><code>V</code>: Production Support (wartość)</div>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background: #e0f2fe; border-left: 4px solid #0284c7; border-radius: 4px;">
                    <h4 style="margin: 0 0 10px 0; color: #0c4a6e; font-size: 14px;"><i class="fas fa-info-circle"></i> Nowe rodzaje kompetencji zawodowych (kolumna B):</h4>
                    <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #075985;">
                        <li><strong>3.PS.</strong> - Zawodowe / Production Support</li>
                        <li><strong>3.N.</strong> - Zawodowe: New Product Implementation (NPI)</li>
                        <li><strong>3.Ex.</strong> - Zawodowe / Expo Designer</li>
                        <li><strong>3.PM.</strong> - Zawodowe: Sales Innovation PM</li>
                    </ul>
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
    color: #1f2937;
}

.column-item code {
    background: var(--primary-color);
    color: #1f2937;
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

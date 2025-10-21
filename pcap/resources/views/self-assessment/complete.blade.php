@extends('layouts.self-assessment')

@section('content')
<style>
/* Scoped styles for complete view - consistent with other assessment pages */
.sa-complete { max-width: 800px; margin: 0 auto; }
.sa-complete .success-icon { color: #059669; font-size: 28px; margin-right: 8px; }
.sa-complete .lead { color:#4b5563; margin-top:6px; }
.sa-complete .actions { display:flex; gap:12px; align-items:center; justify-content:center; margin: 20px 0; flex-wrap:wrap; }
.sa-complete .card { border:1px solid #e5e7eb; border-radius:14px; padding:20px; background:#fff; box-shadow:0 4px 14px rgba(15,23,42,.06); margin: 16px 0; }
.sa-complete .edit-section { text-align:center; }
.sa-complete .edit-section p { color:#6b7280; margin-bottom:12px; }
.sa-complete .link-row { display:flex; gap:8px; align-items:center; justify-content:center; margin-bottom:16px; }
.sa-complete .link-input { flex:1; max-width:480px; border:1px solid #d1d5db; border-radius:10px; padding:10px 12px; font-size:14px; background:#f9fafb; }
.sa-complete .home-section { text-align:center; margin-top:20px; padding-top:20px; border-top:1px solid #e5e7eb; }
@media (max-width:760px){ 
  .sa-complete { padding: 0 6px; } 
  .sa-complete .actions { flex-direction:column; gap:8px; }
  .sa-complete .link-row { flex-direction:column; gap:8px; }
  .sa-complete .link-input { max-width:100%; }
}
</style>

    <div class="sa-complete">
        <input type="hidden" name="uuid" value="{{ $uuid }}">
        
        <div class="center" style="margin-bottom: 12px;">
            <h1><i class="fas fa-check-circle success-icon"></i>Ocena zakończona</h1>
            <p class="lead">Dziękujemy za wypełnienie formularza samooceny.</p>
        </div>

        <div class="actions">
            <a href="{{ route('self.assessment.generate_pdf', ['uuid' => $uuid]) }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> Pobierz PDF
            </a>
            <a href="{{ route('self.assessment.generate_xls', ['uuid' => $uuid]) }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-file-excel"></i> Pobierz XLS
            </a>
        </div>

        <div class="card">
            <div class="edit-section">
                <p>Zapisz ten link. Umożliwi edycję odpowiedzi do czasu zamknięcia okna samooceny.</p>
                <div class="link-row">
                    <input type="text" id="editLink" value="{{ route('form.edit', ['uuid' => $uuid]) }}" readonly class="link-input">
                    <button onclick="copyToClipboard()" class="btn btn-success">
                        <i class="fas fa-copy"></i> Kopiuj link
                    </button>
                </div>
            </div>
            
            <div class="home-section">
                <a href="{{ url('/') }}" class="btn btn-outline">
                    <i class="fas fa-home"></i> Powrót do strony głównej
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
function copyToClipboard(){
    var el = document.getElementById('editLink');
    el.select();
    el.setSelectionRange(0, 99999);
    
    // Modern clipboard API with fallback
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(el.value).then(function() {
            alert('Link został skopiowany do schowka.');
        });
    } else {
        // Fallback for older browsers
        document.execCommand('copy');
        alert('Link został skopiowany do schowka.');
    }
}
</script>
@endsection

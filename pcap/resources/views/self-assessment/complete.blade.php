@extends('layouts.app')
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ocena zakończona</title>
    <style>
        .container {
            max-width: 600px;
            @extends('layouts.self-assessment')

            @section('content')
                <input type="hidden" name="uuid" value="{{ $uuid }}">
                <div class="center">
                    <h1 class="mb-2"><i class="fas fa-check-circle"></i> Ocena zakończona</h1>
                    <p class="muted">Dziękujemy za wypełnienie formularza samooceny.</p>
                </div>

                <div class="center" style="margin: 18px 0;">
                    <a href="{{ route('self.assessment.generate_pdf', ['uuid' => $uuid]) }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i> Pobierz podsumowanie w PDF
                    </a>
                    <a href="{{ route('self.assessment.generate_xls', ['uuid' => $uuid]) }}" target="_blank" class="btn btn-primary" style="margin-left:8px;">
                        <i class="fas fa-file-excel"></i> Pobierz podsumowanie w XLS
                    </a>
                </div>

                <div class="center" style="max-width:720px;margin:0 auto;">
                    <p class="muted">Zapisz ten link. Umożliwi edycję odpowiedzi do czasu zamknięcia okna samooceny.</p>
                    <div class="form-row" style="justify-content:center; margin-top:8px;">
                        <input type="text" id="editLink" value="{{ route('form.edit', ['uuid' => $uuid]) }}" readonly style="max-width:480px;">
                        <button onclick="copyToClipboard()" class="btn btn-success" style="white-space:nowrap;">
                            <i class="fas fa-copy"></i> Kopiuj link
                        </button>
                    </div>
                    <div class="center" style="margin-top:24px;">
                        <a href="{{ url('/') }}" class="btn btn-outline"><i class="fas fa-home"></i> Powrót do strony głównej</a>
                    </div>
                </div>
            @endsection

            @section('scripts')
            <script>
            function copyToClipboard(){
                var el = document.getElementById('editLink');
                el.select();
                el.setSelectionRange(0, 99999);
                document.execCommand('copy');
                alert('Link został skopiowany do schowka.');
            }
            </script>
            @endsection
            margin: 5px;

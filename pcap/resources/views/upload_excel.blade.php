@extends('layouts.app')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Excel</title>
</head>
<body>
    <h1>Upload Pytania z Excela</h1>
    <form action="{{ route('upload.excel.post') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="file">Wybierz plik Excel:</label>
        <input type="file" name="file" id="file" required>
        <button type="submit">Prześlij</button>
    </form>

    @if(session('message'))
        <p>{{ session('message') }}</p>
    @endif
</body>
</html>

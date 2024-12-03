@extends('layouts.app')

@section('content')
<style>
    /* Importuj font Roboto z Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
    html, body {
        height: 100%;
    }
    body {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .container {
        max-width: 500px;
        min-width:400px;
        width: 100%;
    }
    /* Stylizacja tekstu logo */
    .logo-text {
        font-family: 'Roboto', sans-serif;
        font-weight: 700; /* Pogrubienie */
        color: #000;      /* Czarny kolor */
        text-align: center;
        font-size: 36px;  /* Rozmiar czcionki */
        margin-bottom: 20px; /* Margines poniżej logo */
    }
</style>
<div class="container">
    <div class="card">
        <div class="card-body">
            <!-- Logo -->
            <div class="logo-text">P-CAP</div>
            <!-- Formularz logowania -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Pola formularza -->
                <div class="mb-3">
                    <label for="username" class="form-label">{{ __('Login') }}</label>
                    <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                    @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
    
                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Hasło') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
    
                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        {{ __('Zapamiętaj mnie') }}
                    </label>
                </div>
    
                <button type="submit" class="btn btn-primary">
                    {{ __('Zaloguj') }}
                </button>
    
                <!--@if (Route::has('password.request'))
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        {{ __('Zapomniałem hasła') }}
                    </a>
                @endif-->
            </form>
        </div>
    </div>
</div>
@endsection

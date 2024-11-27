<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // Dodaj konstruktor z middleware
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // Metoda określająca pole używane do logowania
    public function username()
    {
        return 'username';
    }

    // Opcjonalnie, po zalogowaniu przekieruj do panelu managera
    protected function redirectTo()
    {
        return '/manager-panel';
    }
}

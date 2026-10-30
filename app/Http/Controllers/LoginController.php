<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function form()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('home');
        }

        if (Auth::guard('web')->check()) {
            return redirect()->route('cliente.inicio');
        }

        return view('auth.login');
    }

    public function entrar(Request $request): RedirectResponse
    {
        $credenciais = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Informe o e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'Informe a senha.',
        ]);

        $lembrar = $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credenciais, $lembrar)) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        if (Auth::guard('web')->attempt($credenciais, $lembrar)) {
            $request->session()->regenerate();

            return redirect()->intended(route('cliente.inicio'));
        }

        return back()
            ->withErrors(['email' => 'E-mail ou senha incorretos.'])
            ->onlyInput('email');
    }

   
    public function sair(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
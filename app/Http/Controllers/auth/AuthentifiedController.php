<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthentifiedController extends Controller
{
    /**
     * ──────────────────────────────────────────────
     * LOGIN
     * ──────────────────────────────────────────────
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'L\'adresse e-mail est obligatoire.',
            'email.email'       => 'Veuillez entrer une adresse e-mail valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Ces identifiants ne correspondent à aucun compte.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home.index'));
    }

    /**
     * ──────────────────────────────────────────────
     * REGISTER
     * ──────────────────────────────────────────────
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers(), 'confirmed'],
        ], [
            'name.required'       => 'Le nom complet est obligatoire.',
            'name.max'            => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.required'      => 'L\'adresse e-mail est obligatoire.',
            'email.email'         => 'Veuillez entrer une adresse e-mail valide.',
            'email.unique'        => 'Cette adresse e-mail est déjà utilisée.',
            'password.required'   => 'Le mot de passe est obligatoire.',
            'password.min'        => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.letters'    => 'Le mot de passe doit contenir au moins une lettre.',
            'password.numbers'    => 'Le mot de passe doit contenir au moins un chiffre.',
            'password.confirmed'  => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(route('home.index'));
    }

    /**
     * ──────────────────────────────────────────────
     * LOGOUT
     * ──────────────────────────────────────────────
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('home.index'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NewUserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'dashboard');
        }

        return view('auth.login');
    }

    public function showAdminLogin(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'dashboard');
        }

        return view('auth.admin-login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'The provided credentials are incorrect.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = $request->user();

        // Debugging: help confirm role & redirect target
        if ($user) {
            logger()->info('Auth login redirect check', [
                'user_id' => $user->id,
                'role_raw' => $user->role,
                'is_admin' => $user->isAdmin(),
            ]);
        }

        return $user?->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('dashboard');


    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'role' => ['prohibited'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        User::where('role', 'admin')->get()->each->notify(new NewUserNotification($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

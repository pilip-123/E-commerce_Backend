<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetMail;
use App\Models\User;
use App\Notifications\NewUserNotification;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly PasswordResetService $passwordResetService
    ) {}
    public function showLogin(): View|RedirectResponse
    {
        if (auth()->check()) {
            $route = auth()->user()->hasPermission('dashboard.view') ? 'admin.dashboard' : 'dashboard';
            return redirect()->route($route);
        }

        return view('auth.login');
    }

    public function showAdminLogin(): View|RedirectResponse
    {
        if (auth()->check()) {
            $route = auth()->user()->hasPermission('dashboard.view') ? 'admin.dashboard' : 'dashboard';
            return redirect()->route($route);
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
            'role' => ['nullable', 'string', 'in:' . implode(',', User::ROLES)],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'The provided credentials are incorrect.'])
                ->onlyInput('email', 'role');
        }

        $request->session()->regenerate();

        $user = $request->user();

        if ($request->filled('role') && $user->role !== $request->role) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['role' => 'You do not have the selected role.'])
                ->onlyInput('email', 'role');
        }

        $route = $user->hasPermission('dashboard.view') ? 'admin.dashboard' : 'dashboard';
        return redirect()->route($route);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'role' => ['required', 'string', 'in:admin,manager,staff'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        User::where('role', 'admin')->get()->each->notify(new NewUserNotification($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email', 'exists:users,email']]);

        $result = $this->passwordResetService->createToken($request->email);

        if ($result) {
            Mail::to($result['user']->email)->send(
                new PasswordResetMail($result['user'], $result['token'])
            );
        }

        return back()->with('status', 'We have emailed your password reset link!');
    }

    public function showResetForm(Request $request, string $token): View|RedirectResponse
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $result = $this->passwordResetService->resetPassword(
            $request->email,
            $request->token,
            $request->password
        );

        if (! $result['success']) {
            return back()
                ->withErrors(['email' => $result['message']])
                ->onlyInput('email');
        }

        $user = User::where('email', $request->email)->first();
        $loginRoute = ($user && $user->hasPermission('dashboard.view')) ? 'admin.login' : 'login';

        return redirect()->route($loginRoute)
            ->with('status', 'Your password has been reset successfully. Please log in with your new password.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

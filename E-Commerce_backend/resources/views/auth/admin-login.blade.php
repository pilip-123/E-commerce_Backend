@extends('layouts.auth')

@section('title', 'Admin Login')

@section('content')
    <div class="flex min-h-screen items-center justify-center bg-slate-50 px-6 py-12">
        <div
            class="w-full max-w-md rounded-[28px] border border-slate-200 bg-white px-11 py-12 shadow-[0_20px_40px_-8px_rgba(0,0,0,0.08)]">

            {{-- Brand icon --}}
            <div class="mx-auto mb-6 flex items-center justify-center"
                style="width: 68px; height: 68px; border-radius: 50%; overflow: hidden; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 3px;">
                <img src="{{ asset('images/logo.png') }}" alt="E-Commerce"
                    style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
            </div>

            {{-- Heading --}}
            <div class="mb-8 text-center">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500">Admin access</p>
                <h2 class="mt-2 text-[26px] font-extrabold leading-tight text-slate-900">Sign in as admin</h2>
                <p class="mt-1.5 text-[13.5px] text-slate-500">Authorized personnel only.</p>
            </div>

            {{-- Error messages --}}
            @if ($errors->any())
                <div
                    class="mb-6 rounded-2xl border border-rose-100 bg-rose-50 p-4 text-sm font-semibold text-rose-800 flex items-center gap-2 shadow-sm">
                    <svg class="h-5 w-5 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (session('status'))
                <div
                    class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 flex items-center gap-2 shadow-sm">
                    <svg class="h-5 w-5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-[18px]">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="mb-1.5 block text-[13px] font-semibold text-slate-700">Email address</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="admin@example.com"
                            class="w-full rounded-[14px] border border-slate-200 bg-slate-50/40 py-3 pl-10 pr-4 text-sm text-slate-900 placeholder-slate-400 outline-none transition focus:border-slate-500 focus:bg-white focus:ring-[3px] focus:ring-slate-500/10 @error('email') border-rose-300 bg-rose-50/40 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="mb-1.5 block text-[13px] font-semibold text-slate-700">Password</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </span>
                        <input type="password" name="password" id="admin-password" required placeholder="••••••••"
                            class="w-full rounded-[14px] border border-slate-200 bg-slate-50/40 py-3 pl-10 pr-11 text-sm text-slate-900 placeholder-slate-400 outline-none transition focus:border-slate-500 focus:bg-white focus:ring-[3px] focus:ring-slate-500/10 @error('password') border-rose-300 bg-rose-50/40 @enderror">
                        <button type="button" onclick="togglePwd('admin-password', this)" tabindex="-1"
                            class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600 transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label class="mb-1.5 block text-[13px] font-semibold text-slate-700">Sign in as</label>
                    <select name="role" required
                        class="w-full rounded-[14px] border border-slate-200 bg-slate-50/40 py-3 px-4 text-sm text-slate-900 outline-none transition focus:border-slate-500 focus:bg-white focus:ring-[3px] focus:ring-slate-500/10">
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select role</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                    @error('role')
                        <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember --}}
                <div class="flex items-center justify-between">
                    <label class="flex cursor-pointer items-center gap-2 text-[13px] text-slate-600">
                        <input type="checkbox" name="remember"
                            class="h-3.5 w-3.5 cursor-pointer rounded accent-slate-600">
                        Remember me
                    </label>
                    <a href="#" class="text-[13px] font-semibold text-slate-600 hover:text-slate-700">Forgot
                        password?</a>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full rounded-[14px] bg-gradient-to-br from-slate-700 to-slate-500 py-3.5 text-[15px] font-bold text-white shadow-[0_4px_14px_rgba(0,0,0,0.2)] transition hover:-translate-y-0.5 hover:opacity-90 active:translate-y-0">
                    Sign in
                </button>

            </form>

        </div>
    </div>
@endsection

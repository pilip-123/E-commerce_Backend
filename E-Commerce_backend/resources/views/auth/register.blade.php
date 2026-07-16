@extends('layouts.auth')

@section('title', __('Register'))

@section('content')
    <div class="flex min-h-screen items-center justify-center bg-green-50 px-6 py-10">
        <div
            class="w-full max-w-[460px] rounded-[28px] border border-green-100 bg-white px-11 py-12 shadow-[0_20px_40px_-8px_rgba(5,150,105,0.12)]">

            {{-- Brand icon --}}
            <div
                class="mx-auto mb-6 flex h-11 w-11 items-center justify-center rounded-[14px] bg-gradient-to-br from-emerald-600 to-emerald-400 shadow-[0_4px_12px_rgba(5,150,105,0.35)]">
                <svg class="h-5 w-5 fill-white" viewBox="0 0 24 24">
                    <path
                        d="M6 2h12a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm6 3a5 5 0 1 0 0 10A5 5 0 0 0 12 5zm0 2a3 3 0 1 1 0 6 3 3 0 0 1 0-6zm-7 11h14v1H5v-1z" />
                </svg>
            </div>

            {{-- Heading --}}
            <div class="mb-8 text-center">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-emerald-600">{{ __('Create account') }}</p>
                <h2 class="mt-2 text-[26px] font-extrabold leading-tight text-slate-900">{{ __('Join E-Commerce') }}</h2>
                <p class="mt-1.5 text-[13.5px] text-slate-500">{{ __('Manage your orders and wishlist in one place.') }}</p>
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

            <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
                @csrf

                {{-- Section: Personal Info --}}
                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">{{ __('Personal info') }}</p>

                {{-- Name + Phone (2-col) --}}
                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="mb-1.5 block text-[13px] font-semibold text-slate-700">{{ __('Full name') }}</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <svg class="h-[15px] w-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </span>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                placeholder="{{ __('Your name') }}
                                class="w-full rounded-[14px] border border-green-100 bg-green-50/40 py-[11px] pl-[38px] pr-3 text-[13.5px] text-slate-900 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-[3px] focus:ring-emerald-500/10">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[13px] font-semibold text-slate-700">{{ __('Phone') }}</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <svg class="h-[15px] w-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.88 13.5 19.79 19.79 0 0 1 1.81 4.9 2 2 0 0 1 3.8 2.72h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 10a16 16 0 0 0 6.09 6.09l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                            </span>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="{{ __('+855 ...') }}
                                class="w-full rounded-[14px] border border-green-100 bg-green-50/40 py-[11px] pl-[38px] pr-3 text-[13.5px] text-slate-900 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-[3px] focus:ring-emerald-500/10">
                        </div>
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="mb-1.5 block text-[13px] font-semibold text-slate-700">{{ __('Email address') }}</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg class="h-[15px] w-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="{{ __('you@example.com') }}
                            class="w-full rounded-[14px] border border-green-100 bg-green-50/40 py-[11px] pl-[38px] pr-3 text-[13.5px] text-slate-900 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-[3px] focus:ring-emerald-500/10">
                    </div>
                </div>

                {{-- Address --}}
                <div>
                    <label class="mb-1.5 block text-[13px] font-semibold text-slate-700">{{ __('Shipping address') }}</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-[13px] text-slate-400">
                            <svg class="h-[15px] w-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                        </span>
                        <textarea name="address" rows="2" placeholder="{{ __('Street, city, province…') }}
                            class="w-full resize-none rounded-[14px] border border-green-100 bg-green-50/40 py-[11px] pl-[38px] pr-3 text-[13.5px] leading-relaxed text-slate-900 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-[3px] focus:ring-emerald-500/10">{{ old('address') }}</textarea>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="!my-5 border-t border-slate-100"></div>

                {{-- Section: Security --}}
                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">{{ __('Security') }}</p>

                {{-- Password + Confirm (2-col) --}}
                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="mb-1.5 block text-[13px] font-semibold text-slate-700">{{ __('Password') }}</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <svg class="h-[15px] w-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </span>
                            <input type="password" name="password" id="register-password" required
                                placeholder="••••••••"
                                class="w-full rounded-[14px] border border-green-100 bg-green-50/40 py-[11px] pl-[38px] pr-10 text-[13.5px] text-slate-900 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-[3px] focus:ring-emerald-500/10">
                            <button type="button" onclick="togglePwd('register-password', this)" tabindex="-1"
                                class="absolute inset-y-0 right-2 flex items-center text-slate-400 hover:text-emerald-600 transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[13px] font-semibold text-slate-700">{{ __('Confirm password') }}</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <svg class="h-[15px] w-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M9 12l2 2 4-4" />
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </span>
                            <input type="password" name="password_confirmation" id="register-password-confirm" required
                                placeholder="••••••••"
                                class="w-full rounded-[14px] border border-green-100 bg-green-50/40 py-[11px] pl-[38px] pr-10 text-[13.5px] text-slate-900 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-[3px] focus:ring-emerald-500/10">
                            <button type="button" onclick="togglePwd('register-password-confirm', this)" tabindex="-1"
                                class="absolute inset-y-0 right-2 flex items-center text-slate-400 hover:text-emerald-600 transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Role --}}
                <div class="!mt-4">
                    <label class="mb-1.5 block text-[13px] font-semibold text-slate-700">{{ __('Register as') }}</label>
                    <select name="role" required
                        class="w-full rounded-[14px] border border-green-100 bg-green-50/40 py-[11px] px-3 text-[13.5px] text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-[3px] focus:ring-emerald-500/10">
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>{{ __('Select role') }}</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                        <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>{{ __('Manager') }}</option>
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>{{ __('Staff') }}</option>
                    </select>
                    @error('role')
                        <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="mt-2 w-full rounded-[14px] bg-gradient-to-br from-emerald-600 to-emerald-400 py-3.5 text-[15px] font-bold text-white shadow-[0_4px_14px_rgba(5,150,105,0.3)] transition hover:-translate-y-0.5 hover:opacity-90 active:translate-y-0">
                    {{ __('Create account') }}
                </button>

            </form>

            {{-- @include('auth.social-buttons') --}}

            <p class="mt-5 text-center text-[13.5px] text-slate-500">
                {{ __('Already have an account?') }}
                <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:text-emerald-700">{{ __('Sign in') }}</a>
            </p>

        </div>
    </div>
@endsection

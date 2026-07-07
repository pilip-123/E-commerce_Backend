@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="min-h-screen bg-[#55C3E6] flex items-center justify-center p-4 sm:p-6 lg:p-8">

        {{-- Decorative circles --}}
        <div class="fixed top-0 left-0 w-[500px] h-[500px] rounded-full bg-white/10 -translate-x-1/4 -translate-y-1/4 pointer-events-none"></div>
        <div class="fixed bottom-0 right-0 w-[600px] h-[600px] rounded-full bg-white/[0.07] translate-x-1/3 translate-y-1/3 pointer-events-none"></div>
        <div class="fixed top-1/3 right-0 w-[300px] h-[300px] rounded-full bg-white/[0.05] translate-x-1/2 pointer-events-none"></div>
        <div class="fixed bottom-1/4 left-0 w-[250px] h-[250px] rounded-full bg-white/[0.06] -translate-x-1/2 pointer-events-none"></div>

        {{-- Main card --}}
        <div class="w-full max-w-[1200px] bg-white rounded-[20px] shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-2 animate-slideUp">

            {{-- Left Column --}}
            <div class="relative bg-gradient-to-br from-[#E0F2FE] to-[#F0F9FF] p-8 lg:p-12 xl:p-16 flex flex-col min-h-[500px] lg:min-h-[650px]">
                {{-- Logo --}}
                <div class="flex-shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="E-Commerce"
                        class="h-20 w-20 rounded-full object-cover border-[4px] border-white shadow-lg">
                </div>

                {{-- Illustration --}}
                <div class="flex-1 flex items-center justify-center py-6">
                    <svg viewBox="0 0 400 400" class="w-full max-w-[320px] h-auto">
                        <defs>
                            <linearGradient id="blueGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#38BDF8" />
                                <stop offset="100%" stop-color="#0284C7" />
                            </linearGradient>
                            <linearGradient id="shieldGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#38BDF8" />
                                <stop offset="100%" stop-color="#0EA5E9" />
                            </linearGradient>
                        </defs>
                        <circle cx="200" cy="200" r="170" fill="url(#blueGrad)" opacity="0.08" />
                        <circle cx="200" cy="200" r="130" fill="url(#blueGrad)" opacity="0.12" />
                        <circle cx="200" cy="200" r="90" fill="url(#blueGrad)" opacity="0.15" />
                        <path d="M200 85 L290 130 L290 215 C290 280 200 335 200 335 C200 335 110 280 110 215 L110 130 Z"
                            fill="url(#shieldGrad)" opacity="0.9" />
                        <rect x="170" y="200" width="60" height="40" rx="6" fill="white" opacity="0.95" />
                        <path d="M180 200 V185 C180 172 188 165 200 165 C212 165 220 172 220 185 V200"
                            fill="none" stroke="white" stroke-width="5" stroke-linecap="round" opacity="0.95" />
                        <circle cx="200" cy="218" r="7" fill="#38BDF8" />
                        <path d="M200 225 V233" stroke="#38BDF8" stroke-width="3" stroke-linecap="round" />
                        <circle cx="130" cy="140" r="8" fill="#38BDF8" opacity="0.3" />
                        <circle cx="280" cy="125" r="5" fill="#38BDF8" opacity="0.4" />
                        <circle cx="145" cy="310" r="6" fill="#38BDF8" opacity="0.25" />
                        <circle cx="270" cy="290" r="10" fill="#38BDF8" opacity="0.2" />
                        <circle cx="100" cy="220" r="4" fill="#38BDF8" opacity="0.2" />
                        <circle cx="300" cy="240" r="6" fill="#38BDF8" opacity="0.15" />
                        <circle cx="310" cy="100" r="20" fill="#22C55E" opacity="0.9" />
                        <path d="M302 100 L308 108 L320 93" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>

                {{-- Copyright --}}
                <div class="flex-shrink-0 text-center lg:text-left">
                    <p class="text-sm text-gray-400">&copy; 2026 E-Commerce. All rights reserved.</p>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="p-8 lg:p-12 xl:p-16 flex flex-col justify-center">
                {{-- Heading --}}
                <h1 class="text-[#0F172A] text-[36px] lg:text-[44px] font-bold leading-tight">Welcome Back!</h1>
                <p class="text-gray-400 mt-2 text-lg">Login to continue</p>

                {{-- Error messages --}}
                @if ($errors->any())
                    <div class="mt-6 p-3 bg-red-50 border border-red-200 rounded-[10px] text-sm text-red-600 font-medium text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="mt-6 p-3 bg-emerald-50 border border-emerald-200 rounded-[10px] text-sm text-emerald-600 font-medium text-center">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login.submit') }}" class="mt-8 space-y-5" novalidate>
                    @csrf

                    {{-- Email --}}
                    <div>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-[#38BDF8] transition-colors duration-300">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                placeholder="Enter your email"
                                class="w-full h-[55px] pl-12 pr-4 border border-gray-200 rounded-[14px] text-[#1E293B] placeholder-gray-400 outline-none transition-all duration-300 focus:border-[#38BDF8] focus:ring-4 focus:ring-[#38BDF8]/10 bg-white text-[15px] @error('email') border-red-300 @enderror">
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-[#38BDF8] transition-colors duration-300">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </span>
                            <input type="password" name="password" id="login-password" required
                                placeholder="Enter your password"
                                class="w-full h-[55px] pl-12 pr-12 border border-gray-200 rounded-[14px] text-[#1E293B] placeholder-gray-400 outline-none transition-all duration-300 focus:border-[#38BDF8] focus:ring-4 focus:ring-[#38BDF8]/10 bg-white text-[15px] @error('password') border-red-300 @enderror">
                            <button type="button" onclick="togglePwd('login-password', this)" tabindex="-1"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition-colors duration-300">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember & Forgot --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-gray-300 text-[#38BDF8] focus:ring-[#38BDF8] accent-[#38BDF8]">
                            <span class="text-sm text-[#64748B]">Remember Me</span>
                        </label>
                        <a href="#" class="text-sm font-semibold text-[#38BDF8] hover:text-[#0EA5E9] transition-colors duration-300">Forgot Password?</a>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full h-[52px] bg-[#38BDF8] hover:bg-[#0EA5E9] rounded-[14px] text-white font-semibold text-[15px] tracking-wide shadow-[0_4px_20px_-4px_rgba(56,189,248,0.4)] transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(56,189,248,0.5)] hover:-translate-y-0.5 active:translate-y-0 active:shadow-[0_2px_10px_-4px_rgba(56,189,248,0.4)] flex items-center justify-center gap-2">
                        <span>Sign In</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />
                        </svg>
                    </button>
                </form>

                {{-- Register link --}}
                <p class="mt-6 text-center text-sm text-[#64748B]">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold text-[#38BDF8] hover:text-[#0EA5E9] transition-colors duration-300">Sign Up</a>
                </p>

                {{-- Bottom links --}}
                <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-center gap-4 text-xs text-gray-400">
                    <a href="#" class="hover:text-gray-600 transition-colors duration-300">Terms &amp; Conditions</a>
                    <span class="text-gray-300">|</span>
                    <a href="#" class="hover:text-gray-600 transition-colors duration-300">Privacy Policy</a>
                </div>
            </div>
        </div>
    </div>
@endsection

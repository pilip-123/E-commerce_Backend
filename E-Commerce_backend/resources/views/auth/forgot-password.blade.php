@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-[#22c55e] to-[#16a34a] flex items-center justify-center p-4 sm:p-6 lg:p-8">

        <div class="fixed top-0 left-0 w-[500px] h-[500px] rounded-full bg-white/10 -translate-x-1/4 -translate-y-1/4 pointer-events-none"></div>
        <div class="fixed bottom-0 right-0 w-[600px] h-[600px] rounded-full bg-white/[0.07] translate-x-1/3 translate-y-1/3 pointer-events-none"></div>
        <div class="fixed top-1/3 right-0 w-[300px] h-[300px] rounded-full bg-white/[0.05] translate-x-1/2 pointer-events-none"></div>
        <div class="fixed bottom-1/4 left-0 w-[250px] h-[250px] rounded-full bg-white/[0.06] -translate-x-1/2 pointer-events-none"></div>

        <div class="w-full max-w-[560px] bg-white rounded-[20px] shadow-2xl overflow-hidden animate-slideUp">

            <div class="p-8 lg:p-12 xl:p-16 flex flex-col items-center text-center">
                {{-- Icon --}}
                <div class="w-16 h-16 bg-gradient-to-br from-[#22c55e] to-[#16a34a] rounded-full flex items-center justify-center shadow-lg mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7v4a3 3 0 0 0 3 3h3m-3 0a9 9 0 1 1-9-9m3 0h-3m3 0V3" />
                    </svg>
                </div>

                <h1 class="text-[#0F172A] text-[28px] lg:text-[32px] font-bold leading-tight">Forgot Password?</h1>
                <p class="text-gray-400 mt-3 text-base max-w-[380px]">
                    No worries! Enter your email address and we'll send you a reset link.
                </p>

                @if (session('status'))
                    <div class="mt-6 w-full p-4 bg-emerald-50 border border-emerald-200 rounded-[14px] text-sm text-emerald-700 font-medium text-center">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('reset_url'))
                    <div class="mt-3 w-full p-4 bg-blue-50 border border-blue-200 rounded-[14px] text-sm text-blue-700 text-left">
                        <p class="font-semibold mb-2">Debug: Copy this reset link (dev mode)</p>
                        <code class="block text-xs break-all bg-white p-2 rounded border border-blue-100 mb-2 select-all">{{ session('reset_url') }}</code>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-6 w-full p-4 bg-red-50 border border-red-200 rounded-[14px] text-sm text-red-600 font-medium text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.forgot.submit') }}" class="mt-8 w-full space-y-5" novalidate>
                    @csrf

                    <div>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 group-focus-within:text-[#22c55e] transition-colors duration-300">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="4" width="20" height="16" rx="2" />
                                    <path d="M22 4L12 13 2 4" />
                                </svg>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                placeholder="Enter your email"
                                class="w-full h-[55px] pl-12 pr-4 border border-gray-200 rounded-[14px] text-[#1E293B] placeholder-gray-400 outline-none transition-all duration-300 focus:border-[#22c55e] focus:ring-4 focus:ring-[#22c55e]/10 bg-white text-[15px]">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full h-[52px] bg-gradient-to-r from-[#22c55e] to-[#16a34a] hover:from-[#16a34a] hover:to-[#15803d] rounded-[14px] text-white font-semibold text-[15px] tracking-wide shadow-[0_4px_20px_-4px_rgba(34,197,94,0.4)] transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(34,197,94,0.5)] hover:-translate-y-0.5 active:translate-y-0 active:shadow-[0_2px_10px_-4px_rgba(34,197,94,0.4)] flex items-center justify-center gap-2">
                        <span>Send Reset Link</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />
                        </svg>
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-[#64748B]">
                    Remember your password?
                    <a href="{{ route('login') }}" class="font-semibold text-[#22c55e] hover:text-[#16a34a] transition-colors duration-300">Sign in</a>
                </p>
            </div>

        </div>
    </div>
@endsection

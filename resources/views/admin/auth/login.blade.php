@extends('layouts.admin')
@section('title', 'Login Admin — Gumay Wedding')
@section('content')

<div class="min-h-screen flex items-center justify-center bg-bgmain text-dark px-6 py-12">
    <form
        action="{{ route('admin.login') }}"
        method="POST"
        class="bg-surface rounded-3xl p-8 sm:p-10 w-full max-w-md
               space-y-6 shadow-xl border border-gold/30"
    >
        @csrf

        {{-- Branding --}}
        <div class="text-center mb-6">
            <img
                src="{{ asset('images/icon/gumaywedding.jpg') }}"
                alt="Gumay Wedding Logo"
                class="w-16 h-16 rounded-full object-cover border border-gold/40 mx-auto mb-3 shadow-md"
            >
            <p class="font-display text-3xl text-gold-dark font-semibold">
                Gumay Wedding
            </p>

            <p class="text-xs text-ink uppercase tracking-[0.2em] font-medium mt-1">
                Admin Panel Login
            </p>
        </div>

        {{-- Error Alert --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-xs sm:text-sm rounded-2xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <span>
                    {{ $errors->first() }}
                </span>
            </div>
        @endif

        {{-- Email --}}
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-ink mb-2">
                Email Admin
            </label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                placeholder="admin@gumaywedding.com"
                class="w-full bg-card border border-gold/30 focus:border-gold-dark focus:ring-1 focus:ring-gold-dark text-dark rounded-xl text-sm px-4 py-3 outline-none transition duration-150 placeholder-ink/40"
            >
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-ink mb-2">
                Password
            </label>
            <input
                type="password"
                name="password"
                required
                placeholder="••••••••"
                class="w-full bg-card border border-gold/30 focus:border-gold-dark focus:ring-1 focus:ring-gold-dark text-dark rounded-xl text-sm px-4 py-3 outline-none transition duration-150 placeholder-ink/40"
            >
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2.5 text-xs font-medium text-ink cursor-pointer select-none">
                <input
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 rounded border-gold/40 bg-card text-gold-dark focus:ring-gold-dark/20 accent-gold-dark cursor-pointer"
                >
                <span>Ingat saya</span>
            </label>
        </div>

        {{-- Submit Button --}}
        <button
            type="submit"
            class="w-full bg-dark text-surface py-3.5 rounded-full font-medium hover:bg-gold-dark hover:text-white transition duration-200 shadow-md hover:shadow-lg text-sm"
        >
            Masuk
        </button>
    </form>
</div>
@endsection
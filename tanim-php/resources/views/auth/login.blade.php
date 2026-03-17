@extends('layouts.app')
@section('title','Sign In — Tanim')
@section('content')
<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:4rem 1rem;background:var(--bg);">
    <div style="width:100%;max-width:28rem;">
        <div style="text-align:center;margin-bottom:2rem;">
            <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:0.6rem;text-decoration:none;margin-bottom:1.25rem;">
                <div style="background:var(--primary-faint);padding:0.65rem;border-radius:0.85rem;box-shadow:var(--shadow-neu-sm);">
                    <svg style="width:1.5rem;height:1.5rem;color:var(--primary);" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                    </svg>
                </div>
                <span style="font-family:Outfit,sans-serif;font-weight:800;font-size:1.6rem;color:var(--primary);">Tanim</span>
            </a>
            <h1 style="font-family:Outfit,sans-serif;font-size:1.6rem;font-weight:800;color:var(--text);margin:0 0 0.3rem;">Welcome back</h1>
            <p style="color:var(--text-muted);font-size:0.9rem;margin:0;">Sign in to your Tanim account</p>
        </div>
        <div class="page-card" style="padding:2.25rem;">
            <div style="height:3px;background:linear-gradient(90deg,var(--primary),var(--accent));border-radius:9999px;margin-bottom:2rem;"></div>
            @if(session('status'))
            <div class="alert-success" style="margin-bottom:1.25rem;">{{ session('status') }}</div>
            @endif
            @if($errors->any())
            <div class="alert-error" style="margin-bottom:1.25rem;">
                @foreach($errors->all() as $error)<div>&bull; {{ $error }}</div>@endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('login') }}" style="display:flex;flex-direction:column;gap:1.25rem;">
                @csrf
                <div>
                    <label class="label">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="input" placeholder="you@email.com" />
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.4rem;">
                        <label class="label" style="margin:0;">Password</label>
                        @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="font-size:0.78rem;color:var(--primary);font-weight:600;text-decoration:none;">Forgot password?</a>
                        @endif
                    </div>
                    <input type="password" name="password" required autocomplete="current-password" class="input" placeholder="Your password" />
                </div>
                <label style="display:flex;align-items:center;gap:0.6rem;cursor:pointer;">
                    <input type="checkbox" name="remember" style="width:1rem;height:1rem;accent-color:var(--primary);" />
                    <span style="font-size:0.875rem;color:var(--text-muted);">Remember me</span>
                </label>
                <button type="submit" class="btn-primary" style="padding:0.9rem;font-size:0.95rem;border-radius:0.85rem;">Sign In</button>
            </form>
            <div style="margin-top:1.75rem;padding-top:1.5rem;border-top:1px solid var(--border);text-align:center;">
                <p style="font-size:0.875rem;color:var(--text-muted);margin:0 0 0.5rem;">
                    Don't have an account? <a href="{{ route('register') }}" style="color:var(--primary);font-weight:700;">Create one free</a>
                </p>
                <a href="{{ route('home') }}" style="font-size:0.8rem;color:var(--text-light);text-decoration:none;">&larr; Back to Home</a>
            </div>
        </div>
    </div>
</div>
@endsection

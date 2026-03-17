@extends('layouts.app')
@section('content')
<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:4rem 1rem;background:var(--bg);">
    <div style="width:100%;max-width:32rem;">
        <div style="text-align:center;margin-bottom:2rem;">
            <a href="{{ route('home') }}" style="display:inline-flex;align-items:center;gap:0.6rem;text-decoration:none;margin-bottom:1rem;">
                <div style="background:var(--primary-faint);padding:0.6rem;border-radius:0.75rem;">
                    <svg style="width:1.4rem;height:1.4rem;color:var(--primary);" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                    </svg>
                </div>
                <span style="font-family:Outfit,sans-serif;font-weight:800;font-size:1.5rem;color:var(--primary);">Tanim</span>
            </a>
            <h1 style="font-family:Outfit,sans-serif;font-size:1.6rem;font-weight:800;color:var(--text);margin:0 0 0.3rem;">Create Your Account</h1>
            <p style="color:var(--text-muted);font-size:0.9rem;margin:0;">Shop fresh farm products from Tanim</p>
        </div>
        <div class="page-card" style="padding:2rem;">
            @if($errors->any())
            <div class="alert-error" style="margin-bottom:1.25rem;">
                @foreach($errors->all() as $error)<div>&#8226; {{ $error }}</div>@endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('register') }}" style="display:flex;flex-direction:column;gap:1.1rem;" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="role" value="buyer" />
                <div>
                    <label class="label">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" class="input" placeholder="Juan dela Cruz" />
                </div>
                <div>
                    <label class="label">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="input" placeholder="you@email.com" />
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="label">Password</label>
                        <input type="password" name="password" required autocomplete="new-password" class="input" placeholder="Min. 8 characters" />
                    </div>
                    <div>
                        <label class="label">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="input" placeholder="Same password" />
                    </div>
                </div>
                <div>
                    <label class="label">Profile Photo <span style="font-weight:400;color:var(--text-light);">(optional)</span></label>
                    <input type="file" name="photo" accept="image/*" class="input" style="cursor:pointer;" />
                    @error('photo')<p style="color:var(--danger);font-size:0.8rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-primary" style="padding:0.85rem;font-size:0.95rem;border-radius:0.75rem;">Create Account</button>
            </form>
            <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--border);text-align:center;">
                <p style="font-size:0.875rem;color:var(--text-muted);margin:0 0 0.5rem;">
                    Already have an account? <a href="{{ route('login') }}" style="color:var(--primary);font-weight:600;">Sign in</a>
                </p>
                <a href="{{ route('home') }}" style="font-size:0.8rem;color:var(--text-light);text-decoration:none;">Back to Home</a>
            </div>
        </div>
    </div>
</div>
@endsection

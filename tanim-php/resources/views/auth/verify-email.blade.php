@extends('layouts.app')
@section('title','Verify Your Email — Tanim')
@section('content')
<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:4rem 1rem;">
    <div style="width:100%;max-width:26rem;text-align:center;">
        <div class="page-card" style="padding:3rem 2.5rem;">
            <div style="font-size:3.5rem;margin-bottom:1.5rem;">&#128231;</div>
            <h1 style="font-family:Outfit,sans-serif;font-size:1.75rem;font-weight:800;color:var(--text);margin:0 0 0.75rem;">Verify Your Email</h1>
            <p style="color:var(--text-muted);font-size:0.95rem;line-height:1.7;margin:0 0 2rem;">
                Thanks for signing up! Before you can access Tanim, please verify your email address by clicking the link we sent you.
            </p>

            @if(session('success'))
            <div class="alert-success" style="margin-bottom:1.5rem;">&#10003; {{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-primary" style="width:100%;padding:0.85rem;font-size:0.95rem;border-radius:0.75rem;">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" style="margin-top:1rem;">
                @csrf
                <button type="submit" style="background:none;border:none;color:var(--text-light);font-size:0.875rem;cursor:pointer;text-decoration:underline;">
                    Sign out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

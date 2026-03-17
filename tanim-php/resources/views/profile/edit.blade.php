@extends('layouts.app')
@section('title','My Profile — Tanim')
@section('content')
<div class="page-wrap-xs">
    <h1 class="section-title" style="margin-bottom:2rem;">My Profile</h1>

    @if(session('success'))
    <div class="alert-success" style="margin-bottom:1.5rem;">✓ {{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="alert-error" style="margin-bottom:1.5rem;">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <div class="page-card" style="padding:2rem;">
        <div style="display:flex;align-items:center;gap:1.5rem;margin-bottom:2rem;padding-bottom:2rem;border-bottom:1px solid var(--border);">
            <img src="{{ $user->photoUrl() }}" alt="{{ $user->name }}"
                 style="width:5rem;height:5rem;border-radius:9999px;object-fit:cover;border:3px solid var(--primary-soft);" />
            <div>
                <h2 style="font-size:1.1rem;font-weight:800;color:var(--text);margin:0 0 0.25rem;">{{ $user->name }}</h2>
                <span class="badge">{{ ucfirst($user->role) }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:1.25rem;">
            @csrf

            <div>
                <label class="label">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input" />
            </div>

            <div>
                <label class="label">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input" />
            </div>

            <div>
                <label class="label">Profile Photo</label>
                <input type="file" name="photo" accept="image/*" class="input" style="cursor:pointer;" />
                <p style="font-size:0.75rem;color:var(--text-light);margin-top:0.25rem;">Leave blank to keep current photo. Max 2MB.</p>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label class="label">New Password <span style="font-weight:400;color:var(--text-light);">(optional)</span></label>
                    <input type="password" name="password" class="input" placeholder="Min. 8 characters" />
                </div>
                <div>
                    <label class="label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="input" placeholder="Same password" />
                </div>
            </div>

            <button type="submit" class="btn-primary" style="padding:0.85rem;font-size:0.95rem;border-radius:0.75rem;">
                Save Changes
            </button>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title','My Profile — Tanim')
@section('content')
<style>
.profile-grid {
    display: grid;
    grid-template-columns: 290px 1fr;
    gap: 1.25rem;
}
.profile-side,
.profile-main {
    background: var(--bg-glass-2);
    border: 1px solid var(--border-glass);
    border-radius: 1.25rem;
    box-shadow: var(--shadow-neu-sm), var(--shadow-card);
}
.profile-side {
    padding: 1.25rem;
    height: fit-content;
}
.profile-main {
    padding: 1.5rem;
}
.profile-file {
    width: 100%;
    padding: 0.6rem 0.75rem;
    border-radius: 0.8rem;
    border: 1px dashed var(--border);
    background: var(--bg);
    color: var(--text-muted);
}
.profile-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.65rem;
    margin-top: 0.35rem;
}
@media (max-width: 900px) {
    .profile-grid { grid-template-columns: 1fr; }
}
@media (max-width: 680px) {
    .profile-pass-grid { grid-template-columns: 1fr !important; }
    .profile-actions { justify-content: stretch; }
    .profile-actions .btn-primary,
    .profile-actions .btn-ghost { width: 100%; }
}
</style>
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

    <div class="profile-grid">
        <aside class="profile-side">
            <div style="display:flex;flex-direction:column;align-items:center;text-align:center;gap:0.65rem;">
                <img src="{{ $user->photoUrl() }}" alt="{{ $user->name }}"
                     style="width:6rem;height:6rem;border-radius:9999px;object-fit:cover;border:3px solid var(--primary-soft);box-shadow:var(--shadow-neu-sm);" />
                <h2 style="font-size:1.05rem;font-weight:800;color:var(--text);margin:0;">{{ $user->name }}</h2>
                <span class="badge">{{ ucfirst($user->role) }}</span>
                <p style="margin:0.2rem 0 0;font-size:0.8rem;color:var(--text-muted);word-break:break-word;">{{ $user->email }}</p>
            </div>
            <div style="height:1px;background:linear-gradient(90deg,transparent,var(--border-glass),transparent);margin:1rem 0;"></div>
            <p style="margin:0;font-size:0.8rem;line-height:1.5;color:var(--text-muted);">
                Keep your account details accurate so order updates and notifications reach you.
            </p>
        </aside>

        <section class="profile-main">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem;margin-bottom:1.2rem;">
                <div>
                    <h3 style="margin:0;font-size:1rem;font-weight:800;color:var(--text);">Profile Settings</h3>
                    <p style="margin:0.2rem 0 0;font-size:0.82rem;color:var(--text-muted);">Update your personal info and password.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:1rem;">
                @csrf

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="label">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input" />
                    </div>

                    <div>
                        <label class="label">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input" />
                    </div>
                </div>

                <div>
                    <label class="label">Profile Photo</label>
                    <input type="file" name="photo" accept="image/*" class="profile-file" style="cursor:pointer;" />
                    <p style="font-size:0.75rem;color:var(--text-light);margin-top:0.35rem;">Leave blank to keep current photo. Max 2MB.</p>
                </div>

                <div style="height:1px;background:linear-gradient(90deg,transparent,var(--border-glass),transparent);"></div>

                <div class="profile-pass-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="label">New Password <span style="font-weight:400;color:var(--text-light);">(optional)</span></label>
                        <input type="password" name="password" class="input" placeholder="Min. 8 characters" />
                    </div>
                    <div>
                        <label class="label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="input" placeholder="Same password" />
                    </div>
                </div>

                <div class="profile-actions">
                    <a href="{{ route('dashboard') }}" class="btn-ghost" style="padding:0.82rem 1.1rem;border-radius:0.8rem;font-size:0.9rem;">Cancel</a>
                    <button type="submit" class="btn-primary" style="padding:0.82rem 1.2rem;font-size:0.92rem;border-radius:0.8rem;">
                        Save Changes
                    </button>
                </div>

            </form>
        </section>
    </div>
</div>
@endsection

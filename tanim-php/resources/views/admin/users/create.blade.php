@extends('layouts.admin')
@section('title','Add User')
@section('page-title','👤 Add New User')
@section('content')
<div style="max-width:36rem;">
<div class="page-card" style="padding:1.75rem;">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div style="display:grid;gap:1rem;">
            <div>
                <label class="label">Full Name</label>
                <input name="name" type="text" class="input" value="{{ old('name') }}" required placeholder="Juan dela Cruz" />
            </div>
            <div>
                <label class="label">Email Address</label>
                <input name="email" type="email" class="input" value="{{ old('email') }}" required placeholder="juan@example.com" />
            </div>
            <div>
                <label class="label">Role</label>
                <select name="role" class="input" required>
                    @foreach(['buyer','admin'] as $r)
                    <option value="{{ $r }}" {{ old('role')===$r?'selected':'' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Password</label>
                <input name="password" type="password" class="input" required placeholder="Min 8 characters" />
            </div>
            <div>
                <label class="label">Confirm Password</label>
                <input name="password_confirmation" type="password" class="input" required />
            </div>
            <div style="display:flex;align-items:center;gap:.6rem;">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active','1')?'checked':'' }} style="width:1rem;height:1rem;accent-color:var(--primary);" />
                <label for="is_active" style="font-size:.875rem;font-weight:600;color:var(--text);cursor:pointer;">Active account</label>
            </div>
        </div>
        <div style="display:flex;gap:.75rem;margin-top:1.5rem;">
            <button type="submit" class="btn-primary" style="padding:.7rem 1.5rem;font-size:.875rem;border-radius:.75rem;">Create User</button>
            <a href="{{ route('admin.users.index') }}" class="btn-ghost" style="padding:.7rem 1.25rem;font-size:.875rem;border-radius:.75rem;">Cancel</a>
        </div>
    </form>
</div>
</div>
@endsection

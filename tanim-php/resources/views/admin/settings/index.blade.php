@extends('layouts.admin')
@section('title','Settings')
@section('page-title','⚙️ System Settings')
@section('content')

<div style="max-width:48rem;">
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf

    @foreach($settings as $group => $groupSettings)
    <div class="page-card" style="padding:1.5rem;margin-bottom:1.25rem;">
        <h2 style="font-family:'Outfit',sans-serif;font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1.25rem;padding-bottom:.75rem;border-bottom:1px solid var(--border);">
            @php $groupIcons = ['general'=>'🌐','email'=>'📧','security'=>'🔒','backup'=>'💾']; @endphp
            {{ $groupIcons[$group] ?? '⚙️' }} {{ ucfirst($group) }} Settings
        </h2>
        <div style="display:grid;gap:1rem;">
            @foreach($groupSettings as $setting)
            <div>
                <label class="label" for="s_{{ $setting->key }}">{{ $setting->label }}</label>
                @if($setting->type === 'boolean')
                <div style="display:flex;align-items:center;gap:.6rem;">
                    <input type="checkbox" name="{{ $setting->key }}" value="1" id="s_{{ $setting->key }}"
                        {{ $setting->value == '1' ? 'checked' : '' }}
                        style="width:1.1rem;height:1.1rem;accent-color:var(--primary);" />
                    <label for="s_{{ $setting->key }}" style="font-size:.875rem;color:var(--text-muted);cursor:pointer;">Enable</label>
                </div>
                @elseif($setting->type === 'textarea')
                <textarea name="{{ $setting->key }}" id="s_{{ $setting->key }}" class="input" rows="3">{{ $setting->value }}</textarea>
                @else
                <input type="text" name="{{ $setting->key }}" id="s_{{ $setting->key }}" class="input" value="{{ $setting->value }}" />
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    {{-- Role & Permission Info --}}
    <div class="page-card" style="padding:1.5rem;margin-bottom:1.25rem;">
        <h2 style="font-family:'Outfit',sans-serif;font-size:1rem;font-weight:800;color:var(--text);margin:0 0 1rem;">🛡️ Role & Permission Overview</h2>
        <div style="display:grid;gap:.6rem;">
            @foreach([
                ['admin','Administrator','Full access to all admin features, user management, settings, and data.','var(--danger)'],
                ['buyer','Customer / Buyer','Can browse marketplace, add to cart, place orders, and leave reviews.','var(--sky)'],
            ] as [$role,$label,$desc,$color])
            <div style="display:flex;align-items:flex-start;gap:.75rem;padding:.75rem;background:var(--bg);border-radius:.75rem;border:1px solid var(--border);">
                <span style="font-size:.7rem;font-weight:800;padding:.2rem .6rem;border-radius:9999px;background:{{ $color }}20;color:{{ $color }};flex-shrink:0;margin-top:.1rem;">{{ ucfirst($role) }}</span>
                <div>
                    <p style="font-size:.85rem;font-weight:700;color:var(--text);margin:0 0 .15rem;">{{ $label }}</p>
                    <p style="font-size:.78rem;color:var(--text-muted);margin:0;">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <p style="font-size:.75rem;color:var(--text-light);margin:.75rem 0 0;">To change a user's role, go to <a href="{{ route('admin.users.index') }}" style="color:var(--primary);">User Management</a>.</p>
    </div>

    <button type="submit" class="btn-primary" style="padding:.75rem 2rem;font-size:.9rem;border-radius:.85rem;">Save All Settings</button>
</form>
</div>
@endsection

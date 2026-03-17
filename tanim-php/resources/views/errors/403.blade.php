<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Restricted | Tanim</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            background: #0d1a0f;
            overflow: hidden;
            position: relative;
        }

        /* Animated background blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.18;
            animation: drift 12s ease-in-out infinite alternate;
        }
        .blob-1 { width: 520px; height: 520px; background: #16a34a; top: -120px; left: -100px; animation-delay: 0s; }
        .blob-2 { width: 380px; height: 380px; background: #4ade80; bottom: -80px; right: -60px; animation-delay: -4s; }
        .blob-3 { width: 260px; height: 260px; background: #a16e3c; top: 40%; left: 55%; animation-delay: -8s; }

        @keyframes drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(30px, 20px) scale(1.08); }
        }

        /* Floating leaves */
        .leaf {
            position: absolute;
            font-size: 1.6rem;
            opacity: 0.18;
            animation: fall linear infinite;
            pointer-events: none;
        }
        @keyframes fall {
            0%   { transform: translateY(-60px) rotate(0deg); opacity: 0; }
            10%  { opacity: 0.18; }
            90%  { opacity: 0.18; }
            100% { transform: translateY(110vh) rotate(360deg); opacity: 0; }
        }

        /* Card */
        .card {
            position: relative;
            z-index: 10;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(74,222,128,0.18);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 2rem;
            padding: 3rem 3.5rem;
            text-align: center;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 32px 80px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.06);
        }

        .icon-wrap {
            width: 96px;
            height: 96px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            background: rgba(220,38,38,0.12);
            border: 2px solid rgba(220,38,38,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            animation: pulse 2.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(220,38,38,0.25); }
            50%       { box-shadow: 0 0 0 14px rgba(220,38,38,0); }
        }

        .code {
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .15em;
            color: #dc2626;
            text-transform: uppercase;
            margin-bottom: .5rem;
            opacity: .8;
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 900;
            color: #f0fdf4;
            line-height: 1.2;
            margin-bottom: .75rem;
        }

        p {
            font-size: .95rem;
            color: rgba(240,253,244,0.55);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .actions {
            display: flex;
            gap: .75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .65rem 1.4rem;
            background: linear-gradient(135deg, #16a34a, #4ade80);
            color: #052e16;
            font-family: 'Outfit', sans-serif;
            font-size: .875rem;
            font-weight: 800;
            border-radius: .75rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: opacity .2s, transform .15s;
        }
        .btn-primary:hover { opacity: .88; transform: translateY(-1px); }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .65rem 1.4rem;
            background: rgba(255,255,255,0.06);
            color: rgba(240,253,244,0.7);
            font-family: 'Outfit', sans-serif;
            font-size: .875rem;
            font-weight: 700;
            border-radius: .75rem;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.1);
            cursor: pointer;
            transition: background .2s, transform .15s;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.1); transform: translateY(-1px); }

        .divider {
            height: 1px;
            background: rgba(74,222,128,0.12);
            margin: 1.75rem 0;
        }

        .hint {
            font-size: .78rem;
            color: rgba(240,253,244,0.3);
        }
        .hint span { color: rgba(74,222,128,0.6); font-weight: 700; }
    </style>
</head>
<body>

    {{-- Background blobs --}}
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    {{-- Floating leaves --}}
    @foreach([['🌿',8,'6s'],['🍃',22,'9s'],['🌱',40,'7s'],['🍀',60,'11s'],['🌿',75,'8s'],['🍃',90,'10s']] as [$emoji,$left,$dur])
    <span class="leaf" style="left:{{ $left }}%;animation-duration:{{ $dur }};animation-delay:-{{ rand(0,8) }}s;">{{ $emoji }}</span>
    @endforeach

    <div class="card">
        <div class="icon-wrap">🚫</div>

        <p class="code">Error 403 — Forbidden</p>

        <h1>Admins don't shop here.</h1>

        <p>
            Your admin account can't access the marketplace or place orders.
            Head back to the dashboard to manage products, users, and orders.
        </p>

        <div class="actions">
            <a href="{{ route('admin.dashboard') }}" class="btn-primary">
                🌾 Go to Dashboard
            </a>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.dashboard') }}" class="btn-ghost">
                ← Go Back
            </a>
        </div>

        <div class="divider"></div>

        <p class="hint">
            Logged in as <span>{{ auth()->user()->name ?? 'Admin' }}</span> &middot; Admin Account
        </p>
    </div>

</body>
</html>

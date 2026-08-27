<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'DS Gaming Panel' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; }
        .glass { background: rgba(24,24,27,.85); backdrop-filter: blur(12px); }
        .glow-red { box-shadow: 0 0 20px rgba(239,68,68,.25); }
        .nav-active { background: linear-gradient(90deg,#7f1d1d,#991b1b); border-left: 3px solid #f87171; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 4px; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen">
<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="w-64 shrink-0 glass border-r border-zinc-800 hidden md:flex flex-col">
        <div class="p-5 border-b border-zinc-800">
            <div class="text-xl font-bold tracking-wide">
                <span class="text-red-500">DS</span> GAMING
            </div>
            <div class="text-xs text-zinc-500 mt-1">Premium Key Panel</div>
        </div>
        <nav class="flex-1 p-3 space-y-1 text-sm">
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-zinc-800/80 {{ request()->is('dashboard') ? 'nav-active' : '' }}">
                <span>📊</span> Dashboard
            </a>
            <a href="{{ route('keys.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-zinc-800/80 {{ request()->is('keys') && !request()->is('keys/create') ? 'nav-active' : '' }}">
                <span>🔑</span> All Keys
            </a>
            <a href="{{ route('keys.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-zinc-800/80 {{ request()->is('keys/create') ? 'nav-active' : '' }}">
                <span>➕</span> Generate Keys
            </a>
            <a href="{{ url('/make-user') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-zinc-800/80 {{ request()->is('make-user') ? 'nav-active' : '' }}">
                <span>👤</span> User + Password
            </a>
        </nav>
        <div class="p-4 border-t border-zinc-800">
            <div class="text-xs text-zinc-500 mb-2 truncate">{{ auth()->user()->email ?? '' }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm text-red-400 hover:bg-red-950/50">Logout</button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Top bar mobile --}}
        <header class="md:hidden glass border-b border-zinc-800 px-4 py-3 flex items-center justify-between">
            <div class="font-bold"><span class="text-red-500">DS</span> GAMING</div>
            <div class="flex gap-2 text-xs">
                <a href="{{ url('/dashboard') }}" class="px-2 py-1 rounded bg-zinc-800">Home</a>
                <a href="{{ route('keys.index') }}" class="px-2 py-1 rounded bg-zinc-800">Keys</a>
                <a href="{{ route('keys.create') }}" class="px-2 py-1 rounded bg-zinc-800">Gen</a>
                <a href="{{ url('/make-user') }}" class="px-2 py-1 rounded bg-zinc-800">User</a>
            </div>
        </header>

        <main class="flex-1 p-4 md:p-8 overflow-x-auto">
            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-emerald-950/50 border border-emerald-800 text-emerald-300 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 rounded-lg bg-red-950/50 border border-red-800 text-red-300 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>

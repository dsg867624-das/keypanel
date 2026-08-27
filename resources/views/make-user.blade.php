@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-red-500 to-red-900 shadow-lg shadow-red-900/40 text-xl font-bold mb-4">UP</div>
        <h1 class="text-2xl font-bold tracking-tight">User + Password</h1>
        <p class="text-zinc-500 text-sm mt-2">App login credentials · Max 0 = unlimited</p>
    </div>

    <div class="rounded-2xl border border-zinc-800/80 bg-zinc-900/70 p-7 shadow-2xl shadow-black/40"
         style="box-shadow:0 0 40px rgba(239,68,68,.08),0 25px 50px rgba(0,0,0,.4)">
        <form method="POST" action="{{ url('/make-user') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-zinc-500 uppercase tracking-widest mb-2">Username</label>
                <input type="text" name="username" required placeholder="Enter username"
                       class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3.5 text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 transition">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-zinc-500 uppercase tracking-widest mb-2">Password</label>
                <input type="password" name="password" required placeholder="Enter password"
                       class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3.5 text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 transition">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-zinc-500 uppercase tracking-widest mb-2">Max uses <span class="text-zinc-600 normal-case tracking-normal">(0 = unlimited)</span></label>
                <input type="number" name="max_uses" value="0" min="0" max="999999"
                       class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3.5 text-sm text-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 transition">
            </div>
            <button type="submit"
                    class="w-full py-3.5 rounded-xl font-bold text-sm text-white mt-2
                           bg-gradient-to-b from-red-500 via-red-600 to-red-800
                           hover:brightness-110 active:scale-[0.99] transition
                           shadow-lg shadow-red-900/50">
                Create User
            </button>
        </form>
    </div>

    <div class="mt-8 flex justify-center gap-6 text-sm text-zinc-500">
        <a href="{{ url('/dashboard') }}" class="hover:text-red-400 transition">← Dashboard</a>
        <a href="{{ route('keys.index') }}" class="hover:text-red-400 transition">All Keys</a>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
@php
    $total = \App\Models\Key::count();
    $active = \App\Models\Key::where('status','active')->count();
    $banned = \App\Models\Key::whereIn('status',['banned','inactive','disabled'])->count();
    $users = \App\Models\Key::whereNotNull('username')->where('username','!=','')->count();
@endphp

<div class="mb-8">
    <h1 class="text-2xl md:text-3xl font-bold">Dashboard</h1>
    <p class="text-zinc-500 text-sm mt-1">Keys, users & live status — ek jagah</p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="rounded-2xl border border-zinc-800 bg-zinc-900/60 p-5 glow-red">
        <div class="text-zinc-500 text-xs uppercase tracking-wider">Total Keys</div>
        <div class="text-3xl font-bold mt-2">{{ $total }}</div>
    </div>
    <div class="rounded-2xl border border-emerald-900/50 bg-emerald-950/20 p-5">
        <div class="text-emerald-500/80 text-xs uppercase tracking-wider">Active</div>
        <div class="text-3xl font-bold mt-2 text-emerald-400">{{ $active }}</div>
    </div>
    <div class="rounded-2xl border border-red-900/50 bg-red-950/20 p-5">
        <div class="text-red-400/80 text-xs uppercase tracking-wider">Banned / Off</div>
        <div class="text-3xl font-bold mt-2 text-red-400">{{ $banned }}</div>
    </div>
    <div class="rounded-2xl border border-blue-900/50 bg-blue-950/20 p-5">
        <div class="text-blue-400/80 text-xs uppercase tracking-wider">User+Pass</div>
        <div class="text-3xl font-bold mt-2 text-blue-400">{{ $users }}</div>
    </div>
</div>

{{-- Quick actions --}}
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    <a href="{{ route('keys.create') }}" class="rounded-2xl border border-zinc-800 bg-gradient-to-br from-red-950/40 to-zinc-900 p-6 hover:border-red-700 transition">
        <div class="text-2xl mb-2">🔑</div>
        <div class="font-semibold text-lg">Generate Keys</div>
        <div class="text-zinc-500 text-sm mt-1">Bulk keys · max uses · duration</div>
    </a>
    <a href="{{ url('/make-user') }}" class="rounded-2xl border border-zinc-800 bg-gradient-to-br from-blue-950/40 to-zinc-900 p-6 hover:border-blue-700 transition">
        <div class="text-2xl mb-2">👤</div>
        <div class="font-semibold text-lg">User + Password</div>
        <div class="text-zinc-500 text-sm mt-1">App login with username</div>
    </a>
    <a href="{{ route('keys.index') }}" class="rounded-2xl border border-zinc-800 bg-gradient-to-br from-zinc-800/40 to-zinc-900 p-6 hover:border-zinc-600 transition">
        <div class="text-2xl mb-2">📋</div>
        <div class="font-semibold text-lg">Manage Keys</div>
        <div class="text-zinc-500 text-sm mt-1">Active · Ban · Live battery</div>
    </a>
</div>

{{-- Recent --}}
<div class="rounded-2xl border border-zinc-800 overflow-hidden">
    <div class="px-5 py-4 border-b border-zinc-800 flex justify-between items-center">
        <h2 class="font-semibold">Recent keys</h2>
        <a href="{{ route('keys.index') }}" class="text-sm text-red-400 hover:underline">View all</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-zinc-900/80 text-zinc-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Key / User</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Battery</th>
                    <th class="px-4 py-3 text-left">Device</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @foreach(\App\Models\Key::orderByDesc('id')->take(8)->get() as $key)
                <tr class="hover:bg-zinc-900/50">
                    <td class="px-4 py-3 font-mono text-xs">
                        {{ $key->key }}
                        @if($key->username)
                            <div class="text-zinc-500 mt-0.5">{{ $key->username }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($key->status === 'active')
                            <span class="px-2 py-0.5 rounded-full text-xs bg-emerald-950 text-emerald-400 border border-emerald-800">Active</span>
                        @elseif(in_array($key->status, ['banned','inactive','disabled']))
                            <span class="px-2 py-0.5 rounded-full text-xs bg-red-950 text-red-400 border border-red-800">{{ ucfirst($key->status) }}</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs bg-zinc-800 text-zinc-400">{{ ucfirst($key->status) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($key->live_battery !== null)
                            {{ $key->live_battery }}% <span class="text-zinc-500 text-xs">{{ $key->live_charging }}</span>
                        @else
                            <span class="text-zinc-600">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-zinc-400">{{ $key->device_name ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold">Keys Management</h1>
        <p class="text-zinc-500 text-sm">Active / Inactive · Live status · Ban</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('keys.create') }}" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-sm font-semibold">+ Generate Keys</a>
        <a href="{{ url('/make-user') }}" class="px-4 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-sm border border-zinc-700">User + Pass</a>
    </div>
</div>

<form method="GET" class="mb-6 flex flex-wrap gap-3 items-end p-4 rounded-2xl border border-zinc-800 bg-zinc-900/40">
    <div>
        <label class="block text-xs text-zinc-500 mb-1">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Key or user..."
               class="bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-sm w-48 focus:outline-none focus:border-red-600">
    </div>
    <div>
        <label class="block text-xs text-zinc-500 mb-1">Status</label>
        <select name="status" class="bg-zinc-950 border border-zinc-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-red-600">
            <option value="">All</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
            <option value="banned" {{ request('status')=='banned'?'selected':'' }}>Banned</option>
            <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
            <option value="used" {{ request('status')=='used'?'selected':'' }}>Used</option>
        </select>
    </div>
    <button type="submit" class="px-4 py-2 rounded-lg bg-zinc-700 hover:bg-zinc-600 text-sm">Filter</button>
    <a href="{{ route('keys.index') }}" class="text-sm text-zinc-500 underline py-2">Reset</a>
</form>

<div class="rounded-2xl border border-zinc-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-zinc-900 text-zinc-500 text-xs uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3 text-left">Key / User</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Uses</th>
                    <th class="px-4 py-3 text-left">Battery</th>
                    <th class="px-4 py-3 text-left">Net</th>
                    <th class="px-4 py-3 text-left">Device</th>
                    <th class="px-4 py-3 text-left">Last Live</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($keys as $key)
                <tr class="hover:bg-zinc-900/60">
                    <td class="px-4 py-3">
                        <div class="font-mono text-xs">{{ $key->key }}</div>
                        @if($key->username)
                            <div class="text-zinc-500 text-xs mt-1">👤 {{ $key->username }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($key->status === 'active')
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-950 text-emerald-400 border border-emerald-800">● Active</span>
                        @elseif(in_array($key->status, ['banned','inactive','disabled','revoked']))
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-950 text-red-400 border border-red-800">● {{ ucfirst($key->status) }}</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-800 text-zinc-400">{{ ucfirst($key->status) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-zinc-400">
                        {{ $key->used_count ?? 0 }} / {{ ($key->max_uses === null || $key->max_uses == 0) ? '∞' : $key->max_uses }}
                    </td>
                    <td class="px-4 py-3">
                        @if($key->live_battery !== null && $key->live_battery !== '')
                            <span class="font-semibold">{{ $key->live_battery }}%</span>
                            <div class="text-xs text-zinc-500">{{ $key->live_charging }}</div>
                        @else
                            <span class="text-zinc-600">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-zinc-400">{{ $key->live_net ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="text-zinc-300">{{ $key->device_name ?? '—' }}</div>
                        @if($key->android_version)
                            <div class="text-xs text-zinc-600">Android {{ $key->android_version }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-zinc-500">
                        {{ $key->last_login_at ? \Carbon\Carbon::parse($key->last_login_at)->diffForHumans() : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            @if($key->status === 'active')
                            <form action="{{ route('keys.ban', $key) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs px-2 py-1 rounded-lg bg-red-950/50 text-red-400 border border-red-900 hover:bg-red-900/40">Ban</button>
                            </form>
                            @else
                            <form action="{{ route('keys.ban', $key) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs px-2 py-1 rounded-lg bg-emerald-950/50 text-emerald-400 border border-emerald-900 hover:bg-emerald-900/40">Activate</button>
                            </form>
                            @endif
                            <form action="{{ route('keys.destroy', $key) }}" method="POST" class="inline" onsubmit="return confirm('Delete key?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs px-2 py-1 rounded-lg bg-zinc-800 text-zinc-400 border border-zinc-700 hover:bg-zinc-700">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-zinc-500">No keys yet. Generate some.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($keys, 'links'))
    <div class="p-4 border-t border-zinc-800">
        {{ $keys->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

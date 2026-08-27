@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-2">Generate Keys</h1>
    <p class="text-zinc-500 text-sm mb-6">Bulk create · max uses 0 = unlimited</p>

    <form method="POST" action="{{ route('keys.store') }}" class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6 space-y-5">
        @csrf
        <div>
            <label class="block text-sm text-zinc-400 mb-1">Quantity</label>
            <input type="number" name="quantity" value="1" min="1" max="500"
                   class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-red-600">
        </div>
        <div>
            <label class="block text-sm text-zinc-400 mb-1">Duration (days, empty = lifetime)</label>
            <input type="number" name="duration" min="0"
                   class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-red-600"
                   placeholder="Optional">
        </div>
        <div>
            <label class="block text-sm text-zinc-400 mb-1">Max uses (0 = unlimited)</label>
            <input type="number" name="max_uses" value="0" min="0" max="999999"
                   class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-red-600">
        </div>
        <div>
            <label class="block text-sm text-zinc-400 mb-1">Note (optional)</label>
            <input type="text" name="note"
                   class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-red-600"
                   placeholder="Batch name...">
        </div>
        <button type="submit" class="w-full py-3 rounded-xl bg-red-600 hover:bg-red-500 font-semibold text-sm">
            Generate
        </button>
        <a href="{{ route('keys.index') }}" class="block text-center text-sm text-zinc-500 hover:text-zinc-300">← Back to keys</a>
    </form>
</div>
@endsection

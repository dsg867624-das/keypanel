<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Keys Management') }}
            </h2>
            <a href="{{ route('keys.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                + Generate Keys
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Search + Filter -->
            <div class="bg-white p-4 mb-4 shadow-sm sm:rounded-lg">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Search Key</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="XXXX-XXXX-..." class="border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Status</label>
                        <select name="status" class="border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Used</option>
                            <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-md text-sm">
                        Filter
                    </button>
                    <a href="{{ route('keys.index') }}" class="text-sm text-gray-600 underline">Reset</a>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Key</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uses</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($keys as $key)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-mono font-medium">{{ $key->key }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 rounded text-xs
                                            {{ $key->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $key->status === 'used' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $key->status === 'banned' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $key->status === 'expired' ? 'bg-gray-100 text-gray-800' : '' }}
                                        ">
                                            {{ ucfirst($key->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $key->duration ? $key->duration . ' days' : 'Lifetime' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $key->used_count }} / {{ $key->max_uses }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $key->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm space-x-2">
                                        @if($key->status === 'active')
                                            <form action="{{ route('keys.ban', $key) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:underline text-xs" onclick="return confirm('Ban this key?')">Ban</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('keys.destroy', $key) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-600 hover:underline text-xs" onclick="return confirm('Delete this key permanently?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">No keys found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $keys->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

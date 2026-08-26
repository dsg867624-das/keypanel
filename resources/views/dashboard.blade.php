<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                
                <!-- Total Keys -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Keys</div>
                    <div class="text-3xl font-bold text-gray-800">{{ \App\Models\Key::count() }}</div>
                </div>

                <!-- Active Keys -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Active Keys</div>
                    <div class="text-3xl font-bold text-green-600">{{ \App\Models\Key::where('status', 'active')->count() }}</div>
                </div>

                <!-- Used Keys -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Used Keys</div>
                    <div class="text-3xl font-bold text-blue-600">{{ \App\Models\Key::where('status', 'used')->count() }}</div>
                </div>

                <!-- Banned Keys -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Banned Keys</div>
                    <div class="text-3xl font-bold text-red-600">{{ \App\Models\Key::where('status', 'banned')->count() }}</div>
                </div>

            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Quick Actions</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('keys.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                        + Generate Keys
                    </a>
                    <a href="{{ route('keys.index') }}" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-md text-sm">
                        Manage Keys
                    </a>
                    <a href="{{ route('activity-logs.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm">
                        Activity Logs
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

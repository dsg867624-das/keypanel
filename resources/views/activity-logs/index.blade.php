<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Activity Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="px-4 py-3 text-sm">{{ $log->id }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            {{ $log->user->email ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 py-1 rounded text-xs 
                                                {{ $log->event === 'login' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $log->event === 'logout' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ !in_array($log->event, ['login','logout']) ? 'bg-gray-100 text-gray-800' : '' }}
                                            ">
                                                {{ $log->event }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-mono">{{ $log->ip_address }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $log->created_at->format('d M Y, h:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">No activity logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

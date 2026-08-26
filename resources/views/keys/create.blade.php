<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Generate Keys') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('keys.store') }}">
                        @csrf

                        <!-- Quantity -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">How many keys?</label>
                            <input type="number" name="quantity" value="1" min="1" max="100" required
                                   class="w-full border-gray-300 rounded-md shadow-sm">
                            <p class="text-xs text-gray-500 mt-1">Maximum 100 keys at a time</p>
                        </div>

                        <!-- Duration -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                            <select name="duration" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Lifetime (No Expiry)</option>
                                <option value="1">1 Day</option>
                                <option value="7">7 Days</option>
                                <option value="30">30 Days</option>
                                <option value="90">90 Days</option>
                                <option value="365">1 Year</option>
                            </select>
                        </div>

                        <!-- Max Uses -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Uses</label>
                            <input type="number" name="max_uses" value="1" min="1" max="100" required
                                   class="w-full border-gray-300 rounded-md shadow-sm">
                            <p class="text-xs text-gray-500 mt-1">How many times this key can be used</p>
                        </div>

                        <!-- Note -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Note (Optional)</label>
                            <textarea name="note" rows="2" class="w-full border-gray-300 rounded-md shadow-sm"
                                      placeholder="e.g. For customer XYZ"></textarea>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                                Generate Keys
                            </button>
                            <a href="{{ route('keys.index') }}" class="text-gray-600 hover:underline">Cancel</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

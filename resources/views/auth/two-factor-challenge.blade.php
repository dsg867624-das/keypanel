<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
    </div>

    <form method="POST" action="{{ route('two-factor.login') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Code')" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" inputmode="numeric" autofocus autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-4 text-sm text-gray-600">
        {{ __('Or you can confirm access to your account by entering one of your emergency recovery codes.') }}
    </div>

    <form method="POST" action="{{ route('two-factor.login') }}" class="mt-4">
        @csrf

        <div>
            <x-input-label for="recovery_code" :value="__('Recovery Code')" />
            <x-text-input id="recovery_code" class="block mt-1 w-full" type="text" name="recovery_code" autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('recovery_code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

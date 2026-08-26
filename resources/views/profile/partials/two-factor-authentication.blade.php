<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Two-Factor Authentication') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Secure your admin account with an authenticator app.') }}
        </p>
    </header>

    @if (! auth()->user()->two_factor_confirmed_at)

        <div class="mt-6">
            <p class="text-sm text-gray-600">
                {{ __('Scan this QR code with your authenticator app.') }}
            </p>

            <div class="mt-4 p-4 bg-white border rounded-lg inline-block">
                {!! auth()->user()->twoFactorQrCodeSvg() !!}
            </div>

            <p class="mt-4 text-sm text-gray-600">
                {{ __('Or enter this secret key manually:') }}
            </p>

            <code class="block mt-2 p-3 bg-gray-100 rounded text-sm break-all">
                {{ decrypt(auth()->user()->two_factor_secret) }}
            </code>
        </div>

        @if ($errors->confirmTwoFactorAuthentication->any())
            <div class="mt-4 p-3 bg-red-100 text-red-700 rounded">
                {{ $errors->confirmTwoFactorAuthentication->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('/user/confirmed-two-factor-authentication') }}" class="mt-6">
            @csrf

            <label for="code" class="block text-sm font-medium text-gray-700">
                {{ __('6-digit authentication code') }}
            </label>

            <input
                id="code"
                name="code"
                type="text"
                inputmode="numeric"
                maxlength="6"
                autocomplete="one-time-code"
                class="mt-2 block w-full rounded-md border-gray-300 shadow-sm"
                required
            >

            <button type="submit" class="mt-4 px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                {{ __('Confirm 2FA') }}
            </button>
        </form>

    @else

        <p class="mt-4 text-sm text-green-600 font-medium">
            {{ __('Two-factor authentication is enabled and confirmed.') }}
        </p>

        <div class="mt-6">
            <h3 class="font-medium text-gray-900">
                {{ __('Recovery Codes') }}
            </h3>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Store these codes somewhere safe. Each code can be used once if you lose access to your authenticator.') }}
            </p>

            <div class="mt-4 p-4 bg-gray-100 rounded-lg space-y-1">
                @foreach (auth()->user()->recoveryCodes() as $code)
                    <div class="font-mono text-sm">{{ $code }}</div>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ url('/user/two-factor-recovery-codes') }}" class="mt-6">
            @csrf
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                {{ __('Regenerate Recovery Codes') }}
            </button>
        </form>

    @endif
</section>

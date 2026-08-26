<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - DS Gaming</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center p-4">
<div class="bg-gray-800 text-white p-8 rounded-xl shadow-lg w-full max-w-md">
    <h1 class="text-2xl font-bold mb-2 text-center">DS Gaming</h1>
    <p class="text-gray-400 text-center text-sm mb-6">Admin Panel Login</p>

    @if (isset($errors) && $errors->any())
        <div class="bg-red-500/20 border border-red-500 text-red-300 p-3 rounded mb-4 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="/do-login">
        @csrf
        <div class="mb-4">
            <label class="block text-sm mb-1 text-gray-300">Email</label>
            <input type="email" name="email" required
                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white"
                   placeholder="das@admin.com" value="{{ old('email') }}">
        </div>
        <div class="mb-4">
            <label class="block text-sm mb-1 text-gray-300">Password</label>
            <input type="password" name="password" required
                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white"
                   placeholder="Password">
        </div>
        <label class="flex items-center mb-6 text-sm text-gray-300">
            <input type="checkbox" name="remember" class="mr-2"> Remember me
        </label>
        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 rounded-lg">
            Sign in
        </button>
    </form>
</div>
</body>
</html>

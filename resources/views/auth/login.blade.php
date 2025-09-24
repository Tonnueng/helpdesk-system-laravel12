<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <!-- Logo -->
        <div>
            <a href="/">
                <svg class="w-20 h-20 fill-current text-gray-500" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </a>
        </div>

        <!-- Login Form Container -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email Field -->
                <x-form-input 
                    name="email" 
                    type="email" 
                    label="Email" 
                    required 
                    autocomplete="username" 
                />

                <!-- Password Field -->
                <x-form-input 
                    name="password" 
                    type="password" 
                    label="Password" 
                    required 
                    autocomplete="current-password" 
                />

                <!-- Remember Me Checkbox -->
                <x-form-checkbox 
                    name="remember" 
                    label="Remember me" 
                />

                <!-- Form Actions -->
                <div class="flex items-center justify-between">
                    @if (Route::has('password.request'))
                        <a 
                            class="text-sm text-indigo-600 hover:text-indigo-500 underline" 
                            href="{{ route('password.request') }}"
                        >
                            Forgot your password?
                        </a>
                    @endif

                    <x-submit-button text="Log in" />
                </div>
            </form>
        </div>
    </div>
</body>
</html>

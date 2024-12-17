<x-guest-layout>
    <!-- Title for the Login Page -->
    <div class="max-w-md mx-auto mt-4 p-6 bg-white dark:bg-gray-800 rounded-lg">
        <h1 class="text-center text-3xl font-extrabold text-gray-900 dark:text-white mb-8">
            {{ __('Asset Issuance') }}
        </h1>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-5">
                <x-input-label for="email" :value="__('Email Address')" class="text-lg" />
                <x-text-input id="email" class="block mt-2 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white" 
                              type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
            </div>

            <!-- Password -->
            <div class="mb-6">
                <x-input-label for="password" :value="__('Password')" class="text-lg" />
                <x-text-input id="password" class="block mt-2 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                              type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between mb-6">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" 
                           class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600" 
                           name="remember">
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember Me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" 
                       class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Forgot Password?') }}
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center custom-login-button">
                <button type="submit" class="w-full flex justify-center items-center py-1 text-lg text-center text-white rounded-md focus:outline-none focus:ring-4 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                    {{ __('Log In') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>

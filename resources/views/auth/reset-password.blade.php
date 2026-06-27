<x-guest-layout title="Reinitialiser le mot de passe">
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-2xl font-semibold tracking-tight">Reinitialiser le mot de passe</h1>
            <p class="text-sm text-muted-foreground">Saisissez votre nouveau mot de passe ci-dessous.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <!-- Jeton de reinitialisation -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email -->
            <div class="space-y-2">
                <x-input-label for="email" :value="__('Email')" :required="true" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Mot de passe -->
            <div class="mt-4 space-y-2">
                <x-input-label for="password" :value="__('Mot de passe')" :required="true" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirmation du mot de passe -->
            <div class="mt-4 space-y-2">
                <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" :required="true" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="w-full" ::disabled="loading">
                    <svg x-show="loading" style="display: none;" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Reinitialiser le mot de passe') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>

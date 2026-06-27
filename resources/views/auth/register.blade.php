<x-guest-layout title="Inscription">
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-2xl font-semibold tracking-tight">Creer un compte</h1>
            <p class="text-sm text-muted-foreground">Saisissez vos informations pour creer votre compte</p>
        </div>

        <form method="POST" action="{{ route('register') }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <!-- Nom -->
            <x-form-input
                name="name"
                label="Nom"
                type="text"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                :messages="$errors->get('name')"
            />

            <!-- Identifiant -->
            <div class="mt-4">
                <x-form-input
                    name="username"
                    label="Identifiant"
                    type="text"
                    :value="old('username')"
                    required
                    autocomplete="username"
                    :messages="$errors->get('username')"
                />
            </div>

            <!-- Email -->
            <div class="mt-4">
                <x-form-input
                    name="email"
                    label="Email"
                    type="email"
                    :value="old('email')"
                    required
                    autocomplete="username"
                    :messages="$errors->get('email')"
                />
            </div>

            <!-- Mot de passe -->
            <div class="mt-4">
                <x-form-input
                    name="password"
                    label="Mot de passe"
                    type="password"
                    required
                    autocomplete="new-password"
                    :messages="$errors->get('password')"
                />
            </div>

            <!-- Confirmation du mot de passe -->
            <div class="mt-4">
                <x-form-input
                    name="password_confirmation"
                    label="Confirmer le mot de passe"
                    type="password"
                    required
                    autocomplete="new-password"
                    :messages="$errors->get('password_confirmation')"
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="w-full" ::disabled="loading">
                    <svg x-show="loading" style="display: none;" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Creer le compte') }}
                </x-primary-button>
            </div>

            <div class="mt-4 text-center text-sm">
                Vous avez deja un compte ?
                <a href="{{ route('login') }}" class="underline text-primary">
                    Se connecter
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>

<x-guest-layout title="Verifier l'e-mail">
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-2xl font-semibold tracking-tight">Verifier l'e-mail</h1>
            <div class="text-sm text-muted-foreground">
                Merci pour votre inscription. Avant de commencer, veuillez verifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer. Si vous ne l'avez pas recu, nous pouvons vous en envoyer un autre.
            </div>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                Un nouveau lien de verification a ete envoye a l'adresse e-mail indiquee lors de l'inscription.
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between flex-col space-y-4">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                <div>
                    <x-primary-button class="w-full" ::disabled="loading">
                        <svg x-show="loading" style="display: none;" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Renvoyer l'e-mail de verification
                    </x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="w-full text-center">
                @csrf

                <button type="submit" class="underline text-sm text-muted-foreground hover:text-primary rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ring">
                    Se deconnecter
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>

<x-modal name="user-detail-modal" focusable>
    @if($user)
        <div class="p-6">
            <!-- Header -->
            <div class="mb-6 space-y-1.5 text-center sm:text-left border-b border-gray-200 pb-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                        {{ __('Details utilisateur') }}
                    </h3>
                </div>
                <p class="text-sm text-muted-foreground">
                    Informations detaillees pour {{ $user->name }}.
                </p>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Nom') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $user->name }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Nom d utilisateur') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $user->username }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Email') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $user->email }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Inscrit le') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $user->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex items-center justify-end gap-x-2 pt-4 border-t border-border">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'user-detail-modal' })">
                    {{ __('Fermer') }}
                </x-secondary-button>
                <x-primary-button type="button" x-on:click="$dispatch('close-modal', { name: 'user-detail-modal' }); $dispatch('edit-user', { user: {{ $user->id }} })">
                    <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                    {{ __('Modifier l utilisateur') }}
                </x-primary-button>
            </div>
        </div>
    @else
        <div class="p-8 text-center flex flex-col items-center justify-center space-y-3">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            <span class="text-sm text-muted-foreground">{{ __('Chargement des details...') }}</span>
        </div>
    @endif
</x-modal>

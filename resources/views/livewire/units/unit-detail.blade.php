<x-modal name="unit-detail-modal" focusable>
    @if($unit)
        <div class="p-6">
            <!-- Header -->
            <div class="mb-6 space-y-1.5 text-center sm:text-left border-b border-gray-200 pb-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                        {{ __('Details de l unite') }}
                    </h3>
                </div>
                <p class="text-sm text-muted-foreground">
                    {{ __('Informations detaillees sur') }} {{ $unit->name }}.
                </p>
            </div>

            <div class="space-y-6">
                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Nom') }}</label>
                    <p class="text-sm text-foreground font-medium">{{ $unit->name }}</p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Symbole') }}</label>
                    <p class="text-sm text-foreground font-medium">{{ $unit->symbol }}</p>
                </div>

                <!-- Meta -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Cree le') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $unit->created_at?->format('d M Y, H:i') ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Derniere mise a jour') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $unit->updated_at?->format('d M Y, H:i') ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex items-center justify-end gap-x-2 pt-4 border-t border-border">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'unit-detail-modal' })">
                    {{ __('Fermer') }}
                </x-secondary-button>
                <x-primary-button type="button" x-on:click="$dispatch('close-modal', { name: 'unit-detail-modal' }); $dispatch('edit-unit', { unit: {{ $unit->id }} })">
                    <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                    {{ __('Modifier l unite') }}
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

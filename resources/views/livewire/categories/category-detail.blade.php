<x-modal name="category-detail-modal" focusable>
    @if($category)
        <div class="p-6">
            <div class="mb-6 space-y-1.5 border-b border-gray-200 pb-4 text-center sm:text-left">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                        {{ __('Details de la famille stock') }}
                    </h3>
                </div>
                <p class="text-sm text-muted-foreground">
                    {{ __('Informations detaillees sur') }} {{ $category->name }}.
                </p>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">Designation Famille</label>
                        <p class="text-sm text-foreground font-medium">{{ $category->name }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">Famille Article</label>
                        <p class="text-sm text-foreground font-medium">{{ $category->code ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">Feuille Excel</label>
                        <p class="text-sm text-foreground font-medium">{{ $category->worksheet_name ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Slug') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $category->slug }}</p>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Description') }}</label>
                    <p class="text-sm text-foreground font-medium">
                        {{ $category->description ?? '-' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Cree le') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $category->created_at?->format('d M Y, H:i') ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Derniere mise a jour') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $category->updated_at?->format('d M Y, H:i') ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-2 border-t border-border pt-4">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'category-detail-modal' })">
                    {{ __('Fermer') }}
                </x-secondary-button>
                <x-primary-button type="button" x-on:click="$dispatch('close-modal', { name: 'category-detail-modal' }); $dispatch('edit-category', { category: {{ $category->id }} })">
                    <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                    {{ __('Modifier la famille') }}
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

<x-modal name="product-stock-increase-modal" :title="''" maxWidth="md">
    <div class="p-6">
        <div class="mb-6 space-y-1.5 border-b border-gray-200 pb-4 text-center sm:text-left">
            <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                Ajouter une quantite
            </h3>
            <p class="text-sm text-muted-foreground">
                {{ $product?->designation ?? 'Selectionnez un article pour mettre a jour son stock.' }}
            </p>
        </div>

        @if($product)
            <form wire:submit="save" class="space-y-5">
                <div class="grid grid-cols-2 gap-4 rounded-md border border-border bg-muted/30 p-4 text-sm">
                    <div>
                        <p class="text-muted-foreground">Stock restant</p>
                        <p class="mt-1 font-semibold text-foreground">
                            {{ $product->quantity }} {{ $product->unit?->symbol }}
                        </p>
                    </div>
                    <div>
                        <p class="text-muted-foreground">Entre en Qte</p>
                        <p class="mt-1 font-semibold text-foreground">
                            {{ $product->entry_quantity ?? $product->quantity }} {{ $product->unit?->symbol }}
                        </p>
                    </div>
                </div>

                <x-form-input
                    name="amount"
                    label="Quantite a ajouter"
                    type="number"
                    wire:model="amount"
                    min="1"
                    placeholder="1"
                    required
                />

                <x-form-input
                    name="new_unit_pr_ht"
                    label="Nouveau PR unitaire HT"
                    type="number"
                    wire:model="new_unit_pr_ht"
                    min="0"
                    step="0.001"
                    placeholder="{{ $product->unit_pr_ht }}"
                />

                <p class="text-xs text-muted-foreground">
                    CUMP = (ancienne quantite x ancien PR HT + quantite ajoutee x nouveau PR HT) / quantite totale.
                </p>

                <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-4">
                    <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'product-stock-increase-modal' })">
                        {{ __('Annuler') }}
                    </x-secondary-button>

                    <x-primary-button type="submit" wire:loading.attr="disabled">
                        <svg wire:loading wire:target="save" class="-ml-1 mr-2 h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <x-heroicon-o-plus wire:loading.remove wire:target="save" class="mr-2 h-4 w-4" />
                        {{ __('Ajouter la quantite') }}
                    </x-primary-button>
                </div>
            </form>
        @endif
    </div>
</x-modal>

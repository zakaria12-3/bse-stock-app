<x-modal name="product-detail-modal" focusable>
    @if($product)
        <div class="space-y-6 p-6">
            <div class="flex items-center justify-between border-b border-border pb-4">
                <div>
                    <h3 class="text-xl font-bold tracking-tight text-foreground">{{ $product->designation }}</h3>
                    <p class="font-mono text-sm text-muted-foreground">{{ $product->reference }}</p>
                </div>
                <div>
                    @if($product->is_active)
                        <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                            Actif
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                            Inactif
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('REF FR') }}</label>
                    <p class="text-sm font-medium text-foreground">{{ $product->ref_fr ?: '-' }}</p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Designation 2') }}</label>
                    <p class="text-sm font-medium text-foreground">{{ $product->designation_2 ?: '-' }}</p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Designation Famille') }}</label>
                    <p class="text-sm font-medium text-foreground">{{ $product->category->name ?? '-' }}</p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Famille Article') }}</label>
                    <p class="text-sm font-medium text-foreground">{{ $product->category->code ?? '-' }}</p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Fournisseur') }}</label>
                    <p class="text-sm font-medium text-foreground">{{ $product->supplier->name ?? '-' }}</p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">Feuille Excel</label>
                    <p class="text-sm font-medium text-foreground">{{ $product->source_sheet_name ?: ($product->category->worksheet_name ?? '-') }}</p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Unite') }}</label>
                    <p class="text-sm font-medium text-foreground">
                        @if($product->unit)
                            {{ $product->unit->name }} <span class="text-muted-foreground">({{ $product->unit->symbol }})</span>
                        @else
                            -
                        @endif
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">Prix d'achat EUR</label>
                    <p class="text-sm font-medium text-foreground">
                        {{ blank($product->purchase_price_eur) || (float) $product->purchase_price_eur === 0.0 ? '-' : format_money($product->purchase_price_eur) }}
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('PR unitaire HT initial') }}</label>
                    <p class="text-sm font-medium text-foreground">@money($product->unit_pr_ht)</p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">Nouveau PR unitaire HT</label>
                    <p class="text-sm font-medium text-foreground">
                        {{ blank($product->new_unit_pr_ht) ? '-' : format_money($product->new_unit_pr_ht) }}
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">CUMP / PR HT dossier</label>
                    <p class="text-sm font-medium text-foreground">
                        {{ blank($product->cump_after_entry) ? '-' : format_money($product->cump_after_entry) }}
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Total HT') }}</label>
                    <p class="text-sm font-medium text-foreground">@money($product->total_ht)</p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">Entree en Qte</label>
                    <p class="text-sm font-medium text-foreground">
                        {{ ($product->entry_quantity ?? $product->quantity) . ' ' . ($product->unit->symbol ?? '') }}
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">Sortie en Qte</label>
                    <p class="text-sm font-medium text-foreground">
                        {{ $product->exit_quantity . ' ' . ($product->unit->symbol ?? '') }}
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">Reservation</label>
                    <p class="text-sm font-medium text-foreground">
                        {{ $product->reserved_quantity . ' ' . ($product->unit->symbol ?? '') }}
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">Stock restant</label>
                    <p class="text-sm font-medium text-foreground {{ $product->quantity <= $product->min_stock ? 'text-red-500' : '' }}">
                        {{ $product->quantity . ' ' . ($product->unit->symbol ?? '') }}
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Alerte stock minimum') }}</label>
                    <p class="text-sm font-medium text-foreground">{{ $product->min_stock }}</p>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Description') }}</label>
                <p class="text-sm font-medium text-foreground">
                    {{ $product->description ?: 'Aucune description renseignee.' }}
                </p>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Observation interne') }}</label>
                <div class="rounded-md border border-secondary bg-gray-50 p-3">
                    <p class="font-mono text-sm leading-relaxed text-foreground whitespace-pre-wrap">{{ $product->notes ?: 'Aucune observation.' }}</p>
                </div>
            </div>

            @if(!empty($product->custom_fields))
                <div class="space-y-3">
                    <label class="text-sm font-medium leading-none text-muted-foreground">Colonnes importees</label>
                    <div class="grid grid-cols-1 gap-3 rounded-md border border-secondary bg-gray-50 p-3 sm:grid-cols-2">
                        @foreach($product->custom_fields as $label => $value)
                            <div>
                                <p class="text-xs font-medium text-muted-foreground">{{ $label }}</p>
                                <p class="mt-1 text-sm font-medium text-foreground">{{ filled($value) ? $value : '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Cree le') }}</label>
                    <p class="text-sm font-medium text-foreground">{{ $product->created_at?->format('d M Y, H:i') ?? '-' }}</p>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Derniere mise a jour') }}</label>
                    <p class="text-sm font-medium text-foreground">{{ $product->updated_at?->format('d M Y, H:i') ?? '-' }}</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-x-2 border-t border-border pt-4">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'product-detail-modal' })">
                    {{ __('Fermer') }}
                </x-secondary-button>
                <x-primary-button type="button" x-on:click="$dispatch('close-modal', { name: 'product-detail-modal' }); $dispatch('edit-product', { product: {{ $product->id }} })">
                    <x-heroicon-o-pencil-square class="mr-2 h-4 w-4" />
                    {{ __('Modifier l article') }}
                </x-primary-button>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center justify-center space-y-3 p-8 text-center">
            <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-primary"></div>
            <span class="text-sm text-muted-foreground">{{ __('Chargement des details...') }}</span>
        </div>
    @endif
</x-modal>

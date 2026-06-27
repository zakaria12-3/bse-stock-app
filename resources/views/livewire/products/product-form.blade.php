<x-modal name="product-form-modal" :title="''" maxWidth="4xl">
    <div class="p-6">
        <div class="mb-6 space-y-1.5 border-b border-gray-200 pb-4 text-center sm:text-left">
            <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                {{ $isEditing ? 'Modifier l\'article de stock' : 'Creer un article de stock' }}
            </h3>
            <p class="text-sm text-muted-foreground">
                {{ $isEditing ? 'Mettez a jour l\'article en conservant la structure du classeur de stock.' : 'Ajoutez un article avec les memes champs que votre fichier Excel.' }}
            </p>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-form-input
                    name="reference"
                    label="Reference article"
                    type="text"
                    wire:model="reference"
                    placeholder="e.g. AO-0001"
                />

                <x-form-input
                    name="ref_fr"
                    label="REF FR"
                    type="text"
                    wire:model="ref_fr"
                    placeholder="Reference francaise optionnelle"
                />

                <x-form-input
                    name="designation"
                    label="Designation article"
                    type="text"
                    wire:model="designation"
                    placeholder="e.g. Adaptateur pour scie a cloche Metabo"
                    required
                    class="md:col-span-2"
                />

                <x-form-input
                    name="designation_2"
                    label="Designation 2"
                    type="text"
                    wire:model="designation_2"
                    placeholder="Designation secondaire optionnelle"
                    class="md:col-span-2"
                />
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="space-y-2">
                    <x-input-label for="category_id" :value="__('Designation Famille')" required />
                    <div wire:ignore>
                        <x-tom-select
                            id="category_id"
                            name="category_id"
                            wire:model="category_id"
                            :url="route('ajax.categories.search')"
                            method="POST"
                            placeholder="Choisir une famille"
                            data-initial-label="{{ $categoryName }}"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('category_id')" />
                </div>

                <x-form-input
                    name="source_sheet_name"
                    label="Feuille Excel"
                    type="text"
                    wire:model="source_sheet_name"
                    placeholder="e.g. Outillage"
                />

                <div class="space-y-2">
                    <x-input-label for="supplier_id" :value="__('Fournisseur')" />
                    <div wire:ignore>
                        <x-tom-select
                            id="supplier_id"
                            name="supplier_id"
                            wire:model="supplier_id"
                            :url="route('ajax.suppliers.search')"
                            method="POST"
                            placeholder="Choisir un fournisseur"
                            data-initial-label="{{ $supplierName }}"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('supplier_id')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="unit_id" :value="__('Unite')" />
                    <div wire:ignore>
                        <x-tom-select
                            id="unit_id"
                            name="unit_id"
                            wire:model="unit_id"
                            :url="route('ajax.units.search')"
                            method="POST"
                            placeholder="Unite optionnelle"
                            data-initial-label="{{ $unitName }}"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('unit_id')" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="space-y-2">
                    <x-input-label for="purchase_price_eur" value="Prix d'achat EUR" />
                    <x-currency-input
                        id="purchase_price_eur"
                        wire:model.live.debounce.500ms="purchase_price_eur"
                        placeholder=""
                    />
                    <x-input-error :messages="$errors->get('purchase_price_eur')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="unit_pr_ht" value="PR unitaire HT initial" required />
                    <x-currency-input
                        id="unit_pr_ht"
                        wire:model.live.debounce.500ms="unit_pr_ht"
                        placeholder="0"
                    />
                    <x-input-error :messages="$errors->get('unit_pr_ht')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="new_unit_pr_ht" value="Nouveau PR unitaire HT" />
                    <x-currency-input
                        id="new_unit_pr_ht"
                        wire:model.live.debounce.500ms="new_unit_pr_ht"
                        placeholder=""
                    />
                    <x-input-error :messages="$errors->get('new_unit_pr_ht')" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="cump_after_entry" value="CUMP / PR HT dossier" />
                    <x-currency-input
                        id="cump_after_entry"
                        wire:model.live.debounce.500ms="cump_after_entry"
                        placeholder=""
                    />
                    <x-input-error :messages="$errors->get('cump_after_entry')" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-5">
                <x-form-input
                    name="entry_quantity"
                    label="Entre en Qte"
                    type="number"
                    wire:model.live="entry_quantity"
                    min="0"
                    placeholder="0"
                    required
                />

                <x-form-input
                    name="exit_quantity"
                    label="Sortie en Qte"
                    type="number"
                    wire:model.live="exit_quantity"
                    min="0"
                    placeholder="0"
                />

                <x-form-input
                    name="reserved_quantity"
                    label="Reservation"
                    type="number"
                    wire:model.live="reserved_quantity"
                    min="0"
                    placeholder="0"
                />

                <x-form-input
                    name="quantity"
                    label="Stock restant"
                    type="number"
                    wire:model.live="quantity"
                    min="0"
                    placeholder="0"
                    required
                    readonly
                />

                <div class="space-y-2">
                    <x-input-label for="total_ht" value="Total HT" />
                    <x-currency-input
                        id="total_ht"
                        wire:model.live.debounce.500ms="total_ht"
                        placeholder=""
                        readonly
                    />
                    <x-input-error :messages="$errors->get('total_ht')" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <x-form-input
                    name="min_stock"
                    label="Alerte stock minimum"
                    type="number"
                    wire:model="min_stock"
                    min="0"
                    placeholder="0"
                    required
                />

                <div class="flex items-center h-full pt-8">
                    <label class="inline-flex cursor-pointer items-center">
                        <input
                            type="checkbox"
                            wire:model="is_active"
                            class="h-6 w-6 rounded-full border-2 border-primary text-primary focus:ring-primary/20"
                        >
                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Actif') }}
                        </span>
                    </label>
                </div>
            </div>

            <div class="space-y-2">
                <x-input-label for="description" value="Description" />
                <textarea
                    id="description"
                    wire:model="description"
                    rows="3"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    placeholder="Description optionnelle..."
                ></textarea>
                <x-input-error :messages="$errors->get('description')" />
            </div>

            <div class="space-y-2">
                <x-input-label for="notes" value="Observation" />
                <textarea
                    id="notes"
                    wire:model="notes"
                    rows="3"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    placeholder="Observation optionnelle du classeur..."
                ></textarea>
                <x-input-error :messages="$errors->get('notes')" />
            </div>

            <div class="space-y-4 border-t border-gray-200 pt-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-foreground">Proprietes personnalisees</h4>
                        <p class="text-xs text-muted-foreground">Ajoutez les colonnes supplementaires du classeur a conserver.</p>
                    </div>
                    <button type="button" wire:click="addCustomField" class="flex items-center gap-1 rounded-md bg-primary/10 px-3 py-1.5 text-sm font-medium text-primary hover:bg-primary/20 hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Ajouter une colonne
                    </button>
                </div>

                @if(count($custom_fields) === 0)
                    <p class="rounded-md border border-dashed border-border bg-muted/50 py-4 text-center text-sm italic text-muted-foreground">Aucune colonne personnalisee ajoutee.</p>
                @endif

                <div class="space-y-3">
                    @foreach($custom_fields as $index => $field)
                        <div class="flex items-start gap-3">
                            <div class="w-[40%]">
                                <x-form-input
                                    name="custom_fields.{{ $index }}.key"
                                    wire:model="custom_fields.{{ $index }}.key"
                                    placeholder="Nom de la colonne"
                                    required
                                />
                            </div>
                            <div class="flex-1">
                                <x-form-input
                                    name="custom_fields.{{ $index }}.value"
                                    wire:model="custom_fields.{{ $index }}.value"
                                    placeholder="Valeur de la colonne"
                                    required
                                />
                            </div>
                            <button type="button" wire:click="removeCustomField({{ $index }})" class="mt-[0.125rem] shrink-0 rounded-md border border-red-200 bg-red-50 p-2.5 text-red-500 transition-colors hover:bg-red-100 hover:text-red-700" title="Supprimer la propriete">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-4">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'product-form-modal' })">
                    {{ __('Annuler') }}
                </x-secondary-button>

                <x-primary-button type="submit" wire:loading.attr="disabled">
                    <svg wire:loading wire:target="save" class="-ml-1 mr-2 h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <x-heroicon-o-check wire:loading.remove wire:target="save" class="mr-2 h-4 w-4" />
                    {{ $isEditing ? __('Enregistrer les modifications') : __('Creer l\'article') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

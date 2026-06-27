<x-modal name="category-form-modal" :title="''">
    <div class="p-6">
        <div class="mb-6 space-y-1.5 border-b border-gray-200 pb-4 text-center sm:text-left">
            <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                {{ $isEditing ? 'Modifier la famille de stock' : 'Creer une famille de stock' }}
            </h3>
            <p class="text-sm text-muted-foreground">
                {{ $isEditing ? 'Mettez a jour les informations de famille du classeur.' : 'Ajoutez une famille avec le meme code et la meme structure que le classeur de stock.' }}
            </p>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-form-input
                    name="code"
                    label="Famille Article"
                    type="text"
                    wire:model="code"
                    placeholder="e.g. O"
                />

                <x-form-input
                    name="worksheet_name"
                    label="Feuille Excel"
                    type="text"
                    wire:model="worksheet_name"
                    placeholder="e.g. Outillage"
                />
            </div>

            <x-form-input
                name="name"
                label="Designation Famille"
                type="text"
                wire:model="name"
                placeholder="e.g. Outillage"
                required
            />

            <div class="space-y-2">
                <x-input-label for="description" :value="__('Description')" />
                <textarea
                    id="description"
                    wire:model="description"
                    rows="3"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    placeholder="Description optionnelle..."
                ></textarea>
                <x-input-error :messages="$errors->get('description')" />
            </div>

            <div class="space-y-4 border-t border-gray-200 pt-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-foreground">Proprietes personnalisees</h4>
                        <p class="text-xs text-muted-foreground">Ajoutez les attributs supplementaires du classeur.</p>
                    </div>
                    <button type="button" wire:click="addCustomField" class="text-sm text-primary hover:underline flex items-center gap-1 font-medium bg-primary/10 px-3 py-1.5 rounded-md hover:bg-primary/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Ajouter une colonne
                    </button>
                </div>

                @if(count($custom_fields) === 0)
                    <p class="text-sm text-muted-foreground italic text-center py-4 bg-muted/50 rounded-md border border-dashed border-border">Aucune colonne personnalisee ajoutee.</p>
                @endif

                <div class="space-y-3">
                    @foreach($custom_fields as $index => $field)
                        <div class="flex items-start gap-3">
                            <div class="w-[40%]">
                                <x-form-input
                                    name="custom_fields.{{ $index }}.key"
                                    wire:model="custom_fields.{{ $index }}.key"
                                    placeholder="Propriete"
                                    required
                                />
                            </div>
                            <div class="flex-1">
                                <x-form-input
                                    name="custom_fields.{{ $index }}.value"
                                    wire:model="custom_fields.{{ $index }}.value"
                                    placeholder="Valeur"
                                    required
                                />
                            </div>
                            <button type="button" wire:click="removeCustomField({{ $index }})" class="mt-[0.125rem] shrink-0 text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2.5 rounded-md border border-red-200 transition-colors" title="Supprimer la propriete">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 border-t border-gray-200 pt-4">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'category-form-modal' })">
                    {{ __('Annuler') }}
                </x-secondary-button>

                <x-primary-button type="submit" wire:loading.attr="disabled">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <x-heroicon-o-check wire:loading.remove wire:target="save" class="w-4 h-4 mr-2" />
                    {{ $isEditing ? __('Enregistrer les modifications') : __('Creer la famille') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>

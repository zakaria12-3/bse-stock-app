<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6">
            <div class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl">
                <div class="border-b border-gray-200 px-6 py-5">
                    <div class="space-y-1.5 text-center sm:text-left">
                        <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                            Importer les articles de stock
                        </h3>
                        <p class="text-sm text-muted-foreground">
                            Importez un fichier Excel avec les memes colonnes que la base de stock.
                        </p>
                    </div>
                </div>

                <div class="overflow-y-auto p-6">
                    <div class="space-y-6">
                        @if($successMessage)
                            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                                {{ $successMessage }}
                            </div>
                        @endif

                        <div class="space-y-2">
                            <x-input-label for="stock-file" value="Excel File" required />
                            <input
                                id="stock-file"
                                type="file"
                                wire:model="file"
                                class="block w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground file:mr-4 file:rounded-md file:border-0 file:bg-primary/10 file:px-3 file:py-2 file:text-sm file:font-medium file:text-primary hover:file:bg-primary/20"
                            >
                            <p class="text-xs text-muted-foreground">
                                Formats acceptes : `.xlsx`, `.xls`, `.csv`
                            </p>
                            @if($file)
                                <p class="text-sm text-foreground">
                                    Fichier selectionne : <span class="font-medium">{{ $file->getClientOriginalName() }}</span>
                                </p>
                            @endif
                            <x-input-error :messages="$errors->get('file')" />
                        </div>

                        <div class="space-y-3 rounded-md border border-gray-200 bg-muted/20 p-4">
                            <div>
                                <h4 class="text-sm font-medium text-foreground">Donnees importees</h4>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    Chaque ligne met a jour ou cree un article en utilisant la reference comme cle unique.
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-foreground">Colonnes attendues</h4>
                                <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    @foreach($expectedColumns as $column)
                                        <div class="rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground">
                                            {{ $column }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4">
                    <x-secondary-button type="button" wire:click="closeModal">
                        Annuler
                    </x-secondary-button>

                    <x-primary-button type="button" wire:click="import" wire:loading.attr="disabled">
                        <svg wire:loading wire:target="import" class="-ml-1 mr-2 h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <x-heroicon-o-arrow-up-tray wire:loading.remove wire:target="import" class="mr-2 h-4 w-4" />
                        Importer la feuille de stock
                    </x-primary-button>
                </div>
            </div>
        </div>
    @endif
</div>

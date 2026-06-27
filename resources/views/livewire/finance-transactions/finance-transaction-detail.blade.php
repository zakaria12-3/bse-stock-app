<x-modal name="finance-transaction-detail-modal" focusable>
    @if($transaction)
        <div class="p-6">
            <!-- Header -->
            <div class="mb-6 space-y-1.5 text-center sm:text-left border-b border-gray-200 pb-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                        {{ __('Details de la transaction') }}
                    </h3>
                </div>
                <p class="text-sm text-muted-foreground">
                    {{ __('Informations detaillees sur la transaction') }} #{{ $transaction->id }}.
                </p>
            </div>

            <div class="space-y-6">
                <!-- Row 1 -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Code') }}</label>
                        <p class="text-sm text-foreground font-medium font-mono">{{ $transaction->code }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Date') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $transaction->transaction_date->format('d M Y') }}</p>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Categorie') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $transaction->category->name }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Montant') }}</label>
                        <p class="text-sm text-foreground font-medium">@money($transaction->amount)</p>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Type') }}</label>
                        <div>
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $transaction->category->type->color() }}">
                                {{ $transaction->category->type->label() }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Reference externe') }}</label>
                        <p class="text-sm text-foreground font-medium font-mono">{{ $transaction->external_reference ?? '-' }}</p>
                    </div>
                </div>

                <!-- Row 4 -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Cree par') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $transaction->creator->name }}</p>
                    </div>
                </div>


                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Description') }}</label>
                    <p class="text-sm text-foreground font-medium">
                        {{ $transaction->description ?? '-' }}
                    </p>
                </div>

                <!-- Meta -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Cree le') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $transaction->created_at?->format('d M Y, H:i') ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Derniere mise a jour') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $transaction->updated_at?->format('d M Y, H:i') ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex items-center justify-end gap-x-2 pt-4 border-t border-border">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'finance-transaction-detail-modal' })">
                    {{ __('Fermer') }}
                </x-secondary-button>

                @if(!$transaction->reference_type)
                    <x-primary-button type="button" x-on:click="$dispatch('close-modal', { name: 'finance-transaction-detail-modal' }); $dispatch('edit-finance-transaction', { transaction: {{ $transaction->id }} })">
                        <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                        {{ __('Modifier la transaction') }}
                    </x-primary-button>
                @endif
            </div>
        </div>
    @else
        <div class="p-8 text-center flex flex-col items-center justify-center space-y-3">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            <span class="text-sm text-muted-foreground">{{ __('Chargement des details...') }}</span>
        </div>
    @endif
</x-modal>

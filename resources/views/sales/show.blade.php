<x-app-layout title="Details du dossier">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                {{ __('Details du dossier') }} #{{ $sale->invoice_number ?: $sale->id }}
            </h2>
            <div class="flex items-center gap-2">
                <x-secondary-button href="{{ route('sales.index') }}">
                    &larr; {{ __('Retour a la liste') }}
                </x-secondary-button>
                <x-primary-button href="{{ route('sales.print', $sale) }}" target="_blank">
                    <x-heroicon-o-printer class="w-4 h-4 mr-2" />
                    {{ __('Imprimer la facture') }}
                </x-primary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Main Info Card -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden border border-gray-200">
                <div class="p-6">
                    <!-- Header Info -->
                    <div class="flex items-start justify-between border-b border-gray-100 pb-4 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Informations du dossier') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('Details de l amenagement, renovation ou SAV ambulance') }}</p>
                        </div>
                        <div class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-medium border border-slate-200">
                            ID: #{{ $sale->id }}
                        </div>
                    </div>

                    <!-- Content Grid -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Client -->
                        <x-detail-item label="Client" :value="$sale->customer->name ?? 'Client passager'">
                            <x-heroicon-o-user class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Invoice -->
                        <x-detail-item label="Numero de facture" :value="$sale->invoice_number ?? '-'">
                            <x-heroicon-o-document-text class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <x-detail-item label="Nom du dossier" :value="$sale->build_name ?? '-'">
                            <x-heroicon-o-wrench-screwdriver class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Date de vente -->
                        <x-detail-item label="Date du dossier" :value="$sale->sale_date->format('d M Y')">
                            <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Payment Method -->
                        <x-detail-item label="Mode de paiement" :value="$sale->payment_method->label()">
                            <x-heroicon-o-check-badge class="w-4 h-4 text-gray-400" />
                        </x-detail-item>

                        <!-- Status -->
                        <div>
                            <label class="text-sm font-medium leading-none text-gray-500">Statut</label>
                            <div class="mt-1">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $sale->status->color() }}">
                                    {{ $sale->status->label() }}
                                </span>
                            </div>
                        </div>



                        <!-- Cree par -->
                        <x-detail-item label="Cree par" :value="$sale->creator->name ?? 'Inconnu'">
                            <x-heroicon-o-user class="w-4 h-4 text-gray-400" />
                        </x-detail-item>
                    </div>

                    <!-- Notes -->
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <div class="space-y-1">
                            <label class="text-sm font-medium leading-none text-gray-500">
                                Notes
                            </label>
                            <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                                <p class="text-sm text-slate-700 italic leading-relaxed">{{ $sale->notes ?: 'Aucune note supplementaire.' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table Section -->
                    <div class="mt-6 border-t overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Code</th>
                                    <th class="px-6 py-3">Piece</th>
                                    <th class="px-6 py-3">Unite</th>
                                    <th class="px-6 py-3 text-center">Qte</th>
                                    <th class="px-6 py-3 text-right">Prix piece</th>
                                    <th class="px-6 py-3 text-right">Pose</th>
                                    <th class="px-6 py-3 text-right">Prix installe</th>
                                    <th class="px-6 py-3 text-right">Remise</th>
                                    <th class="px-6 py-3 text-right">Sous-total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($sale->items as $item)
                                    <tr class="bg-white hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $item->product?->product_code ?? $item->product?->sku ?? $item->product_reference ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{ $item->product?->name ?? $item->product_name ?? 'Article supprime' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $item->product?->unit?->symbol ?? $item->product?->unit?->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            {{ number_format($item->quantity) }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @money($item->unit_price)
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-gray-600">
                                            @if((float) $item->labor_total > 0)
                                                <div>{{ number_format((float) $item->labor_hours, 2, ',', ' ') }} h x @money($item->labor_rate)</div>
                                                <div class="font-medium text-gray-900">@money($item->labor_total)</div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium">
                                            @money((float) $item->unit_price + (float) $item->labor_total)
                                        </td>
                                        <td class="px-6 py-4 text-right text-red-500">
                                            {!! $item->discount > 0 ? "- <span>" . format_money($item->discount) . "</span>" : '-' !!}
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium">
                                            @money($item->subtotal)
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 font-bold">
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-right">Sous-total</td>
                                    <td class="px-6 py-4 text-right text-gray-700">
                                        @money($sale->subtotal)
                                    </td>
                                </tr>
                                @if($sale->total_discount > 0)
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-right text-red-600">Remise totale (pieces)</td>
                                        <td class="px-6 py-4 text-right text-red-600">
                                            - @money($sale->total_discount - $sale->global_discount)
                                        </td>
                                    </tr>
                                @endif
                                @if($sale->global_discount > 0)
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-right text-red-600">Remise globale (dossier)</td>
                                        <td class="px-6 py-4 text-right text-red-600">
                                            - @money($sale->global_discount)
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-right">Total</td>
                                    <td class="px-6 py-4 text-right text-indigo-600 text-lg">
                                        @money($sale->total)
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-right text-gray-600">Montant recu</td>
                                    <td class="px-6 py-4 text-right text-gray-800">
                                        @money($sale->cash_received)
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-right text-gray-600">Monnaie</td>
                                    <td class="px-6 py-4 text-right text-green-600">
                                        @money($sale->change)
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Workflow -->
            <div x-data="{
                actionUrl: '',
                actionMethod: '',
                modalTitle: '',
                modalMessage: '',
                confirmButtonText: '',
                confirmButtonClass: '',

                confirmAction(url, method, title, message, btnText, btnClass) {
                    this.actionUrl = url;
                    this.actionMethod = method;
                    this.modalTitle = title;
                    this.modalMessage = message;
                    this.confirmButtonText = btnText;
                    this.confirmButtonClass = btnClass;
                    $dispatch('open-modal', { name: 'confirmation-modal' });
                }
            }" class="flex flex-col sm:flex-row justify-end gap-4">

                @if($sale->status === \App\Enums\SaleStatus::PENDING)
                    {{-- Complete / Pay Action --}}
                    <x-primary-button
                        class="!bg-green-600 hover:!bg-green-700 focus:!ring-green-500"
                        @click="confirmAction('{{ route('sales.complete', $sale) }}', 'PATCH', 'Terminer le dossier', 'Marquer ce dossier comme termine ? Cela confirme que le paiement a ete recu.', 'Terminer le dossier', '!bg-green-600 hover:!bg-green-700 focus:!ring-green-500')"
                    >
                        {{ __('Terminer le dossier') }}
                    </x-primary-button>

                    {{-- Cancel Pending Action (Modal) --}}
                    <div x-data="{ cancelOpen: false }">
                        <x-danger-button @click="cancelOpen = true">
                            {{ __('Annuler le dossier') }}
                        </x-danger-button>

                        <!-- Cancel Modal -->
                        <div x-show="cancelOpen"
                             style="display: none;"
                             x-transition.opacity
                             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 flex items-center justify-center p-4">

                            <div @click.outside="cancelOpen = false"
                                 x-transition.scale
                                 class="relative bg-white rounded-lg max-w-md w-full p-6 shadow-xl text-left">

                                <h3 class="text-lg font-medium text-gray-900 mb-2">
                                    {{ __('Annuler le dossier en attente') }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    {{ __('Voulez-vous annuler ce dossier en attente ? Merci d indiquer la raison.') }}
                                </p>

                                <form action="{{ route('sales.destroy', $sale) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <div class="mb-4">
                                        <x-input-label for="reason" :value="__('Raison')" />
                                        <textarea
                                            name="reason"
                                            id="reason"
                                            rows="3"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            placeholder="Changement client, correction atelier..."
                                            required
                                        ></textarea>
                                    </div>

                                    <div class="mt-6 flex justify-end gap-3">
                                        <x-secondary-button type="button" @click="cancelOpen = false">
                                            {{ __('Retour') }}
                                        </x-secondary-button>
                                        <x-danger-button type="submit">
                                            {{ __('Annuler le dossier') }}
                                        </x-danger-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                @if($sale->status === \App\Enums\SaleStatus::COMPLETED)
                    {{-- Cancel Action --}}
                    <x-secondary-button
                        class="text-red-600 hover:bg-red-50 border-red-200"
                        @click="confirmAction('{{ route('sales.destroy', $sale) }}', 'DELETE', 'Annuler le dossier', 'Voulez-vous annuler ce dossier ? Les stocks reserves seront remis en stock.', 'Oui, annuler le dossier', '!bg-red-600 hover:!bg-red-700 focus:!ring-red-500')"
                    >
                        {{ __('Annuler le dossier') }}
                    </x-secondary-button>
                @endif

                @if($sale->status === \App\Enums\SaleStatus::CANCELLED)
                    {{-- Restore Action --}}
                    <x-secondary-button
                        class="bg-gray-800 text-white hover:bg-gray-700 focus:ring-gray-500"
                        @click="confirmAction('{{ route('sales.restore', $sale) }}', 'PATCH', 'Restaurer le dossier', 'Remettre ce dossier en attente ? Vous pourrez ensuite le terminer a nouveau.', 'Remettre en attente', '!bg-gray-800 hover:!bg-gray-700 text-white')"
                    >
                        {{ __('Remettre en attente') }}
                    </x-secondary-button>
                @endif

                <!-- Shared Confirmation Modal -->
                <x-modal name="confirmation-modal">
                    <div class="p-6" x-data="{ submitting: false }">
                        <h2 class="text-lg font-medium text-gray-900" x-text="modalTitle"></h2>

                        <p class="mt-1 text-sm text-gray-600" x-text="modalMessage"></p>

                        <div class="mt-6 flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close-modal', { name: 'confirmation-modal' })" x-bind:disabled="submitting">
                                {{ __('Retour') }}
                            </x-secondary-button>

                            <form :action="actionUrl" method="POST" class="ml-3" @submit="submitting = true">
                                @csrf
                                <input type="hidden" name="_method" :value="actionMethod">

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 py-2 text-white shadow-sm bg-primary"
                                    x-bind:class="confirmButtonClass + (submitting ? ' opacity-75 cursor-not-allowed' : '')"
                                    x-bind:disabled="submitting"
                                >
                                    <svg x-show="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="confirmButtonText"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </x-modal>

            </div>
        </div>
    </div>
</x-app-layout>

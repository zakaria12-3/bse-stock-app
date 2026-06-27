<x-app-layout title="Articles de stock">
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-2xl font-semibold leading-tight text-foreground">
                    {{ __('Articles de stock') }}
                </h2>
            </div>

            <div class="flex flex-wrap gap-3">
                <x-primary-button x-data x-on:click="$dispatch('create-product')">
                    <x-heroicon-o-plus class="mr-2 h-4 w-4" />
                    {{ __('Creer un article') }}
                </x-primary-button>

                <a
                    href="{{ route('products.export') }}"
                    class="inline-flex items-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium text-foreground shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground"
                >
                    <x-heroicon-o-arrow-down-tray class="mr-2 h-4 w-4" />
                    {{ __('Exporter le classeur') }}
                </a>

                <x-secondary-button x-data x-on:click="$dispatch('open-import-modal')">
                    <x-heroicon-o-arrow-up-tray class="mr-2 h-4 w-4" />
                    {{ __('Importer la feuille de stock') }}
                </x-secondary-button>

                @if(\Illuminate\Support\Facades\Auth::user()->isAdmin())
                    <x-danger-button
                        x-data
                        x-on:click="$dispatch('open-delete-modal', {
                            component: 'products.product-table',
                            method: 'deleteAll',
                            title: 'Supprimer tous les articles ?',
                            description: 'Cette action supprimera tous les articles de stock. Les anciens achats et dossiers garderont le nom et la reference de chaque article.',
                            confirmButtonText: 'Supprimer tous les articles'
                        })"
                    >
                        <x-heroicon-o-trash class="mr-2 h-4 w-4" />
                        {{ __('Supprimer tous les articles') }}
                    </x-danger-button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <livewire:products.product-table />
        </div>
    </div>

    <livewire:products.product-import />
    <livewire:products.product-form />
    <livewire:products.product-stock-increase />
    <livewire:products.product-detail />
</x-app-layout>

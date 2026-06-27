<x-app-layout title="Importer les articles de stock">
    <div class="mx-auto max-w-4xl py-10 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(8,145,178,0.12),_transparent_42%),linear-gradient(135deg,#f8fafc_0%,#ecfeff_100%)] px-6 py-6">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-700">Import Excel</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">Importer les articles de stock</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                    Utilisez cette entree pour charger un classeur conforme au fichier de stock BSE : references articles, familles, fournisseurs, quantites et valeurs HT.
                </p>
            </div>

            <div class="px-6 py-6">
                <div class="mb-5 flex flex-wrap justify-start gap-3">
                    <a
                        href="{{ route('products.index') }}"
                        class="inline-flex items-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium text-foreground shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground"
                    >
                        <x-heroicon-o-arrow-left class="mr-2 h-4 w-4" />
                        Retour aux articles
                    </a>

                    <x-primary-button x-data x-on:click="$dispatch('open-import-modal')">
                        <x-heroicon-o-arrow-up-tray class="mr-2 h-4 w-4" />
                        Ouvrir l'import
                    </x-primary-button>
                </div>

                <livewire:products.product-import />

                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm leading-6 text-amber-800">
                    Le flux principal se fait dans la fenetre d'import des articles de stock. Cette page reste disponible comme acces rapide.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

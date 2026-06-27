<x-app-layout title="Transactions financieres">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                Transactions financieres
            </h2>
            <x-primary-button x-data x-on:click="$dispatch('create-finance-transaction')">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                Creer une transaction
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:finance-transactions.finance-transaction-table />
        </div>
    </div>

    <livewire:finance-transactions.finance-transaction-form />
    <livewire:finance-transactions.finance-transaction-detail />
</x-app-layout>

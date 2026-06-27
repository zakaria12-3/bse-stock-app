<?php

namespace App\Livewire\Products;

use App\Imports\ProductsImport;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductImport extends Component
{
    use WithFileUploads;

    public $file;
    public $showModal = false;
    public $successMessage = null;
    public array $expectedColumns = [
        'Reference Article',
        'REF FR',
        'Designation Article',
        'Designation 2',
        'Famille Article',
        'Designation Famille',
        'Fournisseur',
        'Qte / Entre en Qte',
        "Prix d'achat EUR",
        'PR unitaire HT',
        'Total HT',
        'Nouveau PR unitaire HT',
        'CUMP / PR HT dossier',
        'Sortie en Qte',
        'Reservation',
        'Stock restant',
        'Observation',
    ];

    protected $listeners = ['open-import-modal' => 'openModal'];

    public function openModal()
    {
        $this->reset(['file', 'successMessage']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function import()
    {
        $this->resetErrorBag();

        $this->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls'
        ]);

        $result = (new ProductsImport())->import($this->file->getRealPath());
        Cache::forget('categories_list_all');

        if (($result['products'] ?? 0) === 0) {
            $this->addError('file', 'Aucune ligne de stock detectee dans ce classeur. Verifiez le fichier selectionne puis reessayez.');
            $this->successMessage = null;
            return;
        }

        $message = "Classeur de stock importe avec succes. {$result['products']} articles synchronises depuis {$result['sheets']} feuilles.";

        $this->reset(['file', 'successMessage']);
        $this->showModal = false;
        $this->dispatch('pg:eventRefresh-product-table');
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function render()
    {
        return view('livewire.products.product-import');
    }
}

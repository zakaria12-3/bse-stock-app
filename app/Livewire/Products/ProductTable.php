<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Services\ProductService;
use App\Exceptions\ProductException;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

final class ProductTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'product-table';
    public string $sortField = 'designation';
    public string $sortDirection = 'asc';

    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'outside']);
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable('product_export_' . now()->format('Y_m_d'))
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header()->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage(perPage: 10, perPageValues: [10, 25, 50, 100])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Product::query()
            ->select([
                'id',
                'category_id',
                'supplier_id',
                'unit_id',
                'reference',
                'ref_fr',
                'designation',
                'designation_2',
                'source_sheet_name',
                'purchase_price_eur',
                'unit_pr_ht',
                'new_unit_pr_ht',
                'cump_after_entry',
                'selling_price',
                'entry_quantity',
                'quantity',
                'exit_quantity',
                'reserved_quantity',
                'min_stock',
                'total_ht',
                'notes',
                'is_active',
                'created_at',
            ])
            ->with([
                'category:id,name,code,worksheet_name',
                'supplier:id,name',
                'unit:id,symbol',
            ]);
    }

    public function fields(): PowerGridFields
    {
        $fields = PowerGrid::fields()
            ->add('id')
            ->add('reference')
            ->add('ref_fr')
            ->add('designation')
            ->add('designation_display', function (Product $model) {
                return $model->is_active ? $model->designation : '(INACTIVE) ' . $model->designation;
            })
            ->add('designation_2')
            ->add('category_name', fn (Product $model) => $model->category?->name ?? '-')
            ->add('category_code', fn (Product $model) => $model->category?->code ?? '-')
            ->add('worksheet_name', fn (Product $model) => $model->source_sheet_name ?: ($model->category?->worksheet_name ?? '-'))
            ->add('supplier_name', fn (Product $model) => $model->supplier?->name ?? '-')
            ->add('unit_symbol', fn (Product $model) => $model->unit?->symbol ?? '-')
            ->add('purchase_price_eur_formatted', fn (Product $model) => blank($model->purchase_price_eur) || (float) $model->purchase_price_eur === 0.0 ? '-' : format_money($model->purchase_price_eur))
            ->add('unit_pr_ht_formatted', fn (Product $model) => format_money($model->unit_pr_ht))
            ->add('new_unit_pr_ht_formatted', fn (Product $model) => blank($model->new_unit_pr_ht) ? '-' : format_money($model->new_unit_pr_ht))
            ->add('cump_after_entry_formatted', fn (Product $model) => blank($model->cump_after_entry) ? '-' : format_money($model->cump_after_entry))
            ->add('selling_price_formatted', fn (Product $model) => format_money($model->selling_price))
            ->add('total_ht_formatted', fn (Product $model) => format_money($model->total_ht))
            ->add('entry_quantity')
            ->add('quantity')
            ->add('exit_quantity')
            ->add('reserved_quantity')
            ->add('min_stock')
            ->add('notes')
            ->add('is_active_label', function (Product $model) {
                return $model->is_active
                    ? '<div class="flex items-center justify-center text-green-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></div>'
                    : '<div class="flex items-center justify-center text-red-500"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></div>';
            })
            ->add('is_active_export', fn (Product $model) => $model->is_active ? 'true' : 'false')
            ->add('created_at_formatted', fn (Product $model) => $model->created_at?->format('d/m/Y H:i'));

        foreach ($this->customFieldColumns() as $key => $label) {
            $fields->add($key, fn (Product $model) => $model->custom_fields[$label] ?? '-');
        }

        return $fields;
    }

    public function columns(): array
    {
        $columns = [
            Column::action('Action'),

            Column::make('Reference', 'reference')
                ->sortable()
                ->searchable(),

            Column::make('REF FR', 'ref_fr')
                ->searchable(),

            Column::make('Designation', 'designation_display', 'designation')
                ->sortable()
                ->searchable()
                ->visibleInExport(false),

            Column::make('Designation', 'designation')
                ->hidden()
                ->visibleInExport(true),

            Column::make('Designation 2', 'designation_2')
                ->searchable(),

            Column::make('Famille', 'category_name', 'category_id')
                ->sortable()
                ->searchable(),

            Column::make('Code famille', 'category_code')
                ->visibleInExport(true),

            Column::make('Feuille', 'worksheet_name', 'source_sheet_name')
                ->sortable()
                ->searchable(),

            Column::make('Fournisseur', 'supplier_name', 'supplier_id')
                ->sortable()
                ->searchable(),

            Column::make('Stock restant', 'quantity')
                ->sortable()
                ->bodyAttribute('text-center'),

            Column::make('Entree', 'entry_quantity', 'entry_quantity')
                ->sortable()
                ->bodyAttribute('text-center'),

            Column::make('Sortie', 'exit_quantity')
                ->sortable()
                ->bodyAttribute('text-center'),

            Column::make('Reservation', 'reserved_quantity')
                ->sortable()
                ->bodyAttribute('text-center'),

            Column::make('Prix achat EUR', 'purchase_price_eur_formatted', 'purchase_price_eur')
                ->sortable()
                ->bodyAttribute('text-right'),

            Column::make('PR HT initial', 'unit_pr_ht_formatted', 'unit_pr_ht')
                ->sortable()
                ->bodyAttribute('text-right'),

            Column::make('New PR HT', 'new_unit_pr_ht_formatted', 'new_unit_pr_ht')
                ->sortable()
                ->bodyAttribute('text-right'),

            Column::make('CUMP / PR dossier', 'cump_after_entry_formatted', 'cump_after_entry')
                ->sortable()
                ->bodyAttribute('text-right'),

            Column::make('Total HT', 'total_ht_formatted', 'total_ht')
                ->sortable()
                ->bodyAttribute('text-right'),

            Column::make('PR dossier legacy', 'selling_price_formatted', 'selling_price')
                ->hidden()
                ->visibleInExport(true),

            Column::make('Stock min', 'min_stock')
                ->sortable()
                ->bodyAttribute('text-center'),

            Column::make('Unite', 'unit_symbol', 'unit_id')
                ->sortable()
                ->searchable(),

            Column::make('Observation', 'notes')
                ->searchable(),

            Column::make('Statut', 'is_active_label', 'is_active')
                ->sortable()
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center')
                ->visibleInExport(false),

            Column::make('Statut', 'is_active_export', 'is_active')
                ->hidden()
                ->visibleInExport(true),

            Column::make('Cree le', 'created_at_formatted', 'created_at')
                ->hidden()
                ->visibleInExport(true),
        ];

        foreach ($this->customFieldColumns() as $key => $label) {
            $columns[] = Column::make($label, $key);
        }

        return $columns;
    }

    public function filters(): array
    {
        return [
            Filter::multiSelectAsync('category_name', 'category_id')
                ->url(route('ajax.categories.search'))
                ->method('POST')
                ->optionValue('value')
                ->optionLabel('text'),

            Filter::multiSelectAsync('supplier_name', 'supplier_id')
                ->url(route('ajax.suppliers.search'))
                ->method('POST')
                ->optionValue('value')
                ->optionLabel('text'),

            Filter::multiSelectAsync('unit_symbol', 'unit_id')
                ->url(route('ajax.units.search'))
                ->method('POST')
                ->optionValue('value')
                ->optionLabel('text'),

            Filter::multiSelect('is_active_label', 'is_active')
                ->dataSource(collect([
                    ['value' => 1, 'text' => 'Actif'],
                    ['value' => 0, 'text' => 'Inactif'],
                ]))
                ->optionValue('value')
                ->optionLabel('text'),
        ];
    }

    private function customFieldColumns(): array
    {
        return Product::query()
            ->whereNotNull('custom_fields')
            ->get(['custom_fields'])
            ->flatMap(fn (Product $product) => array_keys($product->custom_fields ?? []))
            ->unique()
            ->values()
            ->mapWithKeys(fn (string $label) => ['custom_' . md5($label) => $label])
            ->all();
    }

    public function actions(Product $row): array
    {
        $actions = [
            Button::add('view')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>')
                ->class('bg-blue-400 hover:bg-blue-500 text-white p-2 rounded-md flex items-center justify-center')
                ->dispatch('show-product', ['product' => $row->id])
                ->tooltip('Voir l article'),

            Button::add('increase-stock')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>')
                ->class('bg-emerald-500 hover:bg-emerald-600 text-white p-2 rounded-md flex items-center justify-center')
                ->dispatch('increase-product-stock', ['product' => $row->id])
                ->tooltip('Ajouter une quantite'),
        ];

        if (\Illuminate\Support\Facades\Auth::user()->isAdmin()) {
            $actions[] = Button::add('edit')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>')
                ->class('bg-amber-500 hover:bg-amber-600 text-white p-2 rounded-md flex items-center justify-center')
                ->dispatch('edit-product', ['product' => $row->id])
                ->tooltip('Modifier l article');

            $actions[] = Button::add('delete')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>')
                ->class('bg-red-500 hover:bg-red-600 text-white p-2 rounded-md flex items-center justify-center')
                ->dispatch('open-delete-modal', [
                    'component' => 'products.product-table',
                    'method' => 'delete',
                    'params' => ['rowId' => $row->id],
                    'title' => 'Supprimer l article ?',
                    'description' => "Voulez-vous vraiment supprimer l article '{$row->designation}' ? Cette action est definitive.",
                ])
                ->tooltip('Supprimer l article');
        }

        return $actions;
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId, ProductService $service): void
    {
        $product = Product::find($rowId);

        if ($product) {
            try {
                $service->deleteProduct($product);
                $this->dispatch('toast', message: 'Article supprime avec succes.', type: 'success');
            } catch (\Exception $e) {
                $message = $e instanceof ProductException
                    ? $e->getMessage()
                    : 'Echec de la suppression de l article : ' . $e->getMessage();

                $this->dispatch('toast', message: $message, type: 'error');
            }
        }
    }

    #[\Livewire\Attributes\On('deleteAll')]
    public function deleteAll(ProductService $service): void
    {
        if (! \Illuminate\Support\Facades\Auth::user()->isAdmin()) {
            $this->dispatch('toast', message: 'Vous n avez pas l autorisation de supprimer tous les articles.', type: 'error');
            return;
        }

        $deletedProducts = $service->deleteAllProducts();

        $this->dispatch('pg:eventRefresh-product-table');
        $this->dispatch('toast', message: "{$deletedProducts} article(s) supprimes avec succes.", type: 'success');
    }
}

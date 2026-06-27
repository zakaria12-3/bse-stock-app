<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use App\Models\Category;
use App\DTOs\CategoryData;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use App\Services\CategoryService;
use App\Exceptions\CategoryException;

class CategoryForm extends Component
{
    public bool $isEditing = false;
    public ?Category $category = null;

    public ?string $code = null;
    public ?string $worksheet_name = null;
    public string $name = '';

    public string $description = '';
    public array $custom_fields = [];

    public function rules(): array
    {
        return [
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('categories', 'code')->ignore($this->category?->id),
            ],
            'worksheet_name' => ['nullable', 'string', 'max:100'],
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($this->category?->id),
            ],
            'description' => ['nullable', 'string'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*.key' => ['required', 'string'],
            'custom_fields.*.value' => ['required', 'string'],
        ];
    }

    public function addCustomField()
    {
        $this->custom_fields[] = ['key' => '', 'value' => ''];
    }

    public function removeCustomField($index)
    {
        unset($this->custom_fields[$index]);
        $this->custom_fields = array_values($this->custom_fields);
    }

    public function render()
    {
        return view('livewire.categories.category-form');
    }

    #[On('create-category')]
    public function create(): void
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->reset(['code', 'worksheet_name', 'name', 'description', 'category', 'isEditing', 'custom_fields']);
        $this->dispatch('open-modal', name: 'category-form-modal');
    }

    #[On('edit-category')]
    public function edit(Category $category): void
    {
        $this->resetValidation();
        $this->resetErrorBag();

        if (!\Illuminate\Support\Facades\Auth::user()->isAdmin()) {
            $this->dispatch('toast', message: 'Vous n avez pas l autorisation de modifier les categories existantes. Contactez un administrateur.', type: 'error');
            return;
        }

        $this->category = $category;
        $this->code = $category->code;
        $this->worksheet_name = $category->worksheet_name;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        
        $this->custom_fields = [];
        if (is_array($category->custom_fields)) {
            foreach ($category->custom_fields as $key => $value) {
                $this->custom_fields[] = ['key' => $key, 'value' => $value];
            }
        }

        $this->isEditing = true;
        $this->dispatch('open-modal', name: 'category-form-modal');
    }

    public function save(CategoryService $service): void
    {
        $validated = $this->validate();

        $slug = Str::slug(str_replace('&', '', $this->name));

        $formattedCustomFields = [];
        foreach ($this->custom_fields as $field) {
            if (!empty($field['key'])) {
                $formattedCustomFields[$field['key']] = $field['value'];
            }
        }

        $validated['slug'] = $slug;
        $validated['custom_fields'] = $formattedCustomFields;

        $data = CategoryData::fromArray($validated);

        try {
            if ($this->isEditing && $this->category) {
                $service->updateCategory($this->category, $data);
                $message = 'Categorie mise a jour avec succes.';
            } else {
                $service->createCategory($data);
                $message = 'Categorie creee avec succes.';
            }

            $this->dispatch('close-modal', name: 'category-form-modal');
            $this->dispatch('pg:eventRefresh-category-table');
            $this->dispatch('toast', message: $message, type: 'success');
            $this->resetValidation();
            $this->resetErrorBag();
        } catch (CategoryException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: 'Une erreur inattendue est survenue.', type: 'error');
        }
    }
}

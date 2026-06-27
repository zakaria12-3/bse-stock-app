<?php

namespace App\DTOs;

use Illuminate\Support\Str;

class CategoryData
{
    public function __construct(
        public readonly ?string $code,
        public readonly ?string $worksheet_name,
        public readonly string $name,
        public readonly ?string $slug,
        public readonly ?string $description,
        public readonly array $custom_fields = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            code: !empty($data['code']) ? $data['code'] : null,
            worksheet_name: !empty($data['worksheet_name']) ? $data['worksheet_name'] : null,
            name: $data['name'],
            slug: !empty($data['slug']) ? $data['slug'] : Str::slug($data['name']),
            description: empty($data['description']) ? null : $data['description'],
            custom_fields: $data['custom_fields'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'worksheet_name' => $this->worksheet_name,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'custom_fields' => $this->custom_fields,
        ];
    }
}

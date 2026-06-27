<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'code',
        'worksheet_name',
        'name',
        'slug',
        'description',
        'custom_fields',
    ];

    protected $casts = [
        'code' => 'string',
        'worksheet_name' => 'string',
        'name' => 'string',
        'slug' => 'string',
        'description' => 'string',
        'custom_fields' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            $slug = Str::slug($category->name);
            $original = $slug;
            $count = 1;

            while (self::where('slug', $slug)->exists()) {
                $slug = $original . '-' . $count++;
            }

            $category->slug = $slug;
        });

        static::updating(function ($category) {
            $slug = Str::slug($category->name);
            $original = $slug;
            $count = 1;

            while (
                self::where('slug', $slug)
                    ->where('id', '!=', $category->id)
                    ->exists()
            ) {
                $slug = $original . '-' . $count++;
            }

            $category->slug = $slug;
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

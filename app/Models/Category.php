<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Category extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'slug', 'description',
        'image',
        'is_active',
        'sort_order',
        'ai_generated',
        'ai_prompt',
        'image_generated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'ai_generated' => 'boolean',
        'image_generated_at' => 'datetime',
    ];

    // Mutateur pour générer automatiquement le slug
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // Relations
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('is_active', true)->where('stock_quantity', '>', 0);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    // Accesseurs
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/categories/' . $this->image) : asset('images/default-category.jpg');
    }//
}

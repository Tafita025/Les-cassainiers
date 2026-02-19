<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'brand',
        'description',
        'price',
        'image',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'integer',
        'stock'     => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Note moyenne arrondie (1-5)
     */
    public function getAverageRatingAttribute(): int
    {
        return (int) round($this->reviews()->where('is_approved', true)->avg('rating'));
    }

    /**
     * Nombre d'avis approuvés
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->where('is_approved', true)->count();
    }

    /**
     * Prix formaté en Ariary
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', ' ') . ' Ar';
    }
}
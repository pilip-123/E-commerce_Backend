<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image',
        'status',
        'expiry_date',
        'unit_cost',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'status' => 'boolean',
        'expiry_date' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class);
    }

    public function activePromotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class)
            ->wherePivot('promotion_id', '!=', null)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function getBestPromotion(): ?Promotion
    {
        $promotions = $this->relationLoaded('activePromotions')
            ? $this->activePromotions
            : $this->activePromotions()->get();

        return $promotions->sortByDesc(function (Promotion $promotion) {
            if ($promotion->discount_type === 'percentage') {
                return (float) $this->price * (float) $promotion->discount_value / 100;
            }
            return (float) $promotion->discount_value;
        })->first();
    }

    public function getDiscountPrice(): ?float
    {
        $promotion = $this->getBestPromotion();

        if (! $promotion) {
            return null;
        }

        if ($promotion->discount_type === 'percentage') {
            return round((float) $this->price - ((float) $this->price * (float) $promotion->discount_value / 100), 2);
        }

        return max(0, round((float) $this->price - (float) $promotion->discount_value, 2));
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}

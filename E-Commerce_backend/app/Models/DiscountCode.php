<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'max_uses',
        'used_count',
        'sent_to',
        'sent_count',
    ];

    protected function casts(): array
    {
        return [
            'sent_to' => 'array',
            'discount_value' => 'decimal:2',
            'max_uses' => 'integer',
            'used_count' => 'integer',
        ];
    }

    public function isValid(): bool
    {
        return $this->used_count < $this->max_uses;
    }

    public function markUsed(): void
    {
        $this->increment('used_count');
    }
}

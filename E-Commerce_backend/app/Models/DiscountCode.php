<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'sent_to',
        'sent_count',
    ];

    protected function casts(): array
    {
        return [
            'sent_to' => 'array',
            'discount_value' => 'decimal:2',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountCode extends Model
{
    use SoftDeletes;
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

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    public function isValid(): bool
    {
        return $this->used_count < $this->max_uses;
    }

    public function isValidForUser(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        return !$this->users()->where('user_id', $user->id)->exists();
    }

    public function markUsed(): void
    {
        $this->increment('used_count');
    }

    public function markUsedBy(User $user): void
    {
        $this->users()->attach($user->id);
        $this->increment('used_count');
    }
}

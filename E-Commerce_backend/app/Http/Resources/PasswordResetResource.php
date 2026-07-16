<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PasswordResetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'expires_at' => $this->expires_at,
            'is_expired' => $this->isExpired(),
            'created_at' => $this->created_at,
        ];
    }
}

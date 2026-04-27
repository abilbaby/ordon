<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'match_score' => $this->match_score ?? $this->score,
            'reason' => $this->reason,
            'donor' => [
                'id' => $this->donor_id,
                'name' => $this->donor?->user?->name,
            ],
            'recipient' => [
                'id' => $this->recipient_id,
                'name' => $this->recipient?->user?->name,
            ],
            'created_at' => $this->created_at,
        ];
    }
}

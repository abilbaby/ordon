<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->user?->name,
            'blood_group' => $this->blood_group,
            'organ_type' => $this->organ_type,
            'medical_status' => $this->medical_status,
            'is_available' => (bool) $this->is_available,
            'created_at' => $this->created_at,
        ];
    }
}

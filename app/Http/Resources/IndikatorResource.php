<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndikatorResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'id_standar' => $this->id_standar,
            'note' => $this->note,
            'targets' => TargetResource::collection($this->whenLoaded('target'))
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenetapanResource extends JsonResource {
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'id_sheet' => $this->id_sheet,
        ];
    }
}

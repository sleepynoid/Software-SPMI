<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StandarResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'id_penetapan' => $this->id_penetapan,
            'note' => $this->note,
            'tipe' => $this->tipe,
            'indikators' => IndikatorResource::collection($this->whenLoaded('indikator'))
        ];
    }
}

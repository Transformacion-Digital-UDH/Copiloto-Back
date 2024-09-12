<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CorrectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'documento_id' => $this->investigation_id,
            'revisor_id' => $this->adviser_id,
            'estado_id' => $this->state_id,
            'archivo' => $this->archive,
            'comentario' => $this->comment,
        ];
    }
}

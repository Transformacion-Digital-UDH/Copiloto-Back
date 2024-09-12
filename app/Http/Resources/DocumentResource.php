<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'estudiante_id' => $this->student_id,
            'documento' => $this->name,
            'tipo' => $this->tipe,
            'archivo' => $this->archive,
            'estado_id' => $this->state_id,
        ];
    }
}

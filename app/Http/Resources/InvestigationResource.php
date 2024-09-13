<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvestigationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'tipo_id' => $this->type_id,
            'archivo' => $this->archive,
            'estudiante_id' => $this->student_id,
            'jurado_id' => $this->jury_id,
        ];
    }
}

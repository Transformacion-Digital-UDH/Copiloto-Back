<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcedureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'proceso' => $this->name,
            'expediente' => $this->expediente,
            'estudiante_id' => $this->student_id,
            'estado_id' => $this->state_id,
            'secre_escuela_id' => $this->secre_school_id,
            'secre_pa_id' => $this->secre_pa_id,
        ];
    }
}

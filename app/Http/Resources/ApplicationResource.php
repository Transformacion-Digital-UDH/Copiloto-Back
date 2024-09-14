<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
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
            'asesor_id' => $this->adviser_id,
            'estado_id' => $this->state_id,
        ];
    }
}

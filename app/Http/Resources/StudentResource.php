<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nombre' => $this->name,
            'apellido_paterno' => $this->lastname_f,
            'apellido_materno' => $this->lastname_m,
            'dni' => $this->dni,
            'codigo' => $this->code,
            'titulo_tesis' => $this->investigation_title,
            'asesor_id' => $this->adviser_id,
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Adviser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocOfResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->_id,
            'nombre_de_oficio' => $this->of_name,
            'estado' => $this->of_status,
            'numero_de_oficio' => $this->of_num_of,
            'fecha_creado' => $this->created_at,
            'estudiante_nombre' => $this->solicitude->student->getFullName(),
            'asesor_nombre' => $this->solicitude->adviser ? $this->solicitude->adviser->getFullName() : null,
        ];
    }
}
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocResolutionResource extends JsonResource
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
            'nombre' => $this->docres_name,
            'fecha_creado' => $this->getCreatedFormattedAttribute(),
            'numero_resolucion' => $this->docres_num_res,
            'estado' => $this->docres_status
        ];
    }
}

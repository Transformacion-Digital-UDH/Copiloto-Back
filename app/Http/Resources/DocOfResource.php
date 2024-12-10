<?php

namespace App\Http\Resources;

use App\Models\Adviser;
use App\Models\DocResolution;
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
            'id' => $this->_id ?? '',
            'nombre_de_oficio' => 'Solicitud de resolución de designación de asesor' ?? '',
            'estado' => $this->of_status ?? '',
            'numero_de_oficio' => $this->of_num_of ?? '',
            'fecha_creado' => $this->updated_at ?? '',
            'estudiante_nombre' => $this->solicitude->student->getFullName() ?? '',
            'asesor_nombre' => $this->solicitude->adviser ? $this->solicitude->adviser->getFullName() : null ?? '',
            'resolucion_estado' => DocResolution::where('docof_id', $this->_id)->first()?->docres_status ?? '',
            'resolucion_id' => DocResolution::where('docof_id', $this->_id)->first()?->_id ?? '',
        ];
    }
}
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudeResource extends JsonResource
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
            'titulo' => $this->sol_title_inve,
            'asesor' => [
                'id' => $this->adviser->_id,
                'nombre_completo' => $this->adviser->adv_name . ' ' . $this->adviser->adv_lastname_m . ' ' . $this->adviser->adv_latsname_f,
            ],
            'estudiante' => [
                'id' => $this->student->_id,
                'nombre_completo' => $this->student->stu_name . ' ' . $this->student->stu_lastname_m . ' ' . $this->student->stu_latsname_f,
            ],
            'estado' => $this->sol_status,
            'link' => $this->docs_link
        ];
    }
}

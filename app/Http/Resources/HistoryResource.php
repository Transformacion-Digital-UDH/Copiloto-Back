<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'accion' => $this->action,
            'fecha' => $this->getCreatedFormattedAttribute(),
            'observacion' => $this->observation,
            'titulo' => $this->sol_title_inve,
            'asesor' => $this->adviser->getFullName(),
        ];
    }
}

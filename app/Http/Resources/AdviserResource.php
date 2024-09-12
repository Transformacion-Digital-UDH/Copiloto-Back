<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdviserResource extends JsonResource
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
            'orcid' => $this->orcid,
            'jurado_id' => $this->jury_id,
        ];
    }
}

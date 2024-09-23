<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {   
        $role = $this->role->name;
        $id = null;

        if($role == 'estudiante'){
            $id = $this->student->_id;
        }

        if($role == 'asesor'){
            $id = $this->adviser->_id;
        }

        return [
            'id' => $id,
            'nombre' => $this->name,
            'correo' => $this->email,
            'rol' => $this->role->name,
            'facultad' => $this->us_faculty,
            'programa' => $this->us_program,
        ];
    }
}

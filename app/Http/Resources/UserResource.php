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

        // Inicializar variables solo si es necesario según el rol
        $id = $names = $lastname_m = $lastname_f = $dni = $code = null;
        $is_jury = false;

        switch ($role) {
            case 'estudiante':
                $id = $this->student->_id;
                $names = $this->student->stu_name;
                $lastname_m = $this->student->stu_lastname_m;
                $lastname_f = $this->student->stu_lastname_f;
                $dni = $this->student->stu_dni;
                $code = $this->student->stu_code;
                break;

            case 'asesor':
                $id = $this->adviser->_id;
                $names = $this->adviser->adv_name;
                $lastname_m = $this->adviser->adv_lastname_m;
                $lastname_f = $this->adviser->adv_lastname_f;
                $is_jury = $this->adviser->adv_is_jury;
                break;

            case 'programa':
                $id = $this->program->_id;
                $names = $this->program->pa_name;
                $lastname_m = $this->program->pa_lastname_m;
                $lastname_f = $this->program->pa_lastname_f;
                break;

            case 'facultad':
                $id = $this->faculty->_id;
                $names = $this->faculty->fa_name;
                $lastname_m = $this->faculty->fa_lastname_m;
                $lastname_f = $this->faculty->fa_lastname_f;
                break;
        }

        return [
            'id' => $id,
            'nombre' => $this->name,
            'nombres' => $names,
            'apellido_paterno' => $lastname_m,
            'apellido_materno' => $lastname_f,
            'correo' => $this->email,
            'dni' => $dni,
            'codigo' => $code,
            'rol' => $role,
            'facultad' => $this->us_faculty,
            'programa' => $this->us_program,
            'es_jurado' => $is_jury,
        ];
    }
}

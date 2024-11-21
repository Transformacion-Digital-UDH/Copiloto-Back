<?php
namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir permisos por rol
        $permissions = [
            'student' => [
                'update-solicitude',
                'create-solicitude',
                'view-document'
            ],
            'adviser' => [
                'confirm-solicitude'
            ],
            'paisi' => [
                'create-link',
                'confirm-carte',
                'view-document'
            ],
            'faculty' => [
                'confirm-trade',
                'view-document'
            ],

            'vri' => [

            ],
            'turnitin' => [

            ]
        ];

        // Crear permisos por rol
        foreach ($permissions as $role => $rolePermissions) {
            foreach ($rolePermissions as $permissionName) {
                Permission::updateOrCreate([
                    'name' => $permissionName,
                ], [
                    'description' => 'Permission to ' . $permissionName
                ]);
            }
        }

        // Crear roles y asociar permisos
        $roles = [
            'estudiante' => $permissions['student'],
            'asesor' => $permissions['adviser'],
            'paisi' => $permissions['paisi'],
            'facultad' => $permissions['faculty'],
            'vri' => $permissions['vri'],
            'turnitin' => $permissions['turnitin'],
        ];

        // crear roles con sus respectivos permisos
        foreach ($roles as $roleName => $permissionsList) {
            Role::create([
                'name' => $roleName,
                'permission_ids' => Permission::whereIn('name', $permissionsList)->pluck('_id')->toArray()
            ]);
        }

        // Crear usuarios
        $users = [
            // con rol estudiante
            [
                'name' => 'Estudiante',
                'email' => 'estudiante@udh.edu.pe',
                'role' => 'estudiante',
                'role_data' => [
                    'stu_name' => 'estudiante',
                    'stu_lastname_m' => 'udeachino',
                    'stu_lastname_f' => 'forever',
                    'stu_dni' => '27137222',
                    'stu_code' => '2020203012',
                    'stu_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'stu_faculty' => 'INGENIERÍA',
                ]
            ],
            [
                'name' => 'KEVIN',
                'email' => '2018110451@udh.edu.pe',
                'role' => 'estudiante',
                'role_data' => [
                    'stu_name' => 'KEVIN ARNOLD',
                    'stu_lastname_m' => 'FLORES',
                    'stu_lastname_f' => 'PACHECO',
                    'stu_dni' => '76370345',
                    'stu_code' => '2018110451',
                    'stu_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'stu_faculty' => 'INGENIERÍA',
                ]
            ],

            [
                'name' => 'JEAN',
                'email' => '2018210149@udh.edu.pe',
                'role' => 'estudiante',
                'role_data' => [
                    'stu_name' => 'JEAN RICHARD',
                    'stu_lastname_m' => 'LINO',
                    'stu_lastname_f' => 'BECERRA',
                    'stu_dni' => '77417459',
                    'stu_code' => '2018210149',
                    'stu_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'stu_faculty' => 'INGENIERÍA',
                ]
            ],

            [
                'name' => 'RENZO',
                'email' => '2018110461@udh.edu.pe',
                'role' => 'estudiante',
                'role_data' => [
                    'stu_name' => 'RENZO PAOLO',
                    'stu_lastname_m' => 'LUCIANO',
                    'stu_lastname_f' => 'ESTELA',
                    'stu_dni' => '72269360',
                    'stu_code' => '2018110461',
                    'stu_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'stu_faculty' => 'INGENIERÍA',
                ]
            ],
            [
                'name' => 'JOEL',
                'email' => '2018110397@udh.edu.pe',
                'role' => 'estudiante',
                'role_data' => [
                    'stu_name' => 'JOEL JOSUE',
                    'stu_lastname_m' => 'INQUIEL',
                    'stu_lastname_f' => 'CALDERON',
                    'stu_dni' => '70220442',
                    'stu_code' => '2018110397',
                    'stu_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'stu_faculty' => 'INGENIERÍA',
                ]
            ],
            [
                'name' => 'RENZO',
                'email' => '2020160035@udh.edu.pe',
                'role' => 'estudiante',
                'role_data' => [
                    'stu_name' => 'RENZO ANDRE',
                    'stu_lastname_m' => 'PANDURO',
                    'stu_lastname_f' => 'MOSCOSO',
                    'stu_dni' => '72938469',
                    'stu_code' => '2020160035',
                    'stu_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'stu_faculty' => 'INGENIERÍA',
                ]
            ],
            [
                'name' => 'MARYCIELO',
                'email' => '2020210311@udh.edu.pe',
                'role' => 'estudiante',
                'role_data' => [
                    'stu_name' => 'MARYCIELO OLENKA',
                    'stu_lastname_m' => 'MARTEL',
                    'stu_lastname_f' => 'NOEL',
                    'stu_dni' => '77327670',
                    'stu_code' => '2020210311',
                    'stu_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'stu_faculty' => 'INGENIERÍA',
                ]
            ],
            [
                'name' => 'JHONATAN',
                'email' => '0200811311@udh.edu.pe',
                'role' => 'estudiante',
                'role_data' => [
                    'stu_name' => 'JHONATAN',
                    'stu_lastname_m' => 'TRUJILLO',
                    'stu_lastname_f' => 'ROSALES',
                    'stu_dni' => '46695251',
                    'stu_code' => '0200811311',
                    'stu_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'stu_faculty' => 'INGENIERÍA',
                ]
            ],
            // con rol asesor
            [
                'name' => 'Asesor',
                'email' => 'asesor@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_rank' => 'ING.',
                    'adv_name' => 'asesor',
                    'adv_lastname_m' => 'fernandez',
                    'adv_lastname_f' => 'panduro',
                    'adv_dni' => '98947268',
                    'adv_orcid' => '1231493212314907',
                    'adv_is_jury' => false,
                    'adv_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'adv_faculty' => 'INGENIERÍA',
                ]
            ],
            [
                'name' => 'FREDDY',
                'email' => 'freddy.huapaya@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_rank' => 'DR.',
                    'adv_name' => 'FREDDY RONALD',
                    'adv_lastname_m' => 'HUAPAYA',
                    'adv_lastname_f' => 'CONDORI',
                    'adv_dni' => '43601248',
                    'adv_orcid' => '0000-0003-4783-3803',
                    'adv_is_jury' => true,
                    'adv_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'adv_faculty' => 'INGENIERÍA',
                ]
            ],
            [
                'name' => 'ALBERTO',
                'email' => 'alberto.jara@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_rank' => 'MG.',
                    'adv_name' => 'ALBERTO CARLOS',
                    'adv_lastname_m' => 'JARA',
                    'adv_lastname_f' => 'TRUJILLO',
                    'adv_dni' => '30843198',
                    'adv_orcid' => '0000-0001-8392-1769',
                    'adv_is_jury' => true,
                    'adv_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'adv_faculty' => 'INGENIERÍA',
                ]
            ],
            [
                'name' => 'ALDO',
                'email' => 'aldo.ramirez@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_rank' => 'MG.',
                    'adv_name' => 'ALDO ENRIQUE',
                    'adv_lastname_m' => 'RAMIREZ',
                    'adv_lastname_f' => 'CHAUPIS',
                    'adv_dni' => '45060478',
                    'adv_orcid' => '0009-0006-6249-516X',
                    'adv_is_jury' => false,
                    'adv_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'adv_faculty' => 'INGENIERÍA',
                ]
            ],
            [
                'name' => 'BERTHA',
                'email' => 'bertha.campos@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_rank' => 'MG.',
                    'adv_name' => 'BERTHA LUCILA',
                    'adv_lastname_m' => 'CAMPOS',
                    'adv_lastname_f' => 'RIOS',
                    'adv_dni' => '35938388',
                    'adv_orcid' => '0000-0002-5662-554X',
                    'adv_is_jury' => true,
                    'adv_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'adv_faculty' => 'INGENIERÍA',
                ]
            ],
            [
                'name' => 'CARLOS',
                'email' => 'carlos.suarez@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_rank' => 'MG.',
                    'adv_name' => 'CARLOS ENRIQUE',
                    'adv_lastname_m' => 'SUAREZ',
                    'adv_lastname_f' => 'PAUCAR',
                    'adv_dni' => '52865440',
                    'adv_orcid' => '0000-0001-5123-2088',
                    'adv_is_jury' => true,
                    'adv_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'adv_faculty' => 'INGENIERÍA',
                ]
            ],
            [
                'name' => 'EDGARDO',
                'email' => 'edgardo.lopez@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_rank' => 'MG.',
                    'adv_name' => 'EDGARDO CRISTIAM IVAN',
                    'adv_lastname_m' => 'LOPEZ',
                    'adv_lastname_f' => 'DE LA CRUZ',
                    'adv_dni' => '37774996',
                    'adv_orcid' => '0000-0001-9815-7708',
                    'adv_is_jury' => true,
                    'adv_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'adv_faculty' => 'INGENIERÍA',
                ]
            ],
            [
                'name' => 'JUAN',
                'email' => 'juan.huapalla@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_rank' => 'MG',
                    'adv_name' => 'JUAN MANUEL',
                    'adv_lastname_m' => 'HUAPALLA',
                    'adv_lastname_f' => 'GARCIA',
                    'adv_dni' => '15941099',
                    'adv_orcid' => '0000-0002-2823-3768',
                    'adv_is_jury' => true,
                    'adv_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'adv_faculty' => 'INGENIERÍA',
                ]
            ],
            [
                'name' => 'WALTER',
                'email' => 'walter.baldeon@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_rank' => 'MG.',
                    'adv_name' => 'WALTER TEOFILO',
                    'adv_lastname_m' => 'BALDEON',
                    'adv_lastname_f' => 'CANCHAYA',
                    'adv_dni' => '85071341',
                    'adv_orcid' => '0000-0002-4270-073X',
                    'adv_is_jury' => true,
                    'adv_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'adv_faculty' => 'INGENIERÍA',
                ]
            ],
         
            // con rol programas academicos
            [
                'name' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                'email' => 'paisi@udh.edu.pe',
                'role' => 'paisi',
                'role_data' => [
                    'pa_rank' => 'ING.',
                    'pa_name' => 'PAOLO',
                    'pa_lastname_m' => 'SOLIS',
                    'pa_lastname_f' => 'JARA',
                    'pa_program' => 'INGENIERÍA DE SISTEMAS E INFORMÁTICA',
                    'pa_faculty' => 'INGENIERÍA',
                ]
            ],

            [
                'name' => 'INGENIERÍA CIVIL',
                'email' => 'programa.civil@udh.edu.pe',
                'role' => 'paisi',
                'role_data' => [
                    'pa_rank' => 'ING.',
                    'pa_name' => 'ERIKA',
                    'pa_lastname_m' => 'ARRATEA',
                    'pa_lastname_f' => 'SALVADOR',
                    'pa_program' => 'INGENIERÍA CIVIL',
                    'pa_faculty' => 'INGENIERÍA',
                ]
            ],

            [
                'name' => 'ARQUITECTURA',
                'email' => 'programa.arquitectura@udh.edu.pe',
                'role' => 'paisi',
                'role_data' => [
                    'pa_rank' => 'ARQ.',
                    'pa_name' => 'ALBERTO',
                    'pa_lastname_m' => 'JARA',
                    'pa_lastname_f' => 'TRUJILLO',
                    'pa_program' => 'ARQUITECTURA',
                    'pa_faculty' => 'INGENIERÍA',
                ]
            ],

            // con rol facultad
            [
                'name' => 'INGENIERÍA',
                'email' => 'facultad@udh.edu.pe',
                'role' => 'facultad',
                'role_data' => [
                    'fa_rank' => 'MG.',
                    'fa_name' => 'BERTHA LUCILA',
                    'fa_lastname_m' => 'CAMPOS',
                    'fa_lastname_f' => 'RIOS',
                    'fa_faculty' => 'INGENIERÍA',
                ]
            ],

            [
                'name' => 'CIENCIAS DE LA SALUD',
                'email' => 'facultad.salud@udh.edu.pe',
                'role' => 'facultad',
                'role_data' => [
                    'fa_rank' => 'DRA.',
                    'fa_name' => 'JULIA',
                    'fa_lastname_m' => 'PALACIOS',
                    'fa_lastname_f' => 'SANCHEZ',
                    'fa_faculty' => 'CIENCIAS DE LA SALUD',
                ]
            ],

            // con rol vri
            [
                'name' => 'VICERRECTORADO DE INVESTIGACIÓN',
                'email' => 'vri@udh.edu.pe',
                'role' => 'vri',
                'role_data' => []
            ],

            // con rol turnitin
            [
                'name' => 'TURNITIN',
                'email' => 'turnitin@udh.edu.pe',
                'role' => 'turnitin',
                'role_data' => []
            ]
            
        ];

        // Crear todos los usuarios en un solo batch insert
        foreach ($users as $user) {
            $userData = [
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => bcrypt(123456),
                'role_id' => Role::where('name', $user['role'])->value('_id'),
            ];

            // Crear usuario
            $createdUser = User::create($userData);

            // Insertar datos adicionales en la tabla correspondiente al rol
            switch ($user['role']) {
                case 'estudiante':
                    \DB::table('students')->insert([
                        'user_id' => $createdUser->_id,
                        'stu_name' => $user['role_data']['stu_name'],
                        'stu_lastname_m' => $user['role_data']['stu_lastname_m'],
                        'stu_lastname_f' => $user['role_data']['stu_lastname_f'],
                        'stu_dni' => $user['role_data']['stu_dni'],
                        'stu_code' => $user['role_data']['stu_code'],
                        'stu_program' => $user['role_data']['stu_program'],
                        'stu_faculty' => $user['role_data']['stu_faculty'],
                        // Otros campos específicos de la tabla `students`
                    ]);
                    break;
                case 'asesor':
                    \DB::table('advisers')->insert([
                        'user_id' => $createdUser->_id,
                        'adv_rank' => $user['role_data']['adv_rank'],
                        'adv_name' => $user['role_data']['adv_name'],
                        'adv_lastname_m' => $user['role_data']['adv_lastname_m'],
                        'adv_lastname_f' => $user['role_data']['adv_lastname_f'],
                        'adv_dni' => $user['role_data']['adv_dni'],
                        'adv_orcid' => $user['role_data']['adv_orcid'],
                        'adv_is_jury' => $user['role_data']['adv_is_jury'],
                        'adv_program' => $user['role_data']['adv_program'],
                        'adv_faculty' => $user['role_data']['adv_faculty'],
                        // Otros campos específicos de la tabla `asesors`
                    ]);
                    break;
                case 'paisi':
                    \DB::table('paisis')->insert([
                        'user_id' => $createdUser->_id,
                        'pa_rank' => $user['role_data']['pa_rank'],
                        'pa_name' => $user['role_data']['pa_name'],
                        'pa_lastname_m' => $user['role_data']['pa_lastname_m'],
                        'pa_lastname_f' => $user['role_data']['pa_lastname_f'],
                        'pa_program' => $user['role_data']['pa_program'],
                        'pa_faculty' => $user['role_data']['pa_faculty'],
                        // Otros campos específicos de la tabla `pasis`
                    ]);
                    break;

                case 'facultad':
                    \DB::table('faculties')->insert([
                        'user_id' => $createdUser->_id,
                        'fa_rank' => $user['role_data']['fa_rank'],
                        'fa_name' => $user['role_data']['fa_name'],
                        'fa_lastname_m' => $user['role_data']['fa_lastname_m'],
                        'fa_lastname_f' => $user['role_data']['fa_lastname_f'],
                        'fa_faculty' => $user['role_data']['fa_faculty'],
                    ]);
                    break;

                case 'vri':
                    \DB::table('vri')->insert([
                        'user_id' => $createdUser->_id,
                    ]);
                    break;

                case 'turnitin':
                    \DB::table('turnitin')->insert([
                        'user_id' => $createdUser->_id,
                    ]);

                    break;
            }
        }
    }
}
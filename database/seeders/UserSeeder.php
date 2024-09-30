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
            'jury' => [
                'confirm-correction',
                'upload-correction',
                'make-correction'
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
            'jurado' => $permissions['jury'],
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
                    'stu_latsname_f' => 'forever',
                    'stu_dni' => '27137222',
                    'stu_code' => '2020203012',
                ]
            ],
            [
                'name' => 'KEVIN',
                'email' => '2018110451@udh.edu.pe',
                'role' => 'estudiante',
                'role_data' => [
                    'stu_name' => 'KEVIN ARNOLD',
                    'stu_lastname_m' => 'FLORES',
                    'stu_latsname_f' => 'PACHECO',
                    'stu_dni' => '76370345',
                    'stu_code' => '2018110451',
                ]
            ],
            [
                'name' => 'RENZO',
                'email' => '2018110461@udh.edu.pe',
                'role' => 'estudiante',
                'role_data' => [
                    'stu_name' => 'RENZO PAOLO',
                    'stu_lastname_m' => 'LUCIANO',
                    'stu_latsname_f' => 'ESTELA',
                    'stu_dni' => '72269360',
                    'stu_code' => '2018110461',
                ]
            ],
            [
                'name' => 'JOEL',
                'email' => '2018110397@udh.edu.pe',
                'role' => 'estudiante',
                'role_data' => [
                    'stu_name' => 'JOEL JOSUE',
                    'stu_lastname_m' => 'INQUIEL',
                    'stu_latsname_f' => 'CALDERON',
                    'stu_dni' => '70220442',
                    'stu_code' => '2018110397',
                ]
            ],
            [
                'name' => 'RENZO',
                'email' => '2020160035@udh.edu.pe',
                'role' => 'estudiante',
                'role_data' => [
                    'stu_name' => 'RENZO ANDRE',
                    'stu_lastname_m' => 'PANDURO',
                    'stu_latsname_f' => 'MOSCOSO',
                    'stu_dni' => '72938469',
                    'stu_code' => '2020160035',
                ]
            ],
            [
                'name' => 'MARYCIELO',
                'email' => '2020210311@udh.edu.pe',
                'role' => 'estudiante',
                'role_data' => [
                    'stu_name' => 'MARYCIELO OLENKA',
                    'stu_lastname_m' => 'MARTEL',
                    'stu_latsname_f' => 'NOEL',
                    'stu_dni' => '77327670',
                    'stu_code' => '2020210311',
                ]
            ],
            [
                'name' => 'JHONATAN',
                'email' => '0200811311@udh.edu.pe',
                'role' => 'estudiante',
                'role_data' => [
                    'stu_name' => 'JHONATAN',
                    'stu_lastname_m' => 'TRUJILLO',
                    'stu_latsname_f' => 'ROSALES',
                    'stu_dni' => '46695251',
                    'stu_code' => '0200811311',
                ]
            ],
            // con rol asesor
            [
                'name' => 'Asesor',
                'email' => 'asesor@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_name' => 'asesor',
                    'adv_lastname_m' => 'fernandez',
                    'adv_latsname_f' => 'panduro',
                    'adv_orcid' => '1231493212314907',
                    'adv_is_jury' => true,
                ]
            ],
            [
                'name' => 'DR. FREDDY',
                'email' => '0000002496@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_name' => 'FREDDY RONALD',
                    'adv_lastname_m' => 'HUAPAYA',
                    'adv_latsname_f' => 'CONDORI',
                    'adv_orcid' => '000000249622314907',
                    'adv_is_jury' => false,
                ]
            ],
            [
                'name' => 'MG. ALBERTO',
                'email' => '0000002098@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_name' => 'ALBERTO CARLOS',
                    'adv_lastname_m' => 'JARA',
                    'adv_latsname_f' => 'TRUJILLO',
                    'adv_orcid' => '0000002098',
                    'adv_is_jury' => false,
                ]
            ],
            [
                'name' => 'MG. ALDO',
                'email' => '0000003685@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_name' => 'ALDO ENRIQUE',
                    'adv_lastname_m' => 'RAMIREZ',
                    'adv_latsname_f' => 'CHAUPIS',
                    'adv_orcid' => '0000003685',
                    'adv_is_jury' => false,
                ]
            ],
            [
                'name' => 'MG. BERTHA',
                'email' => '0000000043@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_name' => 'BERTHA LUCILA',
                    'adv_lastname_m' => 'CAMPOS',
                    'adv_latsname_f' => 'RIOS',
                    'adv_orcid' => '0000000043',
                    'adv_is_jury' => false,
                ]
            ],
            [
                'name' => 'MG. CARLOS',
                'email' => '0000001247@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_name' => 'CARLOS ENRIQUE',
                    'adv_lastname_m' => 'SUAREZ',
                    'adv_latsname_f' => 'PAUCAR',
                    'adv_orcid' => '0000001247',
                    'adv_is_jury' => false,
                ]
            ],
            [
                'name' => 'MG. EDGARDO',
                'email' => '0000001378@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_name' => 'EDGARDO CRISTIAM IVAN',
                    'adv_lastname_m' => 'LOPEZ',
                    'adv_latsname_f' => 'DE LA CRUZ',
                    'adv_orcid' => '0000001378',
                    'adv_is_jury' => false,
                ]
            ],
            [
                'name' => 'MG. JUAN',
                'email' => '0000003284@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_name' => 'JUAN MANUEL',
                    'adv_lastname_m' => 'HUAPALLA',
                    'adv_latsname_f' => 'GARCIA',
                    'adv_orcid' => '0000003284',
                    'adv_is_jury' => false,
                ]
            ],
            [
                'name' => 'MG. WALTER',
                'email' => '0000001756@udh.edu.pe',
                'role' => 'asesor',
                'role_data' => [
                    'adv_name' => 'WALTER TEOFILO',
                    'adv_lastname_m' => 'BALDEON',
                    'adv_latsname_f' => 'CANCHAYA',
                    'adv_orcid' => '0000001756',
                    'adv_is_jury' => false,
                ]
            ],
            // con rol jurado
            [
                'name' => 'Jurado',
                'email' => 'jurado@udh.edu.pe',
                'role' => 'jurado',
                'role_data' => [
                    'adv_name' => 'jurado',
                    'adv_lastname_m' => 'marcos',
                    'adv_latsname_f' => 'antonio',
                    'adv_orcid' => '1231493212314907',
                    'adv_is_jury' => true,
                ]
            ],
            // con rol paisi
            [
                'name' => 'PAISI',
                'email' => 'paisi@udh.edu.pe',
                'role' => 'paisi',
                'role_data' => []
            ],
            // con rol facultad
            [
                'name' => 'FACULTAD',
                'email' => 'facultad@udh.edu.pe',
                'role' => 'facultad',
                'role_data' => []
            ]
        ];

        // Crear todos los usuarios en un solo batch insert
        foreach ($users as $user) {
            $userData = [
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => bcrypt(123456),
                'us_faculty' => 'ingeniería',
                'us_program' => 'ingeniería de sistemas e informática',
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
                        'stu_latsname_f' => $user['role_data']['stu_latsname_f'],
                        'stu_dni' => $user['role_data']['stu_dni'],
                        'stu_code' => $user['role_data']['stu_code'],
                        // Otros campos específicos de la tabla `students`
                    ]);
                    break;
                case 'asesor':
                    \DB::table('advisers')->insert([
                        'user_id' => $createdUser->_id,
                        'adv_name' => $user['role_data']['adv_name'],
                        'adv_lastname_m' => $user['role_data']['adv_lastname_m'],
                        'adv_latsname_f' => $user['role_data']['adv_latsname_f'],
                        'adv_orcid' => $user['role_data']['adv_orcid'],
                        'adv_is_jury' => $user['role_data']['adv_is_jury'],
                        // Otros campos específicos de la tabla `asesors`
                    ]);
                    break;
                case 'paisi':
                    \DB::table('paisis')->insert([
                        'user_id' => $createdUser->_id,
                    ]);
                    break;
                case 'facultad':
                    \DB::table('faculties')->insert([
                        'user_id' => $createdUser->_id,
                    ]);
                    break;
            }
        }
    }
}
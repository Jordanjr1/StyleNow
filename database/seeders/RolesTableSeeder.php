<?php
// database/seeders/RolesTableSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['rol_nombre' => 'Administrador', 'rol_descripcion' => 'Acceso total al sistema'],
            ['rol_nombre' => 'Empleado', 'rol_descripcion' => 'Acceso a módulos de empleado'],
            ['rol_nombre' => 'Cliente', 'rol_descripcion' => 'Acceso a módulos de cliente'],
        ];

        foreach ($roles as $rol) {
            Rol::create($rol);
        }
    }
}
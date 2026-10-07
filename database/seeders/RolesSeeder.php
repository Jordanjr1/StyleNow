<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('Tbl_Rol')->insert([
            [
                'rol_nombre' => 'Administrador',
                'rol_descripcion' => 'Acceso total al sistema',
                'rol_estado' => 'A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rol_nombre' => 'Empleado',
                'rol_descripcion' => 'Acceso a módulos de empleado',
                'rol_estado' => 'A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rol_nombre' => 'Cliente',
                'rol_descripcion' => 'Acceso a módulos de cliente',
                'rol_estado' => 'A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
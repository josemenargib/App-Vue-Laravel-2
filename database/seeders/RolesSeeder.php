<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $postulante = Role::create(['name' => 'Postulante', 'guard_name' => 'sanctum', 'modificacion' => false]);
        $estudiante = Role::create(['name' => 'Estudiante', 'guard_name' => 'sanctum', 'modificacion' => false]);
        $egresado = Role::create(['name' => 'Egresado', 'guard_name' => 'sanctum', 'modificacion' => false]);
    }
}

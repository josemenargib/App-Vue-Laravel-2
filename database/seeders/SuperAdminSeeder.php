<?php

namespace Database\Seeders;

use App\Models\Crm_datos_generales;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = User::create([
            'email' => 'superadmin@mail.com',
            'password' => bcrypt('hamiloRoot')
        ]);
        $datosGeneralesSuperAdmin = Crm_datos_generales::create([
            'user_id' => $superAdmin->id,
            'nombre' => 'Super administrador',
            'fecha_nacimiento' => '2000-01-01'
        ]);
        $role = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->assignRole($role);
    }
}

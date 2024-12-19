<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Crm_modulos;
use App\Models\Crm_tecnologias;
use App\Models\Web_empresas;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([web_empresaSeeder::class]);
        $this->call([RolesAndPermissionsSeeder::class]);
        $this->call([SuperAdminSeeder::class]);
        // $this->call([Crm_modulosSeeder::class]);
        // $this->call([Crm_tecnologiasSeeder::class]);
        $this->call([PaginaSeeder::class]);
        $this->call([SeccionSeeder::class]);
        $this->call([RolesSeeder::class]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaginaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('web_paginas')->insert([
            'pagina' => 'home',
            'is_deleted' => false,
        ]);
        DB::table('web_paginas')->insert([
            'pagina' => 'nosotros',
            'is_deleted' => false,
        ]);
        DB::table('web_paginas')->insert([
            'pagina' => 'carreras',
            'is_deleted' => false,
        ]);
        DB::table('web_paginas')->insert([
            'pagina' => 'eventos',
            'is_deleted' => false,
        ]);
        DB::table('web_paginas')->insert([
            'pagina' => 'blog',
            'is_deleted' => false,
        ]);
        DB::table('web_paginas')->insert([
            'pagina' => 'contacto',
            'is_deleted' => false,
        ]);
        DB::table('web_paginas')->insert([
            'pagina' => 'login',
            'is_deleted' => false,
        ]);
        DB::table('web_paginas')->insert([
            'pagina' => 'registro',
            'is_deleted' => false,
        ]);
    }
}

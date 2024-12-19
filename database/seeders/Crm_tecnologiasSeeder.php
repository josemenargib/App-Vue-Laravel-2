<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Crm_tecnologiasSeeder extends Seeder
{
    // Run the database seeds.
    public function run(): void
    {
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'HTML 5',
            'descripcion' => 'Lenguaje de marcado para crear páginas web.',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'CSS 3',
            'descripcion' => 'Lenguaje de programación para estilizar y construir páginas web.',
            'imagen' => null,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'JavaScript',
            'descripcion' => 'Lenguaje de programación para crear páginas web interactivas.',
            'imagen' => null,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'SQL',
            'descripcion' => 'Lenguaje de consulta estructurado para manejar bases de datos relacionales.',
            'imagen' => null,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'MySQL',
            'descripcion' => 'Base de datos relacional de SQL.',
            'imagen' => null,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'Git',
            'descripcion' => 'Sistema de control de versiones para colaboración en proyectos.',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'GitHub',
            'descripcion' => 'Plataforma de código abierto para el desarrollo de software colaborativo. Permite compartir y colaborar en proyectos de software. Ofrece herramientas para gestionar proyectos, documentar código, y mantener registros de cambios.',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'Visual Studio Code',
            'descripcion' => 'Editor de código fuente ligero pero eficaz que se ejecuta en el escritorio y está disponible para Windows, macOS y Linux.',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'Python',
            'descripcion' => 'Lenguaje de programación interpretado',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'MongoDB',
            'descripcion' => 'Base de datos NoSQL para almacenar y procesar grandes volúmenes de datos.',
            'imagen' => null,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'Pandas',
            'descripcion' => 'Librería de Python para manipulación y análisis de datos.',
            'imagen' => null,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'Scikit-learn',
            'descripcion' => 'Librería de Python para machine learning y modelado estadístico.',
            'imagen' => null,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'PowerBI',
            'descripcion' => 'Herramienta de visualización de datos y business intelligence.',
            'imagen' => null,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'TensorFlow',
            'descripcion' => 'Librería de código abierto para machine learning y deep learning.',
            'imagen' => null,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'Keras',
            'descripcion' => 'API de redes neuronales de alto nivel, capaz de ejecutarse sobre TensorFlow.',
            'imagen' => null,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'Flask',
            'descripcion' => 'Framework ligero de Python para crear aplicaciones web y APIs.',
            'imagen' => null,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'Laravel',
            'descripcion' => 'Framework PHP para crear aplicaciones web y APIs.',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'PHP',
            'descripcion' => 'PHP es un lenguaje de código abierto para el desarrollo web que se puede incrustar en HTML.',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'Angular',
            'descripcion' => 'Angular (comúnmente llamado Angular 2+ o Angular 2) es un framework para aplicaciones web desarrollado en TypeScript, de código abierto, mantenido por Google, que se utiliza para crear y mantener aplicaciones web de una sola página. Su objetivo es aumentar las aplicaciones basadas en navegador con capacidad de Modelo Vista Controlador (MVC), en un esfuerzo para hacer que el desarrollo y las pruebas sean más fáciles.',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'Vue 3',
            'descripcion' => 'Vue (pronunciado /vjuː/, como view) es un framework progresivo para construir interfaces de usuario.',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_tecnologias')->insert([
            'nombre' => 'React',
            'descripcion' => 'Framework frontend.',
            'imagen' => null,
            'is_deleted' => false,
        ]);
    }
}

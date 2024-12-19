<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Crm_modulosSeeder extends Seeder
{
    // Run the database seeds.
    public function run(): void
    {
        DB::table('crm_modulos')->insert([
            'nombre' => 'M1: Bases del desarrollo web',
            'objetivo' => 'Aprender los fundamentos de la programación web.',
            'entregable' => 'Aprender los fundamentos de la programación web.',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_modulos')->insert([
            'nombre' => 'M2: Single Page Applications con Vue.js',
            'objetivo' => 'Crear aplicaciones web con Vue.js.',
            'entregable' => 'Crear aplicaciones web con Vue.js.',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_modulos')->insert([
            'nombre' => 'M3: APIs REST con Laravel',
            'objetivo' => 'Crear APIs REST con Laravel.',
            'entregable' => 'Crear APIs REST con Laravel.',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_modulos')->insert([
            'nombre' => 'M0: Introducción a Data Science',
            'objetivo' => 'Familiarizarse con la terminología y los conceptos de Data Science.',
            'entregable' => 'Comprender data architectures, AI, ML, Deep Learning, y Data Strategy.',
            'imagen' => null,
        ]);
        DB::table('crm_modulos')->insert([
            'nombre' => 'M1: Herramientas para Data Science',
            'objetivo' => 'Adquirir soltura en el manejo de las herramientas principales de un Data Scientist.',
            'entregable' => 'Dominio de Python, SQL, Git, y MongoDB.',
            'imagen' => null,
        ]);
        DB::table('crm_modulos')->insert([
            'nombre' => 'M2: Machine Learning',
            'objetivo' => 'Aprender técnicas de aprendizaje supervisado y no supervisado.',
            'entregable' => 'Implementación de modelos de regresión, clasificación, clustering y series temporales.',
            'imagen' => null,
        ]);
        DB::table('crm_modulos')->insert([
            'nombre' => 'M3: Data Analytics & Business Intelligence',
            'objetivo' => 'Profundizar en Data Analytics y Data Visualization.',
            'entregable' => 'Creación de dashboards y análisis de datasets con Python y PowerBI.',
            'imagen' => null,
        ]);
        DB::table('crm_modulos')->insert([
            'nombre' => 'M4: Deep Learning',
            'objetivo' => 'Construir aplicaciones de Deep Learning en Computer Vision, NLP y Reinforcement Learning.',
            'entregable' => 'Implementación de redes neuronales y agentes de IA.',
            'imagen' => null,
        ]);
        DB::table('crm_modulos')->insert([
            'nombre' => 'M5: Machine Learning Operations (MLOps)',
            'objetivo' => 'Aprender sobre la gestión y operación de modelos a escala.',
            'entregable' => 'Implementación de Model Serving y ML como servicio.',
            'imagen' => null,
        ]);
        DB::table('crm_modulos')->insert([
            'nombre' => 'M0. Full Stack- Nivelación',
            'objetivo' => 'Dos semanas previas al inicio del curso podrás ahondar en los conceptos clave de programación (enfocado a frontend y a back-end) junto a un experto',
            'entregable' => 'Entorno de trabajo listo para trabajar, conceptos básicos estandarizados.',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_modulos')->insert([
            'nombre' => 'M1. Fundamentos de programación',
            'objetivo' => 'En este módulo podrás profundizar en los fundamentos de programación a través de la práctica para más adelante lograr desarrollar una aplicación.',
            'entregable' => 'JS avanzado - Trabajo con Arrays y objetos - Manipulando el DOM - Programación funcional vs. OOP React - ¿Qué es un componente? - Especialización vs. composición Protocolo HTTP y TCP/IP - ¿Qué es un API Rest? NodeJS y ExpressJS - Endpoints y rutas - Middlewares y validaciones Bases de datos (NOSQL) - MongoDB y Mongoose',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_modulos')->insert([
            'nombre' => 'M2. Desarrollo de una aplicación (parte 1)',
            'objetivo' => 'A lo largo de este módulo pondrás en práctica lo aprendido, empezarás a desarrollar una aplicación paso a paso con ayuda de los mejores expertos.',
            'entregable' => 'Gestión de proyectos: SCRUMt - Teams Backlog Refinement React avanzadot - Contexts con useContext Hook & useReducert - UseMemo, useCallback, useRef Autenticación (WebTokens)t - Web Security Testingt - Front-end, back-end, end to end testing Dockert - Gestión de imágenes y ficheros Cloudinaryt - Storybook y librerías de componentes Microservicios Introducción a CI/CDt - Despliegue en Cloud Services',
            'imagen' => null,
            'is_deleted' => false,
        ]);
        DB::table('crm_modulos')->insert([
            'nombre' => 'M3. Desarrollo de una aplicación (parte 2)',
            'objetivo' => 'En el último módulo te enfrentarás a retos de diseño y arquitectura de software y tendrás la oportunidad de presentar tu proyecto.',
            'entregable' => '- Deployment en Cloud Servicest - Typescriptt - React Nativet - SQL Databasest - Websocketst - SSR con NextJSt - Hackathont - Presentaciones finales.',
            'imagen' => null,
            'is_deleted' => false,
        ]);
    }
}

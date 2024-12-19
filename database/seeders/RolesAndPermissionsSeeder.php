<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionGroups = [
            "Administracion" => [
                //usuario
                'usuario crear',
                'usuario ver',
                'usuario editar',
                'usuario borrar',
                //rol
                'rol crear',
                'rol ver',
                'rol editar',
                'rol borrar',
                //empresa
                'empresa ver',
                //redes sociales
                'red social crear',
                'red social ver',
                'red social editar',
                'red social borrar',
                //reconocimientos
                'reconocimiento crear',
                'reconocimiento ver',
                'reconocimiento editar',
                'reconocimiento borrar',
                //dashboard
                'dashboard ver'
            ],
            "Gestion de recursos" => [
                //especialidades
                'especialidad crear',
                'especialidad ver',
                'especialidad editar',
                'especialidad borrar',
                //batchs
                'batch crear',
                'batch ver',
                'batch editar',
                'batch borrar',
                //modulos
                'modulo crear',
                'modulo ver',
                'modulo editar',
                'modulo borrar',
                //tecnologias
                'tecnologia crear',
                'tecnologia ver',
                'tecnologia editar',
                'tecnologia borrar',
                //tipo pruebas
                'tipo pruebas crear',
                'tipo pruebas ver',
                'tipo pruebas editar',
                'tipo pruebas borrar',
            ],
            "Proceso de postulacion" => [                
                //postulaciones
                'postulacion crear',
                'postulacion ver',
                'postulacion editar',
                //pruebas
                'prueba crear',
                'prueba ver',
                'prueba editar',
                //entrevistas
                'entrevista crear',
                'entrevista ver',
                'entrevista editar',
                //contactos
                'contacto ver'
            ],
            "Gestion de contratos" => [                
                //contratos
                'contrato crear',
                'contrato ver',
                'contrato editar',
                'contrato borrar',
                //documentos
                'documento crear',
                'documento ver',
                'documento editar',
                'documento borrar',
            ],
            "Rendimiento y avance" => [
                //evaluaciones
                'evaluacion crear',
                'evaluacion ver',
                'evaluacion editar',
                //Egresado
                'gestion egresados'
            ],
            "Posicionamiento" => [
                //solicutudes
                'solicitud crear',
                'solicitud ver',
                'solicitud editar',
                'solicitud borrar',
                //solicitud estados
                'solicitud estado crear',
                'solicitud estado ver',
                'solicitud estado editar',
                'solicitud estado borrar',
                //empleo estados
                'empleo estados ver',
                'empleo estados editar',
                //propuestas
                'propuesta crear',
                'propuesta ver',	
                'propuesta borrar',
            ],
            "Recursos humanos" => [
                //certificaciones
                'certificacion crear',
                'certificacion ver',
                'certificacion editar',
                'certificacion borrar',
                //experiencias
                'experiencia crear',
                'experiencia ver',
                'experiencia editar',
                'experiencia borrar',
            ],
            "Creador de contenido" => [
                //actividades
                'actividad crear',
                'actividad ver',
                'actividad editar',
                'actividad borrar',
                //beneficios
                'beneficio crear',
                'beneficio ver',
                'beneficio editar',
                'beneficio borrar',
                //pasos postulacion
                'paso postulacion crear',
                'paso postulacion ver',
                'paso postulacion editar',
                'paso postulacion borrar',
                //modelos
                'modelo crear',
                'modelo ver',
                'modelo editar',
                'modelo borrar',
                //testimonios
                'testimonio crear',
                'testimonio ver',
                'testimonio editar',
                'testimonio borrar',
                //convocatorias
                'convocatoria crear',
                'convocatoria ver',
                'convocatoria editar',
                'convocatoria borrar',
                //web imagenes
                'web imagenes crear',
                'web imagenes ver',
                'web imagenes editar',
                'web imagenes borrar',
            ],
        ];

        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission, 'grupo' => $group, 'guard_name' => 'sanctum']);
            }
        }

        $superAdminRole = Role::create(['name' => 'Super admin', 'guard_name' => 'sanctum', 'modificacion' => false]);
        $superAdminRole->givePermissionTo(Permission::all());
    }
}

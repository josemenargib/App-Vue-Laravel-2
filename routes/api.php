<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\crm\AuthController;
use App\Http\Controllers\api\crm\UserController;
use App\Http\Controllers\api\crm\BatchController;
use App\Http\Controllers\api\crm\CalendarioController;
use App\Http\Controllers\api\web\BlogsController;
use App\Http\Controllers\api\crm\ModulosController;
use App\Http\Controllers\api\crm\PruebasController;
use App\Http\Controllers\api\web\ImagenesController;
use App\Http\Controllers\api\crm\CurriculaController;
use App\Http\Controllers\api\crm\SolicitudesController;
use App\Http\Controllers\api\crm\TecnologiasController;
use App\Http\Controllers\api\crm\TipoPruebasController;
use App\Http\Controllers\api\web\ActividadesController;
use App\Http\Controllers\api\web\ComentariosController;
use App\Http\Controllers\api\web\TestimoniosController;
use App\Http\Controllers\api\crm\EspecialidadController;
use App\Http\Controllers\api\crm\EvaluacionesController;
use App\Http\Controllers\api\crm\ExperienciasController;
use App\Http\Controllers\api\web\Web_empresasController;
use App\Http\Controllers\api\web\ModelosController;
use App\Http\Controllers\api\crm\EmpleoEstadosController;
use App\Http\Controllers\api\crm\PostulacionesController;
use App\Http\Controllers\api\web\ConvocatoriasController;
use App\Http\Controllers\api\web\RedesSocialesController;
use App\Http\Controllers\api\crm\CertificacionesController;
use App\Http\Controllers\api\web\ReconocimientosController;
use App\Http\Controllers\api\crm\PostulacionFormsController;
use App\Http\Controllers\api\crm\SolicitudEstadosController;
use App\Http\Controllers\api\web\BeneficiosController;
use App\Http\Controllers\api\web\PostulacionPasosController;
use App\Http\Controllers\api\crm\RegistrosController;
use App\Http\Controllers\api\crm\DocumentosController;
use App\Http\Controllers\api\crm\ContratosController;
use App\Http\Controllers\api\crm\EntrevistasController;
use App\Http\Controllers\api\crm\EntrevistasDetallesController;
use App\Http\Controllers\api\crm\RolePermissionController;
use App\Http\Controllers\api\web\PaginaImagenesController;
use App\Http\Controllers\api\web\PaginasController;
use App\Http\Controllers\api\web\PaginasSeccionesController;
use App\Http\Controllers\api\web\SeccionesController;
use App\Http\Controllers\api\crm\EgresadosController;
use App\Http\Controllers\api\web\ContactosController;
use App\Http\Controllers\api\crm\ExportacionesBatchController;
use App\Http\Controllers\api\crm\DashboardController;
use App\Http\Controllers\api\crm\HorasTrabajadasController;
use App\Http\Controllers\api\crm\ImportPostulacionController;
use App\Http\Controllers\api\web\PropuestasEmpleosController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [UserController::class, 'store']);
Route::post('/usuario-registro', [AuthController::class, 'registerAndLogin']);
/***************WEB_BLOGS**************/
Route::get('/blogs', [BlogsController::class, 'index']);
Route::get('/blogs/{id}', [BlogsController::class, 'show']);
Route::get('/blogs-activas', [BlogsController::class, 'blogsActivos']);
Route::get('/blogs-inactivas', [BlogsController::class, 'blogsInactivos']);
Route::put('/blogs-vistas/{id}', [BlogsController::class, 'vistas']);
Route::group(["middleware" => "auth:sanctum"], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    /*************** USUARIOS **************/
    Route::get('/usuario', [UserController::class, 'index']);
    Route::post('/usuario', [UserController::class, 'storeUserWithRole']);
    Route::get('/usuario/{id}', [UserController::class, 'show']);
    Route::put('/usuario', [UserController::class, 'update_credentials']);
    Route::put('/usuario/{id}', [UserController::class, 'update_user_credentials']);
    Route::put('/datos-generales', [UserController::class, 'update_datos_generales']);
    Route::put('/datos-generales/{id}', [UserController::class, 'update_datos_generales_admin']);
    Route::delete('/usuario/{id}', [UserController::class, 'destroy']);
    Route::get('/usuarios-activos', [UserController::class, 'show_actives']);
    Route::delete('/usuario/{id}', [UserController::class, 'destroy']);
    /*************** ROLES Y PERMISOS ******************** */
    Route::get('/permisos', [RolePermissionController::class, 'indexPermissions']);
    Route::get('/roles', [RolePermissionController::class, 'indexRoles']);
    Route::post('/roles', [RolePermissionController::class, 'storeRole']);
    Route::put('/roles/{id}', [RolePermissionController::class, 'updateRole']);
    Route::delete('/roles/{id}', [RolePermissionController::class, 'destroyRole']);
    Route::post('/roles-permisos', [RolePermissionController::class, 'assignPermissionsToRol']);
    Route::get('/rol-permisos/{id}', [RolePermissionController::class, 'showRoleWithPermissions']);
    Route::get('/user/roles/{id}', [RolePermissionController::class, 'showUserRoles']);
    Route::post('/user/roles', [RolePermissionController::class, 'assignRolesToUser']);
    //solicitudes
    Route::get('/solicitudes', [SolicitudesController::class, 'getSolicitudes']);
    Route::get('/solicitudes/{id}', [SolicitudesController::class, 'getSolicitud']);
    Route::delete('/solicitudes/{id}', [SolicitudesController::class, 'setStatus']);
    Route::get('/solicitudes-admin', [SolicitudesController::class, 'buscarSolicitudes']);
    //solicitud-estados
    Route::get('/solicitud-estados', [SolicitudEstadosController::class, 'getSolicitudEstados']);
    Route::get('/solicitud-estados/{id}', [SolicitudEstadosController::class, 'getEstado']);
    Route::put('/solicitud-estados/{id}', [SolicitudEstadosController::class, 'setEstado']);
    Route::post('/solicitud-estados', [SolicitudEstadosController::class, 'addEstado']);
    Route::delete('/solicitud-estados/{id}', [SolicitudEstadosController::class, 'deleteEstado']);
    Route::get('/solicitud-estados-activos', [SolicitudEstadosController::class, 'getSolicitudEstadosActivos']);
    Route::post('/solicitudes', [SolicitudesController::class, 'addSolicitud']);
    Route::put('/solicitudes/{id}', [SolicitudesController::class, 'setSolicitud']);
    Route::get('/solicitudes-user', [SolicitudesController::class, 'getSolicitudesByUser']);
    Route::get('/solicitud-estados-cantidad', [SolicitudesController::class, 'getCountOfAllSolicitudesEstado']);
    Route::put('/empresa', [Web_empresasController::class, 'edicionWebEmpresas']);
    /***************Actividades**************/
    Route::get('/actividades', [ActividadesController::class, 'index']);
    Route::post('/actividades', [ActividadesController::class, 'store']);
    Route::put('/actividades/{id}', [ActividadesController::class, 'update']);
    Route::get('/actividades/{id}', [ActividadesController::class, 'show']);
    Route::delete('/actividades/{id}', [ActividadesController::class, 'destroy']);
    /***************Imagenes**************/
    Route::post('/imagenes/{id}', [ImagenesController::class, 'store']);
    Route::put('/imagenes/{id}', [ImagenesController::class, 'update']);
    Route::delete('/imagenes/{id}', [ImagenesController::class, 'destroy']);
    Route::delete('/imagen/{id}', [ImagenesController::class, 'delete']);
    /***************WEB_BLOGS**************/
    Route::post('/blogs', [BlogsController::class, 'store']);
    Route::put('/blogs/{id}', [BlogsController::class, 'update']);
    Route::delete('/blogs/{id}', [BlogsController::class, 'destroy']);
    Route::post('/comentarios', [ComentariosController::class, 'store']);
    Route::get('/comentarios/{id}', [ComentariosController::class, 'show']);
    Route::put('/comentarios/{id}', [ComentariosController::class, 'update']);
    Route::delete('/comentarios/{id}', [ComentariosController::class, 'destroy']);
    Route::put('/comentarios-puntuar/{id}', [ComentariosController::class, 'puntuar']);
    /***************Experiencias**************/
    Route::get('/experiencias-activas', [ExperienciasController::class, 'index']);
    Route::post('/experiencias', [ExperienciasController::class, 'store']);
    Route::get('/experiencias/{id}', [ExperienciasController::class, 'show']);
    Route::put('/experiencias/{id}', [ExperienciasController::class, 'update']);
    Route::delete('/experiencias/{id}', [ExperienciasController::class, 'destroy']);
    Route::get('/experiencias-usuario', [ExperienciasController::class, 'experienciasByUser']);
    Route::get('/experiencias', [ExperienciasController::class, 'getAllExperiencias']);
    /***************Certificaciones**************/
    Route::get('/certificaciones-activas', [CertificacionesController::class, 'index']);
    Route::post('/certificaciones', [CertificacionesController::class, 'store']);
    Route::get('/certificaciones/{id}', [CertificacionesController::class, 'show']);
    Route::post('/certificaciones/{id}', [CertificacionesController::class, 'update']);
    Route::delete('/certificaciones/{id}', [CertificacionesController::class, 'destroy']);
    Route::get('/certificaciones-usuario', [CertificacionesController::class, 'certificacionesByUser']);
    Route::get('/certificaciones', [CertificacionesController::class, 'getAllCertificaciones']);

    
    /***************Postulaciones**************/
    Route::get('/postulaciones', [PostulacionesController::class, "index"]);
    Route::get('/postulacionescompleto', [PostulacionesController::class, "indexfull"]);
    Route::post('/postulaciones', [PostulacionesController::class, 'store']);
    Route::post('/postulaciones-form-web', [PostulacionesController::class, 'storeFormularioWeb']);
    Route::get('/postulacionesver', [PostulacionesController::class, 'vernopost']);
    Route::get('/postulaciones-search', [PostulacionesController::class, "indexsearch"]);
    Route::get('/postulaciones-searchagrupado', [PostulacionesController::class, "indexsearchagrupado"]);
    Route::get('/postulaciones-filtro', [PostulacionesController::class, "filtrofases"]);
    Route::get('/postulaciones-filtrogroup', [PostulacionesController::class, "filtrofasesagrupado"]);
    Route::get('/postulaciones-groupshow/{id}', [PostulacionesController::class, 'filtrofasesagrupadoshow']);
    Route::get('/postulaciones-show/{id}', [PostulacionesController::class, "show"]);
    Route::get('/postulacionesmostraruser', [PostulacionesController::class, "mostrarUsuarios"]);
    Route::get('/postulaciones-estado', [PostulacionesController::class, "verifestado"]);
    Route::put('/postulacionesestado/{id}', [PostulacionesController::class, 'destroy']);
    Route::put('/postulaciones/{id}', [PostulacionesController::class, 'update']);
    Route::get('/postulaciones-usuario/{id}/{ids}', [PostulacionesController::class, 'showUser']);
    /***************Postulaciones Form**************/
    Route::get('/postulacion/{ord}', [PostulacionFormsController::class, "index"]);
    Route::post('/postulacion', [PostulacionFormsController::class, 'store']);
    Route::get('/postulacion-search', [PostulacionFormsController::class, "indexsearch"]);
    Route::get('/postulacion-show/{id}', [PostulacionFormsController::class, "show"]);
    Route::put('/postulacion/{id}', [PostulacionFormsController::class, 'update']);
    /***************Tipo Pruebas**************/
    Route::post('/tipo-pruebas', [TipoPruebasController::class, 'store']);
    Route::get('/tipo-pruebas', [TipoPruebasController::class, 'index']);
    Route::get('/tipo-pruebas/{id}', [TipoPruebasController::class, 'show']);
    Route::put('/tipo-pruebas/{id}', [TipoPruebasController::class, 'update']);
    Route::get('/tipo-pruebas-total', [TipoPruebasController::class, 'obtenerTodosTiposPruebas']);
    Route::delete('/tipo-pruebas/{id}', [TipoPruebasController::class, 'destroy']);
    Route::get('/tipo-pruebas-buscador', [TipoPruebasController::class, 'buscar']);
    /*********PRUEBAS*********/
    Route::post('/pruebas', [PruebasController::class, 'store']);
    Route::get('/pruebas', [PruebasController::class, 'index']);
    Route::get('/pruebas-buscador', [PruebasController::class, 'buscar']);
    Route::get('/pruebas/{id}', [PruebasController::class, 'selectPersona']);
    Route::get('/pruebas/{id}', [PruebasController::class, 'show']);
    Route::put('/pruebas-revision/{id}', [PruebasController::class, 'update']);
    Route::get('/exportaciones/export/{id?}', [ExportacionesBatchController::class, 'export']);
    Route::post('/enviar-email-pruebas', [PruebasController::class, 'enviarEmail']);
    Route::get('/pruebas-estado', [PruebasController::class, 'listarEstadoPrueba']);
    /*-*-*-*-*HORAS TRABAJADAS-*-*-*-*-*/
    Route::post('/horas-trabajadas',[HorasTrabajadasController::class,'store']);
    Route::get('/horas-trabajadas-listar',[HorasTrabajadasController::class,'index']);
    Route::get('/horas-trabajadas-todos',[HorasTrabajadasController::class,'listarTodos']);
    /***********WEb empresa */
    Route::post('/empresa', [Web_empresasController::class, 'edicionWebEmpresas']);
    /***************WEB_BLOGS**************/
    Route::get('/testimonios', [TestimoniosController::class, 'index']);
    Route::post('/testimonios', [TestimoniosController::class, 'store']);
    Route::get('/testimonios/{id}', [TestimoniosController::class, 'show']);
    Route::put('/testimonios/{id}', [TestimoniosController::class, 'update']);
    Route::delete('/testimonios/{id}', [TestimoniosController::class, 'destroy']);
    /***************Evaluaciones**************/
    Route::get('/evaluaciones', [EvaluacionesController::class, 'index']);
    Route::post('/evaluaciones/{id}', [EvaluacionesController::class, 'store']);
    Route::get('/evaluaciones/{id}', [EvaluacionesController::class, 'show']);
    Route::put('/evaluaciones/{id}', [EvaluacionesController::class, 'update']);
    Route::get('/evaluaciones-registro/{id}', [EvaluacionesController::class, 'showByRegistroId']);
    Route::get('evaluaciones-activas', [EvaluacionesController::class, 'indexEvaluaciones']);
    /***************POSTULACION_PASOS**************/
    Route::get('/postulacion-pasos', [PostulacionPasosController::class, 'index']);
    Route::post('/postulacion-pasos-nuevo', [PostulacionPasosController::class, 'store']);
    Route::get('/postulacion-pasos/{id}', [PostulacionPasosController::class, 'show']);
    Route::put('/postulacion-pasos/{id}', [PostulacionPasosController::class, 'update']);
    Route::delete('/postulacion-pasos/{id}', [PostulacionPasosController::class, 'destroy']);

    /*****************CRM REGISTROS*******************/
    Route::get('/registros', [RegistrosController::class, 'index']);
    Route::post('/nuevo_registro', [RegistrosController::class, 'store']);
    Route::get('/registro/{id}', [RegistrosController::class, 'show']);
    Route::put('/actualizar_registro/{id}', [RegistrosController::class, 'update']);
    Route::get('/eliminar_registro/{id}', [RegistrosController::class, 'delete']);
    Route::delete('/destruir_registro/{id}', [RegistrosController::class, 'destroy']);
    Route::post('/ingresar_registro', [RegistrosController::class, 'register']);
    Route::get('/registros_batch/{id}', [RegistrosController::class, 'users_batch']); //devuelve los ususarios de un batch especifico
    Route::get('/registros_user/{id}', [RegistrosController::class, 'batchs_user']); // devuelve los batchs donde esta registrado un usuario
    Route::get('/registros-search', [RegistrosController::class, 'search_usuarios_no_registrados']);
    Route::get('/registros_batch_all/{id}', [RegistrosController::class, 'users_batch_all']); ////devuelve los ususarios de un batch especifico activos e inactivos

    /*****************CRM DOCUMENTOS*****************/
    Route::get('/documentos', [DocumentosController::class, 'index']);
    Route::get('/documentos/{id}', [DocumentosController::class, 'show']); //muestra un documento especifico
    Route::post('/nuevo_documento', [DocumentosController::class, 'store']);
    Route::put('/actualizar_documento/{id}', [DocumentosController::class, 'update']);
    Route::put('/eliminar_documento/{id}', [DocumentosController::class, 'delete']);
    Route::delete('/destruir_documento/{id}', [DocumentosController::class, 'destroy']);
    Route::get('/documentos_registro/{id}', [DocumentosController::class, 'documentos_registro']); //Muestra los documentos de un registro especifico

    /******************CRM CONTRATOS*****************/
    Route::get('/contratos', [ContratosController::class, 'index']);
    Route::get('/contratos/{id}', [ContratosController::class, 'show']);
    Route::post('/nuevo_contrato', [ContratosController::class, 'store']);
    Route::put('/actualizar_contrato/{id}', [ContratosController::class, 'update']);
    Route::put('/eliminar_contrato', [ContratosController::class, 'delete']);
    Route::delete('/destruir_contrato/{id}', [ContratosController::class, 'destroy']);
    Route::get('/contratos/{id}', [ContratosController::class, 'contratos_registro']); //Muestra los contratos de un registro asociado a un usuario de un batch especifico enviamos el id del registro y no del batch
    Route::get('/estados_contratos', [ContratosController::class, 'estados']);

    // ************* Redes Sociales **********
    Route::get('/redes-sociales', [RedesSocialesController::class, 'index']);
    Route::post('/redes-sociales', [RedesSocialesController::class, 'store']);
    Route::get('/redes-sociales/{id}', [RedesSocialesController::class, 'show']);
    Route::put('/redes-sociales/{id}', [RedesSocialesController::class, 'update']);
    Route::delete('/redes-sociales/{id}', [RedesSocialesController::class, 'destroy']);
    // ************* Reconocimiento **********
    Route::get('/reconocimientos', [ReconocimientosController::class, 'index']);
    Route::post('/reconocimientos', [ReconocimientosController::class, 'store']);
    Route::get('/reconocimientos/{id}', [ReconocimientosController::class, 'show']);
    Route::put('/reconocimientos/{id}', [ReconocimientosController::class, 'update']);
    Route::delete('/reconocimientos/{id}', [ReconocimientosController::class, 'destroy']);
    /**********Empleo Estados ************/
    //Route::get('/empleo-estados', [EmpleoEstadosController::class, 'index']);
    Route::post('/empleo-estados', [EmpleoEstadosController::class, 'store']);
    Route::get('/empleo-estados', [EmpleoEstadosController::class, 'show']);
    Route::put('/empleo-estados', [EmpleoEstadosController::class, 'update']);
    Route::delete('/empleo-estados/{id}', [EmpleoEstadosController::class, 'destroy']);
    Route::get('/empleo-estados-activos', [EmpleoEstadosController::class, 'empleoEstadosActivos']);
    // ******** crm_Modulos ********
    Route::get('/modulos', [ModulosController::class, 'index']);
    Route::post('/modulos', [ModulosController::class, 'store']);
    Route::get('/modulos/{id}', [ModulosController::class, 'show']);
    Route::put('/modulos/{id}', [ModulosController::class, 'update']);
    Route::delete('/modulos/{id}', [ModulosController::class, 'destroy']);
    // ******** crm_Tecnologias ********
    Route::get('/tecnologias', [TecnologiasController::class, 'index']);
    Route::post('/tecnologias', [TecnologiasController::class, 'store']);
    Route::get('/tecnologias/{id}', [TecnologiasController::class, 'show']);
    Route::put('/tecnologias/{id}', [TecnologiasController::class, 'update']);
    Route::delete('/tecnologias/{id}', [TecnologiasController::class, 'destroy']);
    // Especialidades
    Route::get('/especialidad', [EspecialidadController::class, 'index']);
    Route::post('/especialidad-nueva', [EspecialidadController::class, 'store']);
    Route::get('/especialidad/{id}', [EspecialidadController::class, 'show']);
    Route::delete('/especialidad/{id}', [EspecialidadController::class, 'destroy']);
    Route::put('/especialidad/{id}', [EspecialidadController::class, 'update']);
    // Batchs
    Route::get('/batch', [BatchController::class, 'index']);
    Route::post('/batch-nuevo', [BatchController::class, 'store']);
    Route::get('/batch/{id}', [BatchController::class, 'show']);
    Route::delete('/batch/{id}', [BatchController::class, 'destroy']);
    Route::put('/batch/{id}', [BatchController::class, 'update']);
    // Curriculas
    Route::get('/curricula', [CurriculaController::class, 'index']);
    Route::post('/curricula-nueva', [CurriculaController::class, 'store']);
    Route::get('/curricula/{id}', [CurriculaController::class, 'show']);
    Route::delete('/curricula/{id}', [CurriculaController::class, 'destroy']);
    Route::put('/curricula/{id}', [CurriculaController::class, 'update']);
    //Obtener Permisos
    Route::get('/usuario-permiso', [UserController::class, 'obtenerPermisos']);
    /***************Secciones**************/
    Route::get('/secciones', [SeccionesController::class, 'index']);
    Route::put('/secciones/{id}', [SeccionesController::class, 'update']);
    Route::get('/secciones/{id}', [PaginasController::class, 'show']);
    Route::delete('/secciones/{id}', [SeccionesController::class, 'destroy']);
    /***************Paginas**************/
    Route::get('/paginas', [PaginasController::class, 'index']);
    Route::put('/paginas/{id}', [PaginasController::class, 'update']);
    Route::get('/paginas/{id}', [PaginasController::class, 'show']);
    Route::delete('/paginas/{id}', [PaginasController::class, 'destroy']);
    /*****************Paginas y Secciones**********************/
    Route::get('/pagina-seccion', [PaginasSeccionesController::class, 'index']);
    Route::post('/pagina-seccion', [PaginasSeccionesController::class, 'store']);
    Route::put('/pagina-seccion/{id}', [PaginasSeccionesController::class, 'update']);
    Route::get('/pagina-seccion/{id}', [PaginasSeccionesController::class, 'show']);
    Route::get('/pagina-seccion-ids', [PaginasSeccionesController::class, 'showSelected']);
    /***************Paginas-Imagenes**************/
    Route::get('/paginas-imagenes', [PaginaImagenesController::class, 'index']);
    Route::post('/paginas-imagenes/{id}', [PaginaImagenesController::class, 'store']);
    Route::put('/paginas-imagenes/{id}', [PaginaImagenesController::class, 'update']);
    Route::get('/paginas-imagenes/{id}', [PaginaImagenesController::class, 'show']);
    Route::get('/paginas-imagenes-seccion/{id}', [PaginaImagenesController::class, 'showPaginaSeccion']);
    Route::delete('/paginas-imagenes/{id}', [PaginaImagenesController::class, 'destroy']);
    Route::delete('/paginas-imagen/{id}', [PaginaImagenesController::class, 'delete']);
    Route::get('/registros-search', [RegistrosController::class, 'search_usuarios_no_registrados']);
    // Convocatorias
    Route::get('/convocatorias', [ConvocatoriasController::class, 'index']);
    Route::post('/convocatorias', [ConvocatoriasController::class, 'store']);
    Route::get('/convocatorias/{id}', [ConvocatoriasController::class, 'show']);
    Route::put('/convocatorias/{id}', [ConvocatoriasController::class, 'update']);
    Route::delete('/convocatorias/{id}', [ConvocatoriasController::class, 'destroy']);
    /***************MODELOS**************/
    Route::get('/modelos', [ModelosController::class, 'index']);
    Route::post('/modelos', [ModelosController::class, 'store']);
    Route::put('/modelos/{id}', [ModelosController::class, 'update']);
    Route::delete('/modelos/{id}', [ModelosController::class, 'destroy']);
    Route::get('/modelos/{id}', [ModelosController::class, 'show']);
    /***************ENTREVISTAS**************/
    Route::get('/entrevistas/{ord}', [EntrevistasController::class, 'index']);
    Route::get('/entrevistas-show/{id}', [EntrevistasController::class, "show"]);

    /***************ENTREVISTAS**************/
    Route::get('/entrevistadetalle', [EntrevistasDetallesController::class, "index"]);
    Route::get('/entrevistadetalle-show/{id}', [EntrevistasDetallesController::class, "show"]);
    /********************* WEB - beneficios************************/
    Route::get('/web_beneficios', [BeneficiosController::class, 'index']);
    Route::post('/web_beneficios', [BeneficiosController::class, 'store']);
    Route::put('/web_beneficios/{id}', [BeneficiosController::class, 'update']);
    Route::delete('/web_beneficios/{id}', [BeneficiosController::class, 'destroy']);
    Route::get('/web_beneficios/{id}', [BeneficiosController::class, 'show']);
    /***************ENTREVISTAS**************/
    Route::get('/entrevistas', [EntrevistasController::class, 'index']);
    Route::get('/entrevistas-show/{id}', [EntrevistasController::class, "show"]);
    Route::post('/entrevistas', [EntrevistasController::class, 'store']);
    Route::put('/entrevistas/{id}', [EntrevistasController::class, 'update']);
    /***************ENTREVISTAS**************/
    Route::get('/entrevistadetalle', [EntrevistasDetallesController::class, "index"]);
    Route::get('/entrevistadetalle-show/{id}', [EntrevistasDetallesController::class, "show"]);
    Route::post('/entrevistadetalle', [EntrevistasDetallesController::class, 'store']);
    Route::put('/entrevistadetalle/{id}', [EntrevistasDetallesController::class, 'update']);
    Route::get('/entrevistadetalleentrevistadores', [EntrevistasDetallesController::class, 'buscaentrevistadores']);
    Route::get('/entrevistadetallepostulantes', [EntrevistasDetallesController::class, 'buscapostulantes']);
    Route::get('/entrevistapostulante-search', [EntrevistasDetallesController::class, "searchpostulante"]);
    Route::get('/entrevistaentrevista-search', [EntrevistasDetallesController::class, "searchentrevistadores"]);
    //Egresados
    Route::post('/cambiar-rol/{userId}', [EgresadosController::class, 'cambiarRol']);
    Route::get('/registros_batch_estudiantes/{id}', [RegistrosController::class, 'users_batch_estudiantes']);
    Route::get('/usuarios-egresados', [EgresadosController::class, 'users_egresados']);
    Route::get('/usuarios/no-estudiantes', [ExperienciasController::class, 'obtenerUsuariosNoEstudiantes']);
    // ******** DASHBOARD ********
    Route::get('/dashboard/educacion', [DashboardController::class, 'getTodosLosUsuariosConBatchYFases']);
    Route::get('/dashboard/edu-totalPostulantes', [DashboardController::class, 'totalPostulantes']);
    Route::get('/dashboard/edu-totalEstudiantes', [DashboardController::class, 'totalEstudiantes']);
    Route::get('/dashboard/edu-obtenerBatches', [DashboardController::class, 'obtenerBatches']);
    Route::get('/dashboard/edu-postulantesPorBatch', [DashboardController::class, 'postulantesPorBatch']);
    Route::get('/dashboard/edu-estudiantesPorBatch', [DashboardController::class, 'estudiantesPorBatch']);
    Route::get('/usuarios-estudiantes', [UserController::class, 'getCantidadUsersEstudiantes']);
    Route::get('/estudiantes-batch', [DashboardController::class, 'getEstudiantesPorBatch']);
    Route::get('/solicitudes-nro', [SolicitudesController::class, 'getNroSolicitudesActivas']);
    //CALENDARIO    
    Route::get('/calendario', [CalendarioController::class, 'index']);
    Route::get('/calendario/{id}', [CalendarioController::class, 'view']);
    Route::get('/estudiantes-batch', [DashboardController::class, 'getEstudiantesPorBatch']);
    Route::get('/solicitudes-nro', [SolicitudesController::class, 'getNroSolicitudesActivas']);
    Route::put('/entrevista-aprobar/{id}', [EntrevistasController::class, 'agregarARegistros']);
    Route::post('/postulaciones-importar', [ImportPostulacionController::class, 'import']);
});
/******************Paginas********************/
Route::get('/paginas-nombre', [PaginasController::class, 'showByName']);
Route::get('/paginas-activas', [PaginasController::class, 'indexDisponibles']);
Route::post('/paginas', [PaginasController::class, 'store']);
/******************Secciones******************/
Route::get('/secciones-activas', [SeccionesController::class, 'indexDisponibles']);
Route::post('/secciones', [SeccionesController::class, 'store']);
/***************Paginas-Imagenes**************/
Route::get('/paginas-imagenes-activas', [PaginaImagenesController::class, 'indexDisponibles']);
/***************Actividades**************/
Route::get('/actividades-activas', [ActividadesController::class, 'indexDisponibles']);
Route::get('/actividad/{id}', [ActividadesController::class, 'show']);
/***************Imagenes**************/
Route::get('/imagenes/{id}', [ImagenesController::class, 'index']);
Route::get('/imagenes-activas/{id}', [ImagenesController::class, 'indexHabilitadas']);
Route::get('/imagen/{id}', [ImagenesController::class, 'show']);
/***************WEB_TESTIMONIOS**************/
Route::get('/testimonios-activas', [TestimoniosController::class, 'testimoniosActivos']);
/***************POSTULACION_PASOS**************/
Route::get('/postulacion-pasos-activos', [PostulacionPasosController::class, 'postulacionPasosActivos']);
// ESPECIALIDADES_ACTIVAS
Route::get('/especialidades-activas', [EspecialidadController::class, 'especialidadesActivas']);
Route::get('/especialidades-activas/{id}', [EspecialidadController::class, 'showActivas']);
// ******** Módulos y Tecnologías ACTIVAS ********
Route::get('/modulos-activos', [ModulosController::class, 'indexActivos']);
Route::get('/tecnologias-activas', [TecnologiasController::class, 'indexActivos']);
// BATCHS_ACTIVOS
Route::get('/batchs-activos', [BatchController::class, 'batchsActivos']);
Route::get('/batchs-activos/{id}', [BatchController::class, 'batchsEspecialidad']);
// CURRICULAS_ACTIVAS
Route::get('/curriculas-activas', [CurriculaController::class, 'curriculasActivos']);
/***************MODELOS**************/
Route::get('/modelos-disponibles', [ModelosController::class, 'indexDisponibles']);
// Redes Sociales
Route::get('/redes-sociales-activas', [RedesSocialesController::class, 'redesSocialesActivo']);
// Reconocimiento
Route::get('/reconocimientos-activos', [ReconocimientosController::class, 'reconocimientosActivo']);
//empresa
Route::get('/empresa', [Web_empresasController::class, 'lecturaWebEmpresas']);
/********************* WEB - beneficios************************/
Route::get('/web_beneficios-activos', [BeneficiosController::class, 'beneficiosActivos']);
/************************* BATCHS EN TOTAL ************************************** */
Route::get('/batchs-total', [BatchController::class, 'batchsIndex']);
// convocatorias_activas
Route::get('/convocatorias-activos', [ConvocatoriasController::class, 'batchsActivos']);
/***************************TALENTOS******************************/
Route::get('/talentos', [EmpleoEstadosController::class, 'empleo_egresado']);
// contactos
Route::get('/contactanos', [ContactosController::class, 'index']);
Route::post('/contactanos', [ContactosController::class, 'store']);
//************************** PROPUESTAS EMPLEOS ***************************** */
Route::get('/propuestas', [PropuestasEmpleosController::class, 'showAll']);
Route::get('/propuestas-activas', [PropuestasEmpleosController::class, 'showActives']);
Route::post('/propuestas-empresas', [PropuestasEmpleosController::class, 'create']);
Route::delete('propuestas-empresas/{id}', [PropuestasEmpleosController::class, 'destroy']);
Route::get('/download-plantilla', [ImportPostulacionController::class, 'downloadFile']);
<?php

use Controllers\ConfiguracionController;
use Controllers\GestionController;
use Controllers\ReportesController;
date_default_timezone_set('America/Tegucigalpa');
ini_set('max_execution_time', 0); // Tiempo ilimitado
set_time_limit(0);

require_once __DIR__ . '/../includes/app.php';



use Controllers\LoginController;
use Controllers\PrincipalController;
use Controllers\PrestamoController;
use MVC\Router;

$router = new Router();

$router->get('/', [LoginController::class, 'login'], []);
$router->post('/', [LoginController::class, 'login'], []);
$router->get('/logout', [LoginController::class, 'logout']);
$router->get('/principal', [PrincipalController::class, 'principal'], ['ADMIN', 'SUPERVISOR', 'TELECOBRO']);

$router->get('/cobros', [PrincipalController::class, 'buscarPrestamos'], ['ADMIN', 'SUPERVISOR', 'TELECOBRO']);
$router->post('/cobros', [PrincipalController::class, 'buscarPrestamos'], ['ADMIN', 'SUPERVISOR', 'TELECOBRO']);
$router->get('/prestamos/detalle', [PrestamoController::class, 'detalle'], ['ADMIN', 'SUPERVISOR', 'TELECOBRO']);
$router->post('/prestamos/detalle', [PrestamoController::class, 'detalle'], ['ADMIN', 'SUPERVISOR', 'TELECOBRO']);
$router->post('/prestamos/guardar-visita', [PrestamoController::class, 'guardarVisita'], ['ADMIN', 'SUPERVISOR', 'TELECOBRO']);
$router->get('/prestamos/obtener-historial-visitas', [PrestamoController::class, 'obtenerHistorialVisitas'], ['ADMIN', 'SUPERVISOR', 'TELECOBRO']);





//reportes
$router->get('/reportes', [ReportesController::class, 'index'], ['ADMIN', 'SUPERVISOR']);
$router->get('/reportes-gestiones', [ReportesController::class, 'gestiones'], ['ADMIN', 'SUPERVISOR']);
$router->post('/reportes-gestiones', [ReportesController::class, 'gestiones'], ['ADMIN', 'SUPERVISOR']);
$router->post('/descargar-gestiones', [ReportesController::class, 'descargarGestiones'], ['ADMIN', 'SUPERVISOR']);
$router->get('/reportes-recuperacion', [ReportesController::class, 'recuperacion'], ['ADMIN', 'SUPERVISOR']);
$router->post('/reportes-recuperacion', [ReportesController::class, 'descargarPagos'], ['ADMIN', 'SUPERVISOR']);
$router->get('/reportes-deterioro', [ReportesController::class, 'deterioro'], ['ADMIN', 'SUPERVISOR']);
$router->post('/reportes-deterioro', [ReportesController::class, 'descargarReporteDeterioro'], ['ADMIN', 'SUPERVISOR']);




//gestion
$router->get('/configuracion', [ConfiguracionController::class, 'index'], ['ADMIN']);
$router->post('/configuracion-upload', [ConfiguracionController::class, 'index'], ['ADMIN']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();

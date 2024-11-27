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

$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);
$router->get('/principal', [PrincipalController::class, 'principal']);

$router->get('/cobros', [PrincipalController::class, 'buscarPrestamos']);
$router->post('/cobros', [PrincipalController::class, 'buscarPrestamos']);
$router->get('/prestamos/detalle', [PrestamoController::class, 'detalle']);
$router->post('/prestamos/detalle', [PrestamoController::class, 'detalle']);


//reportes
$router->get('/reportes', [ReportesController::class, 'index']);
$router->get('/reportes-gestiones', [ReportesController::class, 'gestiones']);
$router->post('/reportes-gestiones', [ReportesController::class, 'gestiones']);
$router->post('/descargar-gestiones', [ReportesController::class, 'descargarGestiones']);
$router->get('/reportes-recuperacion', [ReportesController::class, 'recuperacion']);
$router->post('/reportes-recuperacion', [ReportesController::class, 'descargarPagos']);




//gestion
$router->get('/configuracion', [ConfiguracionController::class, 'index']);
$router->post('/configuracion-upload', [ConfiguracionController::class, 'index']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();

<?php
namespace Controllers;

use MVC\Router;
use Model\ClientesPrestamos;
use Model\Gestiones;
use Model\ComentariosPermanentes;
use Model\CodigosResultado;
class PrestamoController
{
    public static function detalle(Router $router)
    {

        isAuth();

        $codigosPositivos = CodigosResultado::obtenerPositivos();
        $codigosPositivosArray = array_column($codigosPositivos, 'codigo');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $params = $_POST;

            if (empty($params['prenumero']) || empty($_SESSION['nombre'])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'El número de préstamo o el usuario no están definidos.'
                ]);
                exit;
            }


            $fecha_promesa = in_array($params['codigoResultado'], $codigosPositivosArray)
                ? ($params['fechaPromesa'] ?? null)
                : null;

            $monto_promesa = in_array($params['codigoResultado'], $codigosPositivosArray)
                ? ($params['montoPromesa'] ?? 0)
                : 0;

            // Validar y procesar los datos de la gestión
            $gestionData = [
                'prenumero' => $params['prenumero'],
                'codigo_resultado' => $params['codigoResultado'],
                'fecha_revision' => $params['fechaRevision'] ?? null,
                'fecha_promesa' => $fecha_promesa,
                'numero_contactado' => $params['numeroContactado'],
                'comentario' => $params['comentarioGestion'] ?? '',
                'creado_por' => $_SESSION['nombre'],
                'monto_promesa' => $monto_promesa

            ];

            $gestion = new Gestiones($gestionData);

            if ($gestion->guardar()) {
                // Manejar el comentario permanente
                ComentariosPermanentes::useSQLSrv();
                $comentarioPermanente = ComentariosPermanentes::where('prenumero', $params['prenumero']);

                if ($comentarioPermanente) {
                    $comentarioPermanente->comentario = $params['comentarioPermanente'] ?? '';
                    $comentarioPermanente->ultima_modificacion = date('Y-m-d H:i:s');
                    $comentarioPermanente->guardar();
                } else {
                    $nuevoComentario = new ComentariosPermanentes([
                        'prenumero' => $params['prenumero'],
                        'comentario' => $params['comentarioPermanente'] ?? ''
                    ]);
                    $nuevoComentario->guardar();
                }

                // Recuperar los datos actualizados
                $historialGestiones = Gestiones::whereAll('prenumero', $params['prenumero'], 'ORDER BY fecha_creacion DESC');
                $historialGestiones = is_iterable($historialGestiones) ? $historialGestiones : [];

                $comentarioPermanente = ComentariosPermanentes::where('prenumero', $params['prenumero']);

                // Enviar respuesta al frontend
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Gestión guardada exitosamente',
                    'historialGestiones' => $historialGestiones,
                    'comentarioPermanente' => $comentarioPermanente
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No se pudo guardar la gestión.'
                ]);
            }
            exit;
        }

        // Procesar solicitudes GET
        $prenumero = $_GET['prenumero'] ?? null;
        $identidad = str_replace(' ', '', $_GET['identidad'] ?? null);
        $fecha = self::validarFecha($_GET['fecha'] ?? null, 'd-m-Y');

        $prestamoDetalle = $saldoPrestamo = $pagosClientes = $historialGestiones = $comentarioPermanente = $promesas = null;

        if ($prenumero) {
            $prestamoDetalle = ClientesPrestamos::getInfoClientes($identidad, $fecha);
            $saldoPrestamo = ClientesPrestamos::getSaldoClientes($prenumero);
            $pagosClientes = ClientesPrestamos::ObtenerPagosCliente($prenumero);

            Gestiones::useSQLSrv();
            $historialGestiones = Gestiones::whereAll('prenumero', $prenumero, 'ORDER BY fecha_creacion DESC');
            $historialGestiones = is_iterable($historialGestiones) ? $historialGestiones : [];
            $comentarioPermanente = ComentariosPermanentes::where('prenumero', $prenumero);
            $promesas = Gestiones::obtenerPromesasPorCliente($prenumero);
        }

        $codigosResultado = CodigosResultado::all();


        // Renderizar la vista
        $router->render('prestamos/detalle', [
            'titulo' => 'Detalle del Préstamo',
            'prestamoDetalle' => $prestamoDetalle,
            'saldoPrestamo' => $saldoPrestamo,
            'pagosClientes' => $pagosClientes,
            'historialGestiones' => $historialGestiones,
            'comentarioPermanente' => $comentarioPermanente,
            'promesas' => $promesas,
            'codigosResultado' => $codigosResultado,
            'codigosPositivosArray' => $codigosPositivosArray
        ]);
    }

    private static function validarFecha($fecha, $formato = 'd-m-Y')
    {
        $fecha_obj = \DateTime::createFromFormat($formato, $fecha);
        return $fecha_obj ? $fecha_obj->format('Y-m-d H:i:s') : null;
    }
}

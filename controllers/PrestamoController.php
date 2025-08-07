<?php
namespace Controllers;

use MVC\Router;
use Model\ClientesPrestamos;
use Model\Gestiones;
use Model\ComentariosPermanentes;
use Model\CodigosResultado;
use Model\VisitaDomiciliar;
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
        // Obtener las visitas asociadas a este préstamo
        $visitas = VisitaDomiciliar::whereAll('prenumero', $prenumero, 'ORDER BY creado_el DESC');

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
            'codigosPositivosArray' => $codigosPositivosArray,
            'visitas' => $visitas
        ]);
    }


    public static function guardarVisita()
    {
        isAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'mensaje' => 'Método no permitido']);
            exit;
        }

        // Validación
        if (
            empty($_POST['prenumero']) ||
            empty($_POST['direccion_visitada']) ||
            empty($_POST['fecha_visita']) ||
            empty($_FILES['foto_maps']['name']) ||
            empty($_FILES['foto_lugar']['name'])
        ) {
            echo json_encode(['status' => 'error', 'mensaje' => 'Todos los campos son obligatorios.']);
            exit;
        }

        $prenumero = $_POST['prenumero'];
        $direccion = $_POST['direccion_visitada'];
        $fecha = $_POST['fecha_visita'];
        $creado_por = $_SESSION['nombre'] ?? 'Desconocido';

        // Ruta del directorio
        $upload_dir = __DIR__ . '/../public/uploads/visitas/';

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Crear nombres únicos
        $foto_maps = uniqid('maps_') . '_' . basename($_FILES['foto_maps']['name']);
        $foto_lugar = uniqid('lugar_') . '_' . basename($_FILES['foto_lugar']['name']);

        $maps_path = '/uploads/visitas/' . $foto_maps;
        $lugar_path = '/uploads/visitas/' . $foto_lugar;

        $target_maps = $upload_dir . $foto_maps;
        $target_lugar = $upload_dir . $foto_lugar;

        // Mover archivos
        if (!move_uploaded_file($_FILES['foto_maps']['tmp_name'], $target_maps)) {
            echo json_encode(['status' => 'error', 'mensaje' => 'Error al subir foto Maps.']);
            exit;
        }

        if (!move_uploaded_file($_FILES['foto_lugar']['tmp_name'], $target_lugar)) {
            echo json_encode(['status' => 'error', 'mensaje' => 'Error al subir foto del lugar.']);
            exit;
        }

        // Guardar en BD
        $visita = new VisitaDomiciliar([
            'prenumero' => $prenumero,
            'direccion_visitada' => $direccion,
            'fecha_visita' => $fecha,
            'foto_maps' => $maps_path,
            'foto_lugar' => $lugar_path,
            'creado_por' => $creado_por
        ]);

        if ($visita->guardar()) {
            echo json_encode(['status' => 'success', 'mensaje' => 'Guardado correctamente']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'mensaje' => 'No se pudo guardar en BD.']);
            exit;
        }
    }

    public static function obtenerHistorialVisitas()
    {
        isAuth();

        $prenumero = $_GET['prenumero'] ?? null;
        if (!$prenumero) {
            http_response_code(400);
            echo "Número de préstamo requerido";
            exit;
        }
        $visitas = VisitaDomiciliar::whereAll('prenumero', $prenumero, 'ORDER BY creado_el DESC');
        // Renderiza solo la tabla, sin el layout completo
        include_once __DIR__ . '/../views/prestamos/secciones/historial_visitas.php';
    }






    private static function validarFecha($fecha, $formato = 'd-m-Y')
    {
        $fecha_obj = \DateTime::createFromFormat($formato, $fecha);
        return $fecha_obj ? $fecha_obj->format('Y-m-d H:i:s') : null;
    }
}

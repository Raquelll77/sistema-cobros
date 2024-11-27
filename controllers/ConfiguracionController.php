<?php

namespace Controllers;

use MVC\Router;
use Model\ActiveRecord;
use Clases\Upload;

class ConfiguracionController
{
    public static function index(Router $router)
    {
        session_start();
        isAuth();

        // Configurar tiempo de ejecución ilimitado
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        $message = null;
        $status = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Verificar archivo cargado
                if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                    throw new \Exception('No se seleccionó ningún archivo válido.');
                }

                // Configurar conexión a la base de datos
                ActiveRecord::useSQLSrv();
                $dbConnection = ActiveRecord::getActiveDB();

                // Procesar el archivo
                $uploader = new Upload($_FILES['file'], $dbConnection);
                $result = $uploader->processUpload();

                // Mensaje de éxito
                $message = "Archivo procesado exitosamente. Filas afectadas: $result.";
                $status = 'success';
            } catch (\Exception $e) {
                // Mensaje de error
                $message = htmlspecialchars($e->getMessage());
                $status = 'error';
            }
        }

        // Renderizar vista
        $router->render('configuracion/index', [
            'titulo' => 'Configuración Principal',
            'message' => $message,
            'status' => $status,
        ]);
    }
}

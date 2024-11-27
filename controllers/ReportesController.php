<?php

namespace Controllers;

use Model\ClientesPrestamos;
use MVC\Router;
use Model\Gestiones;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



class ReportesController
{

    public static function index(Router $router)
    {
        session_start();
        isAuth();
        $router->render('reportes/index', [

        ]);
    }
    public static function gestiones(Router $router)
    {
        session_start();
        isAuth();

        // Obtener fecha seleccionada o usar la fecha actual
        $fechaSeleccionada = $_POST['fecha'] ?? date('Y-m-d');
        $fecha = date('Y-m-d H:i:s', strtotime($fechaSeleccionada));

        // Obtener gestiones y procesar datos
        $gestiones = Gestiones::ObtenerGestionesPorFechas($fecha);
        $datosProcesados = Gestiones::procesarDatosGestiones($gestiones);

        // Enviar datos a la vista
        $router->render('reportes/gestiones', [
            'titulo' => 'Reporte Gestiones',
            'fechaSeleccionada' => $fechaSeleccionada,
            'reporteGestionDiaria' => $gestiones,
            'datosProcesados' => $datosProcesados
        ]);
    }

    public static function descargarGestiones(Router $router)
    {
        session_start();
        isAuth();

        $errores = [];

        // Validar las fechas
        $fechaInicio = $_POST['fecha_inicio'] ?? null;
        $fechaFin = $_POST['fecha_fin'] ?? null;

        if ($fechaInicio && $fechaFin) {
            try {
                $fechaInicio = (new \DateTime($fechaInicio))->format('Y-m-d\TH:i:s'); // Inicio del día
                $fechaFin = (new \DateTime($fechaFin))->setTime(23, 59, 59)->format('Y-m-d\TH:i:s'); // Fin del día
            } catch (\Exception $e) {
                $errores[] = 'Formato de fecha inválido.';
            }

            if ($fechaInicio > $fechaFin) {
                $errores[] = 'La fecha de inicio no puede ser posterior a la fecha final.';
            }
        } else {
            $errores[] = 'Fechas inválidas.';
        }

        // Consultar las gestiones en el rango de fechas
        Gestiones::useSQLSrv();
        $gestiones = Gestiones::obtenerPorRangoFechas('fecha_creacion', $fechaInicio, $fechaFin);

        if (empty($gestiones)) {
            $errores[] = 'No hay gestiones para el rango de fechas seleccionado.';
            $router->render('reportes/gestiones', [
                'errores' => $errores
            ]);
            return;
        }

        // Generar el archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Historial de Gestiones');

        // Encabezados
        $encabezados = [
            'A1' => 'ID',
            'B1' => 'Prenumero',
            'D1' => 'Fecha Creación',
            'E1' => 'Fecha Revisión',
            'F1' => 'Fecha Promesa',
            'G1' => 'Número Contactado',
            'C1' => 'Código Resultado',
            'H1' => 'Comentario',
            'I1' => 'Creado Por',
            'J1' => 'Monto Promesa',
        ];


        foreach ($encabezados as $celda => $texto) {
            $sheet->setCellValue($celda, $texto);
        }

        // Agregar datos
        $fila = 2;
        foreach ($gestiones as $gestion) {
            $sheet->setCellValue("A{$fila}", $gestion->id ?? 'N/A');
            $sheet->setCellValue("B{$fila}", $gestion->prenumero ?? 'N/A');
            $sheet->setCellValue("D{$fila}", $gestion->fecha_creacion ?? 'N/A');
            $sheet->setCellValue("E{$fila}", $gestion->fecha_revision ?? 'N/A');
            $sheet->setCellValue("F{$fila}", $gestion->fecha_promesa ?? 'N/A');
            $sheet->setCellValue("G{$fila}", $gestion->numero_contactado ?? 'N/A');
            $sheet->setCellValue("C{$fila}", $gestion->codigo_resultado ?? 'N/A');
            $sheet->setCellValue("H{$fila}", $gestion->comentario ?? 'N/A');
            $sheet->setCellValue("I{$fila}", $gestion->creado_por ?? 'N/A');
            $sheet->setCellValue("J{$fila}", $gestion->montoPromesa ?? 0);
            $fila++;
        }

        // Enviar archivo al cliente
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="gestiones.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public static function recuperacion(Router $router)
    {
        session_start();
        isAuth();

        // Obtener el primer día del mes y el día actual
        $primerDiaMes = (new \DateTime('first day of this month'))->format('Y-m-d');
        $diaActual = (new \DateTime())->format('Y-m-d');

        // Consultar los pagos generales entre las fechas especificadas
        $pagosXGestor = ClientesPrestamos::obtenerPagosGeneral($primerDiaMes, $diaActual);

        // Inicializamos un arreglo para agrupar totales por gestor
        $totalesPorGestor = [];
        foreach ($pagosXGestor as $pago) {
            // Verifica si el valor de 'usuarioCobros' es '-' o vacío
            $gestor = $pago['usuarioCobros'];
            if (empty($gestor) || $gestor === '-') {
                $gestor = 'Sin Asignar';
            }

            // Suma los valores agrupados por gestor
            if (!isset($totalesPorGestor[$gestor])) {
                $totalesPorGestor[$gestor] = 0;
            }
            $totalesPorGestor[$gestor] += $pago['CrMoValor'];
        }


        // Convertimos los datos para ser usados en Chart.js
        $gestores = array_keys($totalesPorGestor); // Nombres de los gestores (incluyendo "Sin Asignar")
        $totales = array_values($totalesPorGestor); // Totales recuperados por cada gestor

        // Debugging opcional
        /* debuguear($gestores);
           debuguear($totales); */

        // Renderizamos la vista con los datos preparados
        $router->render('reportes/recuperacion', [
            'titulo' => 'Reportes de Recuperacion',
            'gestores' => $gestores,
            'totales' => $totales
        ]);
    }


    public static function descargarPagos(Router $router)
    {
        session_start();
        isAuth();

        $errores = [];

        // Validar las fechas
        $fechaInicio = $_POST['fecha_inicio'] ?? null;
        $fechaFin = $_POST['fecha_fin'] ?? null;

        if ($fechaInicio && $fechaFin) {
            try {
                $fechaInicio = (new \DateTime($fechaInicio))->format('Y-m-d\TH:i:s'); // Inicio del día
                $fechaFin = (new \DateTime($fechaFin))->setTime(23, 59, 59)->format('Y-m-d\TH:i:s'); // Fin del día
            } catch (\Exception $e) {
                $errores[] = 'Formato de fecha inválido.';
            }

            if ($fechaInicio > $fechaFin) {
                $errores[] = 'La fecha de inicio no puede ser posterior a la fecha final.';
            }
        } else {
            $errores[] = 'Fechas inválidas.';
        }

        // Consultar las gestiones en el rango de fechas
        ClientesPrestamos::useSQLSrv();
        $pagos = ClientesPrestamos::obtenerPagosGeneral($fechaInicio, $fechaFin);

        if (empty($pagos)) {
            $errores[] = 'No hay pagos para el rango de fechas seleccionado.';
            $router->render('/reportes/recuperacion', [
                'errores' => $errores
            ]);
            return;
        }

        // Generar el archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Historial de pagos');

        // Encabezados
        $encabezados = [
            'A1' => 'Prenumero',
            'B1' => 'Nombre',
            'C1' => 'Pago',
        ];


        foreach ($encabezados as $celda => $texto) {
            $sheet->setCellValue($celda, $texto);
        }

        // Agregar datos
        $fila = 2;
        foreach ($pagos as $pago) {
            $sheet->setCellValue("A{$fila}", $pago['prenumero'] ?? 'N/A');
            $sheet->setCellValue("B{$fila}", $pago['prenombre'] ?? 'N/A');
            $sheet->setCellValue("C{$fila}", $pago['CrMoValor'] ?? 'N/A');
            $fila++;
        }

        // Enviar archivo al cliente
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ReportePagos.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }



}
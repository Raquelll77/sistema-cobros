<?php include_once __DIR__ . '/../principal/header-dashboard.php' ?>

<div class="contenedor">
    <div class="contenedor-95">
        <h1 class="ui header center aligned">Reporte de Gestiones Diario<? //php echo $titulo ?? 'Reporte Gestiones'; ?>
        </h1>
        <div class="grid-2">
            <article>

                <!-- Formulario para seleccionar la fecha -->
                <form method="POST" action="/reportes-gestiones">
                    <div class="campo">
                        <label for="fecha">Seleccione una fecha:</label>
                        <input type="date" id="fecha" name="fecha"
                            value="<?php echo htmlspecialchars($fechaSeleccionada ?? date('Y-m-d')); ?>" required>
                    </div>
                    <input class="boton-submit" type="submit" value="Ver Reporte">
                </form>

                <!-- Tabla para mostrar los datos -->
                <?php if (!empty($reporteGestionDiaria)):
                    // Cálculo del total dinámico
                    $sumaTotal = array_reduce($reporteGestionDiaria, function ($carry, $gestion) {
                        return $carry + (float) $gestion['total'];
                    }, 0); ?>
                    <table class="ui celled striped cyan table" border="1" cellpadding="5" cellspacing="0">
                        <thead>
                            <tr>
                                <th>GESTOR</th>
                                <th>PP</th>
                                <th>CF</th>
                                <th>DEC</th>
                                <th>PRP</th>
                                <th>DAR</th>
                                <th>RLL</th>
                                <th>SMS</th>
                                <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reporteGestionDiaria as $gestion): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($gestion['gestor']); ?></td>
                                    <td><?php echo htmlspecialchars($gestion['PRONTO PAGO']); ?></td>
                                    <td><?php echo htmlspecialchars($gestion['CF']); ?></td>
                                    <td><?php echo htmlspecialchars($gestion['DEC']); ?></td>
                                    <td><?php echo htmlspecialchars($gestion['PRP']); ?></td>
                                    <td><?php echo htmlspecialchars($gestion['DAR']); ?></td>
                                    <td><?php echo htmlspecialchars($gestion['RLL']); ?></td>
                                    <td><?php echo htmlspecialchars($gestion['SMS']); ?></td>
                                    <td><?php echo htmlspecialchars($gestion['total']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <th colspan="8">Suma Total:</th>
                            <th><?php echo number_format($sumaTotal); ?></th>
                        </tfoot>
                    </table>
                <?php else: ?>
                    <p>No hay gestiones para la fecha seleccionada.</p>
                <?php endif; ?>
            </article>
            <article>
                <canvas id="graficoGestiones" width="400" height="200" style="margin-top: 20px;"></canvas>
            </article>
            <article style=" width: 50%; margin: 0 auto;">
                <h1>Resumen gestion</h1>
                <canvas id="graficoPastel" width="150" height="150" style="margin-top: 20px;"></canvas>
            </article>

            <article style=" width: 50%; margin: 0 auto;">
                <h1>Codigo de Resultado</h1>
                <canvas id="graficoCodigos" width="400" height="200" style="margin-top: 20px;"></canvas>
            </article>



        </div>

        <h1>Historico Gestiones</h1>

        <article>
            <form id="form-descargar" action="/descargar-gestiones" method="POST">
                <div class="grid-3">
                    <div class="campo">
                        <label for="">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio">
                    </div>
                    <div class="campo">
                        <label for="">Fecha Final</label>
                        <input type="date" name="fecha_fin">
                    </div>
                    <button id="btn-descargar" class="boton-excel" type="submit"><i
                            class="file alternate outline icon"></i>Descargar
                        Excel</button>
                </div>
            </form>
            <?php if (!empty($errores)): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errores Encontrados',
                            html: `
                        <ul style="text-align: left;">
                            <?php foreach ($errores as $error): ?>
                                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    `,
                            confirmButtonText: 'Aceptar'
                        });
                    });
                </script>
            <?php endif; ?>


        </article>

    </div>


</div>
</div>

<?php include_once __DIR__ . '/../principal/footer-dashboard.php' ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Gráfico de Barras
        const ctxBar = document.getElementById('graficoGestiones').getContext('2d');
        const gestores = <?php echo json_encode($datosProcesados['gestores']); ?>;
        const totales = <?php echo json_encode($datosProcesados['totales']); ?>;

        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: gestores,
                datasets: [{
                    label: 'Gestiones Totales',
                    data: totales,
                    backgroundColor: 'rgba(0, 191, 255, 0.2)',
                    borderColor: 'rgba(0, 191, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 60 // Eje Y configurado para un máximo de 60 gestiones
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        // Gráfico de Pastel Global
        const ctxPie = document.getElementById('graficoPastel').getContext('2d');
        const pastelLabels = ['Realizadas', 'Pendientes'];
        const pastelData = [
            <?php echo $datosProcesados['totalGestiones']; ?>,
            <?php echo (60 * count($datosProcesados['gestores'])) - $datosProcesados['totalGestiones']; ?>
        ];

        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: pastelLabels,
                datasets: [{
                    data: pastelData,
                    backgroundColor: ['rgba(0, 191, 255, 0.8)', 'rgba(191, 191, 191, 0.8)']
                }]
            }
        });

        // Gráfico de Pastel por Código
        const ctxCodigos = document.getElementById('graficoCodigos').getContext('2d');
        const pastelLabelsCodigos = <?php echo json_encode(array_keys($datosProcesados['codigoResultados'])); ?>;
        const pastelDataCodigos = <?php echo json_encode(array_values($datosProcesados['codigoResultados'])); ?>; // Totales en lugar de porcentajes

        new Chart(ctxCodigos, {
            type: 'doughnut',
            data: {
                labels: pastelLabelsCodigos,
                datasets: [{
                    data: pastelDataCodigos, // Usa los totales en lugar de porcentajes
                    backgroundColor: [
                        'rgba(0, 191, 255, 0.8)',
                        'rgba(191, 64, 191, 0.8)',
                        'rgba(64, 191, 64, 0.8)',
                        'rgba(255, 191, 64, 0.8)',
                        'rgba(64, 64, 191, 0.8)',
                        'rgba(191, 64, 64, 0.8)',
                        'rgba(191, 191, 64, 0.8)'
                    ]
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            // Cambia el texto del tooltip para mostrar totales
                            label: function (tooltipItem) {
                                const label = tooltipItem.label || '';
                                const value = tooltipItem.raw || 0; // Obtiene el valor del dataset
                                return `${label}: ${value} gestiones`; // Muestra el total de gestiones
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    });
</script>
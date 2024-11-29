<?php include_once __DIR__ . '/../principal/header-dashboard.php' ?>



<div class="contenedor">
    <div class="contenedor-95">
        <form id="download-form" action="/reportes-deterioro" method="post">
            <button class="boton-excel" type="submit"><i class="file alternate outline icon"
                    onclick="descargarReporte()"></i>Descargar
                Excel</button>
        </form>
        <h1 class="ui header center aligned">Deterioro Mes Actual</h1>
        <canvas id="graficaDeterioro" width="400" height="200"></canvas>

        <br>

        <h1 class="ui header center aligned">Deterioro de Cartera por Gestor y Segmento</h1>
        <canvas id="graficaDeterioro2" width="400" height="200"></canvas>
    </div>
</div>

<?php include_once __DIR__ . '/../principal/footer-dashboard.php' ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"
    integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css"
    integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Obtener los datos de PHP
    const datosGrafica = <?php echo json_encode($datosGrafica); ?>;

    // Configurar la gráfica
    const ctx = document.getElementById('graficaDeterioro').getContext('2d');
    const graficaDeterioro = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: datosGrafica.labels,
            datasets: datosGrafica.datasets
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });


    // Obtener los datos de PHP
    const datosGrafica2 = <?php echo json_encode($datosGrafica2); ?>;

    // Configurar la gráfica
    const ctx2 = document.getElementById('graficaDeterioro2').getContext('2d');
    const config = {
        type: 'bar',
        data: datosGrafica2,
        options: {
            /* plugins: {
                title: {
                    display: true,
                    text: 'Deterioro de Cartera por Gestor y Segmento'
                },
            }, */
            responsive: true,
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            }
        }
    };
    new Chart(ctx2, config);


    document.getElementById('download-form').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevenir el envío normal del formulario

        Swal.fire({
            title: 'Generando archivo...',
            text: 'Por favor, espera mientras se procesa la descarga.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading(); // Mostrar spinner
            }
        });

        // Realizar la solicitud AJAX
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor.');
                }
                return response.blob();
            })
            .then(blob => {
                Swal.close(); // Cerrar la alerta de carga

                // Crear un enlace temporal para descargar el archivo
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'Reporte_Deterioro.xlsx'; // Nombre del archivo a descargar
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);

                // Mostrar alerta de éxito
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'El archivo se ha descargado correctamente.'
                });
            })
            .catch(error => {
                Swal.close(); // Cerrar la alerta de carga
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            });
    });



</script>
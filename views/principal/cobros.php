<div class="contenedor">


    <!-- Pestañas -->
    <div class="tabs">
        <button class="tab-button <?= $tab === 'busqueda-clientes' ? 'active' : '' ?>"
            data-tab="busqueda-clientes">Busqueda Clientes</button>
        <button class="tab-button <?= $tab === 'clientes-asignados' ? 'active' : '' ?>"
            data-tab="clientes-asignados">Clientes Asignados</button>



    </div>



    <div class="tab-content <?= $tab === 'busqueda-clientes' ? 'active' : '' ?>" id="busqueda-clientes">

        <h1 class="titulo-pagina">Busqueda de Clientes</h1>

        <form action="" method="POST" class="contenedor-95">
            <input type="hidden" name="tab" id="hidden-tab" value="<?= $tab ?>">
            <div class="contenido">
                <div class="campo">
                    <label for="identidad">Busqueda por identidad</label>
                    <input type="text" maxlength="14" name="identidad" id="identidad" placeholder="ejem: 0403199809081">
                </div>
                <div class="campo">
                    <label for="nombre">Busqueda por nombre</label>
                    <input type="text" maxlength="30" name="nombre" id="nombre"
                        placeholder="ejem: Maria Cristina Gonzales">
                </div>
                <div class="campo">
                    <label for="prenumero">Busqueda por Prestamo</label>
                    <input type="text" maxlength="14" name="prenumero" id="prenumero"
                        placeholder="ejem: 01022000209232">
                </div>
            </div>

            <input class="boton-submit" type="submit" id="buscar" value="Buscar" name="buscar">

        </form>


        <div class="tabla-contenedor">
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!empty($prestamos)) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>CardCode</th>
                                <th>Nombre</th>
                                <th>Identidad</th>
                                <th>PreNumero</th>
                                <th>Fecha de Aprobacion</th>
                                <th>Estatus</th>
                                <th>Comentario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prestamos as $prestamo): ?>
                                <tr class="clickable-row"
                                    data-href="/prestamos/detalle?prenumero=<?php echo urlencode($prestamo->PreNumero); ?>&identidad=<?php echo urlencode($prestamo->ClNumID); ?>&fecha=<?php echo urlencode($prestamo->PreFecAprobacion); ?>&tab=busqueda-clientes">
                                    <td><?php echo htmlspecialchars($prestamo->ClReferencia); ?></td>
                                    <td><?php echo htmlspecialchars($prestamo->PreNombre); ?></td>
                                    <td><?php echo htmlspecialchars($prestamo->ClNumID); ?></td>
                                    <td><?php echo htmlspecialchars($prestamo->PreNumero); ?></td>
                                    <td><?php echo htmlspecialchars($prestamo->PreFecAprobacion); ?></td>
                                    <td><?php echo htmlspecialchars($prestamo->PreSalCapital); ?></td>
                                    <td><?php echo htmlspecialchars($prestamo->PreComentario); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p>No se encontraron resultados.</p>
                <?php }
            } ?>
        </div>

    </div>

    <div class="tab-content <?= $tab === 'clientes-asignados' ? 'active' : '' ?>" id="clientes-asignados">
        <h1 class="text-second">Clientes Asignados</h1>
        <div class="tabla-contenedor">

            <?php if (!empty($prestamoXGestor)) { ?>
                <table id="clientes-asignados-table" class="display">
                    <thead>
                        <tr>
                            <th>CardCode</th>
                            <th>Nombre</th>
                            <th>Identidad</th>
                            <th>PreNúmero</th>
                            <th>Fecha de Aprobación</th>
                            <th>Estatus</th>
                            <th>Comentario</th>
                            <th>Cod Resultado</th>
                            <th>Fecha Revisión</th>
                            <th>Pagos</th>
                            <th>Atraso</th>
                            <th>Cuotas Atraso</th>
                            <th>Fecha de Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prestamoXGestor as $prestamo): ?>
                            <tr class="clickable-row"
                                data-href="/prestamos/detalle?prenumero=<?php echo urlencode($prestamo['PreNumero']); ?>&identidad=<?php echo urlencode($prestamo['ClNumID']); ?>&fecha=<?php echo urlencode($prestamo['PreFecAprobacion']); ?>&tab=clientes-asignados">
                                <td><?php echo htmlspecialchars($prestamo['ClReferencia']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['PreNombre']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['ClNumID']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['PreNumero']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['PreFecAprobacion']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['PreSalCapital']); ?></td>
                                <td><?php echo htmlspecialchars(substr($prestamo['PreComentario'], 0, 55)) . (strlen($prestamo['PreComentario']) > 55 ? '...' : ''); ?>
                                </td>
                                <td><?php echo htmlspecialchars($prestamo['codigo_resultado']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['fecha_revision']); ?></td>
                                <td><?php echo number_format($prestamo['total_pagos_mes_actual'], 2); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['MaxDiasAtraso']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['CuotasEnAtraso']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['DiaPagoCuota']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No se encontraron resultados.</p>
            <?php } ?>
        </div>
    </div>





</div>

<?php include_once 'footer-dashboard.php' ?>

<script src="/build/js/tabs.js"></script>
<script src="/build/js/app.js"></script>



<!-- Incluir DataTables y el script -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>

    // Inicializar DataTables
    const table = $('#clientes-asignados-table').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "responsive": true,
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros en total)",
            "search": "Buscar:",
            "pageLength": 25,
            "paginate": {
                "previous": "Anterior",
                "next": "Siguiente"
            }
        }
    });

    // Asignar evento de clic a las filas después de inicializar DataTables
    $('#clientes-asignados-table tbody').on('click', 'tr.clickable-row', function () {
        const href = $(this).data('href');
        if (href) {
            window.location.href = href; // Redirigir al enlace especificado en data-href
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Cambiar la pestaña activa al hacer clic
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function () {
                const targetTab = this.getAttribute('data-tab');
                // Actualizar URL sin recargar
                const url = new URL(window.location.href);
                url.searchParams.set('tab', targetTab);
                window.history.replaceState({}, '', url);

                // Cambiar la pestaña activa
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
            });
        });
    });


</script>
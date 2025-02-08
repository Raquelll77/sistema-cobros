<?php
include_once 'header-dashboard.php';
include_once __DIR__ . '/../../includes/menu.php';
?>


<h1 class="titulo-pagina">¿Qué desea hacer?</h1>
<main class="contenido">

    <?php if (in_array('/reportes', array_column($opcionesMostrar, 'ruta'))): ?>
        <a href="/reportes" class="contenido-seccion">
            <img src="/build/img/reporte-gestiones.png" alt="cartera-reportes">
            <p class="texto-contenido">Reportes</p>
        </a>
    <?php endif; ?>

    <?php if (in_array('/cobros', array_column($opcionesMostrar, 'ruta'))): ?>
        <a href="/cobros" class="contenido-seccion">
            <img src="/build/img/reporte-recuperacion.png" alt="cartera-cobro">
            <p class="texto-contenido">Cobros</p>
        </a>
    <?php endif; ?>

    <?php if (in_array('/configuracion', array_column($opcionesMostrar, 'ruta'))): ?>
        <a href="/configuracion" class="contenido-seccion">
            <img src="/build/img/reporte-deterioro.png" alt="cartera-gestion">
            <p class="texto-contenido">Configuración</p>
        </a>
    <?php endif; ?>

</main>

<?php include_once 'footer-dashboard.php'; ?>
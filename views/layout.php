<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistema de Cobros | <?= $titulo ?? '' ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Open+Sans&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="/build/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css">
</head>

<body>
    <?php
    if (session_status() === PHP_SESSION_NONE)
        session_start();
    $hideChrome = $hideChrome ?? false; // viene del render del controlador
    ?>

    <?php if (!$hideChrome): ?>
        <?php include __DIR__ . '/principal/header-dashboard.php'; ?>
    <?php endif; ?>

    <?= $contenido ?? '' ?>
    <?= $script ?? '' ?>

    <!-- JS: carga una sola vez y en orden correcto -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Si usas DataTables, incluye su CSS/JS una sola vez -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        // Inicialización global segura
        $(function () {
            $('.ui.dropdown').dropdown();
            $('.ui.checkbox').checkbox();
        });
    </script>

    <?php if (!$hideChrome): ?>
        <?php include __DIR__ . '/principal/footer-dashboard.php'; ?>
    <?php endif; ?>
</body>

</html>
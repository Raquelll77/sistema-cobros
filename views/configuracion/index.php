<?php include_once __DIR__ . '/../principal/header-dashboard.php'; ?>

<div class="contenedor">
    <div class="contenedor-95">
        <h1 class="ui header">Subir creditos por gestor</h1>

        <form id="upload-form" action="configuracion-upload" method="post" enctype="multipart/form-data"
            class="ui form">
            <div class="field">
                <label for="file">Selecciona el archivo Excel:</label>
                <input type="file" name="file" id="file" accept=".xlsx, .xls, .csv" required>
            </div>
            <button type="submit" class="ui primary button">
                <i class="upload icon"></i>
                Subir
            </button>
        </form>
    </div>
</div>

<!-- Incluye el CSS de SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<!-- Incluye el archivo JS de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('upload-form').addEventListener('submit', function () {
        // Mostrar SweetAlert de carga antes de enviar el formulario
        Swal.fire({
            title: 'Subiendo archivo...',
            text: 'Por favor, espera mientras se procesan los datos.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading(); // Mostrar spinner
            }
        });
    });

    // Mostrar mensajes después del procesamiento
    <?php if (!empty($message)): ?>
        Swal.fire({
            icon: '<?= $status === "success" ? "success" : "error" ?>',
            title: '<?= $status === "success" ? "¡Éxito!" : "Error" ?>',
            text: '<?= htmlspecialchars($message) ?>'
        });
    <?php endif; ?>
</script>

<?php include_once __DIR__ . '/../principal/footer-dashboard.php'; ?>
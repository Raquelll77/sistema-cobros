<?php include_once __DIR__ . '/../../includes/menu.php'; ?>

<aside class="sidebar">
    <a href="/principal">
        <h1>SKG</h1>
    </a>
    <nav class="sidebar-nav">
        <?php if (!empty($opcionesMostrar) && is_array($opcionesMostrar)): ?>
            <?php foreach ($opcionesMostrar as $opcion): ?>
                <a href="<?php echo htmlspecialchars($opcion['ruta']); ?>">
                    <i class="<?php echo htmlspecialchars($opcion['icono']); ?>" style="visibility: visible;"></i>
                    <?php echo htmlspecialchars($opcion['texto']); ?>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tienes accesos asignados.</p>
        <?php endif; ?>
    </nav>
</aside>
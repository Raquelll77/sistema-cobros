<?php include_once __DIR__ . '/../../includes/menu.php'; ?>

<?php include_once __DIR__ . '/../../includes/menu.php'; ?>

<aside class="sidebar">
    <a href="/principal">
        <h1>SKG</h1>
    </a>
    <nav class="sidebar-nav">
        <?php if (!empty($opcionesMostrar) && is_array($opcionesMostrar)): ?>
            <?php foreach ($opcionesMostrar as $opcion): ?>
                <?php
                // Verifica si la ruta actual contiene la ruta del menú o la subruta
                $activeClass = (strpos($_SERVER['REQUEST_URI'], $opcion['ruta']) !== false) ? 'active' : '';
                ?>
                <a href="<?php echo htmlspecialchars($opcion['ruta']); ?>" class="<?php echo $activeClass; ?>">
                    <i class="<?php echo htmlspecialchars($opcion['icono']); ?>" style="visibility: visible;"></i>
                    <?php echo htmlspecialchars($opcion['texto']); ?>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tienes accesos asignados.</p>
        <?php endif; ?>
    </nav>
</aside>
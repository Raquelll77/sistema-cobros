<aside class="sidebar">
    <a href="/principal">
        <h1>SKG</h1>
    </a>
    <nav class="sidebar-nav">
        <?php if ($_SESSION['rol'] === 'TELECOBRO'): ?>
            <a href="/cobros"><i class="hand holding usd icon" style="visibility: visible;"></i>Cobros</a>
        <?php else: ?>
            <a href="/reportes"><i class="file alternate outline icon" style="visibility: visible;"></i>Reportes</a>
            <a href="/cobros"><i class="hand holding usd icon" style="visibility: visible;"></i>Cobros</a>
            <a href="/configuracion"><i class="cogs icon" style="visibility: visible;"></i>Configuracion</a>
        <?php endif; ?>
    </nav>
</aside>
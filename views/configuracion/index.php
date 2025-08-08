<?php
$menuOpciones = [
    'TELECOBRO' => [

    ],
    'SUPERVISOR' => [
        // ['ruta' => '/reportes', 'icono' => 'file alternate outline icon', 'texto' => 'Reportes'],
        // ['ruta' => '/cobros', 'icono' => 'hand holding usd icon', 'texto' => 'Cobros'],
        ['ruta' => '/subir_creditos', 'icono' => 'upload black icon', 'texto' => 'Subir Creditos X Gestor'],
    ],
    'ADMIN' => [
        ['ruta' => '/subir_creditos', 'icono' => 'upload black icon', 'texto' => 'Subir Creditos X Gestor'],
    ]
];

// Obtener el rol actual del usuario
$rolUsuario = $_SESSION['rol'] ?? 'INVITADO';

// Determinar qué opciones del menú mostrar según el rol
$opcionesMostrar = $menuOpciones[$rolUsuario] ?? [];


?>
<main class="contenido">

    <?php foreach ($opcionesMostrar as $opcion): ?>
        <a href="<?= $opcion['ruta'] ?>" class="contenido-seccion">
            <i class="<?= $opcion['icono'] ?> icon" style="font-size: 8rem; margin-bottom: 1rem;"></i>
            <p class="texto-contenido"><?= $opcion['texto'] ?></p>
        </a>
    <?php endforeach; ?>

</main>
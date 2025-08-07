<?php

function debuguear($variable): string
{
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html): string
{
    $s = htmlspecialchars($html);
    return $s;
}

// Función que revisa que el usuario este autenticado
function isAuth(): void
{
    if (!isset($_SESSION['login'])) {
        // Detectar si es una petición AJAX o fetch()
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
        $acceptsJson = strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

        if ($isAjax || $acceptsJson) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'expired',
                'mensaje' => 'Sesión expirada. Por favor inicia sesión de nuevo.'
            ]);
        } else {
            header('Location: /');
        }

        exit;
    }
}

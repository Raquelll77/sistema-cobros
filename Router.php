<?php

namespace MVC;
use Middlewares\AuthMiddleware;

class Router
{
    public $getRoutes = [];
    public $postRoutes = [];

    public function get($url, $fn, $roles = [])
    {
        $this->getRoutes[$url] = ['fn' => $fn, 'roles' => $roles];
    }

    public function post($url, $fn, $roles = [])
    {
        $this->postRoutes[$url] = ['fn' => $fn, 'roles' => $roles];
    }

    public function comprobarRutas()
    {
        // Obtener la URL actual y eliminar parámetros de consulta
        $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
        $currentUrl = strtok($currentUrl, '?'); // Elimina los parámetros GET

        // Elimina el prefijo `/public` si está presente
        $currentUrl = str_replace('/portal_cobros/public', '', $currentUrl);

        $method = $_SERVER['REQUEST_METHOD'];

        // Determinar si la ruta está en GET o POST
        if ($method === 'GET') {
            $ruta = $this->getRoutes[$currentUrl] ?? null;
        } else {
            $ruta = $this->postRoutes[$currentUrl] ?? null;
        }

        if ($ruta) {
            $fn = $ruta['fn']; // La función a ejecutar
            $rolesPermitidos = $ruta['roles']; // Los roles permitidos

            // Si la ruta tiene restricciones de rol, verificar antes de ejecutar
            if (!empty($rolesPermitidos)) {
                AuthMiddleware::verificarRol($rolesPermitidos);
            }

            // Verificar que la función es un callback válido antes de ejecutarla
            if (is_callable($fn)) {
                call_user_func($fn, $this);
            } else {
                echo "Error: La ruta no tiene una función válida en Router.php";
            }
        } else {
            echo "Página no encontrada";
        }
    }

    public function render($view, $datos = [])
    {
        // Leer lo que le pasamos a la vista
        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        ob_start(); // Almacena el buffer temporalmente

        // Incluir la vista dentro del layout
        include_once __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean(); // Limpia el Buffer
        include_once __DIR__ . '/views/layout.php';
    }
}

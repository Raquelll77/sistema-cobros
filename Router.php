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

        if ($method === 'GET') {
            $fn = $this->getRoutes[$currentUrl] ?? null;
        } else {
            $fn = $this->postRoutes[$currentUrl] ?? null;
        }

        if ($fn) {
            $rolesPermitidos = $fn['roles'];
            if (!empty($rolesPermitidos)) {
                AuthMiddleware::verificarRol($rolesPermitidos);
            }
            call_user_func($fn['fn'], $this);
        } else {
            echo "Página no encontrada";
        }
    }


    public function render($view, $datos = [])
    {

        // Leer lo que le pasamos  a la vista
        foreach ($datos as $key => $value) {
            $$key = $value;  // Doble signo de dolar significa: variable variable, básicamente nuestra variable sigue siendo la original, pero al asignarla a otra no la reescribe, mantiene su valor, de esta forma el nombre de la variable se asigna dinamicamente
        }

        ob_start(); // Almacenamiento en memoria durante un momento...

        // entonces incluimos la vista en el layout
        include_once __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean(); // Limpia el Buffer
        include_once __DIR__ . '/views/layout.php';
    }
}

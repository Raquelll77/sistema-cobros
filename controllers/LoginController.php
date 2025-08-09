<?php
namespace Controllers;

use Model\Usuario;
use MVC\Router;

class LoginController
{

    public static function login(Router $router)
    {
        Usuario::useSQLSrv(); // Configurar la conexión a SQL Server
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $auth = new Usuario($_POST);

            // Validar los datos enviados
            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                // Verificar que el usuario exista
                $usuario = Usuario::where('usuario', $auth->usuario);

                if (!$usuario) {
                    // Si el usuario no existe, generar una alerta
                    Usuario::setAlerta('error', 'El usuario no existe');
                } else {
                    // Verificar la contraseña solo si el usuario existe
                    if ($_POST['password'] === $usuario->password) {
                        // Iniciar sesión y guardar los datos del usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['usuario'] = $usuario->usuario;
                        $_SESSION['rol'] = $usuario->rol;
                        $_SESSION['login'] = true;

                        // Redirigir al panel principal
                        if ($usuario->rol === 'TELECOBRO') {
                            header('Location: /cobros');
                            exit;
                        }
                        header('Location: /principal');
                        exit;
                    } else {
                        // Contraseña incorrecta
                        Usuario::setAlerta('error', 'Password incorrecto');
                    }
                }
            }
        }

        // Obtener las alertas generadas
        $alertas = Usuario::getAlertas();

        // Renderizar la vista de inicio de sesión
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesion',
            'alertas' => $alertas,
            'hideChrome' => true
        ]);
    }

    public static function logout()
    {
        // Cerrar la sesión
        session_start();
        $_SESSION = [];
        header('Location: /');
    }
}

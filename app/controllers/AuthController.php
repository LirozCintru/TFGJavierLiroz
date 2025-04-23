<?php
require_once RUTA_APP . '/models/Usuario.php';
require_once RUTA_APP . '/config/configurar.php';

class AuthController {
    public function login() {
        require RUTA_APP . '/views/auth/login.php';
    }

    public function procesarLogin() {
        global $pdo;

        $email = $_POST['email'];
        $password = $_POST['password'];

        $usuario = Usuario::buscarPorEmail($pdo, $email);

        var_dump($usuario); 

        if ($usuario && password_verify($password, $usuario['contrasena'])) {
            $_SESSION['usuario'] = [
                'id' => $usuario['id_usuario'],
                'nombre' => $usuario['nombre'],
                'rol' => $usuario['id_rol'],
                'departamento' => $usuario['id_departamento']
            ];
            // Redirección corregida (absoluta desde raíz del servidor web)
            header('Location: /TFGJavierLiroz/public/index.php?url=dashboard/index');
            exit;
        } else {
            $error = "Email o contraseña incorrectos";
            require RUTA_APP . '/views/auth/login.php';
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /TFGJavierLiroz/public/index.php?url=auth/login');
        exit;
    }
}

<?php
require_once 'models/Usuario.php';
require_once 'config/config.php';
require_once '../app/helpers/Auth.php';

class AuthController {
    public function login() {
        require 'views/auth/login.php';
    }

    public function procesarLogin() {
        global $pdo;

        $email = $_POST['email'];
        $password = $_POST['password'];

        $usuario = Usuario::buscarPorEmail($pdo, $email);

        if ($usuario && password_verify($password, $usuario['contrasena'])) {
            $_SESSION['usuario'] = [
                'id' => $usuario['id_usuario'],
                'nombre' => $usuario['nombre'],
                'rol' => $usuario['id_rol'],
                'departamento' => $usuario['id_departamento']
            ];
            header('Location: index.php?controller=dashboard&action=index');
        } else {
            $error = "Email o contraseña incorrectos";
            require 'views/auth/login.php';
        }
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?controller=auth&action=login');
    }
}

<?php
// require(RUTA_APP . '/core/funciones.php');

class Logins extends Controlador
{

    public function __construct()
    {
        // Acceso al modelo
        $this->modeloUsuario = $this->modelo('Usuario');
    }

    public function index()
    {
        $this->vista('/auth/loginVista');
    }
    public function acceder()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            session_start();

            $email = trim($_POST['email']);
            $contrasena = trim($_POST['contrasena']);

            $usuarioDB = $this->modeloUsuario->iniciarSesion([
                'email' => $email,
                'contrasena' => $contrasena
            ]);

            if ($usuarioDB) {
                $_SESSION['usuario'] = $usuarioDB;
                header('Location: ' . RUTA_URL . '/logins/dashboardVista');
                exit();
            } else {
                $datos['error'] = "Credenciales incorrectas.";
                $this->vista('auth/loginVista', $datos);
            }
        } else {
            header('Location: ' . RUTA_URL . '/logins');
        }
    }

    public function dashboard()
    {
        session_start();
        if (!isset($_SESSION['usuario'])) {
            header('Location: ' . RUTA_URL . '/logins');
            exit();
        }

        $datos = $_SESSION['usuario'];
        $this->vista('dashboard/dashboard', $datos);
    }


    public function salir()
    {
        session_start();
        session_destroy();
        header('Location: ' . RUTA_URL . '/logins');
    }
}

<?php
require(RUTA_APP . '/librerias/Funciones.php');
require_once RUTA_APP . '/config/roles.php';

class LoginsControlador extends Controlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = $this->modelo('LoginModelo');
    }


    public function index()
    {
        session_start();

        // Solo redirige automáticamente si NO viene del restablecimiento
        if (isset($_SESSION['usuario']) && empty($_SESSION['token_restablecer'])) {
            header("Location: " . RUTA_URL . "/ContenidoControlador/inicio");
            exit;
        }

        // Si viene del restablecimiento, simplemente se carga la vista de login
        $this->vista('/login/loginVista');
    }


    public function inicio()
    {
        verificarSesionActiva();
        $this->vista('ContenidoControlador/inicio');
    }

    public function acceder()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            session_start();
            $isValid = true;

            $email = isset($_POST['email']) ? test_input($_POST['email']) : '';
            $contrasena = isset($_POST['contrasena']) ? test_input($_POST['contrasena']) : '';

            $datos = [
                'email' => $email,
                'contrasena' => $contrasena,
                'errorEmail' => '',
                'errorContrasena' => '',
                'errorUnique' => ''
            ];

            if (empty($email)) {
                $isValid = false;
                $datos['errorEmail'] = "Email necesario";
            }
            if (empty($contrasena)) {
                $isValid = false;
                $datos['errorContrasena'] = "Contraseña obligatoria";
            }

            if ($isValid) {

                $usuario = $this->modelo->iniciarSesion($datos);

                if ($usuario) {
                    // Guardar información relevante en la sesión
                    $_SESSION['usuario'] = [
                        'id' => $usuario->id_usuario,
                        'nombre' => $usuario->nombre,
                        'email' => $usuario->email,
                        'id_rol' => $usuario->id_rol,
                        'nombre_rol' => $usuario->nombre_rol,
                        'id_departamento' => $usuario->id_departamento,
                        'imagen' => $usuario->imagen ?? 'default.png'
                    ];
                    unset($_SESSION['token_restablecer']);

                    // Redirigir según rol (dashboards)
                    // if ($usuario->id_rol == 1) {
                    //     header("Location: " . RUTA_URL . "/admin/panel");
                    // } else {
                    //     header("Location: " . RUTA_URL . "/contenido/inicio");
                    // }

                    header("Location: " . RUTA_URL . "/ContenidoControlador/inicio");
                    exit;
                } else {
                    $datos['errorUnique'] = "Usuario o contraseña incorrectos.";
                    $this->vista('login/loginVista', $datos);
                }
            } else {
                $this->vista('login/loginVista', $datos);
            }
        } else {
            $this->vista('login/loginVista');
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

    public function panelAdmin()
    {
        verificarRol([ROL_ADMIN]); // Solo admin
        $this->vista('admin/panel');
    }

    public function salir()
    {
        session_start();
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
        header('Location: ' . RUTA_URL . '/logins');
    }

}

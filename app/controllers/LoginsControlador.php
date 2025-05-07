<?php
require(RUTA_APP . '/librerias/Funciones.php');
require_once RUTA_APP . '/config/roles.php';

class LoginsControlador extends Controlador
{
    public function __construct()
    {
        // Acceso al modelo
        $this->modelo = $this->modelo('LoginModelo');
    }

    public function index()
    {
        session_start();
        if (isset($_SESSION['usuario'])) {
            header("Location: " . RUTA_URL . "/contenido/inicio");
            exit;
        }

        $this->vista('/login/loginVista');
    }

    public function inicio()
    {
        verificarSesionActiva();
        $this->vista('contenido/inicio');
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

                    // Redirigir según rol (si quieres diferenciar dashboards)
                    // if ($usuario->id_rol == 1) {
                    //     header("Location: " . RUTA_URL . "/admin/panel");
                    // } else {
                    //     header("Location: " . RUTA_URL . "/contenido/inicio");
                    // }

                    header("Location: " . RUTA_URL . "/contenido/inicio");
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
        session_destroy();
        header('Location: ' . RUTA_URL . '/logins');
    }
}

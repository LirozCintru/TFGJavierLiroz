<?php

class RestablecerControlador extends Controlador
{
    private $usuarioModelo;

    public function __construct()
    {
        $this->usuarioModelo = $this->modelo('UsuarioModelo');
    }

    public function clave()
    {
        session_start(); // Asegurarse de que la sesión esté iniciada

        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $_SESSION['mensaje_error'] = "Token no proporcionado.";
            redireccionar('/loginsControlador/index');
        }

        $db = new DataBase();
        $db->query("SELECT * FROM tokens_restablecer WHERE token = :token LIMIT 1");
        $db->bind(':token', $token);
        $registro = $db->registro();

        if (!$registro) {
            $_SESSION['mensaje_error'] = "Este enlace ya fue utilizado o no es válido.";
            redireccionar('/loginsControlador/index');
        }

        if (strtotime($registro->expiracion) < time()) {
            $_SESSION['mensaje_error'] = "Este enlace ha caducado. Solicita uno nuevo desde el login.";
            redireccionar('/loginsControlador/index');
        }

        // Si hay sesión de otro usuario, destruirla completamente
        if (isset($_SESSION['usuario']) && $_SESSION['usuario']['id'] != $registro->id_usuario) {
            session_unset();
            session_destroy();
            setcookie(session_name(), '', time() - 3600, '/');
            session_start();
            $_SESSION['mensaje_error'] = "Se ha cerrado tu sesión actual para establecer una nueva contraseña.";
            header("Location: " . RUTA_URL . "/RestablecerControlador/clave?token=" . urlencode($token));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nueva = $_POST['contrasena'] ?? '';
            $repetir = $_POST['repetir_contrasena'] ?? '';
            $errores = [];

            if (strlen($nueva) < 6) {
                $errores[] = "La contraseña debe tener al menos 6 caracteres.";
            }
            if ($nueva !== $repetir) {
                $errores[] = "Las contraseñas no coinciden.";
            }

            if (!empty($errores)) {
                $this->vista('restablecer/clave', [
                    'errores' => $errores,
                    'token' => $token
                ]);
                return;
            }

            // Actualizar contraseña y eliminar token
            $nuevaHash = password_hash($nueva, PASSWORD_DEFAULT);
            $this->usuarioModelo->actualizarContrasena($registro->id_usuario, $nuevaHash);

            $db->query("DELETE FROM tokens_restablecer WHERE token = :token");
            $db->bind(':token', $token);
            $db->execute();

            // Limpiar sesión completamente
            session_unset();
            session_destroy();
            setcookie(session_name(), '', time() - 3600, '/');
            session_start();

            $_SESSION['mensaje'] = "✅ Contraseña establecida correctamente. Ya puedes iniciar sesión.";
            $_SESSION['token_restablecer'] = true; // evita redirección automática si ya había sesión

            redireccionar('/loginsControlador/index');
        } else {
            $this->vista('restablecer/clave', ['token' => $token]);
        }
    }



}

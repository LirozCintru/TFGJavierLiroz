<?php
require_once RUTA_APP . '/librerias/Funciones.php';

class PerfilControlador extends Controlador
{
    private $usuarioModelo;

    public function __construct()
    {
        verificarSesionActiva();
        $this->usuarioModelo = $this->modelo('UsuarioModelo');
    }

    // Mostrar formulario de edición de perfil
    public function editar()
    {
        verificarSesionActiva();

        $idUsuario = $_SESSION['usuario']['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errores = [];
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $contrasena = $_POST['contrasena'] ?? '';
            $imagen = $_SESSION['usuario']['imagen'];

            // Validación básica
            if (empty($nombre))
                $errores[] = "El nombre no puede estar vacío.";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                $errores[] = "Email inválido.";

            // Validar email único si cambia
            $usuarioExistente = $this->usuarioModelo->obtenerPorEmail($email);
            if ($usuarioExistente && $usuarioExistente->id_usuario != $idUsuario) {
                $errores[] = "El email ya está en uso por otro usuario.";
            }

            // Subida de imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nuevoNombre = 'user_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $ruta = RUTA_PUBLIC . '/img/usuarios/' . $nuevoNombre;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
                    $imagen = $nuevoNombre;
                } else {
                    $errores[] = "⚠️ No se pudo subir la imagen.";
                }
            }

            // Si hay errores, volver con los datos
            if (!empty($errores)) {
                $this->vista('perfil/editar', [
                    'usuario' => $this->usuarioModelo->obtenerPorId($idUsuario),
                    'errores' => $errores,
                    'valores' => $_POST
                ]);
                return;
            }

            // Preparar datos para guardar
            $datosActualizados = [
                'id_usuario' => $idUsuario,
                'nombre' => $nombre,
                'email' => $email,
                'imagen' => $imagen
            ];
            if (!empty($contrasena)) {
                $datosActualizados['contrasena'] = password_hash($contrasena, PASSWORD_DEFAULT);
            }

            // Guardar en la base de datos
            $this->usuarioModelo->actualizarPerfil(
                $idUsuario,
                $datosActualizados,
                !empty($contrasena) ? $datosActualizados['contrasena'] : null,
                ($imagen !== $_SESSION['usuario']['imagen']) ? $imagen : null
            );


            // Actualizar sesión
            $_SESSION['usuario']['nombre'] = $nombre;
            $_SESSION['usuario']['email'] = $email;
            $_SESSION['usuario']['imagen'] = $imagen;

            $_SESSION['perfil_actualizado'] = true;
            redireccionar('/PerfilControlador/editar');
        } else {
            // Mostrar formulario
            $this->vista('perfil/editar', [
                'usuario' => $this->usuarioModelo->obtenerPorId($idUsuario)
            ]);
        }
    }



    // Procesar formulario
    public function actualizar()
    {
        verificarSesionActiva();
        $id_usuario = $_SESSION['usuario']['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $valores = [
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'contrasena' => $_POST['contrasena'],
                'activo' => true, // permanece activo
            ];

            $errores = [];

            // Validación
            if (empty($valores['nombre'])) {
                $errores[] = "El nombre es obligatorio.";
            }

            if (!filter_var($valores['email'], FILTER_VALIDATE_EMAIL)) {
                $errores[] = "El correo electrónico no es válido.";
            } else {
                $usuarioExistente = $this->usuarioModelo->obtenerPorEmail($valores['email']);
                if ($usuarioExistente && $usuarioExistente->id_usuario != $id_usuario) {
                    $errores[] = "El email ya está en uso por otro usuario.";
                }
            }

            // Subida de imagen (opcional)
            $imagenNombre = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['imagen']['tmp_name'];
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombreFinal = 'user_' . date('YmdHis') . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
                $rutaFinal = RUTA_PUBLIC . '/img/usuarios/' . $nombreFinal;

                if (move_uploaded_file($tmp, $rutaFinal)) {
                    $imagenNombre = $nombreFinal;
                } else {
                    $errores[] = "Error al subir la imagen.";
                }
            }

            // Si hay errores, volver
            if (!empty($errores)) {
                $usuario = $this->usuarioModelo->obtenerPorId($id_usuario);
                $this->vista('perfil/editar', [
                    'usuario' => $usuario,
                    'errores' => $errores,
                    'valores' => $valores
                ]);
                return;
            }

            // Guardar
            $this->usuarioModelo->actualizarPerfil($id_usuario, $valores, $valores['contrasena'], $imagenNombre);

            // Actualizar sesión
            $_SESSION['usuario']['nombre'] = $valores['nombre'];
            $_SESSION['usuario']['email'] = $valores['email'];
            if ($imagenNombre) {
                $_SESSION['usuario']['imagen'] = $imagenNombre;
            }
            $_SESSION['perfil_actualizado'] = true;

            redireccionar('/PerfilControlador/editar');
        }
    }
}

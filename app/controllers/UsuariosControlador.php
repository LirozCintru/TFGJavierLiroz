<?php
require_once RUTA_APP . '/librerias/Funciones.php';
require_once RUTA_APP . '/config/roles.php';
class UsuariosControlador extends Controlador
{
    private $usuarioModelo;

    public function __construct()
    {
        verificarSesionActiva();
        $this->usuarioModelo = $this->modelo('UsuarioModelo');
    }

    public function index()
    {
        verificarSesionActiva();

        if ($_SESSION['usuario']['id_rol'] != ROL_ADMIN) {
            redireccionar('/ContenidoControlador/inicio');
        }

        $nombre = $_GET['nombre'] ?? '';
        $departamento = $_GET['departamento'] ?? '';

        $usuarios = $this->usuarioModelo->obtenerFiltrados($nombre, $departamento);
        $departamentos = $this->modelo('DepartamentoModelo')->obtenerTodos();

        $this->vista('usuarios/index', [
            'usuarios' => $usuarios,
            'departamentos' => $departamentos,
            'filtro_nombre' => $nombre,
            'filtro_departamento' => $departamento
        ]);
    }

    public function crear()
    {
        verificarSesionActiva();

        if ($_SESSION['usuario']['id_rol'] != ROL_ADMIN) {
            redireccionar('/ContenidoControlador/seccion/inicio');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errores = [];

            // Validar email único
            if ($this->usuarioModelo->existeEmail($_POST['email'])) {
                $errores[] = "Ya existe un usuario con ese email.";
            }

            // Imagen por defecto
            $imagen = 'default.png';

            // Procesar imagen si se sube
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombre = 'user_' . date('YmdHis') . '_' . bin2hex(random_bytes(5)) . '.' . $extension;
                $ruta = RUTA_PUBLIC . '/img/usuarios/' . $nombre;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
                    $imagen = $nombre;
                } else {
                    $errores[] = "Error al subir la imagen.";
                }
            }

            // Si hay errores, recargar la vista
            if (!empty($errores)) {
                $roles = $this->modelo('RolModelo')->obtenerTodos();
                $departamentos = $this->modelo('DepartamentoModelo')->obtenerTodos();

                $this->vista('usuarios/crear', [
                    'roles' => $roles,
                    'departamentos' => $departamentos,
                    'errores' => $errores,
                    'valores' => $_POST
                ]);
                return;
            }

            // Crear el usuario con contraseña temporal vacía (o con una contraseña temporal)
            $datos = [
                'nombre' => $_POST['nombre'],
                'email' => $_POST['email'],
                'contrasena' => password_hash(bin2hex(random_bytes(5)), PASSWORD_DEFAULT), // contraseña temporal aleatoria
                'id_rol' => $_POST['id_rol'],
                'id_departamento' => $_POST['id_departamento'],
                'activo' => isset($_POST['activo']) ? 1 : 0,
                'imagen' => $imagen
            ];

            $this->usuarioModelo->crear($datos);

            // Obtener el último ID insertado
            $idNuevo = $this->usuarioModelo->obtenerUltimoId();

            // Crear token
            $token = bin2hex(random_bytes(32));
            $expiracion = date('Y-m-d H:i:s', strtotime('+1 day'));

            $db = new DataBase();
            $db->query("INSERT INTO tokens_restablecer (id_usuario, token, expiracion) VALUES (:id_usuario, :token, :exp)");
            $db->bind(':id_usuario', $idNuevo);
            $db->bind(':token', $token);
            $db->bind(':exp', $expiracion);
            $db->execute();

            // Enviar correo con enlace de establecer clave
            require_once RUTA_APP . '/librerias/Correo.php';

            $enlace = RUTA_URL . "/RestablecerControlador/clave?token=$token";
            $mensaje = "Hola <strong>" . htmlspecialchars($datos['nombre']) . "</strong>,<br><br>
        Tu cuenta ha sido creada correctamente en <strong>IntraLink</strong>.<br>
        Para establecer tu contraseña y activar tu cuenta, haz clic en el siguiente botón. Este enlace caduca en 1 hora.";

            Correo::enviarBonito(
                $datos['email'],
                '🔐 Establece tu contraseña en IntraLink',
                $mensaje,
                'Establecer contraseña',
                $enlace
            );

            redireccionar('/UsuariosControlador/index');
        } else {
            $roles = $this->modelo('RolModelo')->obtenerTodos();
            $departamentos = $this->modelo('DepartamentoModelo')->obtenerTodos();

            $this->vista('usuarios/crear', [
                'roles' => $roles,
                'departamentos' => $departamentos
            ]);
        }
    }

    public function editar($id)
    {
        verificarSesionActiva();

        if ($_SESSION['usuario']['id_rol'] != ROL_ADMIN) {
            redireccionar('/ContenidoControlador/seccion/inicio');
        }

        $usuario = $this->usuarioModelo->obtenerPorId($id);
        if (!$usuario) {
            die("Usuario no encontrado.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errores = [];

            if ($this->usuarioModelo->existeEmail($_POST['email'], $id)) {
                $errores[] = "Ese email ya está en uso por otro usuario.";
            }

            $imagen = $usuario->imagen;

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $nombre = 'user_' . date('YmdHis') . '_' . bin2hex(random_bytes(5)) . '.' . $extension;
                $ruta = RUTA_PUBLIC . '/img/usuarios/' . $nombre;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
                    $imagen = $nombre;
                } else {
                    $errores[] = "Error al subir la imagen.";
                }
            }

            if (!empty($errores)) {
                $roles = $this->modelo('RolModelo')->obtenerTodos();
                $departamentos = $this->modelo('DepartamentoModelo')->obtenerTodos();

                $this->vista('usuarios/editar', [
                    'usuario' => $usuario,
                    'roles' => $roles,
                    'departamentos' => $departamentos,
                    'errores' => $errores,
                    'valores' => $_POST
                ]);
                return;
            }

            $datos = [
                'id_usuario' => $id,
                'nombre' => $_POST['nombre'],
                'email' => $_POST['email'],
                'id_rol' => $_POST['id_rol'],
                'id_departamento' => $_POST['id_departamento'],
                'activo' => isset($_POST['activo']) ? 1 : 0,
                'imagen' => $imagen
            ];

            if (!empty($_POST['contrasena'])) {
                $datos['contrasena'] = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
            }

            $this->usuarioModelo->actualizar($datos);
            redireccionar('/UsuariosControlador/index');
        } else {
            $roles = $this->modelo('RolModelo')->obtenerTodos();
            $departamentos = $this->modelo('DepartamentoModelo')->obtenerTodos();

            $this->vista('usuarios/editar', [
                'usuario' => $usuario,
                'roles' => $roles,
                'departamentos' => $departamentos
            ]);
        }
    }



    public function eliminar($id)
    {
        verificarSesionActiva();
        if ($_SESSION['usuario']['id_rol'] != ROL_ADMIN) {
            redireccionar('/ContenidoControlador/seccion/inicio');
        }

        $this->usuarioModelo->eliminar($id);
        redireccionar('/UsuariosControlador/index');
    }


}

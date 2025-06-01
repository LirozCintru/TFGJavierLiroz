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

            // Datos validados
            $datos = [
                'nombre' => $_POST['nombre'],
                'email' => $_POST['email'],
                'contrasena' => password_hash($_POST['contrasena'], PASSWORD_DEFAULT),
                'id_rol' => $_POST['id_rol'],
                'id_departamento' => $_POST['id_departamento'],
                'activo' => isset($_POST['activo']) ? 1 : 0,
                'imagen' => $imagen
            ];

            // Crear en BD
            $this->usuarioModelo->crear($datos);

            // Enviar correo al nuevo usuario
            require_once RUTA_APP . '/librerias/Correo.php';

            $mensaje = "Hola <strong>" . htmlspecialchars($datos['nombre']) . "</strong>,<br><br>
        Tu cuenta ha sido creada correctamente en <strong>IntraLink</strong>.<br>
        Puedes acceder al sistema con el enlace siguiente.";

            $enlace = RUTA_URL . "/logins"; // Se puede personalizar más adelante

            Correo::enviarBonito(
                $datos['email'],
                '👋 Bienvenido a IntraLink',
                $mensaje,
                'Acceder a IntraLink',
                $enlace
            );

            // Redirigir
            redireccionar('/UsuariosControlador/index');
        } else {
            // Mostrar formulario por GET
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

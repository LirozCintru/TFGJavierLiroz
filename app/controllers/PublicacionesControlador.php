<?php
require_once RUTA_APP . '/librerias/Funciones.php';
require_once RUTA_APP . '/config/roles.php';

class PublicacionesControlador extends Controlador
{
    private $modelo;
    private $comentarioModelo;
    private $notificacionModelo;



    public function __construct()
    {
        $this->modelo = $this->modelo('PublicacionModelo');
        $this->comentarioModelo = $this->modelo('ComentarioModelo');
        $this->notificacionModelo = $this->modelo('NotificacionModelo');
    }


    public function index()
    {
        verificarSesionActiva();
        $publicaciones = $this->modelo->obtenerTodas($_SESSION['usuario']);
        $this->vista('publicaciones/index', ['publicaciones' => $publicaciones]);
    }

    public function crear()
    {
        verificarSesionActiva();
        $categorias = require RUTA_APP . '/config/categorias_evento.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'titulo' => trim($_POST['titulo']),
                'contenido' => trim($_POST['contenido']),
                'tipo' => $_POST['tipo'],
                'id_autor' => $_SESSION['usuario']['id'],
                'id_departamento' => $_SESSION['usuario']['id_departamento'],
                'imagen_destacada' => $this->procesarImagen('imagen_destacada', 'pub_')
            ];

            $id_publicacion = $this->modelo->crear($datos);

            if (!$id_publicacion) {
                $_SESSION['errorPublicacion'] = 'No se pudo crear la publicación.';
                header('Location: ' . RUTA_URL . '/ContenidoControlador/inicio');
                exit;
            }

            $this->procesarImagenesAdicionales($id_publicacion);

            // =========================
            // 🔔 Generar notificación
            // =========================
            $notificacionModelo = $this->modelo('NotificacionModelo');

            // Si la publicación es urgente o de tipo departamento, enviar notificaciones
            if ($_POST['tipo'] === 'urgente') {
                $usuarios = $notificacionModelo->obtenerTodosMenos($_SESSION['usuario']['id']);

                foreach ($usuarios as $usuario) {
                    $notificacionModelo->crear([
                        'id_usuario_destino' => $usuario->id_usuario,
                        'mensaje' => '📢 Nueva publicación urgente: "' . $datos['titulo'] . '"',
                        'tipo' => 'urgente',
                        'id_referencia' => $id_publicacion
                    ]);
                }

            } elseif ($_POST['tipo'] === 'departamento') {
                $usuarios = $notificacionModelo->obtenerPorDepartamento($_SESSION['usuario']['id_departamento'], $_SESSION['usuario']['id']);

                foreach ($usuarios as $usuario) {
                    $notificacionModelo->crear([
                        'id_usuario_destino' => $usuario->id_usuario,
                        'mensaje' => '🗂 Nueva publicación para tu departamento: "' . $datos['titulo'] . '"',
                        'tipo' => 'departamento',
                        'id_referencia' => $id_publicacion
                    ]);
                }
            }



            // Si hay evento
            if (!empty($_POST['activar_evento']) && !empty($_POST['evento_titulo']) && !empty($_POST['evento_fecha'])) {
                $eventoModelo = $this->modelo('EventoModelo');
                $eventoModelo->crear([
                    'titulo' => trim($_POST['evento_titulo']),
                    'descripcion' => trim($_POST['evento_descripcion']),
                    'fecha' => $_POST['evento_fecha'],
                    'hora' => $_POST['evento_hora'],
                    'fecha_fin' => $_POST['evento_fecha_fin'] ?? null,
                    'hora_fin' => $_POST['evento_hora_fin'] ?? null,
                    'todo_el_dia' => isset($_POST['evento_todo_el_dia']) ? 1 : 0,
                    'url' => $_POST['evento_url'] ?? null,
                    'color' => $categorias[$_POST['evento_categoria']]['color'] ?? '#0d6efd',
                    'id_departamento' => $_SESSION['usuario']['id_departamento'],
                    'id_publicacion' => $id_publicacion,
                    'categoria' => $_POST['evento_categoria'] ?? 'general'
                ]);
                // 🔔 Notificar si hay evento vinculado
                if (!empty($evento_id)) {
                    $usuariosDestino = [];

                    if ($_POST['tipo'] === 'departamento') {
                        $usuariosDestino = $this->notificacionModelo->obtenerPorDepartamento($_SESSION['usuario']['id_departamento'], $_SESSION['usuario']['id']);
                    } else {
                        $usuariosDestino = $this->notificacionModelo->obtenerTodosMenos($_SESSION['usuario']['id']);
                    }

                    foreach ($usuariosDestino as $u) {
                        $this->notificacionModelo->crear([
                            'id_usuario_destino' => $u->id_usuario,
                            'mensaje' => '📅 Nueva publicación con evento programado.',
                            'tipo' => 'evento',
                            'id_referencia' => $evento_id
                        ]);
                    }
                }

            }

            $_SESSION['mensajeExito'] = 'Publicación creada correctamente.';
            header('Location: ' . RUTA_URL . '/ContenidoControlador/inicio');
        } else {
            $this->vista('publicaciones/crear');
        }
    }


    public function eliminar($id)
    {
        verificarSesionActiva();
        $usuario = $_SESSION['usuario'];
        $publicacion = $this->modelo->obtenerPorId($id);

        if (!$publicacion) {
            $_SESSION['errorPublicacion'] = 'La publicación no existe.';
        } elseif (
            in_array($usuario['id_rol'], [ROL_ADMIN, ROL_JEFE]) &&
            $usuario['id'] == $publicacion->id_autor
        ) {
            $this->modelo->eliminar($id);
            $_SESSION['mensajeExito'] = 'Publicación eliminada correctamente.';
        } else {
            $_SESSION['errorPublicacion'] = 'No tienes permisos para eliminar esta publicación.';
        }

        header('Location: ' . RUTA_URL . '/ContenidoControlador/inicio');
    }

    public function editar($id)
    {
        verificarSesionActiva();
        $categorias = require RUTA_APP . '/config/categorias_evento.php';

        $publicacion = $this->modelo->obtenerPorId($id);
        if (!$publicacion) {
            header('Location: ' . RUTA_URL . '/ContenidoControlador/inicio');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imagen_destacada = $publicacion->imagen_destacada;

            $eliminarDestacada = isset($_POST['eliminar_imagen']) && $imagen_destacada;
            if ($eliminarDestacada) {
                $this->eliminarImagenFisica($imagen_destacada);
                $imagen_destacada = null;
            }

            $nuevaImagen = $this->procesarImagen('imagen_destacada', 'pub_');
            if ($nuevaImagen) {
                if ($imagen_destacada && !$eliminarDestacada) {
                    $this->eliminarImagenFisica($imagen_destacada);
                }
                $imagen_destacada = $nuevaImagen;
            }

            $this->modelo->actualizar([
                'id_publicacion' => $id,
                'titulo' => trim($_POST['titulo']),
                'contenido' => trim($_POST['contenido']),
                'tipo' => $_POST['tipo'],
                'id_departamento' => $_SESSION['usuario']['id_departamento'],
                'imagen_destacada' => $imagen_destacada
            ]);

            if (!empty($_POST['eliminar_imagenes'])) {
                foreach ($_POST['eliminar_imagenes'] as $rutaImg) {
                    $this->modelo->eliminarImagenAdicional($id, $rutaImg);
                }
            }

            $this->procesarImagenesAdicionales($id);

            // ================================
            // BLOQUE PARA EVENTO VINCULADO
            // ================================
            $eventoModelo = $this->modelo('EventoModelo');
            $evento = $eventoModelo->obtenerPorPublicacion($id)[0] ?? null;

            if ($evento) {
                if (isset($_POST['eliminar_evento'])) {
                    $eventoModelo->eliminar($evento->id_evento);
                } else {
                    $eventoModelo->actualizar([
                        'id_evento' => $evento->id_evento,
                        'titulo' => trim($_POST['evento_titulo']),
                        'descripcion' => trim($_POST['evento_descripcion']),
                        'fecha' => $_POST['evento_fecha'],
                        'hora' => $_POST['evento_hora'],
                        'fecha_fin' => $_POST['evento_fecha_fin'] ?? null,
                        'hora_fin' => $_POST['evento_hora_fin'] ?? null,
                        'todo_el_dia' => isset($_POST['evento_todo_el_dia']) ? 1 : 0,
                        'url' => $_POST['evento_url'] ?? null,
                        'color' => $categorias[$_POST['evento_categoria']]['color'] ?? '#0d6efd',
                        'categoria' => $_POST['evento_categoria'] ?? 'General',
                        'id_departamento' => $_SESSION['usuario']['id_departamento'],
                        'id_publicacion' => $id
                    ]);
                }
            }

            // ================================

            $_SESSION['mensajeExito'] = 'Publicación actualizada correctamente.';
            header('Location: ' . RUTA_URL . '/ContenidoControlador/inicio');
            exit;
        } else {
            $imagenes_adicionales = $this->modelo->obtenerImagenesPublicacion($id);

            // Incluir evento en la vista (si existe)
            $eventoModelo = $this->modelo('EventoModelo');
            $evento = $eventoModelo->obtenerPorPublicacion($id)[0] ?? null;
            $publicacion->evento = $evento;

            $this->vista('publicaciones/editar', [
                'publicacion' => $publicacion,
                'imagenes_adicionales' => $imagenes_adicionales
            ]);
        }
    }


    // ==============================
    // FUNCIONES AUXILIARES PRIVADAS
    // ==============================

    private function extensionesPermitidas()
    {
        return ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    }

    private function procesarImagen($inputName, $prefijo)
    {
        if (!empty($_FILES[$inputName]['name'])) {
            $extension = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));

            if (in_array($extension, $this->extensionesPermitidas())) {
                $nombre = $prefijo . date('YmdHis') . '_' . bin2hex(random_bytes(5)) . '.' . $extension;
                $ruta = RUTA_PUBLIC . '/img/publicaciones/' . $nombre;

                if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $ruta)) {
                    return $nombre;
                }
            }
        }
        return null;
    }

    private function procesarImagenesAdicionales($id_publicacion)
    {
        if (!empty($_FILES['imagenes']['name'][0])) {
            foreach ($_FILES['imagenes']['tmp_name'] as $i => $tmp) {
                $nombreOriginal = $_FILES['imagenes']['name'][$i];
                $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

                if (in_array($extension, $this->extensionesPermitidas())) {
                    $nombreFinal = 'pubimg_' . date('YmdHis') . '_' . bin2hex(random_bytes(5)) . '.' . $extension;
                    $ruta = RUTA_PUBLIC . '/img/publicaciones/' . $nombreFinal;

                    if (move_uploaded_file($tmp, $ruta)) {
                        $this->modelo->guardarImagenPublicacion($id_publicacion, $nombreFinal);
                    }
                }
            }
        }
    }

    private function eliminarImagenFisica($nombreArchivo)
    {
        $ruta = RUTA_PUBLIC . '/img/publicaciones/' . $nombreArchivo;
        if (file_exists($ruta)) {
            unlink($ruta);
        }
    }

    public function comentar($id_publicacion)
    {
        verificarSesionActiva();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contenido = trim($_POST['contenido']);

            if (!empty($contenido)) {
                $this->comentarioModelo->insertar([
                    'contenido' => $contenido,
                    'id_usuario' => $_SESSION['usuario']['id'],
                    'id_publicacion' => $id_publicacion
                ]);
            }

            // ✅ Si es AJAX, devolver solo el HTML del comentario
            if (
                !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            ) {
                $comentario = $this->comentarioModelo->obtenerUltimo($_SESSION['usuario']['id'], $id_publicacion);

                if ($comentario) {
                    ob_start();
                    require RUTA_APP . '/views/publicaciones/_comentario.php';
                    $html = ob_get_clean();
                    echo $html;
                    exit;
                }

                http_response_code(204); // Sin contenido
                exit;
            }

            // Si NO es AJAX, redirigir como antes
            header('Location: ' . RUTA_URL . '/ContenidoControlador/inicio#pub-' . $id_publicacion);
            exit;
        }
    }



    public function eliminarComentario($id_comentario)
    {
        verificarSesionActiva();

        $usuario_id = $_SESSION['usuario']['id'];
        $this->comentarioModelo->eliminar($id_comentario, $usuario_id);

        // Si es petición AJAX (JavaScript Fetch)
        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            // Enviar código 200 y terminar
            http_response_code(200);
            echo 'OK';
            exit;
        }

        // Si no es AJAX, redirigir normalmente
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }





}

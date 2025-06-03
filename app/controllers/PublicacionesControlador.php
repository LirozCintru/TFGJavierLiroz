<?php
require_once RUTA_APP . '/librerias/Funciones.php';
require_once RUTA_APP . '/config/roles.php';

class PublicacionesControlador extends Controlador
{
    private $modelo;
    private $comentarioModelo;
    private $notificacionModelo;
    private $departamentoModelo;


    public function __construct()
    {
        $this->modelo = $this->modelo('PublicacionModelo');
        $this->comentarioModelo = $this->modelo('ComentarioModelo');
        $this->notificacionModelo = $this->modelo('NotificacionModelo');
        $this->departamentoModelo = $this->modelo('DepartamentoModelo');
    }


    public function index()
    {
        verificarSesionActiva();
        $publicaciones = $this->modelo->obtenerTodas($_SESSION['usuario']);
        $this->vista('publicaciones/index', ['publicaciones' => $publicaciones]);
    }

    /** Devuelve true si el usuario actual es ADMIN o JEFE */
    private function esAdminOJefe(): bool
    {
        $rol = $_SESSION['usuario']['id_rol'] ?? 0;
        return in_array($rol, [ROL_ADMIN, ROL_JEFE]);
    }

    private function listaDepartamentos(): array
    {
        return $this->departamentoModelo->obtenerTodos();   // id_departamento, nombre
    }

    public function crear()
    {
        verificarSesionActiva();
        $categorias = require RUTA_APP . '/config/categorias_evento.php';

        /* 🔒 Solo Admin/Jefe */
        if (!$this->esAdminOJefe()) {
            redireccionar('/ContenidoControlador/inicio');   // sin permiso
        }

        /* =============== 1. Formulario enviado =============== */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            /* 1.1  Datos de la publicación */
            $depSolicitado = (int) ($_POST['id_departamento'] ?? 0);

            $datosPub = [
                'titulo' => trim($_POST['titulo']),
                'contenido' => trim($_POST['contenido']),
                'tipo' => $_POST['tipo'],
                'id_autor' => $_SESSION['usuario']['id'],

                // ⭐️ Selección segura del departamento:
                'id_departamento' => ($_SESSION['usuario']['id_rol'] == ROL_ADMIN)
                    ? $depSolicitado                      // Admin escoge cualquiera
                    : $_SESSION['usuario']['id_departamento'], // Jefe → el suyo

                'imagen_destacada' => $this->procesarImagen('imagen_destacada', 'pub_')
            ];

            /* 1.2  Insertar publicación */
            $id_publicacion = $this->modelo->crear($datosPub);

            if (!$id_publicacion) {
                $_SESSION['errorPublicacion'] = 'No se pudo crear la publicación.';
                redireccionar('/ContenidoControlador/inicio');
            }

            /* 1.3  Guardar imágenes adicionales */
            $this->procesarImagenesAdicionales($id_publicacion);

            /* =========================
               2. Notificaciones publicación
               ========================= */
            if (strtolower($datosPub['tipo']) === 'urgente') {

                $usuarios = $this->notificacionModelo
                    ->obtenerTodosMenos($_SESSION['usuario']['id']);

                foreach ($usuarios as $u) {
                    $this->notificacionModelo->crear([
                        'id_usuario_destino' => $u->id_usuario,
                        'mensaje' => '📢 Nueva publicación urgente: "' . $datosPub['titulo'] . '"',
                        'tipo' => 'urgente',
                        'id_referencia' => $id_publicacion
                    ]);
                }

            } elseif ($datosPub['tipo'] === 'departamento') {

                $usuarios = $this->notificacionModelo
                    ->obtenerPorDepartamento(
                        $_SESSION['usuario']['id_departamento'],
                        $_SESSION['usuario']['id']
                    );

                foreach ($usuarios as $u) {
                    $this->notificacionModelo->crear([
                        'id_usuario_destino' => $u->id_usuario,
                        'mensaje' => '🗂 Nueva publicación para tu departamento: "' . $datosPub['titulo'] . '"',
                        'tipo' => 'departamento',
                        'id_referencia' => $id_publicacion
                    ]);
                }
            }

            /* =========================
               3. Evento vinculado (opcional)
               ========================= */
            $evento_id = null;
            if (
                !empty($_POST['activar_evento']) &&
                !empty($_POST['evento_titulo']) &&
                !empty($_POST['evento_fecha'])
            ) {
                $eventoModelo = $this->modelo('EventoModelo');

                $evento_id = $eventoModelo->crear([
                    'titulo' => trim($_POST['evento_titulo']),
                    'descripcion' => trim($_POST['evento_descripcion']),
                    'fecha' => $_POST['evento_fecha'],
                    'hora' => $_POST['evento_hora'],
                    'fecha_fin' => $_POST['evento_fecha_fin'] ?? null,
                    'hora_fin' => $_POST['evento_hora_fin'] ?? null,
                    'todo_el_dia' => isset($_POST['evento_todo_el_dia']) ? 1 : 0,
                    'url' => $_POST['evento_url'] ?? null,
                    'color' => $categorias[$_POST['evento_categoria']]['color'] ?? '#0d6efd',
                    'categoria' => $_POST['evento_categoria'] ?? 'general',
                    'id_departamento' => $_SESSION['usuario']['id_departamento'],
                    'id_publicacion' => $id_publicacion
                ]);

                /* 3.1  Notificación por evento */
                if ($evento_id) {
                    $destinos = ($datosPub['tipo'] === 'departamento')
                        ? $this->notificacionModelo->obtenerPorDepartamento(
                            $_SESSION['usuario']['id_departamento'],
                            $_SESSION['usuario']['id']
                        )
                        : $this->notificacionModelo->obtenerTodosMenos($_SESSION['usuario']['id']);

                    foreach ($destinos as $u) {
                        $this->notificacionModelo->crear([
                            'id_usuario_destino' => $u->id_usuario,
                            'mensaje' => '📅 Nueva publicación con evento programado.',
                            'tipo' => 'evento',
                            'id_referencia' => $evento_id
                        ]);
                    }
                }
            }

            /* 4. Éxito */
            $_SESSION['mensajeExito'] = 'Publicación creada correctamente.';
            redireccionar('/ContenidoControlador/inicio');

            /* =============== 2. Primera carga del formulario =============== */
        } else {
            $this->vista('publicaciones/crear', [
                'departamentos' => $this->listaDepartamentos()
            ]);
        }
    }



    public function eliminar($id)
    {
        verificarSesionActiva();

        /* 🔒 Solo Admin / Jefe */
        if (!$this->esAdminOJefe()) {
            redireccionar('/ContenidoControlador/inicio');
        }

        $publicacion = $this->modelo->obtenerPorId($id);
        if (!$publicacion) {
            $_SESSION['errorPublicacion'] = 'La publicación no existe.';
            redireccionar('/ContenidoControlador/inicio');
        }

        /* 1. Borrar imágenes */
        if ($publicacion->imagen_destacada) {
            $this->eliminarImagenFisica($publicacion->imagen_destacada);
        }
        foreach ($this->modelo->obtenerImagenesPublicacion($id) as $img) {
            $this->eliminarImagenFisica($img->ruta_imagen);
        }

        /* 2. Borrar eventos */
        $eventoModelo = $this->modelo('EventoModelo');
        foreach ($eventoModelo->obtenerPorPublicacion($id) as $ev) {
            $eventoModelo->eliminar($ev->id_evento);
        }

        /* 3. Borrar notificaciones */
        $this->notificacionModelo->eliminarPorReferencia($id);

        /* 4. Borrar publicación */
        $this->modelo->eliminar($id);

        $_SESSION['mensajeExito'] = 'Publicación eliminada correctamente.';
        redireccionar('/ContenidoControlador/inicio');
    }

    public function editar($id)
    {
        verificarSesionActiva();

        /* 🔒 Solo Admin / Jefe */
        if (!$this->esAdminOJefe()) {
            redireccionar('/ContenidoControlador/inicio');
        }

        $categorias = require RUTA_APP . '/config/categorias_evento.php';
        $publicacion = $this->modelo->obtenerPorId($id);

        if (!$publicacion) {
            redireccionar('/ContenidoControlador/inicio');
        }

        /* ------------- POST ------------- */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            /* Imagen destacada */
            $imagen_destacada = $publicacion->imagen_destacada;

            if (isset($_POST['eliminar_imagen']) && $imagen_destacada) {
                $this->eliminarImagenFisica($imagen_destacada);
                $imagen_destacada = null;
            }

            $nuevaImg = $this->procesarImagen('imagen_destacada', 'pub_');
            if ($nuevaImg) {
                if ($imagen_destacada)
                    $this->eliminarImagenFisica($imagen_destacada);
                $imagen_destacada = $nuevaImg;
            }

            /* Actualizar publicación */
            $this->modelo->actualizar([
                'id_publicacion' => $id,
                'titulo' => trim($_POST['titulo']),
                'contenido' => trim($_POST['contenido']),
                'tipo' => $_POST['tipo'],

                'id_departamento' => ($_SESSION['usuario']['id_rol'] == ROL_ADMIN)
                    ? (int) ($_POST['id_departamento'] ?? 0)
                    : $_SESSION['usuario']['id_departamento'],

                'imagen_destacada' => $imagen_destacada
            ]);

            /* Imágenes adicionales */
            if (!empty($_POST['eliminar_imagenes'])) {
                foreach ($_POST['eliminar_imagenes'] as $ruta)
                    $this->modelo->eliminarImagenAdicional($id, $ruta);
            }
            $this->procesarImagenesAdicionales($id);

            /* Evento vinculado */
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

            $_SESSION['mensajeExito'] = 'Publicación actualizada correctamente.';
            redireccionar('/ContenidoControlador/inicio');
        }

        /* ------------- GET ------------- */
        $imagenes_adicionales = $this->modelo->obtenerImagenesPublicacion($id);
        $publicacion->evento = ($this->modelo('EventoModelo')
            ->obtenerPorPublicacion($id)[0] ?? null);

        $this->vista('publicaciones/editar', [
            'publicacion' => $publicacion,
            'imagenes_adicionales' => $imagenes_adicionales,
            'departamentos' => $this->listaDepartamentos()
        ]);
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

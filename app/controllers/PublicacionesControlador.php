<?php
require_once RUTA_APP . '/librerias/Funciones.php';
require_once RUTA_APP . '/config/roles.php';

class PublicacionesControlador extends Controlador
{
    private $modelo;
    private $comentarioModelo;


    public function __construct()
    {
        $this->modelo = $this->modelo('PublicacionModelo');
        $this->comentarioModelo = $this->modelo('ComentarioModelo');
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

        $publicacion = $this->modelo->obtenerPorId($id);
        if (!$publicacion) {
            header('Location: ' . RUTA_URL . '/ContenidoControlador/inicio');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imagen_destacada = $publicacion->imagen_destacada;

            // Marcar para eliminar
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
            $_SESSION['mensajeExito'] = 'Publicación actualizada correctamente.';
            header('Location: ' . RUTA_URL . '/ContenidoControlador/inicio');
            exit;
        } else {
            $imagenes_adicionales = $this->modelo->obtenerImagenesPublicacion($id);
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

            // Redirige de vuelta a la publicación
            // Guardar ID de la publicación comentada para expandirla después
            $_SESSION['expandir_publicacion'] = $id_publicacion;

            // Reconstruir la URL con filtros activos
            $query = http_build_query([
                'tipo' => $_GET['tipo'] ?? '',
                'departamento' => $_GET['departamento'] ?? '',
                'busqueda' => $_GET['busqueda'] ?? '',
                'pagina' => $_GET['pagina'] ?? '',
                'limite' => $_GET['limite'] ?? ''
            ]);

            header('Location: ' . RUTA_URL . '/ContenidoControlador/inicio?' . $query . '#pub-' . $id_publicacion);
            exit;

        }
    }

    public function eliminarComentario($id_comentario)
    {
        verificarSesionActiva();

        $usuario_id = $_SESSION['usuario']['id'];
        $this->comentarioModelo->eliminar($id_comentario, $usuario_id);

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }



}

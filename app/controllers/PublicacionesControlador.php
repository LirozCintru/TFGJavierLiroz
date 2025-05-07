<?php
require_once RUTA_APP . '/librerias/Funciones.php';

class PublicacionesControlador extends Controlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = $this->modelo('PublicacionModelo');
    }

    public function index()
    {
        verificarSesionActiva();
        $publicaciones = $this->modelo->obtenerTodas($_SESSION['usuario']);
        print_r($publicaciones);

        $this->vista('publicaciones/index', ['publicaciones' => $publicaciones]);
    }
    public function crear()
    {
        require_once RUTA_APP . '/librerias/Funciones.php';
        verificarSesionActiva();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = trim($_POST['titulo']);
            $contenido = trim($_POST['contenido']);
            $tipo = $_POST['tipo'];
            $id_autor = $_SESSION['usuario']['id'];
            $id_departamento = $_SESSION['usuario']['id_departamento'];

            // Validación de extensiones permitidas
            $extensiones_validas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            // Procesar imagen destacada
            $imagen_destacada = null;
            if (!empty($_FILES['imagen_destacada']['name'])) {
                $extension = strtolower(pathinfo($_FILES['imagen_destacada']['name'], PATHINFO_EXTENSION));
                if (in_array($extension, $extensiones_validas)) {
                    $nombreImg = 'pub_' . date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
                    $ruta = RUTA_PUBLIC . '/img/publicaciones/' . $nombreImg;

                    if (move_uploaded_file($_FILES['imagen_destacada']['tmp_name'], $ruta)) {
                        $imagen_destacada = $nombreImg;
                    }
                }
            }

            // Insertar publicación
            $datos = [
                'titulo' => $titulo,
                'contenido' => $contenido,
                'tipo' => $tipo,
                'id_autor' => $id_autor,
                'id_departamento' => $id_departamento,
                'imagen_destacada' => $imagen_destacada
            ];

            $id_publicacion = $this->modelo->crear($datos);

            if (!$id_publicacion) {
                $_SESSION['errorPublicacion'] = 'No se pudo crear la publicación.';
                header('Location: ' . RUTA_URL . '/ContenidoControlador/inicio');
                exit;
            }

            // Procesar imágenes adicionales
            if (!empty($_FILES['imagenes']['name'][0])) {
                foreach ($_FILES['imagenes']['tmp_name'] as $i => $tmp) {
                    if (!empty($_FILES['imagenes']['name'][$i])) {
                        $extension = strtolower(pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION));

                        if (in_array($extension, $extensiones_validas)) {
                            $nombreArchivo = 'pubimg_' . date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
                            $ruta = RUTA_PUBLIC . '/img/publicaciones/' . $nombreArchivo;

                            if (move_uploaded_file($tmp, $ruta)) {
                                $this->modelo->guardarImagenPublicacion($id_publicacion, $nombreArchivo);
                            }
                        }
                    }
                }
            }

            header('Location: ' . RUTA_URL . '/ContenidoControlador/inicio');
        } else {
            $this->vista('publicaciones/crear');
        }
    }
    public function eliminar($id)
    {
        verificarSesionActiva();
        require_once RUTA_APP . '/config/roles.php';

        $usuario = $_SESSION['usuario'];

        // Obtener la publicación
        $publicacion = $this->modelo->obtenerPorId($id);

        if (!$publicacion) {
            $_SESSION['errorPublicacion'] = 'La publicación no existe.';
            header('Location: ' . RUTA_URL . '/ContenidoControlador/inicio');
            exit;
        }

        // Solo puede eliminar si es admin o jefe Y es el autor
        if (
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
            $titulo = trim($_POST['titulo']);
            $contenido = trim($_POST['contenido']);
            $tipo = $_POST['tipo'];
            $id_departamento = $_SESSION['usuario']['id_departamento'];

            $imagen_destacada = $publicacion->imagen_destacada;

            if (isset($_POST['eliminar_imagen']) && $imagen_destacada) {
                $ruta = RUTA_PUBLIC . '/img/publicaciones/' . $imagen_destacada;
                if (file_exists($ruta)) {
                    unlink($ruta);
                }
                $imagen_destacada = null;
            }

            if (!empty($_FILES['imagen_destacada']['name'])) {
                $extension = strtolower(pathinfo($_FILES['imagen_destacada']['name'], PATHINFO_EXTENSION));
                $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($extension, $permitidas)) {
                    $nombreNuevo = 'pub_' . date('YmdHis') . '_' . bin2hex(random_bytes(5)) . '.' . $extension;
                    $rutaNueva = RUTA_PUBLIC . '/img/publicaciones/' . $nombreNuevo;
                    if (move_uploaded_file($_FILES['imagen_destacada']['tmp_name'], $rutaNueva)) {
                        if ($imagen_destacada) {
                            $rutaAntigua = RUTA_PUBLIC . '/img/publicaciones/' . $imagen_destacada;
                            if (file_exists($rutaAntigua)) {
                                unlink($rutaAntigua);
                            }
                        }
                        $imagen_destacada = $nombreNuevo;
                    }
                }
            }

            $this->modelo->actualizar([
                'id_publicacion' => $id,
                'titulo' => $titulo,
                'contenido' => $contenido,
                'tipo' => $tipo,
                'id_departamento' => $id_departamento,
                'imagen_destacada' => $imagen_destacada
            ]);

            // Eliminar imágenes adicionales marcadas
            if (!empty($_POST['eliminar_imagenes'])) {
                foreach ($_POST['eliminar_imagenes'] as $rutaImg) {
                    $this->modelo->eliminarImagenAdicional($id, $rutaImg);
                }
            }

            // Guardar nuevas imágenes adicionales
            if (!empty($_FILES['imagenes']['name'][0])) {
                foreach ($_FILES['imagenes']['tmp_name'] as $i => $tmp) {
                    $nombreOriginal = $_FILES['imagenes']['name'][$i];
                    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
                    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if (in_array($extension, $permitidas)) {
                        $nombreFinal = 'pubimg_' . date('YmdHis') . '_' . bin2hex(random_bytes(5)) . '.' . $extension;
                        $ruta = RUTA_PUBLIC . '/img/publicaciones/' . $nombreFinal;

                        if (move_uploaded_file($tmp, $ruta)) {
                            $this->modelo->guardarImagenPublicacion($id, $nombreFinal);
                        }
                    }
                }
            }

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





}

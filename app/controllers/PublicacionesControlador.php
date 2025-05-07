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
                header('Location: ' . RUTA_URL . '/contenido/inicio');
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

            header('Location: ' . RUTA_URL . '/contenido/inicio');
        } else {
            $this->vista('publicaciones/crear');
        }
    }


    public function eliminar($id)
    {
        verificarSesionActiva();
        $this->modelo->eliminar($id, $_SESSION['usuario']['id']);
        header('Location: ' . RUTA_URL . '/publicaciones');
    }
}

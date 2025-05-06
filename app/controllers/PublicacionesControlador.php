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
        verificarSesionActiva();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo = trim($_POST['titulo']);
            $contenido = trim($_POST['contenido']);
            $tipo = $_POST['tipo'];
            $id_autor = $_SESSION['usuario']['id'];
            $id_departamento = ($tipo === 'Departamental') ? $_SESSION['usuario']['id_departamento'] : null;

            // Manejar imagen destacada
            $imagen_destacada = null;
            if (!empty($_FILES['imagen_destacada']['name'])) {
                $nombreImg = uniqid() . '_' . basename($_FILES['imagen_destacada']['name']);
                $ruta = RUTA_PUBLIC . '/img/publicaciones/' . $nombreImg;

                if (move_uploaded_file($_FILES['imagen_destacada']['tmp_name'], $ruta)) {
                    $imagen_destacada = $nombreImg;
                }
            }

            // Insertar publicación
            $this->modelo->crear([
                'titulo' => $titulo,
                'contenido' => $contenido,
                'tipo' => $tipo,
                'id_autor' => $id_autor,
                'id_departamento' => $id_departamento,
                'imagen_destacada' => $imagen_destacada
            ]);

            // Obtener ID insertado
            $id_publicacion = $this->modelo->ultimoIdInsertado();

            // Manejar imágenes adicionales
            if (!empty($_FILES['imagenes']['name'][0])) {
                foreach ($_FILES['imagenes']['tmp_name'] as $i => $tmp) {
                    if (!empty($_FILES['imagenes']['name'][$i])) {
                        $nombreArchivo = uniqid() . '_' . basename($_FILES['imagenes']['name'][$i]);
                        $ruta = RUTA_PUBLIC . '/img/publicaciones/' . $nombreArchivo;

                        if (move_uploaded_file($tmp, $ruta)) {
                            $this->modelo->guardarImagenPublicacion($id_publicacion, $nombreArchivo);
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

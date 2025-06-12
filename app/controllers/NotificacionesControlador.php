<?php
require_once RUTA_APP . '/librerias/Funciones.php';
require_once RUTA_APP . '/config/roles.php';

class NotificacionesControlador extends Controlador
{
    private $notificacionModelo;

    public function __construct()
    {
        $this->notificacionModelo = $this->modelo('NotificacionModelo');
    }

    //Muestra todas las notificaciones del usuario
    public function index()
    {
        verificarSesionActiva();
        $usuario_id = $_SESSION['usuario']['id'];

        $notificaciones = $this->notificacionModelo->obtenerTodas($usuario_id); // Incluye leídas

        $this->vista('notificaciones/index', [
            'notificaciones' => $notificaciones
        ]);
    }

    public function accionesMasivas()
    {
        verificarSesionActiva();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seleccionadas']) && is_array($_POST['seleccionadas'])) {
            $ids = $_POST['seleccionadas'];
            $accion = $_POST['accion'];

            foreach ($ids as $id) {
                if ($accion === 'marcar') {
                    $this->notificacionModelo->marcarComoLeida($id);
                } elseif ($accion === 'eliminar') {
                    $this->notificacionModelo->eliminar($id);
                }
            }
        }

        redireccionar('/NotificacionesControlador/index');
    }


    //Contador de notificaciones no leídas (JSON para el badge)
    public function contador()
    {
        verificarSesionActiva();
        $usuario_id = $_SESSION['usuario']['id'];

        $pendientes = $this->notificacionModelo->contarNoLeidas($usuario_id);
        echo json_encode(['pendientes' => $pendientes]);
    }

    //Marcar como leída una notificación (desde el botón)
    public function marcarLeida($id)
    {
        verificarSesionActiva();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->notificacionModelo->marcarComoLeida($id);
        }

        redireccionar('/NotificacionesControlador/index');
    }

    //Marcar todas como leídas
    public function marcarTodasLeidas()
    {
        verificarSesionActiva();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_SESSION['usuario']['id'];
            $this->notificacionModelo->marcarTodasComoLeidas($usuario_id);
        }

        redireccionar('/NotificacionesControlador/index');
    }

    //Eliminar una notificación
    public function eliminar($id)
    {
        verificarSesionActiva();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->notificacionModelo->eliminar($id);
        }

        redireccionar('/NotificacionesControlador/index');
    }
}

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

    public function index()
    {
        verificarSesionActiva();
        $usuario = $_SESSION['usuario'];
        $notificaciones = $this->notificacionModelo->obtenerPorUsuario($usuario['id']);

        // Marcar como leídas al entrar
        $this->notificacionModelo->marcarComoLeidas($usuario['id']);

        $this->vista('notificaciones/index', [
            'notificaciones' => $notificaciones
        ]);
    }

    public function obtener()
    {
        verificarSesionActiva();
        $notificaciones = $this->notificacionModelo->obtenerNoLeidas($_SESSION['usuario']['id']);
        echo json_encode($notificaciones);
    }

    public function contar()
    {
        verificarSesionActiva();
        $total = $this->notificacionModelo->contarNoLeidas($_SESSION['usuario']['id']);
        echo json_encode(['total' => $total]);
    }


    public function contador()
    {
        verificarSesionActiva();
        $usuario = $_SESSION['usuario'];
        $total = $this->notificacionModelo->contarPendientes($usuario['id']);
        echo json_encode(['pendientes' => $total]);
    }

    public function marcarLeida($id)
    {
        verificarSesionActiva();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->notificacionModelo->marcarComoLeida($id);
        }

        redireccionar('/NotificacionesControlador/index');
    }

    public function marcarTodasLeidas()
    {
        verificarSesionActiva();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioId = $_SESSION['usuario']['id'];
            $this->notificacionModelo->marcarTodasComoLeidas($usuarioId);
        }

        redireccionar('/NotificacionesControlador/index');
    }

    public function eliminar($id)
    {
        verificarSesionActiva();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->notificacionModelo->eliminar($id);
        }

        redireccionar('/NotificacionesControlador/index');
    }




}

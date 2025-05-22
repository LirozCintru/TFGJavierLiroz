<?php
require_once RUTA_APP . '/librerias/Funciones.php';
require_once RUTA_APP . '/config/roles.php';
class SeccionesControlador extends Controlador
{
    public function cargar($seccion)
    {
        verificarSesionActiva();

        switch ($seccion) {
            case 'inicio':
                $modelo = $this->modelo('PublicacionModelo');
                $datos = $modelo->obtenerTodas($_SESSION['usuario']);
                $this->vistaDirecta('secciones/inicio', ['publicaciones' => $datos]);
                break;

            case 'calendario':
                $this->vistaDirecta('secciones/calendario');
                break;

            case 'notificaciones':
                $this->vistaDirecta('secciones/notificaciones'); // Placeholder
                break;

            case 'perfil':
                $this->vistaDirecta('secciones/perfil'); // Placeholder
                break;

            default:
                echo '<div class="alert alert-warning">Sección no encontrada.</div>';
        }
    }

    private function vistaDirecta($vista, $datos = [])
    {
        extract($datos);
        require RUTA_APP . "/views/{$vista}.php";
    }
}

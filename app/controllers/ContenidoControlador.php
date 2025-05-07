<?php
require_once RUTA_APP . '/librerias/ControladorProtegido.php';

class ContenidoControlador extends Controlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = $this->modelo('PublicacionModelo');
    }

    public function inicio()
    {
        verificarSesionActiva();

        $modelo = $this->modelo('PublicacionModelo');
        $publicaciones = $modelo->obtenerTodas($_SESSION['usuario']);

        $this->vista('contenido/inicio', ['publicaciones' => $publicaciones]);
    }

}

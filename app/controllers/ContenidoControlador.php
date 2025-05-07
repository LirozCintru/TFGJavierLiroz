<?php
require(RUTA_APP . '/librerias/Funciones.php');
require_once RUTA_APP . '/config/roles.php';
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

        $publicaciones = $this->modelo->obtenerTodas($_SESSION['usuario']);

        $this->vista('contenido/inicio', ['publicaciones' => $publicaciones]);
    }


}

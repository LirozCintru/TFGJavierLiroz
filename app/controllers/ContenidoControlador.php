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
        // Obtener publicaciones visibles para el usuario actual
        $publicaciones = $this->modelo->obtenerTodas($_SESSION['usuario']);

        // Cargar la vista principal pasando las publicaciones
        $this->vista('contenido/Inicio', [
            'publicaciones' => $publicaciones
        ]);
    }
}

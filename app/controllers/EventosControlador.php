<?php
require_once RUTA_APP . '/librerias/Funciones.php';
require_once RUTA_APP . '/config/roles.php';

class EventosControlador extends Controlador
{
    private $modelo;
    private $departamentoModelo;

    public function __construct()
    {
        $this->modelo = $this->modelo('EventoModelo');
        $this->departamentoModelo = $this->modelo('DepartamentoModelo');
    }

    public function index()
    {
        verificarSesionActiva();

        $usuario = $_SESSION['usuario'];
        $eventos = $this->modelo->obtenerTodos($usuario);

        $this->vista('eventos/index', [
            'eventos' => $eventos
        ]);
    }

    public function crear()
    {
        verificarSesionActiva();

        $departamentos = $this->departamentoModelo->obtenerTodos();

        $this->vista('eventos/crear', [
            'departamentos' => $departamentos
        ]);
    }

    public function guardar()
    {
        verificarSesionActiva();

        $datos = [
            'titulo' => $_POST['titulo'],
            'descripcion' => $_POST['descripcion'],
            'fecha' => $_POST['fecha'],
            'hora' => $_POST['hora'],
            'id_departamento' => $_POST['id_departamento'] ?? null
        ];

        $this->modelo->crear($datos);
        header('Location: ' . RUTA_URL . '/EventosControlador/index');
    }
}

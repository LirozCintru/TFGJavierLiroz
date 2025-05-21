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

    public function obtener()
    {
        verificarSesionActiva();
        $usuario = $_SESSION['usuario'];
        $eventos = $this->modelo->obtenerTodos($usuario);

        $resultado = [];

        foreach ($eventos as $evento) {
            // Construir fecha/hora de inicio
            $start = $evento->fecha;
            if (!$evento->todo_el_dia && !empty($evento->hora)) {
                $start .= 'T' . $evento->hora;
            }

            // Construir fecha/hora de fin si existe
            $end = null;
            if (!empty($evento->fecha_fin)) {
                $end = $evento->fecha_fin;
                if (!$evento->todo_el_dia && !empty($evento->hora_fin)) {
                    $end .= 'T' . $evento->hora_fin;
                }
            }

            $resultado[] = [
                'title' => $evento->titulo,
                'start' => $start,
                'end' => $end,
                'allDay' => $evento->todo_el_dia == 1,
                'descripcion' => $evento->descripcion,
                'nombre_departamento' => $evento->nombre_departamento,
                'id_publicacion' => $evento->id_publicacion,
                'id_evento' => $evento->id_evento,
                'color' => $evento->color ?: '#0d6efd',
                'url' => $evento->url ?: null
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($resultado);
    }

    public function eliminar($id)
    {
        verificarSesionActiva();
        $this->modelo->eliminar($id);
        $_SESSION['mensajeExito'] = 'Evento eliminado correctamente.';
        header('Location: ' . RUTA_URL . '/EventosControlador/index');
    }



}

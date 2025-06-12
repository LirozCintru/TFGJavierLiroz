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

        $categorias = require RUTA_APP . '/config/categorias_evento.php';

        $resultado = [];

        foreach ($eventos as $evento) {
            $start = $evento->fecha;
            if (!$evento->todo_el_dia && !empty($evento->hora)) {
                $start .= 'T' . $evento->hora;
            }

            $end = null;
            if (!empty($evento->fecha_fin)) {
                $end = $evento->fecha_fin;
                if (!$evento->todo_el_dia && !empty($evento->hora_fin)) {
                    $end .= 'T' . $evento->hora_fin;
                }
            }

            $categoria = $evento->categoria ?? 'General';
            $color = $categorias[$categoria]['color'] ?? '#0d6efd';

            $resultado[] = [
                'title' => $evento->titulo,
                'start' => $start,
                'end' => $end,
                'allDay' => $evento->todo_el_dia == 1,
                'descripcion' => $evento->descripcion,
                'nombre_departamento' => $evento->nombre_departamento,
                'id_publicacion' => $evento->id_publicacion,
                'id_evento' => $evento->id_evento,
                'hora' => $evento->hora,
                'hora_fin' => $evento->hora_fin,
                'url' => $evento->url ?: null,
                'color' => $color,
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($resultado);
    }

    public function eliminar($id)
    {
        verificarSesionActiva();

        // ADMIN o JEFE
        if (!in_array($_SESSION['usuario']['id_rol'], [ROL_ADMIN, ROL_JEFE])) {
            $_SESSION['error'] = 'No tienes permisos para eliminar eventos.';
            header('Location: ' . RUTA_URL . '/ContenidoControlador/inicio');
            exit;
        }

        $this->modelo->eliminar($id);
        $_SESSION['mensajeExito'] = 'Evento eliminado correctamente.';
        header('Location: ' . RUTA_URL . '/EventosControlador/index');
        exit;
    }




}

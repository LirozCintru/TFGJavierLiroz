<?php
require_once RUTA_APP . '/librerias/Funciones.php';
require_once RUTA_APP . '/config/roles.php';

class DepartamentoControlador extends Controlador
{
    private $modelo;

    public function __construct()
    {
        verificarSesionActiva();

        if ($_SESSION['usuario']['id_rol'] != ROL_ADMIN) {
            redireccionar('/ContenidoControlador/inicio');
        }

        $this->modelo = $this->modelo('DepartamentoModelo');
    }

    //Listado de departamentos
    public function index()
    {
        $filtro = $_GET['busqueda'] ?? '';
        $todos = $this->modelo->obtenerTodos();

        foreach ($todos as $d) {
            $d->bloqueado = $this->modelo->tieneUsuariosAsignados($d->id_departamento);
        }

        if ($filtro) {
            $departamentos = array_values(array_filter(
                $todos,
                fn($d) =>
                stripos($d->nombre, $filtro) !== false
            ));
        } else {
            $departamentos = $todos;
        }

        $this->vista('departamentos/index', [
            'departamentos' => $departamentos,
            'busqueda' => $filtro
        ]);
    }

    //Crear nuevo
    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion']);

            $errores = [];

            if (empty($nombre)) {
                $errores['nombre'] = 'El nombre es obligatorio.';
            } elseif ($this->modelo->existeNombre($nombre)) {
                $errores['nombre'] = 'Ya existe un departamento con ese nombre.';
            }

            if (empty($errores)) {
                $this->modelo->crear([
                    'nombre' => $nombre,
                    'descripcion' => $descripcion
                ]);
                redireccionar('/DepartamentoControlador/index');
            }

            $this->vista('departamentos/formulario', [
                'modo' => 'crear',
                'errores' => $errores,
                'datos' => [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion
                ]
            ]);
        } else {
            $this->vista('departamentos/formulario', [
                'modo' => 'crear',
                'errores' => [],
                'datos' => [
                    'nombre' => '',
                    'descripcion' => ''
                ]
            ]);
        }
    }

    //Editar existente
    public function editar($id)
    {
        $departamento = $this->modelo->obtenerPorId($id);
        if (!$departamento) {
            redireccionar('/DepartamentoControlador/index');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion']);

            $errores = [];

            if (empty($nombre)) {
                $errores['nombre'] = 'El nombre es obligatorio.';
            } elseif ($this->modelo->existeNombre($nombre, $id)) {
                $errores['nombre'] = 'Ya existe otro departamento con ese nombre.';
            }

            if (empty($errores)) {
                $this->modelo->actualizar($id, [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion
                ]);
                redireccionar('/DepartamentoControlador/index');
            }

            $this->vista('departamentos/formulario', [
                'modo' => 'editar',
                'errores' => $errores,
                'datos' => [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion
                ],
                'id' => $id
            ]);
        } else {
            $this->vista('departamentos/formulario', [
                'modo' => 'editar',
                'errores' => [],
                'datos' => [
                    'nombre' => $departamento->nombre,
                    'descripcion' => $departamento->descripcion
                ],
                'id' => $id
            ]);
        }
    }

    //Eliminar departamento
    public function eliminar($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->modelo->tieneUsuariosAsignados($id)) {
                $_SESSION['error_departamento'] = 'No se puede eliminar un departamento con usuarios asignados.';
            } else {
                $this->modelo->eliminar($id);
            }
        }

        redireccionar('/DepartamentoControlador/index');
    }
}

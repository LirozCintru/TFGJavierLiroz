<?php
require_once RUTA_APP . '/librerias/Funciones.php';

class ChatControlador extends Controlador
{
    private $usuarioModelo;
    private $msgModelo;
    private $departamentoModelo;

    public function __construct()
    {
        $this->usuarioModelo = $this->modelo('UsuarioModelo');
        $this->departamentoModelo = $this->modelo('DepartamentoModelo');
        $this->msgModelo = $this->modelo('MensajeModelo');
    }

    /**
     * index():
     *   - Verifica sesión.
     *   - Recoge filtros de GET (nombre, departamento).
     *   - Obtiene departamentos y cuenta mensajes pendientes.
     *   - Llama a UsuarioModelo::buscar(...) para filtrar usuarios.
     *   - Envía todo dentro de la clave 'datos' a la vista.
     */
    public function index()
    {
        verificarSesionActiva();
        $miId = $_SESSION['usuario']['id'];

        // 1) Leer filtros
        $nombreFiltro = trim($_GET['nombre'] ?? '');
        $departamentoFiltro = filter_input(INPUT_GET, 'departamento', FILTER_VALIDATE_INT);

        // 2) Traer departamentos
        $departamentos = $this->departamentoModelo->obtenerTodos();

        // 3) Contar pendientes por remitente
        $pendientesPorRemitente = $this->msgModelo->contarNoLeidosPorRemitente($miId);

        // 4) Obtener lista de usuarios filtrados
        $usuarios = $this->usuarioModelo->buscar(
            $miId,
            $nombreFiltro,
            $departamentoFiltro
        );

        // 5) Si viene "con=ID", cargamos mensajes y marcamos leídos
        $con = filter_input(INPUT_GET, 'con', FILTER_VALIDATE_INT);
        $mensajes = [];
        if ($con && $con > 0) {
            $mensajes = $this->msgModelo->obtenerConversacion($miId, $con);
            $this->msgModelo->marcarLeidosDeUsuario($miId, $con);
        }

        // 6) Llamamos a la vista asegurándonos de enviar estas claves sueltas
        $this->vista('chat/list', [
            'usuarios' => $usuarios,
            'pendientesPorRemitente' => $pendientesPorRemitente,
            'departamentos' => $departamentos,
            'datos' => [
                'filtro_nombre' => $nombreFiltro,
                'filtro_departamento' => $departamentoFiltro
            ],
            'con' => $con,
            'mensajes' => $mensajes,
            'yo' => $miId
        ]);


    }


    /**
     * enviar():
     *   - POST { destinatario, mensaje }
     *   - Inserta mensaje y devuelve JSON { ok, id, fecha }.
     */
    public function enviar()
    {
        verificarSesionActiva();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $yo = $_SESSION['usuario']['id'];
        $dest = filter_input(INPUT_POST, 'destinatario', FILTER_VALIDATE_INT);
        $texto = trim($_POST['mensaje'] ?? '');

        if (!$dest || $texto === '') {
            echo json_encode(['ok' => false]);
            return;
        }

        $nuevoId = $this->msgModelo->crear([
            'contenido' => $texto,
            'id_remitente' => $yo,
            'id_destinatario' => $dest
        ]);

        echo json_encode([
            'ok' => true,
            'id' => $nuevoId,
            'fecha' => date('c')
        ]);
    }

    /**
     * nuevos():
     *   - GET ?con=ID&ultimo=ID_último
     *   - Devuelve JSON con los mensajes nuevos.
     */
    public function nuevos()
    {
        verificarSesionActiva();
        $yo = $_SESSION['usuario']['id'];
        $con = filter_input(INPUT_GET, 'con', FILTER_VALIDATE_INT);
        $last = filter_input(INPUT_GET, 'ultimo', FILTER_VALIDATE_INT);

        if (!$con || $last === false) {
            echo json_encode([]);
            return;
        }

        $nuevos = $this->msgModelo->obtenerNuevos($yo, $con, $last);
        if (!empty($nuevos)) {
            $this->msgModelo->marcarLeidosDeUsuario($yo, $con);
        }
        echo json_encode($nuevos);
    }

    /**
     * contador():
     *   - Devuelve JSON { pendientes: totalSinLeer } para el badge global.
     */
    public function contador()
    {
        verificarSesionActiva();
        $yo = $_SESSION['usuario']['id'];
        $total = $this->msgModelo->contarNoLeidosTotales($yo);
        echo json_encode(['pendientes' => $total]);
    }
}

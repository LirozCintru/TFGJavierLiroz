<?php
require_once RUTA_APP . '/librerias/Funciones.php';
require_once RUTA_APP . '/config/roles.php';

class ContenidoControlador extends Controlador
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = $this->modelo('PublicacionModelo');
    }

    public function index()
    {
        // Simplemente redirige a la lógica “inicio”
        $this->inicio();
    }

    public function inicio()
    {
        verificarSesionActiva();
        $usuario = $_SESSION['usuario'];

        //
        // 1) LEER TODOS LOS PARÁMETROS DE FILTRO / ORDEN / PAGINACIÓN
        //
        $filtro_tipo = trim($_GET['tipo'] ?? '');
        $filtro_busqueda = trim($_GET['busqueda'] ?? '');
        $filtro_departamento = trim($_GET['departamento'] ?? '');

        // Si NO es admin, forzamos su propio departamento (no lista los demás).
        if ($usuario['id_rol'] != ROL_ADMIN) {
            $filtro_departamento = $usuario['id_departamento'];
        }

        $orden = trim($_GET['orden'] ?? 'desc');    // 'asc' o 'desc'
        $limite = (int) ($_GET['limite'] ?? ($_SESSION['limite_publicaciones'] ?? 10));
        if ($limite <= 0) {
            $limite = 10;
        }
        // Guardar en sesión el límite si vino por GET:
        if (isset($_GET['limite']) && (int) $_GET['limite'] > 0) {
            $_SESSION['limite_publicaciones'] = $limite;
        }

        $pagina = isset($_GET['pagina']) && (int) $_GET['pagina'] > 0
            ? (int) $_GET['pagina']
            : 1;

        //
        // 2) CALCULAR CUÁNTAS FILAS HAY EN TOTAL CON ESOS FILTROS
        //
        $total_filtradas = $this->modelo->contarFiltradas(
            $usuario,
            $filtro_tipo,
            $filtro_busqueda,
            $filtro_departamento
        );

        $total_paginas = max(1, (int) ceil($total_filtradas / $limite));
        if ($pagina > $total_paginas) {
            $pagina = $total_paginas;
        }

        $offset = ($pagina - 1) * $limite;

        //
        // 3) OBTENER LA PÁGINA CORRESPONDIENTE CON TODOS LOS FILTROS + ORDEN + LIMIT/OFFSET
        //
        $publicaciones = $this->modelo->obtenerPaginadas(
            $usuario,
            $filtro_tipo,
            $filtro_busqueda,
            $filtro_departamento,
            $limite,
            $offset,
            $orden       // ahora se usa para ORDER BY p.fecha ASC|DESC
        );

        //
        // 4) AÑADIR COMENTARIOS Y EVENTOS A CADA PUBLICACIÓN (si ya lo tenías así)
        //
        $comentarioModelo = $this->modelo('ComentarioModelo');
        foreach ($publicaciones as $p) {
            $p->comentarios = $comentarioModelo->obtenerPorPublicacion($p->id_publicacion);
        }

        $eventoModelo = $this->modelo('EventoModelo');
        foreach ($publicaciones as $p) {
            $p->evento = $eventoModelo->obtenerPorPublicacion($p->id_publicacion)[0] ?? null;
        }

        //
        // 5) LISTA DE DEPARTAMENTOS (sólo se muestra si el usuario es ADMIN)
        //
        $departamentos = [];
        if ($usuario['id_rol'] == ROL_ADMIN) {
            $departamentos = $this->modelo('DepartamentoModelo')->obtenerTodos();
        }

        //
        // 6) ID DE PUBLICACIÓN A EXPANDIR (si existe en sesión tras comentar)
        //
        $expandirId = $_SESSION['expandir_publicacion'] ?? null;
        unset($_SESSION['expandir_publicacion']);

        //
        // 7) LLAMAR A LA VISTA PASÁNDOLE TODOS LOS DATOS
        //
        $this->vista('contenido/inicio', [
            'publicaciones' => $publicaciones,
            'departamentos' => $departamentos,
            'filtro_tipo' => $filtro_tipo,
            'filtro_busqueda' => $filtro_busqueda,
            'filtro_departamento' => $filtro_departamento,
            'orden' => $orden,
            'pagina' => $pagina,
            'total_paginas' => $total_paginas,
            'limite' => $limite,
            'expandir_publicacion' => $expandirId
        ]);
    }

    public function seccion($nombre)
    {
        verificarSesionActiva();

        switch ($nombre) {
            case 'inicio':
                $publicaciones = $this->modelo('PublicacionModelo')->obtenerTodas($_SESSION['usuario']);
                $departamentos = $this->modelo('DepartamentoModelo')->obtenerTodos();
                $this->vista('secciones/inicio', [
                    'publicaciones' => $publicaciones,
                    'pagina' => 1,
                    'limite' => 10,
                    'total_paginas' => 1,
                    'departamentos' => $departamentos
                ]);
                break;

            case 'calendario':
                $this->vista('secciones/calendario');
                break;

            case 'notificaciones':
                $this->vista('secciones/notificaciones');
                break;

            case 'perfil':
                $this->vista('secciones/perfil');
                break;

            default:
                echo '<div class="alert alert-warning">Sección no encontrada.</div>';
                break;
        }
    }
}

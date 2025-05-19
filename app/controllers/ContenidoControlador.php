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

    public function inicio()
    {
        verificarSesionActiva();

        $usuario = $_SESSION['usuario'];
        $tipo = $_GET['tipo'] ?? '';
        $busqueda = $_GET['busqueda'] ?? '';
        $id_departamento = $_GET['departamento'] ?? '';

        

        // Si no es admin, limitar el filtro al departamento propio
        if ($usuario['id_rol'] != ROL_ADMIN) {
            $id_departamento = $usuario['id_departamento'];
        }

        // Límite de publicaciones por página
        if (isset($_GET['limite']) && (int) $_GET['limite'] > 0) {
            $_SESSION['limite_publicaciones'] = (int) $_GET['limite'];
        }
        $limite = $_SESSION['limite_publicaciones'] ?? 10;
        if ($limite <= 0) {
            $limite = 10;
        }

        // Página actual
        $pagina = isset($_GET['pagina']) && (int) $_GET['pagina'] > 0 ? (int) $_GET['pagina'] : 1;
        $offset = ($pagina - 1) * $limite;

        // Total de publicaciones filtradas
        $total = $this->modelo->contarFiltradas($usuario, $tipo, $busqueda, $id_departamento);
        $total_paginas = max(1, ceil($total / $limite));

        // Publicaciones paginadas
        $publicaciones = $this->modelo->obtenerPaginadas($usuario, $tipo, $busqueda, $id_departamento, $limite, $offset);

        // echo '<pre>'; print_r($publicaciones); echo '</pre>'; exit;


        // Añadir comentarios a cada publicación
        $comentarioModelo = $this->modelo('ComentarioModelo');
        foreach ($publicaciones as $p) {
            $p->comentarios = $comentarioModelo->obtenerPorPublicacion($p->id_publicacion);
        }

        $eventoModelo = $this->modelo('EventoModelo');
        foreach ($publicaciones as $p) {
            $p->evento = $eventoModelo->obtenerPorPublicacion($p->id_publicacion)[0] ?? null;
        }


        // Departamentos (solo para admin)
        $departamentos = $usuario['id_rol'] == ROL_ADMIN
            ? $this->modelo('DepartamentoModelo')->obtenerTodos()
            : [];

        // ID de publicación a expandir (tras comentar)
        $expandirId = $_SESSION['expandir_publicacion'] ?? null;
        unset($_SESSION['expandir_publicacion']);

        // Vista
        $this->vista('contenido/inicio', [
            'publicaciones' => $publicaciones,
            'departamentos' => $departamentos,
            'filtro_tipo' => $tipo,
            'filtro_busqueda' => $busqueda,
            'filtro_departamento' => $id_departamento,
            'pagina' => $pagina,
            'total_paginas' => $total_paginas,
            'limite' => $limite,
            'expandir_publicacion' => $expandirId
        ]);
    }
}

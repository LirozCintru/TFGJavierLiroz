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

        // Si no es admin, solo puede ver su departamento
        if ($usuario['id_rol'] != ROL_ADMIN) {
            $id_departamento = $usuario['id_departamento'];
        }

        // ✅ Manejar límite por página
        if (isset($_GET['limite'])) {
            $_SESSION['limite_publicaciones'] = (int) $_GET['limite'];
        }
        $limite = $_SESSION['limite_publicaciones'] ?? 10;

        // ✅ Página actual
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $offset = ($pagina - 1) * $limite;

        // ✅ Total de publicaciones filtradas
        $total = $this->modelo->contarFiltradas($usuario, $tipo, $busqueda, $id_departamento);
        $total_paginas = max(1, ceil($total / $limite));

        // ✅ Obtener publicaciones
        $publicaciones = $this->modelo->obtenerPaginadas($usuario, $tipo, $busqueda, $id_departamento, $limite, $offset);

        // Cargar departamentos (solo admin)
        $departamentos = $usuario['id_rol'] == ROL_ADMIN
            ? $this->modelo('DepartamentoModelo')->obtenerTodos()
            : [];

        $this->vista('contenido/inicio', [
            'publicaciones' => $publicaciones,
            'departamentos' => $departamentos,
            'filtro_tipo' => $tipo,
            'filtro_busqueda' => $busqueda,
            'filtro_departamento' => $id_departamento,
            'pagina' => $pagina,
            'total_paginas' => $total_paginas,
            'limite' => $limite
        ]);
    }

}
// public function inicio()
// {
//     verificarSesionActiva();

//     try {
//         $usuario = $_SESSION['usuario'];
//         $publicaciones = $this->modelo->obtenerTodas($usuario);

//         $this->vista('contenido/inicio', [
//             'publicaciones' => $publicaciones
//         ]);
//     } catch (Exception $e) {
//         // Si ocurre un error inesperado en la carga
//         $_SESSION['errorGeneral'] = 'Error al cargar publicaciones.';
//         $this->vista('contenido/inicio', ['publicaciones' => []]);
//     }
// }

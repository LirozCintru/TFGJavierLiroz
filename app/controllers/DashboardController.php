<?php
require_once RUTA_APP . '/models/Usuario.php';
require_once RUTA_APP . '/helpers/Auth.php';

class DashboardController {
    public function index() {
        redirectIfNotLoggedIn();

        global $pdo;
        $id_usuario = $_SESSION['usuario']['id'];
        $usuario = Usuario::obtenerDetalle($pdo, $id_usuario);

        require RUTA_APP . '/views/dashboard/index.php';
    }
}

<?php
require_once __DIR__ . '/../models/Usuario.php';
class DashboardController {
    public function index() {
        redirectIfNotLoggedIn(); // Solo usuarios logueados pueden ver esto

        $usuario = $_SESSION['usuario'];
        $nombre = $usuario['nombre'];
        $rol = $usuario['rol']; // Podrías mapearlo a un string con una consulta, si lo prefieres

        require '../app/views/dashboard/index.php';
    }

    public function logout() {
        session_destroy();
        header('Location: /auth/login');
        exit;
    }
    
}

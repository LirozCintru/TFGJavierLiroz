<?php
    
class Usuario {
    private $db;

    public function __construct() {
        $this->db = new DataBase();
    }

    public static function buscarPorEmail($pdo, $email) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function obtenerDetalle($pdo, $id_usuario) {
        $stmt = $pdo->prepare("
            SELECT u.nombre, u.email, r.nombre_rol, d.nombre AS nombre_departamento
            FROM usuarios u
            JOIN roles r ON u.id_rol = r.id_rol
            JOIN departamentos d ON u.id_departamento = d.id_departamento
            WHERE u.id_usuario = ?
        ");
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    // public function obtenerUsuarios() {
    //     $this->db->query("SELECT * from Usuarios");

    //     $resultados = $this->db->registros();
    //     return $resultados;
    // }

    // public function agregarUsuario($datos){
    //     $this->db->query("INSERT INTO usuarios (nombre, email, telefono) VALUES (:nombre, :email, :telefono)");

    //     // Vinculamos los valores
    //     $this->db->bind(":nombre", $datos["nombre"]);
    //     $this->db->bind(":email", $datos["email"]);
    //     $this->db->bind(":telefono", $datos["telefono"]);

    //     // Ejecutar la consulta
    //     if ($this->db->execute()) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }
}
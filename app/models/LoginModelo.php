<?php

class LoginModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function iniciarSesion($datos)
    {
        $this->db->query("
        SELECT u.*, r.nombre_rol
        FROM usuarios u
        JOIN roles r ON u.id_rol = r.id_rol
        WHERE u.email = :email
          AND u.activo = 1
    ");
        $this->db->bind(':email', $datos['email']);

        $fila = $this->db->registro();

        if ($fila && password_verify($datos['contrasena'], $fila->contrasena)) {
            return $fila;
        }

        return false;
    }


    public function obtenerUsuarioEmail($email)
    {
        $this->db->query("SELECT * FROM cliente WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->registro();
    }
}

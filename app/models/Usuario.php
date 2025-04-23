<?php

class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function iniciarSesion($datos){
        $this->db->query("SELECT * FROM usuarios WHERE email = :email");
        $this->db->bind(':email', $datos['email']);
    
        $fila = $this->db->registro();
    
        if($fila){
            if(password_verify($datos['contrasena'], $fila->contrasena)){
                return $fila;
            }
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
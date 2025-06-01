<?php

class RolModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function obtenerTodos()
    {
        $this->db->query("SELECT * FROM roles ORDER BY nombre_rol");
        return $this->db->registros();
    }
}

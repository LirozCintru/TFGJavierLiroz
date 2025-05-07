<?php
class DepartamentoModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function obtenerTodos()
    {
        $this->db->query("SELECT * FROM departamentos ORDER BY nombre ASC");
        return $this->db->registros();
    }
}

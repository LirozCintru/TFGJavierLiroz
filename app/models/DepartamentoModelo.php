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
        $this->db->query("
            SELECT id_departamento, nombre
            FROM departamentos
            ORDER BY nombre
        ");
        return $this->db->registros();   
    }

    public function obtenerPorId($id)
    {
        $this->db->query("SELECT * FROM departamentos WHERE id_departamento = :id");
        $this->db->bind(':id', $id);
        return $this->db->registro();
    }

    public function crear($datos)
    {
        $this->db->query("INSERT INTO departamentos (nombre, descripcion) VALUES (:nombre, :descripcion)");
        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':descripcion', $datos['descripcion']);
        return $this->db->execute();
    }

    public function actualizar($id, $datos)
    {
        $this->db->query("UPDATE departamentos SET nombre = :nombre, descripcion = :descripcion WHERE id_departamento = :id");
        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':descripcion', $datos['descripcion']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function eliminar($id)
    {
        $this->db->query("DELETE FROM departamentos WHERE id_departamento = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function existeNombre($nombre, $excluirId = null)
    {
        $query = "SELECT COUNT(*) as total FROM departamentos WHERE nombre = :nombre";
        if ($excluirId) {
            $query .= " AND id_departamento != :excluir";
        }
        $this->db->query($query);
        $this->db->bind(':nombre', $nombre);
        if ($excluirId)
            $this->db->bind(':excluir', $excluirId);
        return $this->db->registro()->total > 0;
    }

    public function tieneUsuariosAsignados($id_departamento)
    {
        $this->db->query("SELECT COUNT(*) as total FROM usuarios WHERE id_departamento = :id");
        $this->db->bind(':id', $id_departamento);
        return $this->db->registro()->total > 0;
    }

}

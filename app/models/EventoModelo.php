<?php
class EventoModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function obtenerTodos($usuario)
    {
        $sql = "
            SELECT e.*, d.nombre AS nombre_departamento
            FROM eventos e
            LEFT JOIN departamentos d ON e.id_departamento = d.id_departamento
            WHERE 1=1
        ";

        if ($usuario['id_rol'] != ROL_ADMIN) {
            $sql .= " AND (e.id_departamento IS NULL OR e.id_departamento = :id_departamento)";
            $this->db->query($sql);
            $this->db->bind(':id_departamento', $usuario['id_departamento']);
        } else {
            $this->db->query($sql);
        }

        return $this->db->registros();
    }

    public function obtenerPorId($id)
    {
        $this->db->query("SELECT * FROM eventos WHERE id_evento = :id");
        $this->db->bind(':id', $id);
        return $this->db->registro();
    }

    public function obtenerPorPublicacion($id_publicacion)
    {
        $this->db->query("SELECT * FROM eventos WHERE id_publicacion = :id_publicacion");
        $this->db->bind(':id_publicacion', $id_publicacion);
        return $this->db->registros();
    }

    public function crear($datos)
    {
        $this->db->query("
        INSERT INTO eventos (titulo, descripcion, fecha, hora, fecha_fin, hora_fin, todo_el_dia, url, color, id_departamento, id_publicacion)
        VALUES (:titulo, :descripcion, :fecha, :hora, :fecha_fin, :hora_fin, :todo_el_dia, :url, :color, :id_departamento, :id_publicacion)
    ");

        $this->db->bind(':titulo', $datos['titulo']);
        $this->db->bind(':descripcion', $datos['descripcion']);
        $this->db->bind(':fecha', $datos['fecha']);
        $this->db->bind(':hora', $datos['hora']);
        $this->db->bind(':fecha_fin', $datos['fecha_fin']);
        $this->db->bind(':hora_fin', $datos['hora_fin']);
        $this->db->bind(':todo_el_dia', $datos['todo_el_dia']);
        $this->db->bind(':url', $datos['url']);
        $this->db->bind(':color', $datos['color']);
        $this->db->bind(':id_departamento', $datos['id_departamento']);
        $this->db->bind(':id_publicacion', $datos['id_publicacion']);

        return $this->db->execute();
    }


    public function actualizar($datos)
    {
        $this->db->query("
        UPDATE eventos SET
            titulo = :titulo,
            descripcion = :descripcion,
            fecha = :fecha,
            hora = :hora,
            fecha_fin = :fecha_fin,
            hora_fin = :hora_fin,
            todo_el_dia = :todo_el_dia,
            url = :url,
            color = :color,
            id_departamento = :id_departamento
        WHERE id_evento = :id_evento
    ");

        $this->db->bind(':titulo', $datos['titulo']);
        $this->db->bind(':descripcion', $datos['descripcion']);
        $this->db->bind(':fecha', $datos['fecha']);
        $this->db->bind(':hora', $datos['hora']);
        $this->db->bind(':fecha_fin', $datos['fecha_fin']);
        $this->db->bind(':hora_fin', $datos['hora_fin']);
        $this->db->bind(':todo_el_dia', $datos['todo_el_dia']);
        $this->db->bind(':url', $datos['url']);
        $this->db->bind(':color', $datos['color']);
        $this->db->bind(':id_departamento', $datos['id_departamento']);
        $this->db->bind(':id_evento', $datos['id_evento']);

        return $this->db->execute();
    }



    public function eliminar($id)
    {
        $this->db->query("DELETE FROM eventos WHERE id_evento = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // private function bindDatos($datos)
    // {
    //     $this->db->bind(':titulo', $datos['titulo']);
    //     $this->db->bind(':descripcion', $datos['descripcion']);
    //     $this->db->bind(':fecha', $datos['fecha']);
    //     $this->db->bind(':hora', $datos['hora']);
    //     $this->db->bind(':id_departamento', $datos['id_departamento']);
    // }
}

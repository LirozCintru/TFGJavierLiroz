<?php
class ComentarioModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function obtenerPorPublicacion($id_publicacion)
    {
        $this->db->query("
            SELECT c.*, u.nombre, u.imagen
            FROM comentarios c
            JOIN usuarios u ON c.id_usuario = u.id_usuario
            WHERE c.id_publicacion = :id_publicacion
            ORDER BY c.fecha ASC
        ");
        $this->db->bind(':id_publicacion', $id_publicacion);
        return $this->db->registros();
    }

    public function insertar($datos)
    {
        $this->db->query("
            INSERT INTO comentarios (contenido, id_usuario, id_publicacion)
            VALUES (:contenido, :id_usuario, :id_publicacion)
        ");
        $this->db->bind(':contenido', $datos['contenido']);
        $this->db->bind(':id_usuario', $datos['id_usuario']);
        $this->db->bind(':id_publicacion', $datos['id_publicacion']);
        return $this->db->execute();
    }

    public function eliminar($id_comentario, $id_usuario)
    {
        $this->db->query("
            DELETE FROM comentarios 
            WHERE id_comentario = :id AND id_usuario = :usuario
        ");
        $this->db->bind(':id', $id_comentario);
        $this->db->bind(':usuario', $id_usuario);
        return $this->db->execute();
    }

    public function contarPorPublicacion($id_publicacion)
    {
        $this->db->query("SELECT COUNT(*) AS total FROM comentarios WHERE id_publicacion = :id");
        $this->db->bind(':id', $id_publicacion);
        return $this->db->registro()->total ?? 0;
    }

    public function obtenerUltimo($id_usuario, $id_publicacion)
    {
        $this->db->query("
        SELECT c.*, u.nombre, u.imagen
        FROM comentarios c
        JOIN usuarios u ON c.id_usuario = u.id_usuario
        WHERE c.id_usuario = :usuario AND c.id_publicacion = :publicacion
        ORDER BY c.fecha DESC
        LIMIT 1
    ");
        $this->db->bind(':usuario', $id_usuario);
        $this->db->bind(':publicacion', $id_publicacion);
        return $this->db->registro();
    }

    public function eliminarPorPublicacion($id_publicacion)
{
    $this->db->query("DELETE FROM comentarios WHERE id_publicacion = :id");
    $this->db->bind(':id', $id_publicacion);
    return $this->db->execute();
}


    

}

<?php
class PublicacionModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function obtenerTodas($usuario)
    {
        $this->db->query("
            SELECT p.*, u.nombre AS autor
            FROM publicaciones p
            JOIN usuarios u ON p.id_autor = u.id_usuario
            WHERE p.tipo = 'General'
               OR p.tipo = 'Urgente'
               OR (p.tipo = 'Departamental' AND p.id_departamento = :id_departamento)
            ORDER BY p.fecha DESC
        ");
        $this->db->bind(':id_departamento', $usuario['id_departamento']);
        return $this->db->registros();
    }

    public function crear($datos)
    {
        $this->db->query("INSERT INTO publicaciones (titulo, contenido, tipo, id_autor, id_departamento)
                          VALUES (:titulo, :contenido, :tipo, :id_autor, :id_departamento)");
        $this->db->bind(':titulo', $datos['titulo']);
        $this->db->bind(':contenido', $datos['contenido']);
        $this->db->bind(':tipo', $datos['tipo']);
        $this->db->bind(':id_autor', $datos['id_autor']);
        $this->db->bind(':id_departamento', $datos['id_departamento']);
        return $this->db->execute();
    }

    public function ultimoIdInsertado()
    {
        return $this->db->ultimoId(); // Método típico en tu clase DataBase
    }

    public function guardarImagenPublicacion($idPublicacion, $nombreArchivo)
    {
        $this->db->query("INSERT INTO imagenes_publicacion (id_publicacion, ruta_imagen)
                      VALUES (:id, :ruta)");
        $this->db->bind(':id', $idPublicacion);
        $this->db->bind(':ruta', $nombreArchivo);
        $this->db->execute();
    }

    public function obtenerImagenesPublicacion($idPublicacion)
{
    $this->db->query("SELECT ruta_imagen FROM imagenes_publicacion WHERE id_publicacion = :id");
    $this->db->bind(':id', $idPublicacion);
    return $this->db->registros();
}


    public function eliminar($id, $id_autor)
    {
        $this->db->query("DELETE FROM publicaciones WHERE id_publicacion = :id AND id_autor = :id_autor");
        $this->db->bind(':id', $id);
        $this->db->bind(':id_autor', $id_autor);
        return $this->db->execute();
    }
}

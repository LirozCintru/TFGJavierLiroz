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
        $sql = "
            SELECT p.*, u.nombre AS autor, d.nombre AS nombre_departamento
            FROM publicaciones p
            JOIN usuarios u ON p.id_autor = u.id_usuario
            LEFT JOIN departamentos d ON p.id_departamento = d.id_departamento
            WHERE p.tipo IN ('General', 'Urgente')
        ";

        if ($usuario['id_rol'] != 1 && !empty($usuario['id_departamento'])) {
            // Si NO es administrador, filtra también por su departamento
            $sql .= " OR (p.tipo = 'Departamental' AND p.id_departamento = :id_departamento)";
            $this->db->query($sql);
            $this->db->bind(':id_departamento', $usuario['id_departamento']);
        } else {
            // Admin ve todo sin filtro
            $sql .= " OR p.tipo = 'Departamental'";
            $this->db->query($sql);
        }

        return $this->db->registros();
    }

    public function crear($datos)
    {
        $this->db->query("INSERT INTO publicaciones 
        (titulo, contenido, tipo, id_autor, id_departamento, imagen_destacada)
        VALUES (:titulo, :contenido, :tipo, :id_autor, :id_departamento, :imagen_destacada)");

        $this->db->bind(':titulo', $datos['titulo']);
        $this->db->bind(':contenido', $datos['contenido']);
        $this->db->bind(':tipo', $datos['tipo']);
        $this->db->bind(':id_autor', $datos['id_autor']);
        $this->db->bind(':id_departamento', $datos['id_departamento']);
        $this->db->bind(':imagen_destacada', $datos['imagen_destacada']);
        $this->db->execute();

        // Obtener el último ID insertado por el autor
        $this->db->query("SELECT id_publicacion FROM publicaciones 
                      WHERE id_autor = :id_autor AND titulo = :titulo 
                      ORDER BY id_publicacion DESC LIMIT 1");
        $this->db->bind(':id_autor', $datos['id_autor']);
        $this->db->bind(':titulo', $datos['titulo']);
        $row = $this->db->registro();

        return $row ? $row->id_publicacion : null;

        // if ($row) {
        //     return $row->id_publicacion;
        // } else {
        //     return null;
        // }

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


    public function obtenerPorId($id)
    {
        $this->db->query("SELECT * FROM publicaciones WHERE id_publicacion = :id");
        $this->db->bind(':id', $id);
        return $this->db->registro();
    }

    public function eliminar($id)
    {
        // 1. Obtener nombre de imagen destacada
        $this->db->query("SELECT imagen_destacada FROM publicaciones WHERE id_publicacion = :id");
        $this->db->bind(':id', $id);
        $publicacion = $this->db->registro();

        if ($publicacion && !empty($publicacion->imagen_destacada)) {
            $ruta = RUTA_PUBLIC . '/img/publicaciones/' . $publicacion->imagen_destacada;
            if (file_exists($ruta)) {
                unlink($ruta);
            }
        }

        // 2. Obtener y eliminar imágenes adicionales
        $this->db->query("SELECT ruta_imagen FROM imagenes_publicacion WHERE id_publicacion = :id");
        $this->db->bind(':id', $id);
        $imagenes = $this->db->registros();

        foreach ($imagenes as $img) {
            $ruta = RUTA_PUBLIC . '/img/publicaciones/' . $img->ruta_imagen;
            if (file_exists($ruta)) {
                unlink($ruta);
            }
        }

        // 3. Eliminar registros de imagenes adicionales
        $this->db->query("DELETE FROM imagenes_publicacion WHERE id_publicacion = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();

        // 4. Eliminar publicación
        $this->db->query("DELETE FROM publicaciones WHERE id_publicacion = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function actualizar($datos)
    {
        $sql = "UPDATE publicaciones 
                SET titulo = :titulo, 
                    contenido = :contenido, 
                    tipo = :tipo, 
                    id_departamento = :id_departamento, 
                    imagen_destacada = :imagen_destacada
                WHERE id_publicacion = :id_publicacion";

        $this->db->query($sql);
        $this->db->bind(':titulo', $datos['titulo']);
        $this->db->bind(':contenido', $datos['contenido']);
        $this->db->bind(':tipo', $datos['tipo']);
        $this->db->bind(':id_departamento', $datos['id_departamento']);
        $this->db->bind(':imagen_destacada', $datos['imagen_destacada']);
        $this->db->bind(':id_publicacion', $datos['id_publicacion']);

        return $this->db->execute();
    }

    public function eliminarImagenAdicional($idPublicacion, $rutaImagen)
    {
        // Eliminar físicamente la imagen del servidor
        $ruta = RUTA_PUBLIC . '/img/publicaciones/' . $rutaImagen;
        if (file_exists($ruta)) {
            unlink($ruta);
        }

        // Eliminar el registro de la base de datos
        $this->db->query("DELETE FROM imagenes_publicacion 
                      WHERE id_publicacion = :id AND ruta_imagen = :ruta");
        $this->db->bind(':id', $idPublicacion);
        $this->db->bind(':ruta', $rutaImagen);
        return $this->db->execute();
    }



}

<?php
class PublicacionModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    // =============================
    // PUBLICACIONES
    // =============================

    public function obtenerTodas($usuario)
    {
        $sql = "
            SELECT p.*, u.nombre AS autor, d.nombre AS nombre_departamento
            FROM publicaciones p
            JOIN usuarios u ON p.id_autor = u.id_usuario
            LEFT JOIN departamentos d ON p.id_departamento = d.id_departamento
            WHERE p.tipo IN ('General', 'Urgente')
        ";

        // Si NO es administrador, añadir condición por departamento para tipo Departamental
        if ($usuario['id_rol'] != 1 && !empty($usuario['id_departamento'])) {
            $sql .= " OR (p.tipo = 'Departamental' AND p.id_departamento = :id_departamento)";
            $this->db->query($sql);
            $this->db->bind(':id_departamento', $usuario['id_departamento']);
        } else {
            $sql .= " OR p.tipo = 'Departamental'";
            $this->db->query($sql);
        }

        return $this->db->registros();
    }

    public function obtenerTodasFiltradas($usuario, $tipo = '', $busqueda = '', $id_departamento = null)
    {
        $sql = "
        SELECT p.*, u.nombre AS autor, d.nombre AS nombre_departamento
        FROM publicaciones p
        JOIN usuarios u ON p.id_autor = u.id_usuario
        LEFT JOIN departamentos d ON p.id_departamento = d.id_departamento
        WHERE 1=1
    ";

        // VISIBILIDAD según rol
        if ($usuario['id_rol'] != ROL_ADMIN) {
            $sql .= " AND (
            p.tipo IN ('General', 'Urgente')
            OR (p.tipo = 'Departamental' AND p.id_departamento = :id_departamento)
        )";
        }

        if (!empty($tipo)) {
            $sql .= " AND p.tipo = :tipo";
        }

        if (!empty($busqueda)) {
            $sql .= " AND (p.titulo LIKE :busqueda OR p.contenido LIKE :busqueda)";
        }

        if ($usuario['id_rol'] == ROL_ADMIN && !empty($id_departamento)) {
            $sql .= " AND p.id_departamento = :filtro_departamento";
        }


        $this->db->query($sql);

        //
        if ($usuario['id_rol'] != ROL_ADMIN) {
            $this->db->bind(':id_departamento', $id_departamento);
        }

        if (!empty($tipo)) {
            $this->db->bind(':tipo', $tipo);
        }

        if (!empty($busqueda)) {
            $this->db->bind(':busqueda', '%' . $busqueda . '%');
        }

        if ($usuario['id_rol'] == ROL_ADMIN && !empty($id_departamento)) {
            $this->db->bind(':filtro_departamento', $id_departamento);
        }

        return $this->db->registros();
    }


    // PublicacionModelo::obtenerPorId($id)
    public function obtenerPorId($id)
    {
        $this->db->query("
        SELECT p.*,
               u.nombre AS autor,                   -- ← alias autor
               d.nombre AS nombre_departamento
        FROM publicaciones p
        LEFT JOIN usuarios      u ON u.id_usuario      = p.id_autor
        LEFT JOIN departamentos d ON d.id_departamento = p.id_departamento
        WHERE p.id_publicacion = :id
        LIMIT 1
    ");
        $this->db->bind(':id', $id);
        return $this->db->registro();
    }



    public function crear($datos)
    {
        /* 1. Insertar la publicación */
        $this->db->query("
        INSERT INTO publicaciones
            (titulo, contenido, tipo, id_autor, id_departamento, imagen_destacada)
        VALUES
            (:titulo, :contenido, :tipo, :autor, :dep, :img)
    ");
        $this->db->bind(':titulo', $datos['titulo']);
        $this->db->bind(':contenido', $datos['contenido']);
        $this->db->bind(':tipo', $datos['tipo']);
        $this->db->bind(':autor', $datos['id_autor']);
        $this->db->bind(':dep', $datos['id_departamento']);
        $this->db->bind(':img', $datos['imagen_destacada']);
        $this->db->execute();                // ← se hace el INSERT

        /* 2. Recuperar el autoincrement de ESTA inserción */
        $this->db->query("SELECT LAST_INSERT_ID() AS id_pub"); // misma conexión ⇒ id correcto
        $row = $this->db->registro();                           // ejecuta y trae la fila

        return $row ? (int) $row->id_pub : null;                 //  ← aquí devuelves el id
    }



    public function actualizar($datos)
    {
        $this->db->query("
            UPDATE publicaciones SET 
                titulo = :titulo, 
                contenido = :contenido, 
                tipo = :tipo, 
                id_departamento = :id_departamento, 
                imagen_destacada = :imagen_destacada
            WHERE id_publicacion = :id_publicacion
        ");

        $this->bindDatosPublicacion($datos);
        $this->db->bind(':id_publicacion', $datos['id_publicacion']);
        return $this->db->execute();
    }

    public function eliminar($id)
    {
        $publicacion = $this->obtenerPorId($id);

        // Eliminar imagen destacada
        if (!empty($publicacion->imagen_destacada)) {
            $this->eliminarArchivo($publicacion->imagen_destacada);
        }

        // Eliminar imágenes adicionales físicas y en DB
        $imagenes = $this->obtenerImagenesPublicacion($id);
        foreach ($imagenes as $img) {
            $this->eliminarArchivo($img->ruta_imagen);
        }

        $this->db->query("DELETE FROM imagenes_publicacion WHERE id_publicacion = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();

        // Eliminar la publicación
        $this->db->query("DELETE FROM publicaciones WHERE id_publicacion = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // =============================
    // IMÁGENES ADICIONALES
    // =============================

    public function guardarImagenPublicacion($idPublicacion, $nombreArchivo)
    {
        $this->db->query("
            INSERT INTO imagenes_publicacion (id_publicacion, ruta_imagen)
            VALUES (:id, :ruta)
        ");
        $this->db->bind(':id', $idPublicacion);
        $this->db->bind(':ruta', $nombreArchivo);
        $this->db->execute();
    }

    public function obtenerImagenesPublicacion($idPublicacion)
    {
        $this->db->query("
            SELECT ruta_imagen 
            FROM imagenes_publicacion 
            WHERE id_publicacion = :id
        ");
        $this->db->bind(':id', $idPublicacion);
        return $this->db->registros();
    }

    public function eliminarImagenAdicional($idPublicacion, $rutaImagen)
    {
        $this->eliminarArchivo($rutaImagen);

        $this->db->query("
            DELETE FROM imagenes_publicacion 
            WHERE id_publicacion = :id AND ruta_imagen = :ruta
        ");
        $this->db->bind(':id', $idPublicacion);
        $this->db->bind(':ruta', $rutaImagen);
        return $this->db->execute();
    }

    // =============================
    // FUNCIONES PRIVADAS AUXILIARES
    // =============================

    private function bindDatosPublicacion($datos)
    {
        $this->db->bind(':titulo', $datos['titulo']);
        $this->db->bind(':contenido', $datos['contenido']);
        $this->db->bind(':tipo', $datos['tipo']);
        $this->db->bind(':id_departamento', $datos['id_departamento']);
        $this->db->bind(':imagen_destacada', $datos['imagen_destacada']);
    }

    private function eliminarArchivo($nombreArchivo)
    {
        $ruta = RUTA_PUBLIC . '/img/publicaciones/' . $nombreArchivo;
        if (file_exists($ruta)) {
            unlink($ruta);
        }
    }

    public function contarFiltradas($usuario, $tipo = '', $busqueda = '', $id_departamento = null)
    {
        $sql = "SELECT COUNT(*) as total FROM publicaciones WHERE 1=1";

        if ($usuario['id_rol'] != ROL_ADMIN) {
            $sql .= " AND (
            tipo IN ('General', 'Urgente') 
            OR (tipo = 'Departamental' AND id_departamento = :id_departamento)
        )";
        }

        if (!empty($tipo))
            $sql .= " AND tipo = :tipo";
        if (!empty($busqueda))
            $sql .= " AND (titulo LIKE :busqueda OR contenido LIKE :busqueda)";
        if ($usuario['id_rol'] == ROL_ADMIN && !empty($id_departamento))
            $sql .= " AND id_departamento = :dep";

        $this->db->query($sql);

        if ($usuario['id_rol'] != ROL_ADMIN) {
            $this->db->bind(':id_departamento', $id_departamento);
        }
        if (!empty($tipo))
            $this->db->bind(':tipo', $tipo);
        if (!empty($busqueda))
            $this->db->bind(':busqueda', '%' . $busqueda . '%');
        if ($usuario['id_rol'] == ROL_ADMIN && !empty($id_departamento))
            $this->db->bind(':dep', $id_departamento);

        return $this->db->registro()->total ?? 0;
    }

    public function obtenerPaginadas($usuario, $tipo = '', $busqueda = '', $id_departamento = null, $limite = 10, $offset = 0)
    {
        $sql = "
            SELECT p.*, u.nombre AS autor, d.nombre AS nombre_departamento
            FROM publicaciones p
            JOIN usuarios u ON p.id_autor = u.id_usuario
            LEFT JOIN departamentos d ON p.id_departamento = d.id_departamento
            WHERE 1=1
        ";

        if ($usuario['id_rol'] != ROL_ADMIN) {
            $sql .= " AND (
                p.tipo IN ('General', 'Urgente') 
                OR (p.tipo = 'Departamental' AND p.id_departamento = :id_departamento)
            )";
        }

        if (!empty($tipo))
            $sql .= " AND p.tipo = :tipo";
        if (!empty($busqueda))
            $sql .= " AND (p.titulo LIKE :busqueda OR p.contenido LIKE :busqueda)";
        if ($usuario['id_rol'] == ROL_ADMIN && !empty($id_departamento))
            $sql .= " AND p.id_departamento = :dep";

        $sql .= " ORDER BY p.fecha DESC LIMIT :limite OFFSET :offset";

        $this->db->query($sql);

        if ($usuario['id_rol'] != ROL_ADMIN) {
            $this->db->bind(':id_departamento', $id_departamento);
        }
        if (!empty($tipo))
            $this->db->bind(':tipo', $tipo);
        if (!empty($busqueda))
            $this->db->bind(':busqueda', '%' . $busqueda . '%');
        if ($usuario['id_rol'] == ROL_ADMIN && !empty($id_departamento))
            $this->db->bind(':dep', $id_departamento);

        $this->db->bind(':limite', (int) $limite, PDO::PARAM_INT);
        $this->db->bind(':offset', (int) $offset, PDO::PARAM_INT);

        return $this->db->registros();
    }


}

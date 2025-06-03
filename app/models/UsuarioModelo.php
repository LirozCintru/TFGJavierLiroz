<?php
class UsuarioModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function crear($datos)
    {
        $this->db->query("INSERT INTO usuarios (nombre, email, contrasena, id_rol, id_departamento, activo, imagen)
                      VALUES (:nombre, :email, :contrasena, :id_rol, :id_departamento, :activo, :imagen)");

        foreach ($datos as $clave => $valor) {
            $this->db->bind(":$clave", $valor);
        }

        return $this->db->execute();
    }


    public function obtenerFiltrados($nombre = '', $departamento = '')
    {
        $sql = "SELECT u.*, r.nombre_rol AS nombre_rol, d.nombre AS nombre_departamento
            FROM usuarios u
            JOIN roles r ON u.id_rol = r.id_rol
            JOIN departamentos d ON u.id_departamento = d.id_departamento
            WHERE 1=1";

        if (!empty($nombre)) {
            $sql .= " AND (u.nombre LIKE :nombre OR u.email LIKE :nombre)";
        }

        if (!empty($departamento)) {
            $sql .= " AND u.id_departamento = :departamento";
        }

        $sql .= " ORDER BY u.nombre ASC";

        $this->db->query($sql);

        if (!empty($nombre)) {
            $this->db->bind(':nombre', '%' . $nombre . '%');
        }

        if (!empty($departamento)) {
            $this->db->bind(':departamento', $departamento);
        }

        return $this->db->registros();
    }


    public function obtenerPorId($id)
    {
        $this->db->query("SELECT * FROM usuarios WHERE id_usuario = :id");
        $this->db->bind(':id', $id);
        return $this->db->registro();
    }


    public function actualizar($datos)
    {
        $sql = "UPDATE usuarios SET 
                nombre = :nombre,
                email = :email,
                id_rol = :id_rol,
                id_departamento = :id_departamento,
                activo = :activo,
                imagen = :imagen";

        if (!empty($datos['contrasena'])) {
            $sql .= ", contrasena = :contrasena";
        }

        $sql .= " WHERE id_usuario = :id_usuario";

        $this->db->query($sql);

        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':email', $datos['email']);
        $this->db->bind(':id_rol', $datos['id_rol']);
        $this->db->bind(':id_departamento', $datos['id_departamento']);
        $this->db->bind(':activo', $datos['activo']);
        $this->db->bind(':imagen', $datos['imagen']);
        $this->db->bind(':id_usuario', $datos['id_usuario']);

        if (!empty($datos['contrasena'])) {
            $this->db->bind(':contrasena', $datos['contrasena']);
        }

        return $this->db->execute();
    }


    public function eliminar($id)
    {
        $this->db->query("DELETE FROM usuarios WHERE id_usuario = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function existeEmail($email, $excluir_id = null)
    {
        $sql = "SELECT id_usuario FROM usuarios WHERE email = :email";
        if ($excluir_id !== null) {
            $sql .= " AND id_usuario != :excluir_id";
        }

        $this->db->query($sql);
        $this->db->bind(':email', $email);

        if ($excluir_id !== null) {
            $this->db->bind(':excluir_id', $excluir_id);
        }

        return $this->db->registro() ? true : false;
    }

    public function actualizarPerfil($id_usuario, $valores, $nuevaContrasena = null, $imagen = null)
    {
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email";

        if ($nuevaContrasena) {
            $sql .= ", contrasena = :contrasena";
        }

        if ($imagen) {
            $sql .= ", imagen = :imagen";
        }

        $sql .= " WHERE id_usuario = :id_usuario";

        $this->db->query($sql);
        $this->db->bind(':nombre', $valores['nombre']);
        $this->db->bind(':email', $valores['email']);
        $this->db->bind(':id_usuario', $id_usuario);

        if ($nuevaContrasena) {
            $this->db->bind(':contrasena', $nuevaContrasena);
        }

        if ($imagen) {
            $this->db->bind(':imagen', $imagen);
        }

        return $this->db->execute();
    }


    public function obtenerUltimoId()
    {
        $this->db->query("SELECT LAST_INSERT_ID() as id");
        return $this->db->registro()->id;
    }

    public function actualizarContrasena($id_usuario, $contrasena_hash)
    {
        $this->db->query("UPDATE usuarios SET contrasena = :contrasena WHERE id_usuario = :id");
        $this->db->bind(':contrasena', $contrasena_hash);
        $this->db->bind(':id', $id_usuario);
        return $this->db->execute();
    }

    public function obtenerPorEmail($email)
    {
        $this->db->query("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $this->db->bind(':email', $email);
        return $this->db->registro();
    }

    public function obtenerTodosMenos(int $id_excluido)
    {
        $this->db->query("
        SELECT id_usuario, nombre, imagen
        FROM usuarios
        WHERE id_usuario <> :id
          AND activo = 1
        ORDER BY nombre
    ");
        $this->db->bind(':id', $id_excluido);
        return $this->db->registros();
    }

    /**
     * Devuelve usuarios activos distintos de $miId, filtrados opcionalmente
     * por nombre (LIKE) y/o por departamento.
     */
    public function buscar(int $miId, ?string $filtroNombre = null, ?int $filtroDepartamento = null): array
    {
        $sql = "
            SELECT id_usuario, nombre, imagen, id_departamento
            FROM usuarios
            WHERE id_usuario <> :miId
              AND activo = 1
        ";

        if (!empty($filtroNombre)) {
            $sql .= " AND nombre LIKE :fnombre ";
        }
        if ($filtroDepartamento !== null && $filtroDepartamento > 0) {
            $sql .= " AND id_departamento = :fdep ";
        }

        $sql .= " ORDER BY nombre ";

        $this->db->query($sql);
        $this->db->bind(':miId', $miId);

        if (!empty($filtroNombre)) {
            $this->db->bind(':fnombre', '%' . $filtroNombre . '%');
        }
        if ($filtroDepartamento !== null && $filtroDepartamento > 0) {
            $this->db->bind(':fdep', $filtroDepartamento);
        }

        $filas = $this->db->registros();
        return is_array($filas) ? $filas : [];
    }








}

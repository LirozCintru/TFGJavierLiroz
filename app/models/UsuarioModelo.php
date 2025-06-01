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

    public function actualizarPerfil($id_usuario, $datos, $nuevaContrasena = '', $nuevaImagen = null)
    {
        $campos = [];
        $params = [':id' => $id_usuario];

        // Nombre y email
        $campos[] = "nombre = :nombre";
        $params[':nombre'] = $datos['nombre'];

        $campos[] = "email = :email";
        $params[':email'] = $datos['email'];

        // Contraseña (si se quiere cambiar)
        if (!empty($nuevaContrasena)) {
            $campos[] = "contrasena = :contrasena";
            $params[':contrasena'] = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
        }

        // Imagen (si se ha subido una nueva)
        if (!empty($nuevaImagen)) {
            $campos[] = "imagen = :imagen";
            $params[':imagen'] = $nuevaImagen;
        }

        // Construir la consulta
        $sql = "UPDATE usuarios SET " . implode(', ', $campos) . " WHERE id_usuario = :id";
        $this->db->query($sql);

        foreach ($params as $clave => $valor) {
            $this->db->bind($clave, $valor);
        }

        return $this->db->execute();
    }






}

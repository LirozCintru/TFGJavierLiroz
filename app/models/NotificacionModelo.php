<?php
class NotificacionModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function crear($datos)
    {
        $this->db->query("INSERT INTO notificaciones (id_usuario_destino, mensaje, tipo, id_referencia, leida) 
                      VALUES (:id_usuario_destino, :mensaje, :tipo, :id_referencia, 0)");
        $this->db->bind(':id_usuario_destino', $datos['id_usuario_destino']);
        $this->db->bind(':mensaje', $datos['mensaje']);
        $this->db->bind(':tipo', $datos['tipo']);
        $this->db->bind(':id_referencia', $datos['id_referencia']);
        return $this->db->execute();
    }

    public function obtenerTodosMenos($id_autor)
    {
        $this->db->query("SELECT id_usuario FROM usuarios WHERE id_usuario != :id");
        $this->db->bind(':id', $id_autor);
        return $this->db->registros();
    }

    public function obtenerPorDepartamento($id_departamento, $id_autor)
    {
        $this->db->query("SELECT id_usuario FROM usuarios 
                      WHERE id_departamento = :id_dep AND id_usuario != :id");
        $this->db->bind(':id_dep', $id_departamento);
        $this->db->bind(':id', $id_autor);
        return $this->db->registros();
    }

    public function marcarComoLeida($id_notificacion)
    {
        $this->db->query("UPDATE notificaciones SET leida = 1 WHERE id_notificacion = :id");
        $this->db->bind(':id', $id_notificacion);
        return $this->db->execute();
    }

    public function obtenerPorUsuario($id_usuario)
    {
        $this->db->query("SELECT * FROM notificaciones WHERE id_usuario_destino = :id ORDER BY fecha DESC");
        $this->db->bind(':id', $id_usuario);
        return $this->db->registros();
    }

    public function contarPendientes($id_usuario)
    {
        $this->db->query("SELECT COUNT(*) as total FROM notificaciones WHERE id_usuario_destino = :id AND leida = 0");
        $this->db->bind(':id', $id_usuario);
        return $this->db->registro()->total;
    }

    public function marcarComoLeidas($id_usuario)
    {
        $this->db->query("UPDATE notificaciones SET leida = 1 WHERE id_usuario_destino = :id");
        $this->db->bind(':id', $id_usuario);
        return $this->db->execute();
    }

    public function obtenerNoLeidas($id_usuario)
    {
        $this->db->query("SELECT * FROM notificaciones WHERE id_usuario_destino = :id AND leida = 0 ORDER BY fecha DESC");
        $this->db->bind(':id', $id_usuario);
        return $this->db->registros();
    }

    public function contarNoLeidas($id_usuario)
    {
        $this->db->query("SELECT COUNT(*) as total FROM notificaciones WHERE id_usuario_destino = :id AND leida = 0");
        $this->db->bind(':id', $id_usuario);
        return $this->db->registro()->total ?? 0;
    }

    public function marcarTodasComoLeidas($usuarioId)
    {
        $sql = "UPDATE notificaciones SET leida = 1 WHERE id_usuario_destino = :usuarioId";
        $this->db->query($sql);
        $this->db->bind(':usuarioId', $usuarioId);
        $this->db->execute();
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM notificaciones WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->execute();
    }



}

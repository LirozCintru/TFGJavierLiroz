<?php
class NotificacionModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    // 🔔 Crear nueva notificación
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

    // 📬 Obtener TODAS (leídas y no leídas)
    public function obtenerTodas($id_usuario)
    {
        $this->db->query("SELECT * FROM notificaciones 
                          WHERE id_usuario_destino = :id 
                          ORDER BY fecha DESC");
        $this->db->bind(':id', $id_usuario);
        return $this->db->registros();
    }

    // 📬 Obtener solo no leídas
    public function obtenerNoLeidas($id_usuario)
    {
        $this->db->query("SELECT * FROM notificaciones 
                          WHERE id_usuario_destino = :id AND leida = 0 
                          ORDER BY fecha DESC");
        $this->db->bind(':id', $id_usuario);
        return $this->db->registros();
    }

    // 🔢 Contar no leídas
    public function contarNoLeidas($id_usuario)
    {
        $this->db->query("SELECT COUNT(*) as total 
                          FROM notificaciones 
                          WHERE id_usuario_destino = :id AND leida = 0");
        $this->db->bind(':id', $id_usuario);
        return $this->db->registro()->total ?? 0;
    }

    // ✅ Marcar una notificación como leída
    public function marcarComoLeida($id_notificacion)
    {
        $this->db->query("UPDATE notificaciones 
                          SET leida = 1 
                          WHERE id_notificacion = :id");
        $this->db->bind(':id', $id_notificacion);
        return $this->db->execute();
    }

    // ✅ Marcar todas como leídas
    public function marcarTodasComoLeidas($usuarioId)
    {
        $this->db->query("UPDATE notificaciones 
                          SET leida = 1 
                          WHERE id_usuario_destino = :usuarioId");
        $this->db->bind(':usuarioId', $usuarioId);
        return $this->db->execute();
    }

    // 🗑️ Eliminar una notificación (por ID)
    public function eliminar($id)
    {
        $this->db->query("DELETE FROM notificaciones 
                          WHERE id_notificacion = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // 👥 Obtener todos los usuarios excepto el autor
    public function obtenerTodosMenos($id_autor)
    {
        $this->db->query("SELECT id_usuario FROM usuarios 
                          WHERE id_usuario != :id");
        $this->db->bind(':id', $id_autor);
        return $this->db->registros();
    }

    // 👥 Obtener usuarios de un departamento, excepto el autor
    public function obtenerPorDepartamento($id_departamento, $id_autor)
    {
        $this->db->query("SELECT id_usuario FROM usuarios 
                          WHERE id_departamento = :id_dep AND id_usuario != :id");
        $this->db->bind(':id_dep', $id_departamento);
        $this->db->bind(':id', $id_autor);
        return $this->db->registros();
    }
}

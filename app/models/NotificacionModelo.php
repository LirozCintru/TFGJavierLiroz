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
}

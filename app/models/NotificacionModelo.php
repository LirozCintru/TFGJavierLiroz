<?php
class NotificacionModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    // Crear nueva notificación
    public function crear($datos)
    {
        /* 1. Insertar notificación */
        $this->db->query("
        INSERT INTO notificaciones
            (id_usuario_destino, mensaje, tipo, id_referencia, leida)
        VALUES
            (:id_usuario_destino, :mensaje, :tipo, :id_referencia, 0)
    ");
        $this->db->bind(':id_usuario_destino', $datos['id_usuario_destino']);
        $this->db->bind(':mensaje', $datos['mensaje']);
        $this->db->bind(':tipo', $datos['tipo'] ?? null);
        $this->db->bind(':id_referencia', $datos['id_referencia'] ?? null);
        $this->db->execute();

        /* 2. --> correo solo una vez por petición */
        static $yaEnviado = [];
        $uid = $datos['id_usuario_destino'];
        if (isset($yaEnviado[$uid]))
            return;
        $yaEnviado[$uid] = true;

        /* 3. totales pendientes */
        $pendientes = $this->contarNoLeidas($uid);

        /* 4. email destino */
        $this->db->query("SELECT nombre, email FROM usuarios WHERE id_usuario = :id LIMIT 1");
        $this->db->bind(':id', $uid);
        $u = $this->db->registro();
        if (!$u)
            return;

        /* 5. enviar */
        Correo::enviarBonito(
            $u->email,
            "🔔 Tienes $pendientes notificación(es) en IntraLink",
            "Hola <strong>" . htmlspecialchars($u->nombre) . "</strong>,<br><br>
         Tienes <strong>$pendientes</strong> notificación(es) sin leer en IntraLink.",
            "Ver notificaciones",
            RUTA_URL . "/NotificacionesControlador/index"
        );
    }


    // Obtener TODAS (leídas y no leídas)
    public function obtenerTodas($id_usuario)
    {
        $this->db->query("SELECT * FROM notificaciones 
                          WHERE id_usuario_destino = :id 
                          ORDER BY fecha DESC");
        $this->db->bind(':id', $id_usuario);
        return $this->db->registros();
    }

    // Obtener solo no leídas
    public function obtenerNoLeidas($id_usuario)
    {
        $this->db->query("SELECT * FROM notificaciones 
                          WHERE id_usuario_destino = :id AND leida = 0 
                          ORDER BY fecha DESC");
        $this->db->bind(':id', $id_usuario);
        return $this->db->registros();
    }

    //Contar no leídas
    public function contarNoLeidas($id_usuario)
    {
        $this->db->query("SELECT COUNT(*) as total 
                          FROM notificaciones 
                          WHERE id_usuario_destino = :id AND leida = 0");
        $this->db->bind(':id', $id_usuario);
        return $this->db->registro()->total ?? 0;
    }

    //Marcar una notificación como leída
    public function marcarComoLeida($id_notificacion)
    {
        $this->db->query("UPDATE notificaciones 
                          SET leida = 1 
                          WHERE id_notificacion = :id");
        $this->db->bind(':id', $id_notificacion);
        return $this->db->execute();
    }

    //Marcar todas como leídas
    public function marcarTodasComoLeidas($usuarioId)
    {
        $this->db->query("UPDATE notificaciones 
                          SET leida = 1 
                          WHERE id_usuario_destino = :usuarioId");
        $this->db->bind(':usuarioId', $usuarioId);
        return $this->db->execute();
    }

    //Eliminar notificación (por ID)
    public function eliminar($id)
    {
        $this->db->query("DELETE FROM notificaciones 
                          WHERE id_notificacion = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    //Obtener todos los usuarios excepto el autor
    public function obtenerTodosMenos($id_autor)
    {
        $this->db->query("SELECT id_usuario FROM usuarios 
                          WHERE id_usuario != :id");
        $this->db->bind(':id', $id_autor);
        return $this->db->registros();
    }

    //Obtener usuarios de un departamento, excepto el autor
    public function obtenerPorDepartamento($id_departamento, $id_autor)
    {
        $this->db->query("SELECT id_usuario FROM usuarios 
                          WHERE id_departamento = :id_dep AND id_usuario != :id");
        $this->db->bind(':id_dep', $id_departamento);
        $this->db->bind(':id', $id_autor);
        return $this->db->registros();
    }

/*─────────────────────────────────────────────
 | Elimina todas las notificaciones cuya
 | id_referencia apunta a la publicación dada
 *────────────────────────────────────────────*/
    public function eliminarPorReferencia($id_referencia, $tipo = null)
    {
        $sql = "DELETE FROM notificaciones WHERE id_referencia = :id";
        if ($tipo !== null) {
            $sql .= " AND tipo = :tipo";
        }

        $this->db->query($sql);
        $this->db->bind(':id', $id_referencia);

        if ($tipo !== null) {
            $this->db->bind(':tipo', $tipo);
        }

        return $this->db->execute();
    }


}

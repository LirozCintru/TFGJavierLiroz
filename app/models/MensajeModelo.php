<?php
class MensajeModelo
{
    private $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    /**
     * Inserta un nuevo mensaje y devuelve su ID autoincremental.
     * $datos = [
     *   'contenido'        => (string),
     *   'id_remitente'     => (int),
     *   'id_destinatario'  => (int),
     * ];
     */
    public function crear(array $datos): int
    {
        // 1) Insertar fila
        $this->db->query("
        INSERT INTO mensajes (contenido, id_remitente, id_destinatario, leido)
        VALUES (:contenido, :rem, :dest, 0)
    ");
        $this->db->bind(':contenido', (string) $datos['contenido']);
        $this->db->bind(':rem', (int) $datos['id_remitente']);
        $this->db->bind(':dest', (int) $datos['id_destinatario']);
        $this->db->execute();

        // 2) Recuperar último ID insertado
        $this->db->query("SELECT LAST_INSERT_ID() AS id");
        $fila = $this->db->registro();
        return isset($fila->id) ? (int) $fila->id : 0;
    }


    /**
     * Obtiene los últimos $lim mensajes entre $u1 y $u2, ordenados cronológicamente ascendente.
     * Devuelve un array de objetos: { id_mensaje, contenido, id_remitente, id_destinatario, fecha, leido }.
     */
    public function obtenerConversacion(int $u1, int $u2, int $lim = 50, int $off = 0): array
    {
        // MySQL no siempre permite bind directo de LIMIT, así que nos aseguramos de usar PDO::PARAM_INT.
        $this->db->query("
            SELECT id_mensaje, contenido, id_remitente, id_destinatario, fecha, leido
            FROM mensajes
            WHERE (id_remitente = :u1 AND id_destinatario = :u2)
               OR (id_remitente = :u2 AND id_destinatario = :u1)
            ORDER BY fecha DESC
            LIMIT :lim OFFSET :off
        ");
        $this->db->bind(':u1', $u1, PDO::PARAM_INT);
        $this->db->bind(':u2', $u2, PDO::PARAM_INT);
        $this->db->bind(':lim', $lim, PDO::PARAM_INT);
        $this->db->bind(':off', $off, PDO::PARAM_INT);

        $rows = $this->db->registros();
        // Vienen en orden DESC (más reciente primero). Damos vuelta para ASC (más antiguo primero).
        return is_array($rows) ? array_reverse($rows) : [];
    }

    /**
     * Marca como leídos todos los mensajes que $otroId envió a $miId.
     */
    public function marcarLeidosDeUsuario(int $miId, int $otroId): bool
    {
        $this->db->query("
            UPDATE mensajes
            SET leido = 1
            WHERE id_remitente = :otro
              AND id_destinatario = :yo
              AND leido = 0
        ");
        $this->db->bind(':otro', $otroId, PDO::PARAM_INT);
        $this->db->bind(':yo', $miId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Devuelve solo los mensajes NUEVOS (id_mensaje > $ultimoId) entre $u1 y $u2, en orden ascendente.
     */
    public function obtenerNuevos(int $u1, int $u2, int $ultimoId): array
    {
        $this->db->query("
            SELECT id_mensaje, contenido, id_remitente, id_destinatario, fecha, leido
            FROM mensajes
            WHERE id_mensaje > :last
              AND (
                    (id_remitente = :u2 AND id_destinatario = :u1)
                    OR
                    (id_remitente = :u1 AND id_destinatario = :u2)
              )
            ORDER BY fecha ASC
        ");
        $this->db->bind(':last', $ultimoId, PDO::PARAM_INT);
        $this->db->bind(':u1', $u1, PDO::PARAM_INT);
        $this->db->bind(':u2', $u2, PDO::PARAM_INT);

        $rows = $this->db->registros();
        return is_array($rows) ? $rows : [];
    }

    /**
     * Devuelve un array asociativo [ remitenteId => totalSinLeer, ... ]
     * contando cuántos mensajes no leídos tengo POR CADA remitente.
     */
    public function contarNoLeidosPorRemitente(int $uid): array
    {
        $this->db->query("
            SELECT id_remitente, COUNT(*) AS total
            FROM mensajes
            WHERE id_destinatario = :uid AND leido = 0
            GROUP BY id_remitente
        ");
        $this->db->bind(':uid', $uid, PDO::PARAM_INT);
        $filas = $this->db->registros();

        $res = [];
        if (is_array($filas)) {
            foreach ($filas as $r) {
                $res[(int) $r->id_remitente] = (int) $r->total;
            }
        }
        return $res;
    }

    /**
     * Devuelve el total de mensajes no leídos donde $uid es destinatario.
     * Se usa para el badge global en el header.
     */
    public function contarNoLeidosTotales(int $uid): int
    {
        $this->db->query("
            SELECT COUNT(*) AS total
            FROM mensajes
            WHERE id_destinatario = :uid AND leido = 0
        ");
        $this->db->bind(':uid', $uid, PDO::PARAM_INT);
        $fila = $this->db->registro();
        return isset($fila->total) ? (int) $fila->total : 0;
    }
}

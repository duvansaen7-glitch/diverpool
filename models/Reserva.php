<?php

require_once __DIR__ . '/../config/database.php';

class Reserva
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    /**
     * Obtener todas las reservas de un usuario.
     */
    public function obtenerPorUsuario(int $usuarioId): array
    {
        $sql = "
            SELECT
                r.id,
                r.usuario_id,
                r.mascota_id,
                r.servicio_id,
                r.profesional_id,
                r.sede_id,
                r.estado_id,
                r.fecha,
                r.hora_inicio,
                r.hora_fin,
                r.precio,
                r.observaciones,
                r.motivo_cancelacion,
                r.created_at,
                r.updated_at,

                m.nombre AS mascota_nombre,

                s.nombre AS servicio_nombre,
                s.descripcion AS servicio_descripcion,
                s.duracion_minutos,

                er.nombre AS estado_nombre,
                er.descripcion AS estado_descripcion,

                se.nombre AS sede_nombre,
                se.direccion AS sede_direccion,
                se.ciudad AS sede_ciudad,

                p.id AS profesional_id_real,
                p.numero_tarjeta_profesional,
                p.experiencia_anios,
                p.foto AS profesional_foto,

                u.nombres AS profesional_nombres,
                u.apellidos AS profesional_apellidos

            FROM reservas r

            INNER JOIN mascotas m
                ON m.id = r.mascota_id

            INNER JOIN servicios s
                ON s.id = r.servicio_id

            INNER JOIN estados_reserva er
                ON er.id = r.estado_id

            INNER JOIN sedes se
                ON se.id = r.sede_id

            LEFT JOIN profesionales p
                ON p.id = r.profesional_id

            LEFT JOIN usuarios u
                ON u.id = p.usuario_id

            WHERE r.usuario_id = :usuario_id

            ORDER BY
                r.fecha DESC,
                r.hora_inicio DESC,
                r.id DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':usuario_id' => $usuarioId
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Obtener una reserva específica.
     *
     * El usuario_id garantiza que el cliente
     * solamente pueda consultar sus propias reservas.
     */
    public function obtenerPorId(
        int $reservaId,
        int $usuarioId
    ): ?array {
        $sql = "
            SELECT
                r.id,
                r.usuario_id,
                r.mascota_id,
                r.servicio_id,
                r.profesional_id,
                r.sede_id,
                r.estado_id,
                r.fecha,
                r.hora_inicio,
                r.hora_fin,
                r.precio,
                r.observaciones,
                r.motivo_cancelacion,
                r.created_at,
                r.updated_at,

                m.nombre AS mascota_nombre,

                s.nombre AS servicio_nombre,
                s.descripcion AS servicio_descripcion,
                s.duracion_minutos,

                er.nombre AS estado_nombre,
                er.descripcion AS estado_descripcion,

                se.nombre AS sede_nombre,
                se.direccion AS sede_direccion,
                se.ciudad AS sede_ciudad,

                p.numero_tarjeta_profesional,
                p.experiencia_anios,
                p.foto AS profesional_foto,

                u.nombres AS profesional_nombres,
                u.apellidos AS profesional_apellidos

            FROM reservas r

            INNER JOIN mascotas m
                ON m.id = r.mascota_id

            INNER JOIN servicios s
                ON s.id = r.servicio_id

            INNER JOIN estados_reserva er
                ON er.id = r.estado_id

            INNER JOIN sedes se
                ON se.id = r.sede_id

            LEFT JOIN profesionales p
                ON p.id = r.profesional_id

            LEFT JOIN usuarios u
                ON u.id = p.usuario_id

            WHERE r.id = :reserva_id
              AND r.usuario_id = :usuario_id

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':reserva_id' => $reservaId,
            ':usuario_id' => $usuarioId
        ]);

        $reserva = $stmt->fetch();

        return $reserva ?: null;
    }

    /**
     * Obtener las reservas futuras de un usuario.
     */
    public function obtenerFuturas(int $usuarioId): array
    {
        $sql = "
            SELECT
                r.id,
                r.fecha,
                r.hora_inicio,
                r.hora_fin,
                r.precio,

                m.nombre AS mascota_nombre,
                s.nombre AS servicio_nombre,
                er.nombre AS estado_nombre,

                u.nombres AS profesional_nombres,
                u.apellidos AS profesional_apellidos

            FROM reservas r

            INNER JOIN mascotas m
                ON m.id = r.mascota_id

            INNER JOIN servicios s
                ON s.id = r.servicio_id

            INNER JOIN estados_reserva er
                ON er.id = r.estado_id

            LEFT JOIN profesionales p
                ON p.id = r.profesional_id

            LEFT JOIN usuarios u
                ON u.id = p.usuario_id

            WHERE r.usuario_id = :usuario_id
              AND r.fecha >= CURDATE()
              AND er.nombre NOT IN ('Cancelada')

            ORDER BY
                r.fecha ASC,
                r.hora_inicio ASC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':usuario_id' => $usuarioId
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Cancelar una reserva.
     *
     * No elimina físicamente la reserva.
     * Cambia su estado y conserva el historial.
     */
    public function cancelar(
        int $reservaId,
        int $usuarioId,
        string $motivo
    ): bool {
        $sql = "
            UPDATE reservas r

            INNER JOIN estados_reserva er
                ON er.nombre = 'Cancelada'

            SET
                r.estado_id = er.id,
                r.motivo_cancelacion = :motivo

            WHERE r.id = :reserva_id
              AND r.usuario_id = :usuario_id

              AND r.estado_id IN (
                  SELECT id
                  FROM estados_reserva
                  WHERE nombre IN ('Pendiente', 'Confirmada')
              )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':reserva_id' => $reservaId,
            ':usuario_id' => $usuarioId,
            ':motivo' => $motivo
        ]);
    }
}


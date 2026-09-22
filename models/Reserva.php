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
    public function obtenerHorariosDisponibles(
        int $servicioId,
        int $sedeId,
        string $fecha
    ): array {
        $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);

        if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
            return [];
        }

        if ($fecha < date('Y-m-d')) {
            return [];
        }

        /*
         * PHP:
         * 1 = lunes
         * 2 = martes
         * ...
         * 7 = domingo
         */
        $diaSemana = (int) $fechaObj->format('N');

        /*
         * Obtener duración del servicio.
         */
        $sqlServicio = "
        SELECT duracion_minutos
        FROM servicios
        WHERE id = :servicio_id
          AND estado = 'activo'
        LIMIT 1
    ";

        $stmt = $this->db->prepare($sqlServicio);
        $stmt->execute([
            ':servicio_id' => $servicioId
        ]);

        $servicio = $stmt->fetch();

        if (!$servicio) {
            return [];
        }

        $duracion = (int) $servicio['duracion_minutos'];

        if ($duracion <= 0) {
            return [];
        }

        /*
         * Obtener los horarios generales de la sede
         * para el día seleccionado.
         */
        $sqlHorario = "
        SELECT
            id,
            hora_inicio,
            hora_fin
        FROM horarios
        WHERE sede_id = :sede_id
          AND dia_semana = :dia_semana
          AND activo = 1
        ORDER BY hora_inicio ASC
    ";

        $stmt = $this->db->prepare($sqlHorario);
        $stmt->execute([
            ':sede_id' => $sedeId,
            ':dia_semana' => $diaSemana
        ]);

        $horariosBase = $stmt->fetchAll();

        if (!$horariosBase) {
            return [];
        }

        /*
         * Obtener profesionales activos que pueden
         * atender el servicio.
         */
        $sqlProfesionales = "
        SELECT DISTINCT
            p.id
        FROM profesionales p
        INNER JOIN servicio_profesionales sp
            ON sp.profesional_id = p.id
        INNER JOIN usuarios u
            ON u.id = p.usuario_id
        WHERE sp.servicio_id = :servicio_id
          AND p.estado = 'activo'
          AND u.estado = 'activo'
    ";

        $stmt = $this->db->prepare($sqlProfesionales);
        $stmt->execute([
            ':servicio_id' => $servicioId
        ]);

        $profesionales = $stmt->fetchAll();

        if (!$profesionales) {
            return [];
        }

        $profesionalIds = array_map(
            static fn($profesional) => (int) $profesional['id'],
            $profesionales
        );

        $cantidadProfesionales = count($profesionalIds);

        $resultado = [];

        foreach ($horariosBase as $horario) {

            $inicio = new DateTime($fecha . ' ' . $horario['hora_inicio']);
            $finJornada = new DateTime($fecha . ' ' . $horario['hora_fin']);

            while (true) {

                $fin = clone $inicio;
                $fin->modify("+{$duracion} minutes");

                /*
                 * El servicio no puede terminar después
                 * del horario de la sede.
                 */
                if ($fin > $finJornada) {
                    break;
                }

                $horaInicio = $inicio->format('H:i:s');
                $horaFin = $fin->format('H:i:s');

                /*
                 * Profesionales disponibles para este bloque.
                 */
                $profesionalesDisponibles = 0;

                foreach ($profesionalIds as $profesionalId) {

                    /*
                     * Primero revisamos disponibilidad
                     * específica del profesional para esa fecha.
                     *
                     * Si no existe un registro específico,
                     * utilizamos el horario general.
                     */
                    $sqlDisponibilidad = "
                    SELECT COUNT(*)
                    FROM disponibilidad
                    WHERE profesional_id = :profesional_id
                      AND horario_id = :horario_id
                      AND fecha = :fecha
                      AND estado = 'bloqueado'
                      AND hora_inicio < :hora_fin
                      AND hora_fin > :hora_inicio
                ";

                    $stmtDisp = $this->db->prepare($sqlDisponibilidad);
                    $stmtDisp->execute([
                        ':profesional_id' => $profesionalId,
                        ':horario_id' => $horario['id'],
                        ':fecha' => $fecha,
                        ':hora_inicio' => $horaInicio,
                        ':hora_fin' => $horaFin
                    ]);

                    if ((int) $stmtDisp->fetchColumn() > 0) {
                        continue;
                    }

                    /*
                     * Revisar reservas que se cruzan con el horario.
                     */
                    $sqlReserva = "
                    SELECT COUNT(*)
                    FROM reservas r
                    INNER JOIN estados_reserva er
                        ON er.id = r.estado_id
                    WHERE r.profesional_id = :profesional_id
                      AND r.fecha = :fecha
                      AND r.hora_inicio < :hora_fin
                      AND r.hora_fin > :hora_inicio
                      AND er.nombre IN ('Pendiente', 'Confirmada', 'En proceso')
                ";

                    $stmtReserva = $this->db->prepare($sqlReserva);
                    $stmtReserva->execute([
                        ':profesional_id' => $profesionalId,
                        ':fecha' => $fecha,
                        ':hora_inicio' => $horaInicio,
                        ':hora_fin' => $horaFin
                    ]);

                    $reservasExistentes = (int) $stmtReserva->fetchColumn();

                    if ($reservasExistentes === 0) {
                        $profesionalesDisponibles++;
                    }
                }

                if ($profesionalesDisponibles > 0) {
                    $resultado[] = [
                        'hora_inicio' => $horaInicio,
                        'hora_fin' => $horaFin,
                        'profesionales_disponibles' => $profesionalesDisponibles,
                        'capacidad_total' => $cantidadProfesionales
                    ];
                }

                /*
                 * Avanzamos un bloque.
                 */
                $inicio = $fin;
            }
        }

        return $resultado;
    }
}


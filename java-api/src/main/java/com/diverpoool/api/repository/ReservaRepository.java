package com.diverpoool.api.repository;

import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Repository;

import java.sql.Timestamp;
import java.time.LocalDate;
import java.time.LocalTime;
import java.util.List;
import java.util.Map;

@Repository
public class ReservaRepository {

    private final JdbcTemplate jdbcTemplate;

    public ReservaRepository(JdbcTemplate jdbcTemplate) {
        this.jdbcTemplate = jdbcTemplate;
    }

    /*
     * =========================================================
     * VALIDAR MASCOTA
     * =========================================================
     */

    public boolean mascotaPerteneceAUsuario(
            int mascotaId,
            int usuarioId) {

        String sql = """
                SELECT COUNT(*)
                FROM mascotas
                WHERE id = ?
                  AND usuario_id = ?
                  AND estado = '1'
                """;

        Integer cantidad = jdbcTemplate.queryForObject(
                sql,
                Integer.class,
                mascotaId,
                usuarioId);

        return cantidad != null && cantidad > 0;
    }

    /*
     * =========================================================
     * OBTENER SERVICIO
     * =========================================================
     */

    public Map<String, Object> obtenerServicio(
            int servicioId) {

        String sql = """
                SELECT
                    id,
                    nombre,
                    duracion_minutos,
                    precio,
                    estado,
                    requiere_profesional
                FROM servicios
                WHERE id = ?
                  AND estado = 'activo'
                LIMIT 1
                """;

        List<Map<String, Object>> resultados = jdbcTemplate.queryForList(
                sql,
                servicioId);

        return resultados.isEmpty()
                ? null
                : resultados.get(0);
    }

    /*
     * =========================================================
     * OBTENER PROFESIONALES DEL SERVICIO
     * =========================================================
     */

    public List<Integer> obtenerProfesionales(
            int servicioId) {

        String sql = """
                SELECT DISTINCT p.id
                FROM profesionales p
                INNER JOIN servicio_profesionales sp
                    ON sp.profesional_id = p.id
                INNER JOIN usuarios u
                    ON u.id = p.usuario_id
                WHERE sp.servicio_id = ?
                  AND p.estado = 'activo'
                  AND u.estado = 'activo'
                ORDER BY p.id ASC
                """;

        return jdbcTemplate.query(
                sql,
                (rs, rowNum) -> rs.getInt("id"),
                servicioId);
    }

    /*
     * =========================================================
     * BLOQUEAR PROFESIONAL
     *
     * SELECT FOR UPDATE hace que, dentro de la transacción,
     * otro proceso no pueda tomar simultáneamente el mismo
     * profesional.
     * =========================================================
     */

    public void bloquearProfesional(
            int profesionalId) {

        String sql = """
                SELECT id
                FROM profesionales
                WHERE id = ?
                FOR UPDATE
                """;

        jdbcTemplate.queryForObject(
                sql,
                Integer.class,
                profesionalId);
    }

    /*
     * =========================================================
     * COMPROBAR BLOQUEO ESPECÍFICO
     * =========================================================
     */

    public boolean estaBloqueado(
            int profesionalId,
            String fecha,
            String horaInicio,
            String horaFin) {

        String sql = """
                SELECT COUNT(*)
                FROM disponibilidad
                WHERE profesional_id = ?
                  AND fecha = ?
                  AND estado = 'bloqueado'
                  AND hora_inicio < ?
                  AND hora_fin > ?
                """;

        Integer cantidad = jdbcTemplate.queryForObject(
                sql,
                Integer.class,
                profesionalId,
                fecha,
                horaFin,
                horaInicio);

        return cantidad != null && cantidad > 0;
    }

    /*
     * =========================================================
     * COMPROBAR RESERVA EXISTENTE
     * =========================================================
     */

    public boolean tieneReserva(
            int profesionalId,
            String fecha,
            String horaInicio,
            String horaFin) {

        String sql = """
                SELECT COUNT(*)
                FROM reservas r
                INNER JOIN estados_reserva er
                    ON er.id = r.estado_id
                WHERE r.profesional_id = ?
                  AND r.fecha = ?
                  AND r.hora_inicio < ?
                  AND r.hora_fin > ?
                  AND er.nombre IN (
                      'Pendiente',
                      'Confirmada',
                      'En proceso'
                  )
                """;

        Integer cantidad = jdbcTemplate.queryForObject(
                sql,
                Integer.class,
                profesionalId,
                fecha,
                horaFin,
                horaInicio);

        return cantidad != null && cantidad > 0;
    }

    /*
     * =========================================================
     * OBTENER ESTADO PENDIENTE
     * =========================================================
     */

    public int obtenerEstadoPendiente() {

        String sql = """
                SELECT id
                FROM estados_reserva
                WHERE nombre = 'Pendiente'
                LIMIT 1
                """;

        Integer id = jdbcTemplate.queryForObject(
                sql,
                Integer.class);

        if (id == null) {
            throw new IllegalStateException(
                    "No existe el estado Pendiente.");
        }

        return id;
    }

    /*
     * =========================================================
     * CREAR RESERVA
     * =========================================================
     */

    public int crearReserva(
            int usuarioId,
            int mascotaId,
            int servicioId,
            int profesionalId,
            int sedeId,
            int estadoId,
            String fecha,
            String horaInicio,
            String horaFin,
            double precio,
            String observaciones) {

        String sql = """
                INSERT INTO reservas (
                    usuario_id,
                    mascota_id,
                    servicio_id,
                    profesional_id,
                    sede_id,
                    estado_id,
                    fecha,
                    hora_inicio,
                    hora_fin,
                    precio,
                    observaciones,
                    created_at,
                    updated_at
                )
                VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()
                )
                """;

        jdbcTemplate.update(
                sql,
                usuarioId,
                mascotaId,
                servicioId,
                profesionalId,
                sedeId,
                estadoId,
                fecha,
                horaInicio,
                horaFin,
                precio,
                observaciones);

        Integer id = jdbcTemplate.queryForObject(
                "SELECT LAST_INSERT_ID()",
                Integer.class);

        if (id == null) {
            throw new IllegalStateException(
                    "No se pudo obtener el ID de la reserva.");
        }

        return id;
    }
}
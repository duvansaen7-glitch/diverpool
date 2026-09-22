package com.diverpoool.api.repository;

import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Map;

@Repository
public class DisponibilidadRepository {

    private final JdbcTemplate jdbcTemplate;

    public DisponibilidadRepository(JdbcTemplate jdbcTemplate) {
        this.jdbcTemplate = jdbcTemplate;
    }

    public Integer obtenerDuracionServicio(int servicioId) {

        String sql = """
            SELECT duracion_minutos
            FROM servicios
            WHERE id = ?
              AND estado = 'activo'
            LIMIT 1
            """;

        List<Integer> resultados = jdbcTemplate.query(
            sql,
            (rs, rowNum) -> rs.getInt("duracion_minutos"),
            servicioId
        );

        return resultados.isEmpty()
            ? null
            : resultados.get(0);
    }

    public List<Map<String, Object>> obtenerHorariosBase(
        int sedeId,
        int diaSemana
    ) {

        String sql = """
            SELECT
                id,
                hora_inicio,
                hora_fin
            FROM horarios
            WHERE sede_id = ?
              AND dia_semana = ?
              AND activo = 1
            ORDER BY hora_inicio ASC
            """;

        return jdbcTemplate.queryForList(
            sql,
            sedeId,
            diaSemana
        );
    }

    public List<Integer> obtenerProfesionalesDelServicio(
        int servicioId
    ) {

        String sql = """
            SELECT DISTINCT
                p.id
            FROM profesionales p
            INNER JOIN servicio_profesionales sp
                ON sp.profesional_id = p.id
            INNER JOIN usuarios u
                ON u.id = p.usuario_id
            WHERE sp.servicio_id = ?
              AND p.estado = 'activo'
              AND u.estado = 'activo'
            """;

        return jdbcTemplate.query(
            sql,
            (rs, rowNum) -> rs.getInt("id"),
            servicioId
        );
    }

    public boolean estaBloqueado(
        int profesionalId,
        int horarioId,
        String fecha,
        String horaInicio,
        String horaFin
    ) {

        String sql = """
            SELECT COUNT(*)
            FROM disponibilidad
            WHERE profesional_id = ?
              AND horario_id = ?
              AND fecha = ?
              AND estado = 'bloqueado'
              AND hora_inicio < ?
              AND hora_fin > ?
            """;

        Integer cantidad = jdbcTemplate.queryForObject(
            sql,
            Integer.class,
            profesionalId,
            horarioId,
            fecha,
            horaFin,
            horaInicio
        );

        return cantidad != null && cantidad > 0;
    }

    public boolean tieneReserva(
        int profesionalId,
        String fecha,
        String horaInicio,
        String horaFin
    ) {

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
            horaInicio
        );

        return cantidad != null && cantidad > 0;
    }
}


package com.diverpoool.api.service;

import com.diverpoool.api.dto.CrearReservaRequest;
import com.diverpoool.api.repository.ReservaRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDate;
import java.time.LocalTime;
import java.util.List;
import java.util.Map;

@Service
public class ReservaService {

    private final ReservaRepository repository;

    public ReservaService(ReservaRepository repository) {
        this.repository = repository;
    }

    @Transactional
    public int crearReserva(CrearReservaRequest request) {

        /*
         * =====================================================
         * VALIDACIONES BÁSICAS
         * =====================================================
         */

        if (request.usuarioId() <= 0) {
            throw new IllegalArgumentException(
                    "Usuario no válido.");
        }

        if (request.mascotaId() <= 0) {
            throw new IllegalArgumentException(
                    "Mascota no válida.");
        }

        if (request.servicioId() <= 0) {
            throw new IllegalArgumentException(
                    "Servicio no válido.");
        }

        if (request.sedeId() <= 0) {
            throw new IllegalArgumentException(
                    "Sede no válida.");
        }

        /*
         * =====================================================
         * FECHA Y HORA
         * =====================================================
         */

        LocalDate fecha;

        LocalTime horaInicio;
        LocalTime horaFin;

        try {

            fecha = LocalDate.parse(
                    request.fecha());

            horaInicio = LocalTime.parse(
                    request.horaInicio());

            horaFin = LocalTime.parse(
                    request.horaFin());

        } catch (Exception e) {

            throw new IllegalArgumentException(
                    "La fecha o el horario no son válidos.");
        }

        if (fecha.isBefore(LocalDate.now())) {

            throw new IllegalArgumentException(
                    "No puede reservar una fecha anterior a hoy.");
        }

        if (!horaFin.isAfter(horaInicio)) {

            throw new IllegalArgumentException(
                    "El horario seleccionado no es válido.");
        }

        /*
         * =====================================================
         * VALIDAR MASCOTA
         * =====================================================
         */

        if (!repository.mascotaPerteneceAUsuario(
                request.mascotaId(),
                request.usuarioId())) {

            throw new IllegalArgumentException(
                    "La mascota no pertenece al usuario.");
        }

        /*
         * =====================================================
         * SERVICIO
         * =====================================================
         */

        Map<String, Object> servicio = repository.obtenerServicio(
                request.servicioId());

        if (servicio == null) {

            throw new IllegalArgumentException(
                    "El servicio no existe o no está disponible.");
        }

        int duracion = ((Number) servicio.get(
                "duracion_minutos")).intValue();

        double precio = ((Number) servicio.get(
                "precio")).doubleValue();

        /*
         * Comprobar que el horario enviado corresponde
         * realmente a la duración del servicio.
         */

        long minutos = java.time.Duration.between(
                horaInicio,
                horaFin).toMinutes();

        if (minutos != duracion) {

            throw new IllegalArgumentException(
                    "El horario no corresponde a la duración del servicio.");
        }

        /*
         * =====================================================
         * PROFESIONALES
         * =====================================================
         */

        List<Integer> profesionales = repository.obtenerProfesionales(
                request.servicioId());

        if (profesionales.isEmpty()) {

            throw new IllegalStateException(
                    "No hay profesionales disponibles para este servicio.");
        }

        /*
         * =====================================================
         * BUSCAR PROFESIONAL
         *
         * Cada profesional se bloquea dentro de la transacción
         * antes de comprobar nuevamente sus reservas.
         * =====================================================
         */

        Integer profesionalAsignado = null;

        for (Integer profesionalId : profesionales) {

            /*
             * Bloquear fila del profesional.
             */
            repository.bloquearProfesional(
                    profesionalId);

            /*
             * Comprobar bloqueos específicos.
             */

            boolean bloqueado = repository.estaBloqueado(
                    profesionalId,
                    fecha.toString(),
                    horaInicio.toString(),
                    horaFin.toString());

            if (bloqueado) {
                continue;
            }

            /*
             * Comprobar reservas existentes.
             */

            boolean ocupado = repository.tieneReserva(
                    profesionalId,
                    fecha.toString(),
                    horaInicio.toString(),
                    horaFin.toString());

            if (ocupado) {
                continue;
            }

            /*
             * Encontramos un profesional.
             */

            profesionalAsignado = profesionalId;

            break;
        }

        /*
         * =====================================================
         * NINGÚN PROFESIONAL DISPONIBLE
         * =====================================================
         */

        if (profesionalAsignado == null) {

            throw new IllegalStateException(
                    "El horario seleccionado ya no está disponible.");
        }

        /*
         * =====================================================
         * ESTADO PENDIENTE
         * =====================================================
         */

        int estadoPendiente = repository.obtenerEstadoPendiente();

        /*
         * =====================================================
         * CREAR RESERVA
         * =====================================================
         */

        return repository.crearReserva(
                request.usuarioId(),
                request.mascotaId(),
                request.servicioId(),
                profesionalAsignado,
                request.sedeId(),
                estadoPendiente,
                fecha.toString(),
                horaInicio.toString(),
                horaFin.toString(),
                precio,
                request.observaciones());
    }
}
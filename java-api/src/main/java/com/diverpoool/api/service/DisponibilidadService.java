package com.diverpoool.api.service;

import com.diverpoool.api.dto.HorarioDisponible;
import com.diverpoool.api.repository.DisponibilidadRepository;
import org.springframework.stereotype.Service;

import java.time.LocalDate;
import java.time.LocalTime;
import java.time.format.DateTimeFormatter;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

@Service
public class DisponibilidadService {

    private static final DateTimeFormatter HORA_FORMATO =
        DateTimeFormatter.ofPattern("HH:mm:ss");

    private final DisponibilidadRepository repository;

    public DisponibilidadService(
        DisponibilidadRepository repository
    ) {
        this.repository = repository;
    }

    public List<HorarioDisponible> obtenerHorarios(
        int servicioId,
        int sedeId,
        LocalDate fecha
    ) {

        if (fecha.isBefore(LocalDate.now())) {
            return List.of();
        }

        Integer duracion =
            repository.obtenerDuracionServicio(servicioId);

        if (duracion == null || duracion <= 0) {
            return List.of();
        }

        int diaSemana = fecha.getDayOfWeek().getValue();

        List<Map<String, Object>> horarios =
            repository.obtenerHorariosBase(
                sedeId,
                diaSemana
            );

        List<Integer> profesionales =
            repository.obtenerProfesionalesDelServicio(
                servicioId
            );

        if (horarios.isEmpty() || profesionales.isEmpty()) {
            return List.of();
        }

        List<HorarioDisponible> resultado =
            new ArrayList<>();

        for (Map<String, Object> horario : horarios) {

            LocalTime horaInicio =
                convertirHora(horario.get("hora_inicio"));

            LocalTime horaFinJornada =
                convertirHora(horario.get("hora_fin"));

            LocalTime inicio = horaInicio;

            while (true) {

                LocalTime fin =
                    inicio.plusMinutes(duracion);

                if (fin.isAfter(horaFinJornada)) {
                    break;
                }

                String inicioTexto =
                    inicio.format(HORA_FORMATO);

                String finTexto =
                    fin.format(HORA_FORMATO);

                int disponibles = 0;

                for (Integer profesionalId : profesionales) {

                    boolean bloqueado =
                        repository.estaBloqueado(
                            profesionalId,
                            ((Number) horario.get("id")).intValue(),
                            fecha.toString(),
                            inicioTexto,
                            finTexto
                        );

                    if (bloqueado) {
                        continue;
                    }

                    boolean ocupado =
                        repository.tieneReserva(
                            profesionalId,
                            fecha.toString(),
                            inicioTexto,
                            finTexto
                        );

                    if (!ocupado) {
                        disponibles++;
                    }
                }

                if (disponibles > 0) {

                    resultado.add(
                        new HorarioDisponible(
                            inicioTexto,
                            finTexto,
                            disponibles,
                            profesionales.size()
                        )
                    );
                }

                inicio = fin;
            }
        }

        return resultado;
    }

    private LocalTime convertirHora(Object valor) {

        if (valor instanceof java.sql.Time time) {
            return time.toLocalTime();
        }

        return LocalTime.parse(
            valor.toString(),
            HORA_FORMATO
        );
    }
}

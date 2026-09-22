package com.diverpoool.api.controller;

import com.diverpoool.api.dto.HorarioDisponible;
import com.diverpoool.api.service.DisponibilidadService;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import java.time.LocalDate;
import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/api/reservas")
public class DisponibilidadController {

    private final DisponibilidadService service;

    public DisponibilidadController(
        DisponibilidadService service
    ) {
        this.service = service;
    }

    @GetMapping("/disponibilidad")
    public ResponseEntity<?> obtenerDisponibilidad(

        @RequestParam int servicio_id,

        @RequestParam int sede_id,

        @RequestParam String fecha

    ) {

        try {

            LocalDate fechaSeleccionada =
                LocalDate.parse(fecha);

            List<HorarioDisponible> horarios =
                service.obtenerHorarios(
                    servicio_id,
                    sede_id,
                    fechaSeleccionada
                );

            return ResponseEntity.ok(
                Map.of(
                    "ok", true,
                    "fecha", fecha,
                    "servicio_id", servicio_id,
                    "sede_id", sede_id,
                    "horarios", horarios
                )
            );

        } catch (Exception e) {

            return ResponseEntity.badRequest().body(
                Map.of(
                    "ok", false,
                    "mensaje",
                    "Los parámetros de disponibilidad no son válidos."
                )
            );
        }
    }
}

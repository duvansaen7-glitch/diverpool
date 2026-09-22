package com.diverpoool.api.controller;

import com.diverpoool.api.dto.CrearReservaRequest;
import com.diverpoool.api.service.ReservaService;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.Map;

@RestController
@RequestMapping("/api/reservas")
public class ReservaController {

    private final ReservaService service;

    public ReservaController(ReservaService service) {
        this.service = service;
    }

    @PostMapping
    public ResponseEntity<?> crearReserva(
            @RequestBody CrearReservaRequest request) {

        try {

            int reservaId = service.crearReserva(request);

            return ResponseEntity
                    .status(HttpStatus.CREATED)
                    .body(
                            Map.of(
                                    "ok", true,
                                    "mensaje",
                                    "Reserva creada correctamente.",
                                    "reserva_id",
                                    reservaId));

        } catch (IllegalArgumentException e) {

            return ResponseEntity
                    .badRequest()
                    .body(
                            Map.of(
                                    "ok", false,
                                    "mensaje",
                                    e.getMessage()));

        } catch (IllegalStateException e) {

            return ResponseEntity
                    .status(HttpStatus.CONFLICT)
                    .body(
                            Map.of(
                                    "ok", false,
                                    "mensaje",
                                    e.getMessage()));

        } catch (Exception e) {

            e.printStackTrace();

            return ResponseEntity
                    .status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body(
                            Map.of(
                                    "ok", false,
                                    "mensaje",
                                    "No fue posible crear la reserva."));
        }
    }
}
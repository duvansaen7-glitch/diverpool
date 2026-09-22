package com.diverpoool.api.dto;

public record CrearReservaRequest(
        int usuarioId,
        int mascotaId,
        int servicioId,
        int sedeId,
        String fecha,
        String horaInicio,
        String horaFin,
        String observaciones) {
}
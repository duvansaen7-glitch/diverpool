package com.diverpoool.api.dto;

public record HorarioDisponible(
    String horaInicio,
    String horaFin,
    int profesionalesDisponibles,
    int capacidadTotal
) {
}


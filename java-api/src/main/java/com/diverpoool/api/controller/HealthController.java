package com.diverpoool.api.controller;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;

import javax.sql.DataSource;
import java.sql.Connection;

@RestController
public class HealthController {

    private final DataSource dataSource;

    public HealthController(DataSource dataSource) {
        this.dataSource = dataSource;
    }

    @GetMapping("/api/health")
    public HealthResponse health() {

        boolean database;

        try (Connection connection = dataSource.getConnection()) {
            database = connection.isValid(2);
        } catch (Exception e) {
            database = false;
        }

        return new HealthResponse(
            true,
            "Diverpoool API",
            database
        );
    }

    public record HealthResponse(
        boolean ok,
        String service,
        boolean database
    ) {}
}

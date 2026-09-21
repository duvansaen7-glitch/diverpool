<?php

require_once __DIR__ . '/../config/database.php';

class Mascota
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    /**
     * Obtener todas las mascotas activas de un usuario.
     */
    public function obtenerPorUsuario(int $usuarioId): array
    {
        $sql = "
            SELECT
                m.id,
                m.usuario_id,
                m.especie_id,
                m.raza_id,
                m.nombre,
                m.sexo,
                m.fecha_nacimiento,
                m.peso,
                m.color,
                m.microchip,
                m.foto,
                m.esterilizado,
                m.observaciones,
                m.estado,
                e.nombre AS especie_nombre,
                r.nombre AS raza_nombre
            FROM mascotas m
            INNER JOIN especies e
                ON e.id = m.especie_id
            LEFT JOIN razas r
                ON r.id = m.raza_id
            WHERE m.usuario_id = :usuario_id
              AND m.estado = 1
            ORDER BY m.nombre ASC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':usuario_id' => $usuarioId
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Obtener una mascota específica de un usuario.
     *
     * El usuario_id se utiliza para impedir
     * acceder a mascotas de otros usuarios.
     */
    public function obtenerPorId(
        int $id,
        int $usuarioId
    ): ?array {
        $sql = "
            SELECT
                m.id,
                m.usuario_id,
                m.especie_id,
                m.raza_id,
                m.nombre,
                m.sexo,
                m.fecha_nacimiento,
                m.peso,
                m.color,
                m.microchip,
                m.foto,
                m.esterilizado,
                m.observaciones,
                m.estado,
                e.nombre AS especie_nombre,
                r.nombre AS raza_nombre
            FROM mascotas m
            INNER JOIN especies e
                ON e.id = m.especie_id
            LEFT JOIN razas r
                ON r.id = m.raza_id
            WHERE m.id = :id
              AND m.usuario_id = :usuario_id
              AND m.estado = 1
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':usuario_id' => $usuarioId
        ]);

        $mascota = $stmt->fetch();

        return $mascota ?: null;
    }

    /**
     * Crear una mascota.
     */
    public function crear(
        int $usuarioId,
        int $especieId,
        ?int $razaId,
        string $nombre,
        string $sexo,
        ?string $fechaNacimiento = null,
        ?float $peso = null,
        ?string $color = null,
        ?string $microchip = null,
        bool $esterilizado = false,
        ?string $observaciones = null
    ): int {
        $sql = "
            INSERT INTO mascotas (
                usuario_id,
                especie_id,
                raza_id,
                nombre,
                sexo,
                fecha_nacimiento,
                peso,
                color,
                microchip,
                esterilizado,
                observaciones
            )
            VALUES (
                :usuario_id,
                :especie_id,
                :raza_id,
                :nombre,
                :sexo,
                :fecha_nacimiento,
                :peso,
                :color,
                :microchip,
                :esterilizado,
                :observaciones
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':especie_id' => $especieId,
            ':raza_id' => $razaId,
            ':nombre' => $nombre,
            ':sexo' => $sexo,
            ':fecha_nacimiento' => $fechaNacimiento,
            ':peso' => $peso,
            ':color' => $color,
            ':microchip' => $microchip,
            ':esterilizado' => $esterilizado ? 1 : 0,
            ':observaciones' => $observaciones
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Actualizar una mascota perteneciente al usuario.
     */
    public function actualizar(
        int $id,
        int $usuarioId,
        int $especieId,
        ?int $razaId,
        string $nombre,
        string $sexo,
        ?string $fechaNacimiento = null,
        ?float $peso = null,
        ?string $color = null,
        ?string $microchip = null,
        bool $esterilizado = false,
        ?string $observaciones = null
    ): bool {
        $sql = "
            UPDATE mascotas
            SET
                especie_id = :especie_id,
                raza_id = :raza_id,
                nombre = :nombre,
                sexo = :sexo,
                fecha_nacimiento = :fecha_nacimiento,
                peso = :peso,
                color = :color,
                microchip = :microchip,
                esterilizado = :esterilizado,
                observaciones = :observaciones
            WHERE id = :id
              AND usuario_id = :usuario_id
              AND estado = 1
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':usuario_id' => $usuarioId,
            ':especie_id' => $especieId,
            ':raza_id' => $razaId,
            ':nombre' => $nombre,
            ':sexo' => $sexo,
            ':fecha_nacimiento' => $fechaNacimiento,
            ':peso' => $peso,
            ':color' => $color,
            ':microchip' => $microchip,
            ':esterilizado' => $esterilizado ? 1 : 0,
            ':observaciones' => $observaciones
        ]);
    }

    /**
     * Desactivar una mascota.
     *
     * No se elimina físicamente de la base de datos.
     */
    public function desactivar(
        int $id,
        int $usuarioId
    ): bool {
        $sql = "
            UPDATE mascotas
            SET estado = 0
            WHERE id = :id
              AND usuario_id = :usuario_id
              AND estado = 1
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':usuario_id' => $usuarioId
        ]);
    }

    /**
     * Obtener las especies activas.
     */
    public function obtenerEspecies(): array
    {
        $sql = "
            SELECT
                id,
                nombre
            FROM especies
            WHERE estado = 1
            ORDER BY nombre ASC
        ";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    /**
     * Obtener las razas activas de una especie.
     */
    public function obtenerRazasPorEspecie(
        int $especieId
    ): array {
        $sql = "
            SELECT
                id,
                nombre
            FROM razas
            WHERE especie_id = :especie_id
              AND estado = 1
            ORDER BY nombre ASC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':especie_id' => $especieId
        ]);

        return $stmt->fetchAll();
    }
}
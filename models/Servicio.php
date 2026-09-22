<?php

require_once __DIR__ . '/../config/database.php';

class Servicio
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    public function obtenerActivos(): array
    {
        $sql = "
            SELECT
                s.id,
                s.categoria_id,
                s.nombre,
                s.descripcion,
                s.duracion_minutos,
                s.precio,
                s.imagen,
                s.requiere_profesional,
                s.estado,
                c.nombre AS categoria_nombre
            FROM servicios s
            INNER JOIN categorias_servicios c
                ON c.id = s.categoria_id
            WHERE s.estado = 'activo'
            ORDER BY c.id ASC, s.nombre ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = "
            SELECT
                s.id,
                s.categoria_id,
                s.nombre,
                s.descripcion,
                s.duracion_minutos,
                s.precio,
                s.imagen,
                s.requiere_profesional,
                s.estado,
                c.nombre AS categoria_nombre
            FROM servicios s
            INNER JOIN categorias_servicios c
                ON c.id = s.categoria_id
            WHERE s.id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        $servicio = $stmt->fetch();

        return $servicio ?: null;
    }

    public function obtenerTodos(): array
    {
        $sql = "
            SELECT
                s.id,
                s.categoria_id,
                s.nombre,
                s.descripcion,
                s.duracion_minutos,
                s.precio,
                s.imagen,
                s.requiere_profesional,
                s.estado,
                s.created_at,
                s.updated_at,
                c.nombre AS categoria_nombre
            FROM servicios s
            INNER JOIN categorias_servicios c
                ON c.id = s.categoria_id
            ORDER BY s.created_at DESC, s.id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerCategorias(): array
    {
        $sql = "
            SELECT
                id,
                nombre
            FROM categorias_servicios
            WHERE estado = 1
            ORDER BY nombre ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}

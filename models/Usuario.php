<?php

require_once __DIR__ . '/../config/database.php';

class Usuario
{
    private PDO $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    /**
     * Buscar un usuario por correo electrónico.
     */
    public function buscarPorCorreo(string $correo): ?array
    {
        $sql = "
            SELECT
                u.id,
                u.rol_id,
                u.nombres,
                u.apellidos,
                u.correo,
                u.telefono,
                u.password_hash,
                u.documento,
                u.estado,
                u.ultimo_acceso,
                u.created_at,
                u.updated_at,
                r.nombre AS rol_nombre
            FROM usuarios u
            INNER JOIN roles r ON r.id = u.rol_id
            WHERE u.correo = :correo
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':correo' => $correo
        ]);

        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    /**
     * Buscar un usuario por ID.
     */
    public function buscarPorId(int $id): ?array
    {
        $sql = "
            SELECT
                u.id,
                u.rol_id,
                u.nombres,
                u.apellidos,
                u.correo,
                u.telefono,
                u.documento,
                u.estado,
                u.ultimo_acceso,
                u.created_at,
                u.updated_at,
                r.nombre AS rol_nombre
            FROM usuarios u
            INNER JOIN roles r ON r.id = u.rol_id
            WHERE u.id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    /**
     * Crear un nuevo usuario.
     *
     * El password recibido debe ser texto plano.
     * Aquí se transforma mediante password_hash().
     */
    public function crear(
        int $rolId,
        string $nombres,
        string $apellidos,
        string $correo,
        string $password,
        ?string $telefono = null,
        ?string $documento = null
    ): int {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "
            INSERT INTO usuarios (
                rol_id,
                nombres,
                apellidos,
                correo,
                telefono,
                password_hash,
                documento
            )
            VALUES (
                :rol_id,
                :nombres,
                :apellidos,
                :correo,
                :telefono,
                :password_hash,
                :documento
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':rol_id' => $rolId,
            ':nombres' => $nombres,
            ':apellidos' => $apellidos,
            ':correo' => $correo,
            ':telefono' => $telefono,
            ':password_hash' => $passwordHash,
            ':documento' => $documento
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Verificar una contraseña.
     */
    public function verificarPassword(
        string $password,
        string $passwordHash
    ): bool {
        return password_verify($password, $passwordHash);
    }

    /**
     * Actualizar la fecha del último acceso.
     */
    public function actualizarUltimoAcceso(int $id): bool
    {
        $sql = "
            UPDATE usuarios
            SET ultimo_acceso = NOW()
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}

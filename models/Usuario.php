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
                u.foto_perfil,
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
                u.foto_perfil,
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
     * Obtener el ID de un rol por su nombre.
     */
    public function obtenerRolIdPorNombre(string $nombre): ?int
    {
        $sql = "
            SELECT id
            FROM roles
            WHERE nombre = :nombre
              AND estado = 1
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':nombre' => $nombre
        ]);

        $resultado = $stmt->fetch();

        return $resultado
            ? (int) $resultado['id']
            : null;
    }

    /**
     * Obtener todos los roles activos.
     */
    public function obtenerRoles(): array
    {
        $sql = "
            SELECT
                id,
                nombre,
                descripcion
            FROM roles
            WHERE estado = 1
            ORDER BY id
        ";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    /**
     * Obtener todos los usuarios.
     */
    public function obtenerTodos(): array
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
                u.foto_perfil,
                u.estado,
                u.ultimo_acceso,
                u.created_at,
                u.updated_at,
                r.nombre AS rol_nombre
            FROM usuarios u
            INNER JOIN roles r ON r.id = u.rol_id
            ORDER BY u.created_at DESC
        ";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    /**
     * Crear un nuevo usuario.
     *
     * La contraseña recibida se almacena utilizando password_hash().
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
        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

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
     * Actualizar los datos básicos del perfil.
     */
    public function actualizarPerfil(
        int $id,
        string $nombres,
        string $apellidos,
        ?string $telefono,
        ?string $documento
    ): bool {
        $sql = "
            UPDATE usuarios
            SET
                nombres = :nombres,
                apellidos = :apellidos,
                telefono = :telefono,
                documento = :documento
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':nombres' => $nombres,
            ':apellidos' => $apellidos,
            ':telefono' => $telefono,
            ':documento' => $documento
        ]);
    }

    /**
     * Actualizar la foto de perfil.
     */
    public function actualizarFotoPerfil(
        int $id,
        ?string $fotoPerfil
    ): bool {
        $sql = "
            UPDATE usuarios
            SET foto_perfil = :foto_perfil
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':foto_perfil' => $fotoPerfil
        ]);
    }

    /**
     * Verificar una contraseña.
     */
    public function verificarPassword(
        string $password,
        string $passwordHash
    ): bool {
        return password_verify(
            $password,
            $passwordHash
        );
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
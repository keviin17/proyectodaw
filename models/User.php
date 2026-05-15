<?php
// models/User.php
require_once __DIR__ . '/../config/database.php';

class User
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    /** Busca un usuario activo por email */
    public function getByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM usuario WHERE email = ? AND activo = 1"
        );
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    /** Busca un usuario por ID */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM usuario WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Registra un nuevo usuario cliente */
    public function crear(string $nombre, string $email, string $hash): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO usuario (nombre, email, contraseña, rol)
             VALUES (?, ?, ?, 'cliente')"
        );
        $stmt->execute([$nombre, $email, $hash]);
        return (int) $this->pdo->lastInsertId();
    }

    /** Actualiza datos del perfil */
    public function actualizarPerfil(int $id, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE usuario
             SET nombre=?, telefono=?, direccion=?, ciudad=?, codigo_postal=?
             WHERE id=?"
        );
        return $stmt->execute([
            $datos['nombre'],
            $datos['telefono']      ?? null,
            $datos['direccion']     ?? null,
            $datos['ciudad']        ?? null,
            $datos['codigo_postal'] ?? null,
            $id,
        ]);
    }

    /** Cambia la contraseña */
    public function cambiarContrasena(int $id, string $hash): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE usuario SET contraseña = ? WHERE id = ?"
        );
        return $stmt->execute([$hash, $id]);
    }

    // ── Admin ──────────────────────────────────────────────

    /** Lista todos los usuarios (para el panel admin) */
    public function getAll(): array
    {
        return $this->pdo
            ->query("SELECT id, nombre, email, rol, activo, fecha_registro
                     FROM usuario ORDER BY id DESC")
            ->fetchAll();
    }

    /** Activa o desactiva una cuenta */
    public function toggleActivo(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE usuario SET activo = IF(activo=1,0,1) WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    /** Cambia el rol de un usuario */
    public function cambiarRol(int $id, string $rol): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE usuario SET rol = ? WHERE id = ?"
        );
        return $stmt->execute([$rol, $id]);
    }
}
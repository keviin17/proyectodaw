<?php
// models/Review.php
require_once __DIR__ . '/../config/database.php';

class Review
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    /** Valoraciones de un producto con nombre del usuario */
    public function getByProducto(int $idProducto): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT v.*, u.nombre AS usuario_nombre
             FROM valoracion v
             JOIN usuario u ON v.id_usuario = u.id
             WHERE v.id_producto = ?
             ORDER BY v.fecha DESC"
        );
        $stmt->execute([$idProducto]);
        return $stmt->fetchAll();
    }

    /** Media de puntuación de un producto */
    public function getMedia(int $idProducto): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(AVG(puntuacion), 0) FROM valoracion WHERE id_producto = ?"
        );
        $stmt->execute([$idProducto]);
        return round((float) $stmt->fetchColumn(), 1);
    }

    /** Guardar o actualizar valoración (un usuario, un producto) */
    public function guardar(int $idUsuario, int $idProducto, int $puntuacion, string $comentario): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO valoracion (id_usuario, id_producto, puntuacion, comentario)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE puntuacion = VALUES(puntuacion),
                                     comentario = VALUES(comentario),
                                     fecha      = CURRENT_TIMESTAMP"
        );
        $stmt->execute([$idUsuario, $idProducto, $puntuacion, $comentario]);
    }

    /** Comprueba si el usuario ya valoró este producto */
    public function yaValoro(int $idUsuario, int $idProducto): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM valoracion WHERE id_usuario = ? AND id_producto = ?"
        );
        $stmt->execute([$idUsuario, $idProducto]);
        return (bool) $stmt->fetch();
    }
}
<?php
// models/Wishlist.php
require_once __DIR__ . '/../config/database.php';

class Wishlist
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    /** Lista de deseos de un usuario con datos del producto */
    public function getByUsuario(int $idUsuario): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT ld.id, ld.fecha_añadido,
                    p.id AS id_producto, p.nombre, p.precio,
                    p.imagen, p.precio_oferta
             FROM lista_deseo ld
             JOIN producto p ON ld.id_producto = p.id
             WHERE ld.id_usuario = ?
             ORDER BY ld.fecha_añadido DESC"
        );
        $stmt->execute([$idUsuario]);
        return $stmt->fetchAll();
    }

    /** Añadir producto a la lista (ignora si ya existe) */
    public function anadir(int $idUsuario, int $idProducto): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT IGNORE INTO lista_deseo (id_usuario, id_producto)
             VALUES (?, ?)"
        );
        $stmt->execute([$idUsuario, $idProducto]);
    }

    /** Eliminar producto de la lista */
    public function eliminar(int $idUsuario, int $idProducto): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM lista_deseo
             WHERE id_usuario = ? AND id_producto = ?"
        );
        return $stmt->execute([$idUsuario, $idProducto]);
    }

    /** Comprueba si un producto ya está en la lista */
    public function existe(int $idUsuario, int $idProducto): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM lista_deseo
             WHERE id_usuario = ? AND id_producto = ?"
        );
        $stmt->execute([$idUsuario, $idProducto]);
        return (bool) $stmt->fetch();
    }
}

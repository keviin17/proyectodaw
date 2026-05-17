<?php
// models/Cart.php
require_once __DIR__ . '/../config/database.php';

class Cart
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    /** Contenido del carrito de un usuario con datos del producto */
    public function getByUsuario(int $idUsuario): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.id, c.cantidad, c.talla,
                    p.id AS id_producto, p.nombre, p.precio,
                    p.precio_oferta, p.imagen, p.stock,
                    COALESCE(NULLIF(p.precio_oferta, 0), p.precio) AS precio_efectivo,
                    (c.cantidad * COALESCE(NULLIF(p.precio_oferta, 0), p.precio)) AS subtotal
             FROM carrito c
             JOIN producto p ON c.id_producto = p.id
             WHERE c.id_usuario = ?"
        );
        $stmt->execute([$idUsuario]);
        return $stmt->fetchAll();
    }

    /** Número de líneas en el carrito (para el badge del navbar) */
    public function contarItems(int $idUsuario): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(cantidad), 0) FROM carrito WHERE id_usuario = ?"
        );
        $stmt->execute([$idUsuario]);
        return (int) $stmt->fetchColumn();
    }

    /** Añadir o incrementar cantidad */
    public function anadir(int $idUsuario, int $idProducto, int $cantidad, string $talla = ''): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO carrito (id_usuario, id_producto, cantidad, talla)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE cantidad = cantidad + VALUES(cantidad)"
        );
        $stmt->execute([$idUsuario, $idProducto, $cantidad, $talla]);
    }

    /** Actualizar cantidad de una línea */
    public function actualizar(int $idCarrito, int $idUsuario, int $cantidad): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE carrito SET cantidad = ?
             WHERE id = ? AND id_usuario = ?"
        );
        return $stmt->execute([$cantidad, $idCarrito, $idUsuario]);
    }

    /** Eliminar una línea */
    public function eliminarLinea(int $idCarrito, int $idUsuario): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM carrito WHERE id = ? AND id_usuario = ?"
        );
        return $stmt->execute([$idCarrito, $idUsuario]);
    }

    /** Vaciar el carrito completo (después de completar el pedido) */
    public function vaciar(int $idUsuario): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM carrito WHERE id_usuario = ?"
        );
        return $stmt->execute([$idUsuario]);
    }

    /** Obtener el stock disponible del producto asociado a una línea de carrito */
    public function getStockByCarritoId(int $idCarrito, int $idUsuario): ?int
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.stock
             FROM carrito c
             JOIN producto p ON c.id_producto = p.id
             WHERE c.id = ? AND c.id_usuario = ?"
        );
        $stmt->execute([$idCarrito, $idUsuario]);
        $row = $stmt->fetch();
        return $row ? (int) $row['stock'] : null;
    }
}
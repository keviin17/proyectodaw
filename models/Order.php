<?php
// models/Order.php
require_once __DIR__ . '/../config/database.php';

class Order
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    /** Historial de pedidos de un usuario */
    public function getByUsuario(int $idUsuario): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM pedido
             WHERE id_usuario = ?
             ORDER BY fecha DESC"
        );
        $stmt->execute([$idUsuario]);
        return $stmt->fetchAll();
    }

    /** Un pedido por ID (verifica que pertenece al usuario si se pasa) */
    public function getById(int $id, ?int $idUsuario = null): ?array
    {
        if ($idUsuario !== null) {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM pedido WHERE id = ? AND id_usuario = ?"
            );
            $stmt->execute([$id, $idUsuario]);
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM pedido WHERE id = ?"
            );
            $stmt->execute([$id]);
        }
        return $stmt->fetch() ?: null;
    }

    /** Líneas de detalle de un pedido */
    public function getDetalle(int $idPedido): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT dp.*, p.nombre, p.imagen
             FROM detalle_pedido dp
             JOIN producto p ON dp.id_producto = p.id
             WHERE dp.id_pedido = ?"
        );
        $stmt->execute([$idPedido]);
        return $stmt->fetchAll();
    }

    /**
     * Crea un pedido a partir del carrito.
     * $items = resultado de Cart::getByUsuario()
     */
    public function crear(int $idUsuario, array $items, float $total,
                          string $direccionEnvio, string $metodoPago = 'tarjeta',
                          string $notas = ''): int
    {
        // Cabecera del pedido
        $stmt = $this->pdo->prepare(
            "INSERT INTO pedido
             (id_usuario, total, estado, direccion_envio, metodo_pago, notas)
             VALUES (?, ?, 'pendiente', ?, ?, ?)"
        );
        $stmt->execute([$idUsuario, $total, $direccionEnvio, $metodoPago, $notas]);
        $idPedido = (int) $this->pdo->lastInsertId();

        // Líneas de detalle
        $ins = $this->pdo->prepare(
            "INSERT INTO detalle_pedido
             (id_pedido, id_producto, cantidad, precio_unitario, talla)
             VALUES (?, ?, ?, ?, ?)"
        );
        foreach ($items as $item) {
            $ins->execute([
                $idPedido,
                $item['id_producto'],
                $item['cantidad'],
                $item['precio'],
                $item['talla'] ?? null,
            ]);
        }

        return $idPedido;
    }

    // ── Admin ──────────────────────────────────────────────

    /** Todos los pedidos con nombre de cliente */
    public function getAll(): array
    {
        return $this->pdo->query(
            "SELECT p.*, u.nombre AS cliente_nombre, u.email AS cliente_email
             FROM pedido p
             JOIN usuario u ON p.id_usuario = u.id
             ORDER BY p.fecha DESC"
        )->fetchAll();
    }

    /** Cambia el estado de un pedido */
    public function cambiarEstado(int $id, string $estado): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE pedido SET estado = ? WHERE id = ?"
        );
        return $stmt->execute([$estado, $id]);
    }
}
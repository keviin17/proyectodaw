<?php
// models/Product.php
require_once __DIR__ . '/../config/database.php';

class Product
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    /** Todos los productos activos con paginación */
    public function getAll(int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM producto p
             JOIN categoria c ON p.id_categoria = c.id
             WHERE p.activo = 1
             ORDER BY p.destacado DESC, p.created_at DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    /** Buscar productos por texto */
    public function buscar(string $query): array
    {
        $q = "%{$query}%";
        $stmt = $this->pdo->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM producto p
             JOIN categoria c ON p.id_categoria = c.id
             WHERE p.activo = 1
               AND (p.nombre LIKE ? OR p.descripcion LIKE ?)
             ORDER BY p.nombre ASC"
        );
        $stmt->execute([$q, $q]);
        return $stmt->fetchAll();
    }

    /** Filtrar por género (hombre / mujer / niño / unisex) */
    public function getByGenero(string $genero): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM producto p
             JOIN categoria c ON p.id_categoria = c.id
             WHERE p.activo = 1 AND c.genero = ?
             ORDER BY p.destacado DESC, p.nombre ASC"
        );
        $stmt->execute([$genero]);
        return $stmt->fetchAll();
    }

    /** Un producto por ID */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre, c.genero
             FROM producto p
             JOIN categoria c ON p.id_categoria = c.id
             WHERE p.id = ? AND p.activo = 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Productos destacados (para portada) */
    public function getDestacados(int $limit = 4): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM producto p
             JOIN categoria c ON p.id_categoria = c.id
             WHERE p.activo = 1 AND p.destacado = 1
             ORDER BY p.created_at DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /** Total de productos activos (para paginación) */
    public function contar(): int
    {
        return (int) $this->pdo
            ->query("SELECT COUNT(*) FROM producto WHERE activo = 1")
            ->fetchColumn();
    }

    // ── Admin ──────────────────────────────────────────────

    /** Todos los productos (activos e inactivos) para el panel admin */
    public function getAllAdmin(): array
    {
        return $this->pdo->query(
            "SELECT p.*, c.nombre AS categoria_nombre
             FROM producto p
             JOIN categoria c ON p.id_categoria = c.id
             ORDER BY p.id DESC"
        )->fetchAll();
    }

    /** Crear producto */
    public function crear(array $datos): int
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO producto
                 (nombre, descripcion, precio, precio_oferta, stock, imagen,
                  id_categoria, destacado, talla, color)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $datos['nombre'],
                $datos['descripcion']  ?? null,
                $datos['precio'],
                $datos['precio_oferta'] ?? null,
                $datos['stock'],
                $datos['imagen']       ?? null,
                $datos['id_categoria'],
                $datos['destacado']    ?? 0,
                $datos['talla']        ?? null,
                $datos['color']        ?? null,
            ]);
            return (int) $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            if ($e->getCode() == '23000') {
                return -1; // Señal de duplicado
            }
            throw $e;
        }
    }

    /** Actualizar producto */
    public function actualizar(int $id, array $datos): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE producto
             SET nombre=?, descripcion=?, precio=?, precio_oferta=?,
                 stock=?, imagen=?, id_categoria=?, destacado=?,
                 talla=?, color=?
             WHERE id=?"
        );
        return $stmt->execute([
            $datos['nombre'],
            $datos['descripcion']  ?? null,
            $datos['precio'],
            $datos['precio_oferta'] ?? null,
            $datos['stock'],
            $datos['imagen']       ?? null,
            $datos['id_categoria'],
            $datos['destacado']    ?? 0,
            $datos['talla']        ?? null,
            $datos['color']        ?? null,
            $id,
        ]);
    }

    /** Borrado lógico */
    public function eliminar(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE producto SET activo = 0 WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }
}
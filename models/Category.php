<?php
// models/Category.php
require_once __DIR__ . '/../config/database.php';

class Category
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConnection();
    }

    /** Todas las categorías activas */
    public function getAll(): array
    {
        return $this->pdo
            ->query("SELECT * FROM categoria WHERE activo = 1 ORDER BY nombre ASC")
            ->fetchAll();
    }

    /** Categorías filtradas por género */
    public function getByGenero(string $genero): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM categoria WHERE genero = ? AND activo = 1 ORDER BY nombre ASC"
        );
        $stmt->execute([$genero]);
        return $stmt->fetchAll();
    }

    /** Una categoría por ID */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM categoria WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}
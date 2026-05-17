<?php
// controllers/ProductController.php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Wishlist.php';
require_once __DIR__ . '/../config/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class ProductController
{
    private Product  $productModel;
    private Category $categoryModel;
    private Review   $reviewModel;
    private Wishlist $wishlistModel;

    public function __construct()
    {
        $this->productModel  = new Product();
        $this->categoryModel = new Category();
        $this->reviewModel   = new Review();
        $this->wishlistModel = new Wishlist();
    }

    /** Página principal / catálogo general */
    public function catalogo(): void
    {
        $limit    = 12;
        $page     = max(1, (int) ($_GET['pagina'] ?? 1));
        $offset   = ($page - 1) * $limit;

        $productos  = $this->productModel->getAll($limit, $offset);
        $total      = $this->productModel->contar();
        $categorias = $this->categoryModel->getAll();
        $paginas    = ceil($total / $limit);

        require __DIR__ . '/../views/shop/index.php';
    }

    /** Catálogo filtrado por género */
    public function catalogoPorGenero(string $genero): void
    {
        $productos    = $this->productModel->getByGenero($genero);
        $categorias   = $this->categoryModel->getAll();
        $generoActual = $genero;
        // B2 — Definir $paginas para que la vista no falle
        $total   = count($productos);
        $paginas = 1;
        $page    = 1;

        require __DIR__ . '/../views/shop/index.php';
    }

    /** Búsqueda de productos */
    public function buscar(): void
    {
        $q = trim($_GET['q'] ?? '');
        $productos  = $q !== '' ? $this->productModel->buscar($q) : [];
        $categorias = $this->categoryModel->getAll();
        $paginas    = 1;
        $page       = 1;

        require __DIR__ . '/../views/shop/index.php';
    }

    /** Detalle de un producto */
    public function detalle(int $id): void
    {
        $producto = $this->productModel->getById($id);

        if (!$producto) {
            http_response_code(404);
            echo "<h1>Producto no encontrado</h1>";
            return;
        }

        $valoraciones = $this->reviewModel->getByProducto($id);
        $media        = $this->reviewModel->getMedia($id);
        $enDeseos     = false;

        if (!empty($_SESSION['usuario_id'])) {
            $enDeseos = $this->wishlistModel->existe(
                $_SESSION['usuario_id'],
                $id
            );
        }

        require __DIR__ . '/../views/shop/product.php';
    }

    /** Guardar valoración de un producto */
    public function valorar(): void
    {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/?action=login');
            exit;
        }

        $idProducto  = (int) ($_POST['id_producto'] ?? 0);
        $puntuacion  = (int) ($_POST['puntuacion']  ?? 0);
        $comentario  = trim($_POST['comentario']    ?? '');

        // M8 — Verificar que el usuario ha comprado el producto
        if ($idProducto > 0 && $puntuacion >= 1 && $puntuacion <= 5) {
            $pdo = getConnection();
            $comprobacion = $pdo->prepare(
                "SELECT COUNT(*) FROM detalle_pedido dp
                 JOIN pedido p ON dp.id_pedido = p.id
                 WHERE p.id_usuario = ? AND dp.id_producto = ?
                   AND p.estado NOT IN ('cancelado')"
            );
            $comprobacion->execute([$_SESSION['usuario_id'], $idProducto]);
            if ($comprobacion->fetchColumn() == 0) {
                $_SESSION['error'] = 'Solo puedes valorar productos que hayas comprado.';
                header('Location: ' . BASE_URL . '/?action=producto&id=' . $idProducto);
                exit;
            }

            $this->reviewModel->guardar(
                $_SESSION['usuario_id'],
                $idProducto,
                $puntuacion,
                $comentario
            );
        }

        header('Location: ' . BASE_URL . '/?action=producto&id=' . $idProducto);
        exit;
    }
}

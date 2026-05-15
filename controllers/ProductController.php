<?php
// controllers/ProductController.php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Wishlist.php';

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
        $productos  = $this->productModel->getByGenero($genero);
        $categorias = $this->categoryModel->getAll();
        $generoActual = $genero;

        require __DIR__ . '/../views/shop/index.php';
    }

    /** Búsqueda de productos */
    public function buscar(): void
    {
        $q = trim($_GET['q'] ?? '');
        $productos  = $q !== '' ? $this->productModel->buscar($q) : [];
        $categorias = $this->categoryModel->getAll();

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
            header('Location: /velora_shop/public/?action=login');
            exit;
        }

        $idProducto  = (int) ($_POST['id_producto'] ?? 0);
        $puntuacion  = (int) ($_POST['puntuacion']  ?? 0);
        $comentario  = trim($_POST['comentario']    ?? '');

        if ($idProducto > 0 && $puntuacion >= 1 && $puntuacion <= 5) {
            $this->reviewModel->guardar(
                $_SESSION['usuario_id'],
                $idProducto,
                $puntuacion,
                $comentario
            );
        }

        header("Location: /velora_shop/public/?action=producto&id={$idProducto}");
        exit;
    }
}

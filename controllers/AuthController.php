<?php
// controllers/AuthController.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // ── REGISTRO ──────────────────────────────────────────

    public function registrar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../views/auth/register.php';
            return;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $email  = trim($_POST['email']  ?? '');
        $pass   = $_POST['contraseña']  ?? '';
        $pass2  = $_POST['contraseña2'] ?? '';

        // Validaciones
        if (empty($nombre) || empty($email) || empty($pass)) {
            $_SESSION['error'] = "Todos los campos son obligatorios.";
            header('Location: /velora_shop/public/?action=register');
            exit;
        }
        if ($pass !== $pass2) {
            $_SESSION['error'] = "Las contraseñas no coinciden.";
            header('Location: /velora_shop/public/?action=register');
            exit;
        }
        if (strlen($pass) < 6) {
            $_SESSION['error'] = "La contraseña debe tener al menos 6 caracteres.";
            header('Location: /velora_shop/public/?action=register');
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Email no válido.";
            header('Location: /velora_shop/public/?action=register');
            exit;
        }

        // Email duplicado
        if ($this->userModel->getByEmail($email)) {
            $_SESSION['error'] = "Este email ya está registrado.";
            header('Location: /velora_shop/public/?action=register');
            exit;
        }

        // Guardar con hash bcrypt
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        $this->userModel->crear($nombre, $email, $hash);

        $_SESSION['success'] = "Cuenta creada correctamente. ¡Ya puedes iniciar sesión!";
        header('Location: /velora_shop/public/?action=login');
        exit;
    }

    // ── LOGIN ─────────────────────────────────────────────

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        $email = trim($_POST['email']   ?? '');
        $pass  = $_POST['contraseña']   ?? '';

        $usuario = $this->userModel->getByEmail($email);

        if (!$usuario || !password_verify($pass, $usuario['contraseña'])) {
            $_SESSION['error'] = "Email o contraseña incorrectos.";
            header('Location: /velora_shop/public/?action=login');
            exit;
        }

        // Guardar datos en sesión
        $_SESSION['usuario_id']     = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol']    = $usuario['rol'];

        if ($usuario['rol'] === 'admin') {
            header('Location: /velora_shop/public/?action=admin_dashboard');
        } else {
            header('Location: /velora_shop/public/');
        }
        exit;
    }

    // ── LOGOUT ────────────────────────────────────────────

    public function logout(): void
    {
        session_destroy();
        header('Location: /velora_shop/public/?action=login');
        exit;
    }
}
<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = getConnection();
    $stmt = $db->query("SELECT 1");
    echo "✅ Conexión y consulta OK";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
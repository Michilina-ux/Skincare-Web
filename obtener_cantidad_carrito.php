<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['usuario_id'])) {
    echo json_encode(['cantidad' => 0]);
    exit;
}

$host = 'localhost';
$dbname = 'skincare_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $usuario_id = $_SESSION['usuario_id'];
    
    // Obtener carrito activo
    $stmt = $pdo->prepare("SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo'");
    $stmt->execute([$usuario_id]);
    $carrito = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$carrito) {
        echo json_encode(['cantidad' => 0]);
        exit;
    }
    
    // Contar items en el carrito
    $stmt = $pdo->prepare("SELECT SUM(cantidad) as total FROM carrito_items WHERE id_carrito = ?");
    $stmt->execute([$carrito['id_carrito']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $cantidad = $result['total'] ? (int)$result['total'] : 0;
    
    echo json_encode(['cantidad' => $cantidad]);
    
} catch(PDOException $e) {
    echo json_encode(['cantidad' => 0, 'error' => $e->getMessage()]);
}
?>
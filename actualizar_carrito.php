<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$host = 'localhost';
$dbname = 'skincare_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit;
}

$item_id = $_POST['item_id'] ?? null;
$cambio = $_POST['cambio'] ?? 0;

if(!$item_id) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Obtener cantidad actual
$stmt = $pdo->prepare("SELECT cantidad FROM carrito_items WHERE id_item = ?");
$stmt->execute([$item_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$item) {
    echo json_encode(['success' => false, 'message' => 'Item no encontrado']);
    exit;
}

$nueva_cantidad = $item['cantidad'] + $cambio;

if($nueva_cantidad < 1) {
    echo json_encode(['success' => false, 'message' => 'La cantidad mínima es 1']);
    exit;
}

if($nueva_cantidad > 10) {
    echo json_encode(['success' => false, 'message' => 'La cantidad máxima es 10']);
    exit;
}

// Actualizar cantidad
$stmt = $pdo->prepare("UPDATE carrito_items SET cantidad = ? WHERE id_item = ?");
$stmt->execute([$nueva_cantidad, $item_id]);

echo json_encode(['success' => true, 'nueva_cantidad' => $nueva_cantidad]);
?>
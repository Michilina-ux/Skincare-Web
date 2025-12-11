<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en pantalla
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log'); // Guardar errores en archivo

session_start();
header('Content-Type: application/json');

// Limpiar cualquier salida previa
ob_clean();

if(!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
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

if(!isset($_POST['item_id'])) {
    echo json_encode(['success' => false, 'message' => 'Item no especificado']);
    exit;
}

$item_id = (int)$_POST['item_id'];

try {
    // Obtener id_carrito antes de eliminar
    $stmt = $pdo->prepare("SELECT id_carrito FROM carrito_items WHERE id_item = ?");
    $stmt->execute([$item_id]);
    $carrito = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$carrito) {
        echo json_encode(['success' => false, 'message' => 'Item no encontrado']);
        exit;
    }
    
    $carrito_id = $carrito['id_carrito'];
    
    // Eliminar el item
    $stmt = $pdo->prepare("DELETE FROM carrito_items WHERE id_item = ?");
    $stmt->execute([$item_id]);
    
    // Recalcular total del carrito
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(subtotal), 0) as total 
        FROM carrito_items 
        WHERE id_carrito = ?
    ");
    $stmt->execute([$carrito_id]);
    $total = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Actualizar total del carrito
    $stmt = $pdo->prepare("UPDATE carrito SET total = ? WHERE id_carrito = ?");
    $stmt->execute([$total['total'], $carrito_id]);
    
    // Verificar si el carrito quedó vacío
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM carrito_items WHERE id_carrito = ?");
    $stmt->execute([$carrito_id]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Producto eliminado del carrito',
        'carrito_vacio' => ($count['count'] == 0)
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()]);
}
exit;
?>
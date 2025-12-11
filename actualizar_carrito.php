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

if(!isset($_POST['item_id']) || !isset($_POST['cambio'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$item_id = (int)$_POST['item_id'];
$cambio = (int)$_POST['cambio'];

try {
    // Obtener la cantidad actual
    $stmt = $pdo->prepare("SELECT cantidad, precio_unitario, id_carrito FROM carrito_items WHERE id_item = ?");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$item) {
        echo json_encode(['success' => false, 'message' => 'Item no encontrado']);
        exit;
    }
    
    $nueva_cantidad = $item['cantidad'] + $cambio;
    
    // Validar que la cantidad sea mayor a 0
    if($nueva_cantidad < 1) {
        echo json_encode(['success' => false, 'message' => 'La cantidad mínima es 1']);
        exit;
    }
    
    // Validar cantidad máxima (opcional)
    if($nueva_cantidad > 99) {
        echo json_encode(['success' => false, 'message' => 'Cantidad máxima: 99']);
        exit;
    }
    
    // Actualizar cantidad y subtotal
    $nuevo_subtotal = $nueva_cantidad * $item['precio_unitario'];
    
    $stmt = $pdo->prepare("UPDATE carrito_items SET cantidad = ?, subtotal = ? WHERE id_item = ?");
    $stmt->execute([$nueva_cantidad, $nuevo_subtotal, $item_id]);
    
    // Actualizar total del carrito
    $stmt = $pdo->prepare("
        SELECT SUM(subtotal) as total 
        FROM carrito_items 
        WHERE id_carrito = ?
    ");
    $stmt->execute([$item['id_carrito']]);
    $total = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("UPDATE carrito SET total = ? WHERE id_carrito = ?");
    $stmt->execute([$total['total'], $item['id_carrito']]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Cantidad actualizada',
        'nueva_cantidad' => $nueva_cantidad,
        'nuevo_subtotal' => $nuevo_subtotal
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
}
exit;
?>
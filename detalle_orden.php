<?php
// detalle_orden.php - Archivo para obtener detalles de una orden específica
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'skincare_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

if(!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID de orden no proporcionado']);
    exit;
}

$idOrden = $_GET['id'];

// Obtener información de la orden
$stmtOrden = $pdo->prepare("
    SELECT 
        o.id_orden,
        o.total,
        o.estado_orden,
        o.metodo_pago,
        o.direccion_envio,
        o.fecha_orden,
        u.nombre as usuario,
        u.email,
        u.telefono
    FROM ordenes o
    INNER JOIN usuarios u ON o.id_usuario = u.id_usuario
    WHERE o.id_orden = ?
");
$stmtOrden->execute([$idOrden]);
$orden = $stmtOrden->fetch(PDO::FETCH_ASSOC);

if(!$orden) {
    echo json_encode(['error' => 'Orden no encontrada']);
    exit;
}

// Obtener productos de la orden
$stmtProductos = $pdo->prepare("
    SELECT 
        ci.cantidad,
        ci.precio_unitario,
        ci.subtotal,
        p.nombre_producto,
        p.imagen_producto,
        c.nombre_categoria
    FROM carrito_items ci
    INNER JOIN productos p ON ci.id_producto = p.id_producto
    INNER JOIN categoria c ON p.id_categoria = c.id_categoria
    INNER JOIN ordenes o ON ci.id_carrito = o.id_carrito
    WHERE o.id_orden = ?
");
$stmtProductos->execute([$idOrden]);
$productos = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);

// Retornar datos
echo json_encode([
    'success' => true,
    'orden' => $orden,
    'productos' => $productos
]);
?>
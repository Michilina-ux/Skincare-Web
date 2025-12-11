<?php
session_start();

if(!isset($_SESSION['usuario_id']) || !isset($_GET['orden'])) {
    header('Location: productos.php');
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
    die("Error de conexión: " . $e->getMessage());
}

$orden_id = $_GET['orden'];
$usuario_id = $_SESSION['usuario_id'];

// Obtener información de la orden
$stmt = $pdo->prepare("
    SELECT o.*, u.nombre, u.email 
    FROM ordenes o
    INNER JOIN usuarios u ON o.id_usuario = u.id_usuario
    WHERE o.id_orden = ? AND o.id_usuario = ?
");
$stmt->execute([$orden_id, $usuario_id]);
$orden = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$orden) {
    header('Location: productos.php');
    exit;
}

// Obtener productos de la orden
$stmt = $pdo->prepare("
    SELECT ci.*, p.nombre_producto, p.imagen_producto
    FROM carrito_items ci
    INNER JOIN productos p ON ci.id_producto = p.id_producto
    WHERE ci.id_carrito = ?
");
$stmt->execute([$orden['id_carrito']]);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden Confirmada - New Gloow</title>
    <link rel="icon" href="imagenes/Logo (1).png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #1e1f1e;
            --secondary-color: #92e492;
            --dark-color: #2c3e50;
        }
        
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .confirmation-container {
            max-width: 800px;
            margin: 60px auto;
            padding: 0 15px;
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out;
        }
        
        .success-icon i {
            font-size: 3rem;
            color: white;
        }
        
        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .confirmation-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
        }
        
        .order-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 20px 0;
        }
        
        .order-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            margin-top: 30px;
            text-align: left;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .detail-value {
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .product-list {
            margin-top: 30px;
        }
        
        .product-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }
        
        .product-info {
            flex-grow: 1;
        }
        
        .product-name {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .product-quantity {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .action-buttons {
            margin-top: 40px;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(233, 30, 99, 0.4);
        }
        
        .btn-outline-custom {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-outline-custom:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        
        .info-box i {
            color: #ffc107;
            font-size: 1.5rem;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="confirmation-card">
            <!-- Icono de Éxito -->
            <div class="success-icon">
                <i class="bi bi-check-lg"></i>
            </div>
            
            <h1>¡Orden Realizada con Éxito!</h1>
            <p class="text-muted">Gracias por tu compra, <?php echo htmlspecialchars($orden['nombre']); ?></p>
            
            <div class="order-number">
                Orden #<?php echo str_pad($orden['id_orden'], 6, '0', STR_PAD_LEFT); ?>
            </div>
            
            <p>Hemos enviado un correo de confirmación a <strong><?php echo htmlspecialchars($orden['email']); ?></strong></p>
            
            <!-- Detalles de la Orden -->
            <div class="order-details">
                <h5 class="mb-4"><i class="bi bi-info-circle"></i> Detalles de la Orden</h5>
                
                <div class="detail-row">
                    <span class="detail-label">Fecha:</span>
                    <span class="detail-value"><?php echo date('d/m/Y H:i', strtotime($orden['fecha_orden'])); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Método de Pago:</span>
                    <span class="detail-value"><?php echo $orden['metodo_pago']; ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Total:</span>
                    <span class="detail-value text-primary" style="font-size: 1.3rem;">$<?php echo number_format($orden['total'], 2); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Estado:</span>
                    <span class="badge bg-warning text-dark" style="font-size: 0.9rem;">Pendiente</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Dirección de Envío:</span>
                    <span class="detail-value text-end"><?php echo htmlspecialchars($orden['direccion_envio']); ?></span>
                </div>
                
                <?php if($orden['notas_orden']): ?>
                <div class="detail-row">
                    <span class="detail-label">Notas:</span>
                    <span class="detail-value text-end"><?php echo htmlspecialchars($orden['notas_orden']); ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Lista de Productos -->
            <div class="product-list">
                <h5 class="mb-3"><i class="bi bi-box-seam"></i> Productos Ordenados</h5>
                <?php foreach($productos as $producto): ?>
                <div class="product-item">
                    <img src="imagenes/<?php echo $producto['imagen_producto']; ?>" 
                         alt="<?php echo $producto['nombre_producto']; ?>" 
                         class="product-image"
                         onerror="this.src='https://via.placeholder.com/60'">
                    <div class="product-info">
                        <div class="product-name"><?php echo $producto['nombre_producto']; ?></div>
                        <div class="product-quantity">Cantidad: <?php echo $producto['cantidad']; ?> × $<?php echo number_format($producto['precio_unitario'], 2); ?></div>
                    </div>
                    <div class="text-primary fw-bold">$<?php echo number_format($producto['subtotal'], 2); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Información Adicional -->
            <div class="info-box">
                <i class="bi bi-clock-history"></i>
                <strong>Tiempo estimado de entrega:</strong> 3-5 días hábiles
                <br>
                <small>Recibirás un correo con el número de rastreo una vez que tu pedido sea enviado.</small>
            </div>
            
            <!-- Botones de Acción -->
            <div class="action-buttons">
                <a href="productos.php" class="btn btn-outline-custom">
                    <i class="bi bi-shop"></i> Seguir Comprando
                </a>
            </div>

            
            <!-- Compartir en Redes -->
            <div class="mt-4">
                <p class="text-muted mb-2">¡Comparte tu compra!</p>
                <a href="#" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="#" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="bi bi-twitter"></i>
                </a>
                <a href="#" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-whatsapp"></i>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confetti effect (opcional - animación de celebración)
        console.log('¡Orden confirmada exitosamente!');
    </script>
</body>
</html>
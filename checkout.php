<?php
session_start();

// Verificar que el usuario esté logueado
if(!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Configuración de la base de datos
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

$usuario_id = $_SESSION['usuario_id'];
$mensaje = '';
$tipo_mensaje = '';

// Obtener información del usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener carrito activo
$stmt = $pdo->prepare("SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo'");
$stmt->execute([$usuario_id]);
$carrito = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$carrito) {
    header('Location: productos.php?error=carrito_vacio');
    exit;
}

$carrito_id = $carrito['id_carrito'];

// Obtener items del carrito
$stmt = $pdo->prepare("
    SELECT 
        ci.*,
        p.nombre_producto,
        p.imagen_producto
    FROM carrito_items ci
    INNER JOIN productos p ON ci.id_producto = p.id_producto
    WHERE ci.id_carrito = ?
");
$stmt->execute([$carrito_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(count($items) == 0) {
    header('Location: productos.php?error=carrito_vacio');
    exit;
}

// Calcular totales
$subtotal = array_sum(array_column($items, 'subtotal'));
$envio = 99.00; // Costo fijo de envío
$total = $subtotal + $envio;

// Procesar orden
if(isset($_POST['procesar_orden'])) {
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $ciudad = trim($_POST['ciudad']);
    $codigo_postal = trim($_POST['codigo_postal']);
    $metodo_pago = $_POST['metodo_pago'];
    $notas = trim($_POST['notas'] ?? '');
    
    // Validaciones
    $errores = [];
    
    if(empty($nombre)) $errores[] = "El nombre es requerido";
    if(empty($telefono)) $errores[] = "El teléfono es requerido";
    if(empty($direccion)) $errores[] = "La dirección es requerida";
    if(empty($ciudad)) $errores[] = "La ciudad es requerida";
    if(empty($codigo_postal)) $errores[] = "El código postal es requerido";
    if(empty($metodo_pago)) $errores[] = "Selecciona un método de pago";
    
    if(count($errores) == 0) {
        try {
            $pdo->beginTransaction();
            
            // Construir dirección completa
            $direccion_completa = "$direccion, $ciudad, CP: $codigo_postal";
            
            // Crear orden
            $stmt = $pdo->prepare("
                INSERT INTO ordenes (id_usuario, id_carrito, total, metodo_pago, direccion_envio, notas_orden, estado_orden) 
                VALUES (?, ?, ?, ?, ?, ?, 'pendiente')
            ");
            $stmt->execute([$usuario_id, $carrito_id, $total, $metodo_pago, $direccion_completa, $notas]);
            $orden_id = $pdo->lastInsertId();
            
            // Actualizar estado del carrito
            $stmt = $pdo->prepare("UPDATE carrito SET estado = 'completado' WHERE id_carrito = ?");
            $stmt->execute([$carrito_id]);
            
            // Crear nuevo carrito para el usuario
            $stmt = $pdo->prepare("INSERT INTO carrito (id_usuario, estado) VALUES (?, 'activo')");
            $stmt->execute([$usuario_id]);
            
            // Actualizar información del usuario
            $stmt = $pdo->prepare("UPDATE usuarios SET telefono = ?, direccion = ? WHERE id_usuario = ?");
            $stmt->execute([$telefono, $direccion_completa, $usuario_id]);
            
            $pdo->commit();
            
            // Redirigir a confirmación
            header("Location: confirmacion_orden.php?orden=$orden_id");
            exit;
            
        } catch(Exception $e) {
            $pdo->rollBack();
            $mensaje = "Error al procesar la orden: " . $e->getMessage();
            $tipo_mensaje = "danger";
        }
    } else {
        $mensaje = implode("<br>", $errores);
        $tipo_mensaje = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - New Gloow</title>
      <link rel="icon" href="imagenes/Logo (1).png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--Iconos--><script src="https://kit.fontawesome.com/4b5e1ba30c.js" crossorigin="anonymous"></script>
   
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
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }
        
        .navbar-brand {
            color: white !important;
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .checkout-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 15px;
        }
        
        .checkout-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            padding: 10px 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
        }
        
        .order-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            position: sticky;
            top: 20px;
        }
        
        .product-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .product-item:last-child {
            border-bottom: none;
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
            margin-bottom: 5px;
        }
        
        .product-quantity {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .product-price {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 1rem;
        }
        
        .summary-total {
            border-top: 2px solid var(--primary-color);
            margin-top: 15px;
            padding-top: 15px;
            font-size: 1.3rem;
            font-weight: 700;
        }
        
        .payment-method {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .payment-method:hover {
            border-color: var(--primary-color);
            background: #fff5f9;
        }
        
        .payment-method input[type="radio"] {
            margin-right: 10px;
        }
        
        .payment-method.selected {
            border-color: var(--primary-color);
            background: #fff5f9;
        }
        
        .btn-place-order {
            background: linear-gradient(135deg, var(--primary-color) 0%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-place-order:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(233, 30, 99, 0.4);
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }
        
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }
        
        .step {
            text-align: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }
        
        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 600;
        }
        
        .step.active .step-icon {
            background: var(--primary-color);
        }
        
        .step.completed .step-icon {
            background: #28a745;
        }
        
        .step-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .security-badge {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .security-badge i {
            font-size: 2rem;
            color: #28a745;
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="productos.php">
                <i class="bi bi-arrow-left"></i> Volver a la Tienda
            </a>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="checkout-container">
        <!-- Progress Steps -->
        <div class="progress-steps">
            <div class="step completed">
                <div class="step-icon">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="step-label">Carrito</div>
            </div>
            <div class="step active">
                <div class="step-icon">
                    <i class="bi bi-credit-card"></i>
                </div>
                <div class="step-label">Checkout</div>
            </div>
            <div class="step">
                <div class="step-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="step-label">Confirmación</div>
            </div>
        </div>

        <?php if($mensaje): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" id="checkoutForm">
            <div class="row">
                <!-- Columna Izquierda - Formulario -->
                <div class="col-lg-7">
                    <!-- Información de Envío -->
                    <div class="checkout-card">
                        <h2 class="section-title">
                            <i class="bi bi-truck"></i> Información de Envío
                        </h2>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nombre" 
                                       value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="telefono" 
                                       value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>" 
                                       placeholder="5551234567" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Dirección <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="direccion" 
                                   placeholder="Calle y número" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Ciudad <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ciudad" 
                                       placeholder="Ciudad de México" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Código Postal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="codigo_postal" 
                                       placeholder="01000" maxlength="5" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notas adicionales (Opcional)</label>
                            <textarea class="form-control" name="notas" rows="3" 
                                      placeholder="Ej: Dejar con el portero, Timbre azul, etc."></textarea>
                        </div>
                    </div>

                    <!-- Método de Pago -->
                    <div class="checkout-card">
                        <h2 class="section-title">
                            <i class="bi bi-credit-card"></i> Método de Pago
                        </h2>
                        
                        <div class="payment-method" onclick="selectPayment(this, 'tarjeta')">
                            <input type="radio" name="metodo_pago" value="Tarjeta de Crédito" id="tarjeta" required>
                            <label for="tarjeta" style="cursor: pointer;">
                                <i class="bi bi-credit-card-2-front"></i> 
                                <strong>Tarjeta de Crédito/Débito</strong>
                            </label>
                        </div>
                        
                        <div class="payment-method" onclick="selectPayment(this, 'paypal')">
                            <input type="radio" name="metodo_pago" value="PayPal" id="paypal">
                            <label for="paypal" style="cursor: pointer;">
                                <i class="bi bi-paypal"></i> 
                                <strong>PayPal</strong>
                            </label>
                        </div>
                        
                        <div class="payment-method" onclick="selectPayment(this, 'oxxo')">
                            <input type="radio" name="metodo_pago" value="OXXO" id="oxxo">
                            <label for="oxxo" style="cursor: pointer;">
                                <i class="bi bi-shop"></i> 
                                <strong>OXXO (Pago en efectivo)</strong>
                            </label>
                        </div>
                        
                        <div class="payment-method" onclick="selectPayment(this, 'transferencia')">
                            <input type="radio" name="metodo_pago" value="Transferencia Bancaria" id="transferencia">
                            <label for="transferencia" style="cursor: pointer;">
                                <i class="bi bi-bank"></i> 
                                <strong>Transferencia Bancaria</strong>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Resumen -->
                <div class="col-lg-5">
                    <div class="checkout-card">
                        <div class="order-summary">
                            <h2 class="section-title">
                                <i class="bi bi-receipt"></i> Resumen de Orden
                            </h2>
                            
                            <!-- Productos -->
                            <?php foreach($items as $item): ?>
                            <div class="product-item">
                                <img src="images/<?php echo $item['imagen_producto']; ?>" 
                                     alt="<?php echo $item['nombre_producto']; ?>" 
                                     class="product-image"
                                     onerror="this.src='https://via.placeholder.com/60'">
                                <div class="product-info">
                                    <div class="product-name"><?php echo $item['nombre_producto']; ?></div>
                                    <div class="product-quantity">Cantidad: <?php echo $item['cantidad']; ?></div>
                                </div>
                                <div class="product-price">$<?php echo number_format($item['subtotal'], 2); ?></div>
                            </div>
                            <?php endforeach; ?>
                            
                            <!-- Totales -->
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span>$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Envío:</span>
                                <span>$<?php echo number_format($envio, 2); ?></span>
                            </div>
                            <div class="summary-row summary-total">
                                <span>Total:</span>
                                <span class="text-primary">$<?php echo number_format($total, 2); ?></span>
                            </div>
                            
                            <!-- Botón de Pagar -->
                            <button type="submit" name="procesar_orden" class="btn-place-order mt-4">
                                <i class="bi bi-lock"></i> Realizar Pedido
                            </button>
                            
                            <!-- Security Badge -->
                            <div class="security-badge">
                                <i class="bi bi-shield-check"></i>
                                <p class="mb-0 mt-2"><small>Pago 100% seguro y protegido</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Seleccionar método de pago
        function selectPayment(element, id) {
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            element.classList.add('selected');
            document.getElementById(id).checked = true;
        }
        
        // Validar código postal (solo números)
        document.querySelector('input[name="codigo_postal"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 5);
        });
        
        // Validar teléfono (solo números)
        document.querySelector('input[name="telefono"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
        });
        
        // Confirmación antes de enviar
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const metodo = document.querySelector('input[name="metodo_pago"]:checked');
            if(!metodo) {
                e.preventDefault();
                alert('Por favor selecciona un método de pago');
                return false;
            }
            
            if(!confirm('¿Confirmas que los datos son correctos y deseas procesar la orden?')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>
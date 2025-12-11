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
$usuario_nombre = $_SESSION['usuario_nombre'];

// Obtener órdenes del usuario
$stmt = $pdo->prepare("
    SELECT 
        o.id_orden,
        o.total,
        o.estado_orden,
        o.metodo_pago,
        o.direccion_envio,
        o.fecha_orden,
        COUNT(ci.id_item) as total_items
    FROM ordenes o
    INNER JOIN carrito c ON o.id_carrito = c.id_carrito
    LEFT JOIN carrito_items ci ON c.id_carrito = ci.id_carrito
    WHERE o.id_usuario = ?
    GROUP BY o.id_orden, o.total, o.estado_orden, o.metodo_pago, o.direccion_envio, o.fecha_orden
    ORDER BY o.fecha_orden DESC
");
$stmt->execute([$usuario_id]);
$ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener carrito count
$stmt = $pdo->prepare("
    SELECT SUM(ci.cantidad) as total 
    FROM carrito_items ci
    INNER JOIN carrito c ON ci.id_carrito = c.id_carrito
    WHERE c.id_usuario = ? AND c.estado = 'activo'
");
$stmt->execute([$usuario_id]);
$carrito_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Órdenes - New Gloow</title>
    <link rel="icon" href="imagenes/Logo (1).png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://kit.fontawesome.com/4b5e1ba30c.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --primary-color: rgba(0, 0, 0, 1);
            --secondary-color: rgba(57, 214, 96, 1);
            --dark-color: #2c3e50;
        }
        
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* HEADER */
        header {
            background-color: rgb(254, 255, 255);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100px;
            padding: 0 20px;
        }
        
        .inicio {
            width: 100%;
            max-width: 1200px;
            height: 100px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        nav {
            display: flex;
            gap: 40px;
        }
        
        .opciones {
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            text-decoration: none;
            text-transform: uppercase;
            font-weight: 700;
            color: rgb(32, 102, 47);
            font-size: 20px;
            border: 2px solid transparent;
            padding: 5px;
            transition: .4s ease;
        }
        
        .opciones:hover {
            color: rgba(0, 0, 0, 0.76);
            transition: .4s ease;
            border: 2px solid rgb(20, 112, 11);
        }
        
        .logo {
            width: 100px;
            height: 100px;
            flex-shrink: 0;
        }
        
        /* NAVBAR */
        .navbar {
            background: linear-gradient( #000000ff 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }
        
        .navbar .container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .navbar-nav {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            padding: 8px 15px;
            transition: all 0.3s;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
        }
        
        .cart-icon {
            position: relative;
            font-size: 1.5rem;
            color: white;
            cursor: pointer;
        }
        
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ffc107;
            color: #000;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        /* CONTENEDOR PRINCIPAL */
        .orders-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 15px;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 30px;
            text-align: center;
        }
        
        /* TARJETAS DE ÓRDENES */
        .order-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }
        
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 15px;
        }
        
        .order-number {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .order-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .order-body {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .order-info {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            color: #6c757d;
            font-size: 0.85rem;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .info-value {
            color: var(--dark-color);
            font-weight: 600;
            font-size: 1rem;
        }
        
        .order-total {
            font-size: 1.5rem;
            color: var(--primary-color);
            font-weight: 700;
        }
        
        /* BADGES DE ESTADO */
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-block;
        }
        
        .status-pendiente {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-procesando {
            background: #cfe2ff;
            color: #084298;
        }
        
        .status-enviado {
            background: #d1e7dd;
            color: #0f5132;
        }
        
        .status-entregado {
            background: #d1e7dd;
            color: #0a3622;
        }
        
        .status-cancelado {
            background: #f8d7da;
            color: #842029;
        }
        
        /* BOTONES */
        .btn-view-details {
            background: linear-gradient( #4dad5aff 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-view-details:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 233, 30, 0.4);
            color: white;
        }
        
        /* ESTADO VACÍO */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .empty-state i {
            font-size: 5rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: var(--dark-color);
            margin-bottom: 15px;
        }
        
        .empty-state p {
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        /* FOOTER */
        .pie-pagina {
            width: 100%;
            background-color: rgb(250, 250, 250);
            margin-top: 60px;
        }
        
        .pie-pagina .grupo-1 {
            width: 100%;
            max-width: 1200px;
            margin: auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-gap: 50px;
            padding: 45px 0px;
        }
        
        .pie-pagina .grupo-1 .box figure {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .pie-pagina .grupo-1 .box figure img {
            width: 100%;
        }
        
        .pie-pagina .grupo-1 .box h2 {
            color: rgb(60, 104, 71);
            align-items: center;
            margin-bottom: 25px;
            font-size: 20px;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }
        
        .pie-pagina .grupo-1 .box p {
            color: black;
            margin-bottom: 10px;
        }
        
        .pie-pagina .grupo-1 .box .red-social a {
            display: inline-block;
            text-decoration: none;
            width: 45px;
            height: 45px;
            line-height: 45px;
            color: rgb(255, 255, 255);
            margin-right: 10px;
            background-color: rgb(91, 175, 126);
            text-align: center;
            transition: all 300ms ease;
        }
        
        .pie-pagina .grupo-1 .box .red-social a:hover {
            color: black;
        }
        
        .pie-pagina .grupo-2 {
            background-color: rgb(0, 0, 0);
            padding: 15px 10px;
            text-align: center;
            color: white;
        }
        
        .pie-pagina .grupo-2 small {
            font-size: 15px;
        }
        
        @media screen and (max-width:800px) {
            .pie-pagina .grupo-1 {
                width: 90%;
                grid-template-columns: repeat(1, 1fr);
                grid-gap: 30px;
                padding: 35px 0px;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header>
        <div class="inicio">
            <img src="imagenes/Logo (1).png" alt="logo" class="logo">
            <nav>
                <a class="opciones" href="index_cliente.php">Inicio</a>
                <a class="opciones" href="productos.php">Productos</a>
                <a class="opciones" href="nuevo.html">Información</a>
                <a class="opciones" href="logout.php">Cerrar sesión</a>
            </nav>
        </div>
    </header>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="productos.php">
                            <i class="bi bi-shop"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="mis_ordenes.php">
                            <i class="bi bi-bag-check"></i> Mis Órdenes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#modalCarrito">
                            <div class="cart-icon">
                                <i class="bi bi-cart3"></i>
                                <?php if($carrito_count > 0): ?>
                                    <span class="cart-badge"><?php echo $carrito_count; ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo $usuario_nombre; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="perfil.php"><i class="bi bi-person"></i> Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="orders-container">
        <h1 class="page-title">
            <i class="bi bi-bag-check-fill"></i> Mis Órdenes
        </h1>

        <?php if(count($ordenes) > 0): ?>
            <?php foreach($ordenes as $orden): ?>
                <?php
                // Determinar clase de estado
                $estado_class = 'status-' . str_replace(' ', '-', strtolower($orden['estado_orden']));
                ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-number">
                                <i class="bi bi-receipt"></i> 
                                Orden #<?php echo str_pad($orden['id_orden'], 6, '0', STR_PAD_LEFT); ?>
                            </div>
                            <div class="order-date">
                                <i class="bi bi-calendar3"></i> 
                                <?php echo date('d/m/Y H:i', strtotime($orden['fecha_orden'])); ?>
                            </div>
                        </div>
                        <div>
                            <span class="status-badge <?php echo $estado_class; ?>">
                                <?php echo ucfirst($orden['estado_orden']); ?>
                            </span>
                        </div>
                    </div>

                    <div class="order-body">
                        <div class="order-info">
                            <span class="info-label">Total de Items</span>
                            <span class="info-value">
                                <i class="bi bi-box-seam"></i> 
                                <?php echo $orden['total_items']; ?> productos
                            </span>
                        </div>

                        <div class="order-info">
                            <span class="info-label">Método de Pago</span>
                            <span class="info-value">
                                <i class="bi bi-credit-card"></i> 
                                <?php echo $orden['metodo_pago']; ?>
                            </span>
                        </div>

                        <div class="order-info">
                            <span class="info-label">Dirección de Envío</span>
                            <span class="info-value">
                                <i class="bi bi-geo-alt"></i> 
                                <?php echo substr($orden['direccion_envio'], 0, 50); ?>
                                <?php if(strlen($orden['direccion_envio']) > 50) echo '...'; ?>
                            </span>
                        </div>

                        <div class="order-info">
                            <span class="info-label">Total Pagado</span>
                            <span class="order-total">
                                $<?php echo number_format($orden['total'], 2); ?>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn-view-details" onclick="verDetalleOrden(<?php echo $orden['id_orden']; ?>)">
                            <i class="bi bi-eye"></i> Ver Detalles
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-bag-x"></i>
                <h3>No tienes órdenes aún</h3>
                <p>¡Empieza a comprar y tus órdenes aparecerán aquí!</p>
                <a href="productos.php" class="btn-view-details">
                    <i class="bi bi-shop"></i> Ir a Productos
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- MODAL DETALLE ORDEN -->
    <div class="modal fade" id="modalDetalleOrden" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient( #107c19ff 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="bi bi-receipt"></i> Detalle de Orden
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalleOrdenContenido">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="pie-pagina">
        <div class="grupo-1">
            <div class="box">
                <figure>
                    <a href="#">
                        <img src="imagenes/Logo (1).png" alt="logo" class="logo">
                    </a>
                </figure>
            </div>
            <div class="box">
                <h2>CONOCENOS</h2>
                <p>"Descubre el secreto de una piel radiante".</p>
                <p>¡Bienvenido a la familia! Estás a punto de descubrir por qué miles de mujeres han transformado su rutina de skincare con nosotros.</p>
            </div>
            <div class="box">
                <h2>SIGUENOS</h2>
                <div class="red-social">
                    <a href="https://www.facebook.com/profile.php?id=61577896257138" class="fa fa-facebook"></a>
                    <a href="https://www.instagram.com/mich_donas_?igsh=MXk4NWZ6eDZyZDdw" target="blank" class="fa fa-instagram"></a>
                    <a href="#" class="fa fa-twitter"></a>
                </div>
            </div>
        </div>
        <div class="grupo-2">
            <small>&copy; 2025 <b>New Gloow</b> - Todos los Derechos Reservados.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ver detalle de orden
        function verDetalleOrden(ordenId) {
            const modal = new bootstrap.Modal(document.getElementById('modalDetalleOrden'));
            modal.show();
            
            // Mostrar spinner
            document.getElementById('detalleOrdenContenido').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            `;
            
            // Cargar detalles con AJAX
            fetch(`detalle_orden.php?id=${ordenId}`)
                .then(response => response.json())
                .then(data => {
                    if(data.error) {
                        document.getElementById('detalleOrdenContenido').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> ${data.error}
                            </div>
                        `;
                        return;
                    }
                    
                    const orden = data.orden;
                    const productos = data.productos;
                    
                    let html = `
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Información de la Orden</h6>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Orden:</strong> #${String(orden.id_orden).padStart(6, '0')}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Fecha:</strong> ${new Date(orden.fecha_orden).toLocaleString('es-MX')}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Estado:</strong> <span class="badge bg-info">${orden.estado_orden}</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Método de Pago:</strong> ${orden.metodo_pago}
                                </div>
                                <div class="col-12 mb-2">
                                    <strong>Dirección:</strong> ${orden.direccion_envio}
                                </div>
                            </div>
                        </div>
                        
                        <h6 class="text-muted mb-3">Productos</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    productos.forEach(prod => {
                        html += `
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="imagenes/${prod.imagen_producto}" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin-right: 10px;"
                                             onerror="this.src='https://via.placeholder.com/50'">
                                        <div>
                                            <strong>${prod.nombre_producto}</strong><br>
                                            <small class="text-muted">${prod.nombre_categoria}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>${prod.cantidad}</td>
                                <td>$${parseFloat(prod.precio_unitario).toFixed(2)}</td>
                                <td><strong>$${parseFloat(prod.subtotal).toFixed(2)}</strong></td>
                            </tr>
                        `;
                    });
                    
                    html += `
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th><h5 class="text-primary mb-0">$${parseFloat(orden.total).toFixed(2)}</h5></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    `;
                    
                    document.getElementById('detalleOrdenContenido').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('detalleOrdenContenido').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> Error al cargar los detalles
                        </div>
                    `;
                });
        }
    </script>
</body>
</html>
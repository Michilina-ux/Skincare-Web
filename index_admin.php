<?php
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

// Procesar acciones
$mensaje = '';
$tipo_mensaje = '';

// AGREGAR PRODUCTO
if(isset($_POST['agregar_producto'])) {
    $stmt = $pdo->prepare("INSERT INTO productos (nombre_producto, descripcion_producto, id_categoria, precio, imagen_producto) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['nombre'],
        $_POST['descripcion'],
        $_POST['categoria'],
        $_POST['precio'],
        $_POST['imagen']
    ]);
    $mensaje = "Producto agregado exitosamente";
    $tipo_mensaje = "success";
}

// ACTUALIZAR PRODUCTO
if(isset($_POST['actualizar_producto'])) {
    $stmt = $pdo->prepare("UPDATE productos SET nombre_producto=?, descripcion_producto=?, id_categoria=?, precio=?, imagen_producto=? WHERE id_producto=?");
    $stmt->execute([
        $_POST['nombre'],
        $_POST['descripcion'],
        $_POST['categoria'],
        $_POST['precio'],
        $_POST['imagen'],
        $_POST['id_producto']
    ]);
    $mensaje = "Producto actualizado exitosamente";
    $tipo_mensaje = "success";
}

// ELIMINAR PRODUCTO
if(isset($_GET['eliminar'])) {
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id_producto = ?");
    $stmt->execute([$_GET['eliminar']]);
    $mensaje = "Producto eliminado exitosamente";
    $tipo_mensaje = "success";
}

// Obtener categorías
$categorias = $pdo->query("SELECT * FROM categoria ORDER BY nombre_categoria")->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos
$productos = $pdo->query("
    SELECT p.*, c.nombre_categoria 
    FROM productos p 
    INNER JOIN categoria c ON p.id_categoria = c.id_categoria 
    ORDER BY p.fecha_creacion DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Obtener estadísticas
$stats = $pdo->query("
    SELECT 
        COUNT(DISTINCT o.id_orden) as total_ordenes,
        COUNT(DISTINCT u.id_usuario) as total_usuarios,
        COUNT(p.id_producto) as total_productos,
        COALESCE(SUM(o.total), 0) as ventas_totales
    FROM productos p
    LEFT JOIN usuarios u ON 1=1
    LEFT JOIN ordenes o ON 1=1
")->fetch(PDO::FETCH_ASSOC);

// Obtener órdenes recientes
$ordenes = $pdo->query("
    SELECT 
        o.id_orden,
        u.nombre as usuario,
        u.email,
        o.total,
        o.estado_orden,
        o.fecha_orden,
        COUNT(ci.id_item) as total_items
    FROM ordenes o
    INNER JOIN usuarios u ON o.id_usuario = u.id_usuario
    INNER JOIN carrito c ON o.id_carrito = c.id_carrito
    INNER JOIN carrito_items ci ON c.id_carrito = ci.id_carrito
    GROUP BY o.id_orden, u.nombre, u.email, o.total, o.estado_orden, o.fecha_orden
    ORDER BY o.fecha_orden DESC
    LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);

// Obtener usuarios y sus compras
$usuarios = $pdo->query("
    SELECT 
        u.id_usuario,
        u.nombre,
        u.email,
        u.telefono,
        u.fecha_registro,
        COUNT(DISTINCT o.id_orden) as total_compras,
        COALESCE(SUM(o.total), 0) as total_gastado,
        u.activo
    FROM usuarios u
    LEFT JOIN ordenes o ON u.id_usuario = o.id_usuario
    GROUP BY u.id_usuario, u.nombre, u.email, u.telefono, u.fecha_registro, u.activo
    ORDER BY total_gastado DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Productos más vendidos
$productos_vendidos = $pdo->query("
    SELECT 
        p.nombre_producto,
        c.nombre_categoria,
        COUNT(ci.id_item) as veces_comprado,
        SUM(ci.cantidad) as cantidad_total,
        SUM(ci.subtotal) as ingresos_generados
    FROM carrito_items ci
    INNER JOIN productos p ON ci.id_producto = p.id_producto
    INNER JOIN categoria c ON p.id_categoria = c.id_categoria
    INNER JOIN carrito car ON ci.id_carrito = car.id_carrito
    WHERE car.estado = 'completado'
    GROUP BY p.id_producto, p.nombre_producto, c.nombre_categoria
    ORDER BY cantidad_total DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Skincare</title>
    <link rel="stylesheet" href="css/admi.css">
    <link rel="icon" href="imagenes/Logo (1).png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--Iconos--><script src="https://kit.fontawesome.com/4b5e1ba30c.js" crossorigin="anonymous"></script>
   
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="logo-section">
                    <i class="bi bi-flower1" style="font-size: 3rem; color: white;"></i>
                    <h4>Skincare Admin</h4>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#dashboard" data-section="dashboard">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#productos" data-section="productos">
                            <i class="bi bi-box-seam"></i> Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ordenes" data-section="ordenes">
                            <i class="bi bi-cart-check"></i> Órdenes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#usuarios" data-section="usuarios">
                            <i class="bi bi-people"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#reportes" data-section="reportes">
                            <i class="bi bi-graph-up"></i> Reportes
                        </a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="logout.php" >
                            <i class="fa-solid fa-unlock-keyhole"></i>Cerrar Sesion
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contenido Principal -->
            <div class="col-md-10 p-4">
                <?php if($mensaje): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill"></i> <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Dashboard -->
            <div id="dashboard" class="content-section">
                    <h2 class="section-title"><i class="bi bi-speedometer2"></i> Dashboard</h2>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-card position-relative">
                                <h3 class="text-primary"><?php echo $stats['total_productos']; ?></h3>
                                <p>Total Productos</p>
                                <i class="bi bi-box-seam stat-icon text-primary"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card position-relative">
                                <h3 class="text-success"><?php echo $stats['total_ordenes']; ?></h3>
                                <p>Órdenes Totales</p>
                                <i class="bi bi-cart-check stat-icon text-success"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card position-relative">
                                <h3 class="text-info"><?php echo $stats['total_usuarios']; ?></h3>
                                <p>Usuarios Registrados</p>
                                <i class="bi bi-people stat-icon text-info"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card position-relative">
                                <h3 class="text-warning">$<?php echo number_format($stats['ventas_totales'], 2); ?></h3>
                                <p>Ventas Totales</p>
                                <i class="bi bi-currency-dollar stat-icon text-warning"></i>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="table-container">
                                <h5><i class="bi bi-clock-history"></i> Órdenes Recientes</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Usuario</th>
                                                <th>Items</th>
                                                <th>Total</th>
                                                <th>Estado</th>
                                                <th>Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach(array_slice($ordenes, 0, 5) as $orden): ?>
                                            <tr>
                                                <td><strong>#<?php echo $orden['id_orden']; ?></strong></td>
                                                <td><?php echo $orden['usuario']; ?></td>
                                                <td><?php echo $orden['total_items']; ?></td>
                                                <td><strong>$<?php echo number_format($orden['total'], 2); ?></strong></td>
                                                <td>
                                                    <?php 
                                                    $badges = [
                                                        'pendiente' => 'warning',
                                                        'procesando' => 'info',
                                                        'enviado' => 'primary',
                                                        'entregado' => 'success',
                                                        'cancelado' => 'danger'
                                                    ];
                                                    ?>
                                                    <span class="badge bg-<?php echo $badges[$orden['estado_orden']]; ?>">
                                                        <?php echo ucfirst($orden['estado_orden']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($orden['fecha_orden'])); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div id="productos" class="content-section">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="section-title mb-0"><i class="bi bi-box-seam"></i> Gestión de Productos</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProducto">
                            <i class="bi bi-plus-circle"></i> Nuevo Producto
                        </button>
                    </div>

                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Imagen</th>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($productos as $prod): ?>
                                    <tr>
                                        <td><strong><?php echo $prod['id_producto']; ?></strong></td>
                                        <td>
                                            <img src="imagenes/<?php echo $prod['imagen_producto']; ?>" 
                                                 alt="<?php echo $prod['nombre_producto']; ?>" 
                                                 class="product-img"
                                                 onerror="this.src='https://via.placeholder.com/60'">
                                        </td>
                                        <td>
                                            <strong><?php echo $prod['nombre_producto']; ?></strong><br>
                                            <small class="text-muted"><?php echo substr($prod['descripcion_producto'], 0, 60); ?>...</small>
                                        </td>
                                        <td><span class="badge bg-secondary"><?php echo $prod['nombre_categoria']; ?></span></td>
                                        <td><strong class="text-success">$<?php echo number_format($prod['precio'], 2); ?></strong></td>
                                        <td><?php echo date('d/m/Y', strtotime($prod['fecha_creacion'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="editarProducto(<?php echo htmlspecialchars(json_encode($prod)); ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="?eliminar=<?php echo $prod['id_producto']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('¿Eliminar este producto?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Órdenes -->
                <div id="ordenes" class="content-section" >
                    <h2 class="section-title"><i class="bi bi-cart-check"></i>Órdenes</h2>
                    
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Orden</th>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($ordenes as $orden): ?>
                                    <tr>
                                        <td><strong>#<?php echo $orden['id_orden']; ?></strong></td>
                                        <td><?php echo $orden['usuario']; ?></td>
                                        <td><?php echo $orden['email']; ?></td>
                                        <td><?php echo $orden['total_items']; ?> productos</td>
                                        <td><strong class="text-success">$<?php echo number_format($orden['total'], 2); ?></strong></td>
                                        <td>
                                            <?php 
                                            $badges = [
                                                'pendiente' => 'warning',
                                                'procesando' => 'info',
                                                'enviado' => 'primary',
                                                'entregado' => 'success',
                                                'cancelado' => 'danger'
                                            ];
                                            ?>
                                            <span class="badge bg-<?php echo $badges[$orden['estado_orden']]; ?>">
                                                <?php echo ucfirst($orden['estado_orden']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($orden['fecha_orden'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="verDetalleOrden(<?php echo $orden['id_orden']; ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Usuarios -->
                <div id="usuarios" class="content-section" >
                    <h2 class="section-title"><i class="bi bi-people"></i>Usuarios</h2>
                    
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Compras</th>
                                        <th>Total Gastado</th>
                                        <th>Registro</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($usuarios as $user): ?>
                                    <tr>
                                        <td><strong><?php echo $user['id_usuario']; ?></strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-badge">
                                                    <?php echo strtoupper(substr($user['nombre'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <strong><?php echo $user['nombre']; ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $user['email']; ?></td>
                                        <td><?php echo $user['telefono']; ?></td>
                                        <td><span class="badge bg-info"><?php echo $user['total_compras']; ?> órdenes</span></td>
                                        <td><strong class="text-success">$<?php echo number_format($user['total_gastado'], 2); ?></strong></td>
                                        <td><?php echo date('d/m/Y', strtotime($user['fecha_registro'])); ?></td>
                                        <td>
                                            <?php if($user['activo']): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Reportes -->
                <div id="reportes" class="content-section" >
                    <h2 class="section-title"><i class="bi bi-graph-up"></i> Reportes y Estadísticas</h2>
                    
                    <div class="table-container">
                        <h5><i class="bi bi-trophy"></i> Productos Más Vendidos</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Producto</th>
                                        <th>Categoría</th>
                                        <th>Veces Comprado</th>
                                        <th>Cantidad Total</th>
                                        <th>Ingresos Generados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $pos = 1; foreach($productos_vendidos as $pv): ?>
                                    <tr>
                                        <td><strong><?php echo $pos++; ?></strong></td>
                                        <td><?php echo $pv['nombre_producto']; ?></td>
                                        <td><span class="badge bg-secondary"><?php echo $pv['nombre_categoria']; ?></span></td>
                                        <td><?php echo $pv['veces_comprado']; ?></td>
                                        <td><span class="badge bg-primary"><?php echo $pv['cantidad_total']; ?> unidades</span></td>
                                        <td><strong class="text-success">$<?php echo number_format($pv['ingresos_generados'], 2); ?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Producto -->
    <div class="modal fade" id="modalProducto" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProductoTitulo">
                        <i class="bi bi-plus-circle"></i> Agregar Producto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formProducto">
                    <div class="modal-body">
                        <input type="hidden" name="id_producto" id="id_producto">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="descripcion" rows="4" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Categoría</label>
                                    <select class="form-select" name="categoria" id="categoria" required>
                                        <option value="">Seleccionar...</option>
                                        <?php foreach($categorias as $cat): ?>
                                            <option value="<?php echo $cat['id_categoria']; ?>">
                                                <?php echo $cat['nombre_categoria']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
</html>
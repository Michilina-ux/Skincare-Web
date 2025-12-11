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

// Obtener o crear carrito activo del usuario
$stmt = $pdo->prepare("SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo'");
$stmt->execute([$usuario_id]);
$carrito = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$carrito) {
    // Crear carrito si no existe
    $stmt = $pdo->prepare("INSERT INTO carrito (id_usuario, estado) VALUES (?, 'activo')");
    $stmt->execute([$usuario_id]);
    $carrito_id = $pdo->lastInsertId();
} else {
    $carrito_id = $carrito['id_carrito'];
}

// Procesar agregar al carrito
$mensaje = '';
$tipo_mensaje = '';

if(isset($_POST['agregar_carrito'])) {
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'] ?? 1;
    
    // Obtener precio actual del producto
    $stmt = $pdo->prepare("SELECT precio FROM productos WHERE id_producto = ?");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($producto) {
        // Verificar si el producto ya está en el carrito
        $stmt = $pdo->prepare("SELECT id_item, cantidad FROM carrito_items WHERE id_carrito = ? AND id_producto = ?");
        $stmt->execute([$carrito_id, $producto_id]);
        $item_existente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($item_existente) {
            // Actualizar cantidad
            $nueva_cantidad = $item_existente['cantidad'] + $cantidad;
            $stmt = $pdo->prepare("UPDATE carrito_items SET cantidad = ? WHERE id_item = ?");
            $stmt->execute([$nueva_cantidad, $item_existente['id_item']]);
        } else {
            // Insertar nuevo item
            $stmt = $pdo->prepare("INSERT INTO carrito_items (id_carrito, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
            $stmt->execute([$carrito_id, $producto_id, $cantidad, $producto['precio']]);
        }
        
        $mensaje = "Producto agregado al carrito exitosamente";
        $tipo_mensaje = "success";
    }
}

// Obtener filtro de categoría
$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : null;
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : null;

// Obtener categorías
$categorias = $pdo->query("SELECT * FROM categoria ORDER BY nombre_categoria")->fetchAll(PDO::FETCH_ASSOC);

// Construir consulta de productos
$sql = "SELECT p.*, c.nombre_categoria FROM productos p 
        INNER JOIN categoria c ON p.id_categoria = c.id_categoria WHERE 1=1";

$params = [];

if($categoria_filtro) {
    $sql .= " AND p.id_categoria = ?";
    $params[] = $categoria_filtro;
}

if($busqueda) {
    $sql .= " AND (p.nombre_producto LIKE ? OR p.descripcion_producto LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
}

$sql .= " ORDER BY p.fecha_creacion DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener cantidad de items en el carrito
$stmt = $pdo->prepare("SELECT SUM(cantidad) as total FROM carrito_items WHERE id_carrito = ?");
$stmt->execute([$carrito_id]);
$carrito_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - New Gloow</title>
    <link rel="stylesheet" href="css/stilo_produ.css">
    <link rel="icon" href="imagenes/Logo (1).png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--Iconos--><script src="https://kit.fontawesome.com/4b5e1ba30c.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<header>
        <div class="inicio">
            <img src="imagenes/Logo (1).png" alt="logo " class="logo">
            <nav>
                <a class="opciones" href="index_cliente.php">Inició</a>
                <a class="opciones" href="productos.php">Productos</a>
                <a class="opciones" href="nuevo.html">Información</a>
                <a class="opciones" href="logout.php">Cerrar sesión</a>
          
            </nav>
        </div>
        <style>
                    
header{
    background-color: rgb(254, 255, 255);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100px;
    padding: 0 20px; /* Padding para evitar que el contenido toque los bordes */
}
.inicio{
    width: 100%;
    max-width: 1200px;
    height: 100px;
    display: flex;
    justify-content: space-between; /* Distribuye el logo y nav en los extremos */
    align-items: center;
    padding: 0 20px;
}
nav{
    display: flex;
    gap: 40px;
}
.opciones{
   font-family:'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
   text-decoration: none; 
   text-transform: uppercase;
   font-weight: 700;
   color: rgb(32, 102, 47);
   font-size: 20px;
   border: 2px solid transparent;
   padding: 5px;
   transition: .4s ease;
}
.opciones:hover{
    color: rgba(0, 0, 0, 0.76);
    transition: .4s ease;
    border: 2px solid rgb(20, 112, 11);
}
.logo{
    width: 100px; 
    height: 100px;
    flex-shrink: 0; /* Evita que el logo se reduzca */
}
.titulo {
    text-align: center;
    color: #f0f0f0;
}
    </style>
</header>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
        <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="productos.php">
                            <i class="bi bi-shop">Productos</i> 
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mis_ordenes.php">
                            <i class="bi bi-bag-check">Mis Órdenes</i> 
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
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <!-- CONTENIDO PRINCIPAL -->
    <div class="container">
        <?php if($mensaje): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
                <i class="bi bi-check-circle-fill"></i> <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- FILTROS Y BÚSQUEDA -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <form method="GET" class="d-flex">
                        <input type="text" name="buscar" class="form-control search-box" 
                               placeholder="Buscar productos..." 
                               value="<?php echo htmlspecialchars($busqueda ?? ''); ?>">
                        <button type="submit" class="btn btn-primary ms-2">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="text-center">
                        <a href="productos.php" class="filter-btn <?php echo !$categoria_filtro ? 'active' : ''; ?>">
                            Todos
                        </a>
                        <?php foreach($categorias as $cat): ?>
                            <a href="?categoria=<?php echo $cat['id_categoria']; ?>" 
                               class="filter-btn <?php echo $categoria_filtro == $cat['id_categoria'] ? 'active' : ''; ?>">
                                <?php echo $cat['nombre_categoria']; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- PRODUCTOS -->
        <div class="row">
            <?php if(count($productos) > 0): ?>
                <?php foreach($productos as $producto): ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="product-card">
                           <img src="imagenes/<?php echo $producto['imagen_producto']; ?>" 
                                 alt="<?php echo $producto['nombre_producto']; ?>" 
                                        class="product-image"
                                 onerror="this.src='https://via.placeholder.com/300x250?text=<?php echo urlencode($producto['nombre_producto']); ?>'">
                            <div class="product-body">
                                <span class="product-category"><?php echo $producto['nombre_categoria']; ?></span>
                                <h5 class="product-title"><?php echo $producto['nombre_producto']; ?></h5>
                                <p class="product-description"><?php echo $producto['descripcion_producto']; ?></p>
                                <div class="product-price">$<?php echo number_format($producto['precio'], 2); ?></div>
                                <button class="btn btn-add-cart" onclick="mostrarDetalleProducto(<?php echo htmlspecialchars(json_encode($producto)); ?>)">
                                    <i class="bi bi-cart-plus"></i> Agregar al Carrito
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> No se encontraron productos
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- MODAL DETALLE PRODUCTO -->
    <div class="modal fade" id="modalProducto" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProductoTitulo"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-5">
                                <img id="modalProductoImagen" src="" alt="" class="img-fluid rounded">
                            </div>
                            <div class="col-md-7">
                                <span id="modalProductoCategoria" class="product-category"></span>
                                <p id="modalProductoDescripcion" class="mt-3"></p>
                                <h3 id="modalProductoPrecio" class="text-primary mt-3"></h3>
                                <div class="mt-4">
                                    <label class="form-label">Cantidad:</label>
                                    <input type="number" name="cantidad" class="quantity-input form-control" value="1" min="1" max="10">
                                </div>
                                <input type="hidden" name="producto_id" id="modalProductoId">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="agregar_carrito" class="btn btn-primary">
                            <i class="bi bi-cart-plus"></i> Agregar al Carrito
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL CARRITO -->
    <div class="modal fade" id="modalCarrito" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-cart3"></i> Mi Carrito
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="carritoContenido">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<!--Footer-->
<style>
    
/*FOOTER */

.pie-pagina{
   width: 100%;
   background-color:rgb(250, 250, 250); 
}
.pie-pagina .grupo-1{
    width: 100%;
    max-width: 1200px;
    margin: auto;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-gap: 50px;
    padding: 45px 0px;
}
.pie-pagina .grupo-1 .box figure{
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;

}
.pie-pagina .grupo-1 .box figure img{
    width: 100%;   
    
}
.pie-pagina .grupo-1 .box h2{
    color:  rgb(60, 104, 71);
    align-items: center;
    margin-bottom: 25px;
    font-size: 20px;
    font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
}
.pie-pagina .grupo-1 .box p{
    color: black;
    margin-bottom: 10px;
}
.pie-pagina .grupo-1 .box .red-social a{
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
.pie-pagina .grupo-1 .box .red-social a:hover{
    color: black;
}
.pie-pagina .grupo-2{
    background-color: rgb(0, 0, 0);
    padding: 15px 10px;
    text-align: center;
    color: white;

}
.pie-pagina .grupo-2 small{
    font-size: 15px;
}
@media screen and (max-width:800px) {
    .pie-pagina .grupo-1{
        width: 90%;
        grid-template-columns: repeat(1, 1fr);
        grid-gap: 30px;
        padding: 35px 0px;
        
    }
}
</style>
<footer class="pie-pagina">
    <div class="grupo-1">
        <div class="box">
            <figure>
                <a href="#"> 
                <img src="imagenes/Logo (1).png" alt="logo de mich" class="logo">
                </a>
            </figure>
        </div>
        <div class="box">
            <h2>CONOCENOS</h2>
            <p>"Descubre el secreto de una piel radiante".</p>
        <p>¡Bienvenido a la familia! Estás a punto de descubrir por qué miles de mujeres han transformado su rutina de skincare con nosotros. Prepárate para enamorarte de tu piel.</p>
        </div>
        <div class="box">
            <h2>SIGUENOS</h2>
            <div class="red-social">
                <a href="https://www.facebook.com/profile.php?id=61583747990511" class="fa fa-facebook"></a>
                <a href="#" target="blank" class="fa fa-instagram"></a>
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
        // Mostrar detalle del producto
        function mostrarDetalleProducto(producto) {
            document.getElementById('modalProductoTitulo').textContent = producto.nombre_producto;
            document.getElementById('modalProductoImagen').src = 'imagenes/' + producto.imagen_producto;
            document.getElementById('modalProductoImagen').onerror = function() {
                this.src = 'https://via.placeholder.com/400x400?text=' + encodeURIComponent(producto.nombre_producto);
            };
            document.getElementById('modalProductoCategoria').textContent = producto.nombre_categoria;
            document.getElementById('modalProductoDescripcion').textContent = producto.descripcion_producto;
            document.getElementById('modalProductoPrecio').textContent = '$' + parseFloat(producto.precio).toFixed(2);
            document.getElementById('modalProductoId').value = producto.id_producto;
            
            new bootstrap.Modal(document.getElementById('modalProducto')).show();
        }

        // Cargar carrito
        document.getElementById('modalCarrito').addEventListener('show.bs.modal', function() {
            fetch('ver_carrito.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('carritoContenido').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('carritoContenido').innerHTML = '<div class="alert alert-danger">Error al cargar el carrito</div>';
                });
        });

        // Auto cerrar alertas
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 3000);
    </script>
</body>
</html>
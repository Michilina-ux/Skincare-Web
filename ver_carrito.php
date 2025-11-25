<?php
session_start();

if(!isset($_SESSION['usuario_id'])) {
    echo '<div class="alert alert-warning">Debes iniciar sesión</div>';
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
    echo '<div class="alert alert-danger">Error de conexión</div>';
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener carrito activo
$stmt = $pdo->prepare("SELECT id_carrito FROM carrito WHERE id_usuario = ? AND estado = 'activo'");
$stmt->execute([$usuario_id]);
$carrito = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$carrito) {
    echo '<div class="alert alert-info text-center">
            <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
            <p class="mt-3">Tu carrito está vacío</p>
            <button class="btn btn-primary" data-bs-dismiss="modal">Seguir Comprando</button>
          </div>';
    exit;
}

$carrito_id = $carrito['id_carrito'];

// Obtener items del carrito
$stmt = $pdo->prepare("
    SELECT 
        ci.id_item,
        ci.cantidad,
        ci.precio_unitario,
        ci.subtotal,
        p.nombre_producto,
        p.imagen_producto,
        p.id_producto
    FROM carrito_items ci
    INNER JOIN productos p ON ci.id_producto = p.id_producto
    WHERE ci.id_carrito = ?
");
$stmt->execute([$carrito_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(count($items) == 0) {
    echo '<div class="alert alert-info text-center">
            <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
            <p class="mt-3">Tu carrito está vacío</p>
            <button class="btn btn-primary" data-bs-dismiss="modal">Seguir Comprando</button>
          </div>';
    exit;
}

// Calcular total
$total = array_sum(array_column($items, 'subtotal'));
?>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $item): ?>
            <tr id="item-<?php echo $item['id_item']; ?>">
                <td>
                    <div class="d-flex align-items-center">
                        <img src="imagenes/<?php echo $item['imagen_producto']; ?>" 
                             alt="<?php echo $item['nombre_producto']; ?>" 
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin-right: 10px;"
                             onerror="this.src='https://via.placeholder.com/50'">
                        <span><?php echo $item['nombre_producto']; ?></span>
                    </div>
                </td>
                <td>$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                <td>
                    <div class="input-group" style="width: 120px;">
                        <button class="btn btn-sm btn-outline-secondary" onclick="actualizarCantidad(<?php echo $item['id_item']; ?>, -1)">-</button>
                        <input type="text" class="form-control form-control-sm text-center" value="<?php echo $item['cantidad']; ?>" readonly>
                        <button class="btn btn-sm btn-outline-secondary" onclick="actualizarCantidad(<?php echo $item['id_item']; ?>, 1)">+</button>
                    </div>
                </td>
                <td><strong>$<?php echo number_format($item['subtotal'], 2); ?></strong></td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="eliminarItem(<?php echo $item['id_item']; ?>)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total:</th>
                <th colspan="2"><h4 class="text-primary mb-0">$<?php echo number_format($total, 2); ?></h4></th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="d-grid gap-2">
    <a href="checkout.php" class="btn btn-success btn-lg">
        <i class="bi bi-credit-card"></i> Proceder al Pago
    </a>
    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Seguir Comprando</button>
</div>

<script>
function actualizarCantidad(itemId, cambio) {
    fetch('actualizar_carrito.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `item_id=${itemId}&cambio=${cambio}`
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Recargar carrito
            fetch('ver_carrito.php')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('carritoContenido').innerHTML = html;
                    // Actualizar badge
                    location.reload();
                });
        } else {
            alert(data.message);
        }
    });
}

function eliminarItem(itemId) {
    if(confirm('¿Eliminar este producto del carrito?')) {
        fetch('eliminar_item.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `item_id=${itemId}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
}
</script>
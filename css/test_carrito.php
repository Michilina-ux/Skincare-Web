<?php
session_start();

// Simular que hay un usuario logueado
if(!isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_id'] = 1; // Cambia este número por un ID válido de tu base de datos
}

$host = 'localhost';
$dbname = 'skincare_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3>✅ Conexión exitosa</h3>";
    
    // Verificar sesión
    echo "<p><strong>Usuario ID en sesión:</strong> " . $_SESSION['usuario_id'] . "</p>";
    
    // Verificar carrito
    $stmt = $pdo->prepare("SELECT * FROM carrito WHERE id_usuario = ? AND estado = 'activo'");
    $stmt->execute([$_SESSION['usuario_id']]);
    $carrito = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($carrito) {
        echo "<p>✅ <strong>Carrito encontrado:</strong> ID " . $carrito['id_carrito'] . "</p>";
        
        // Verificar items
        $stmt = $pdo->prepare("SELECT * FROM carrito_items WHERE id_carrito = ?");
        $stmt->execute([$carrito['id_carrito']]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p><strong>Items en carrito:</strong> " . count($items) . "</p>";
        
        if(count($items) > 0) {
            echo "<table border='1' cellpadding='10'>";
            echo "<tr><th>ID Item</th><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>";
            foreach($items as $item) {
                echo "<tr>";
                echo "<td>" . $item['id_item'] . "</td>";
                echo "<td>Producto #" . $item['id_producto'] . "</td>";
                echo "<td>" . $item['cantidad'] . "</td>";
                echo "<td>$" . $item['precio_unitario'] . "</td>";
                echo "<td>$" . $item['subtotal'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Botón de prueba
            if(count($items) > 0) {
                $primer_item = $items[0]['id_item'];
                echo "<br><h4>Prueba de botones:</h4>";
                echo "<button onclick='testActualizar($primer_item, 1)'>➕ Aumentar cantidad</button> ";
                echo "<button onclick='testActualizar($primer_item, -1)'>➖ Disminuir cantidad</button> ";
                echo "<button onclick='testEliminar($primer_item)'>🗑️ Eliminar</button>";
                echo "<div id='resultado' style='margin-top: 20px; padding: 10px; background: #f0f0f0;'></div>";
            }
        } else {
            echo "<p>❌ No hay items en el carrito</p>";
        }
    } else {
        echo "<p>❌ No se encontró carrito activo</p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<script>
function testActualizar(itemId, cambio) {
    document.getElementById('resultado').innerHTML = '⏳ Procesando...';
    
    fetch('actualizar_carrito.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `item_id=${itemId}&cambio=${cambio}`
    })
    .then(response => response.text())
    .then(text => {
        document.getElementById('resultado').innerHTML = '<strong>Respuesta:</strong><pre>' + text + '</pre>';
        console.log('Respuesta:', text);
        
        // Intentar parsear como JSON
        try {
            const data = JSON.parse(text);
            if(data.success) {
                document.getElementById('resultado').innerHTML = '✅ ' + data.message;
                setTimeout(() => location.reload(), 1000);
            } else {
                document.getElementById('resultado').innerHTML = '❌ ' + data.message;
            }
        } catch(e) {
            document.getElementById('resultado').innerHTML = '❌ Error: La respuesta no es JSON válido';
        }
    })
    .catch(error => {
        document.getElementById('resultado').innerHTML = '❌ Error de conexión: ' + error;
        console.error('Error:', error);
    });
}

function testEliminar(itemId) {
    if(!confirm('¿Eliminar este item?')) return;
    
    document.getElementById('resultado').innerHTML = '⏳ Eliminando...';
    
    fetch('eliminar_item.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `item_id=${itemId}`
    })
    .then(response => response.text())
    .then(text => {
        document.getElementById('resultado').innerHTML = '<strong>Respuesta:</strong><pre>' + text + '</pre>';
        console.log('Respuesta:', text);
        
        try {
            const data = JSON.parse(text);
            if(data.success) {
                document.getElementById('resultado').innerHTML = '✅ ' + data.message;
                setTimeout(() => location.reload(), 1000);
            } else {
                document.getElementById('resultado').innerHTML = '❌ ' + data.message;
            }
        } catch(e) {
            document.getElementById('resultado').innerHTML = '❌ Error: La respuesta no es JSON válido';
        }
    })
    .catch(error => {
        document.getElementById('resultado').innerHTML = '❌ Error de conexión: ' + error;
        console.error('Error:', error);
    });
}
</script>

<style>
body { font-family: Arial; padding: 20px; }
button { padding: 10px 20px; margin: 5px; cursor: pointer; font-size: 16px; }
pre { background: white; padding: 10px; border: 1px solid #ccc; }
</style>
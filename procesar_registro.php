<?php
// procesar_registro.php - Procesar registro de nuevos usuarios
header('Content-Type: application/json');

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'skincare_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos'
    ]);
    exit;
}

// Verificar que se recibieron los datos
if(!isset($_POST['nombre']) || !isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor completa todos los campos obligatorios'
    ]);
    exit;
}

// Sanitizar datos
$nombre = trim($_POST['nombre']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : null;
$direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : null;

// Validaciones
if(empty($nombre) || strlen($nombre) < 3) {
    echo json_encode([
        'success' => false,
        'message' => 'El nombre debe tener al menos 3 caracteres'
    ]);
    exit;
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'El email no es válido'
    ]);
    exit;
}

if(strlen($password) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'La contraseña debe tener al menos 6 caracteres'
    ]);
    exit;
}

// Verificar si el email ya existe
$stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
$stmt->execute([$email]);

if($stmt->fetch()) {
    echo json_encode([
        'success' => false,
        'message' => 'Este email ya está registrado'
    ]);
    exit;
}

// Encriptar contraseña (en producción usar password_hash)
// Por simplicidad, aquí guardamos directamente
// $passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Insertar nuevo usuario
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (nombre, email, password, telefono, direccion, activo) 
        VALUES (?, ?, ?, ?, ?, 1)
    ");
    
    $stmt->execute([
        $nombre,
        $email,
        $password, // En producción usar $passwordHash
        $telefono,
        $direccion
    ]);
    
    $nuevoId = $pdo->lastInsertId();
    
    // Crear carrito automáticamente para el nuevo usuario
    $stmt = $pdo->prepare("INSERT INTO carrito (id_usuario, estado) VALUES (?, 'activo')");
    $stmt->execute([$nuevoId]);
    
    echo json_encode([
        'success' => true,
        'message' => '¡Registro exitoso! Redirigiendo al inicio de sesión...',
        'usuario_id' => $nuevoId
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al registrar usuario: ' . $e->getMessage()
    ]);
}
?>
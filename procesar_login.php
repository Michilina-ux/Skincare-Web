<?php
// procesar_login.php - Procesar inicio de sesión
session_start();
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
if(!isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor completa todos los campos'
    ]);
    exit;
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];

// Validar email
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'El email no es válido'
    ]);
    exit;
}

// Buscar usuario por email
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
$stmt->execute([$email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$usuario) {
    echo json_encode([
        'success' => false,
        'message' => 'Email o contraseña incorrectos'
    ]);
    exit;
}

// Verificar contraseña
// NOTA: En producción deberías usar password_hash() y password_verify()
// Por simplicidad, aquí comparamos directamente
if($password !== $usuario['password']) {
    echo json_encode([
        'success' => false,
        'message' => 'Email o contraseña incorrectos'
    ]);
    exit;
}

// Login exitoso - Crear sesión
$_SESSION['usuario_id'] = $usuario['id_usuario'];
$_SESSION['usuario_nombre'] = $usuario['nombre'];
$_SESSION['usuario_email'] = $usuario['email'];
$_SESSION['usuario_rol'] = $usuario['rol'] ?? 'cliente'; // admin o cliente

// Verificar si es admin
$esAdmin = (isset($usuario['rol']) && $usuario['rol'] === 'admin');

// Actualizar último acceso (opcional)
$stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id_usuario = ?");
$stmt->execute([$usuario['id_usuario']]);

echo json_encode([
    'success' => true,
    'message' => '¡Inicio de sesión exitoso! Redirigiendo...',
    'redirect' => $esAdmin ? 'index_admin.php' : 'index_cliente.php',
    'usuario' => [
        'id' => $usuario['id_usuario'],
        'nombre' => $usuario['nombre'],
        'email' => $usuario['email'],
        'rol' => $_SESSION['usuario_rol']
    ]
]);
?>
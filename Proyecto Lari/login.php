<?php
// Conexión a la base de datos
include("conexionlogin.php");

// Recibe los datos del formulario
$email = $_POST['email'];
$password = $_POST['password'];

// Consulta para buscar al usuario por su email
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

// Verifica si encontró al usuario
if ($usuario = $resultado->fetch_assoc()) {

    // Compara la contraseña ingresada con la guardada (encriptada)
    if (password_verify($password, $usuario['password'])) {

        // Si coincide, inicia sesión
        session_start();
        $_SESSION['user'] = $usuario['email'];

        // Redirige a la página de bienvenida
        header("Location: inicio.html");
        exit();

    } else {
        echo "⚠️ Contraseña incorrecta. <a href='login.html'>Volver</a>";
    }

} else {
    echo "❌ Usuario no encontrado. <a href='login.html.html'>Volver</a>";
}

// Cierra la conexión
$conn->close();
?>

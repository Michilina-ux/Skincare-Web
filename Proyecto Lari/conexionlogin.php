<?php
$conn = new mysqli("localhost", "root", "", "login_glow");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>

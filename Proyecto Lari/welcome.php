<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: inicio.html");
    exit();
}
?>

<h1>Bienvenido, <?php echo $_SESSION['user']; ?>!</h1>
<a href="inicio.html">Cerrar sesión</a>
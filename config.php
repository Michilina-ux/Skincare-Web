<?php
// config.php - Archivo de configuración centralizado
session_start();

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'skincare_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Función para conectar a la base de datos
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Función para verificar si el usuario está logueado
function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

// Función para verificar si el usuario es admin
function esAdmin() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

// Función para obtener el ID del usuario actual
function getUsuarioId() {
    return $_SESSION['usuario_id'] ?? null;
}

// Función para redirigir si no está logueado
function requiereLogin() {
    if(!estaLogueado()) {
        header('Location: login.php');
        exit;
    }
}

// Función para redirigir si no es admin
function requiereAdmin() {
    if(!esAdmin()) {
        header('Location: index_cliente.php');
        exit;
    }
}

// Configuración de zona horaria
date_default_timezone_set('America/Mexico_City');
?>
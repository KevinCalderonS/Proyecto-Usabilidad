<?php
// Iniciar buffer de salida para evitar errores de headers
ob_start();

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function verificarSesion() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: ../index.php");
        exit();
    }
}

function verificarRol($rolRequerido) {
    if (!isset($_SESSION['usuario'])) {
        header("Location: ../index.php");
        exit();
    }
    if ($_SESSION['usuario']['rol'] !== $rolRequerido) {
        // Opcional: redirigir a página de acceso denegado o menú principal
        header("Location: ../views/menu_principal.php");
        exit();
    }
}
?>
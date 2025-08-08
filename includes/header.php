<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Proyecto Accesibilidad Mejorado</title>
    <link rel="stylesheet" href="../assets/css/estilos.css" />
</head>
<body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        fetch("../includes/accesibilidad.php")
            .then(response => response.text())
            .then(html => {
                const div = document.createElement("div");
                div.innerHTML = html;
                document.body.appendChild(div);
            })
            .catch(error => console.error("Error cargando el menú de accesibilidad:", error));
    });
</script>

<?php
$usuario = $_SESSION['usuario'] ?? null;
?>

<nav role="navigation" aria-label="Menú principal">
    <ul>
        <?php if ($usuario && $usuario['rol'] === 'admin'): ?>
            <li><a href="../views/usuarios.php" accesskey="u">Usuarios</a></li>
            <li><a href="../views/contenidos.php" accesskey="c">Contenidos</a></li>
            <li><a href="../views/contact_messages.php" accesskey="m">Mensajes de Contacto</a></li>
        <?php elseif ($usuario && $usuario['rol'] === 'usuario'): ?>
            <li><a href="../views/contenidos.php" accesskey="c">Contenidos</a></li>
        <?php endif; ?>
        <li><a href="../views/contact.php" accesskey="o">Contacto</a></li>
        <li><a href="../views/mensajes.php">Mensajes</a></li>
        <li><a href="../logout.php">Cerrar Sesión</a></li>
    </ul>
</nav>

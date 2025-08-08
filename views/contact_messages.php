<?php
include '../includes/auth.php';
include '../includes/db_connect.php';
verificarSesion();

$usuario = $_SESSION['usuario'] ?? null;
if (!$usuario || $usuario['rol'] !== 'admin') {
    header("Location: ../views/login.php");
    exit();
}

$sql = "SELECT id, nombre, correo, asunto, mensaje, fecha_envio FROM contact_messages ORDER BY fecha_envio DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mensajes de Contacto</title>
    <link rel="stylesheet" href="../assets/css/estilos.css" />
</head>
<body>
<?php include '../includes/header.php'; ?>

<main>
    <h1>Mensajes de Contacto</h1>
    <?php if ($result && $result->num_rows > 0): ?>
        <div style="overflow-x:auto;">
        <table class="table-contacto">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Asunto</th>
                    <th>Mensaje</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['fecha_envio']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><a href="mailto:<?php echo htmlspecialchars($row['correo']); ?>"><?php echo htmlspecialchars($row['correo']); ?></a></td>
                    <td><?php echo htmlspecialchars($row['asunto']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['mensaje'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    <?php else: ?>
        <p>No hay mensajes de contacto.</p>
    <?php endif; ?>
</main>
<?php include '../includes/accesibilidad.php'; ?>

</body>
</html>

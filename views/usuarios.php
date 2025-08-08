<?php
include '../includes/header.php';
include '../includes/auth.php';
include '../includes/db_connect.php';

verificarSesion();
verificarRol('admin');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Usuarios</title>
    <link rel="stylesheet" href="../assets/css/estilos.css" />
</head>
<body>
<?php include '../includes/accesibilidad.php'; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $rol = $conn->real_escape_string($_POST['rol']);

    $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES ('$nombre', '$correo', '$contrasena', '$rol')";
    $mensaje = ($conn->query($sql) === TRUE) ? "Usuario agregado correctamente." : "Error: " . $conn->error;
}

$result = $conn->query("SELECT id, nombre, correo, rol, fecha_registro FROM usuarios ORDER BY fecha_registro DESC");
?>

<main>
    <nav role="navigation" aria-label="Menú principal">
        <ul>
            <li><a href="menu_principal.php" accesskey="m">Menú Principal</a></li>
            <li><a href="contenidos.php" accesskey="c">Contenidos</a></li>
            <li><a href="../logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>
    <h1>Usuarios</h1>
    <?php if (isset($mensaje)): ?>
        <p role="alert" class="<?php echo (strpos($mensaje, 'correctamente') !== false) ? 'mensaje-exito' : 'mensaje-error'; ?>">
            <?php echo $mensaje; ?>
        </p>
    <?php endif; ?>
    <form method="post" action="" aria-label="Formulario para agregar usuario">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required />
        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" required />
        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required />
        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="usuario">Usuario</option>
            <option value="admin">Administrador</option>
        </select>
        <button type="submit">Agregar Usuario</button>
    </form>

    <h2>Lista de Usuarios</h2>
    <table border="1" aria-describedby="usuarios_desc">
        <caption id="usuarios_desc">Lista de usuarios registrados</caption>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Fecha de Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['correo']); ?></td>
                <td><?php echo htmlspecialchars($row['rol']); ?></td>
                <td><?php echo htmlspecialchars($row['fecha_registro']); ?></td>
                <td><a href="edit_usuario.php?id=<?php echo $row['id']; ?>" aria-label="Editar usuario <?php echo htmlspecialchars($row['nombre']); ?>">Editar</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>
</body>
</html>

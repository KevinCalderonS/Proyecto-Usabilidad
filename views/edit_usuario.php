<?php
include '../includes/auth.php';
include '../includes/db_connect.php';
include '../includes/accesibilidad.php';

verificarSesion();
verificarRol('admin');

if (!isset($_GET['id'])) {
    header('Location: usuarios.php');
    exit;
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM usuarios WHERE id = $id");
if ($result->num_rows !== 1) {
    echo "Usuario no encontrado.";
    exit;
}
$usuario = $result->fetch_assoc();

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $rol = $conn->real_escape_string($_POST['rol']);

    // Evitar duplicados de correo
    $check = $conn->query("SELECT id FROM usuarios WHERE correo='$correo' AND id!=$id");
    if ($check->num_rows > 0) {
        $mensaje = "Error: El correo ya está registrado por otro usuario.";
        $mensajeClase = "mensaje-error";
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, correo=?, rol=? WHERE id=?");
        $stmt->bind_param("sssi", $nombre, $correo, $rol, $id);
        if ($stmt->execute()) {
            $mensaje = "Usuario actualizado correctamente.";
            $mensajeClase = "mensaje-exito";
        } else {
            $mensaje = "Error al actualizar usuario.";
            $mensajeClase = "mensaje-error";
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar'])) {
    $conn->query("DELETE FROM usuarios WHERE id = $id");
    header('Location: usuarios.php?msg=eliminado');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../assets/css/estilos.css" />
</head>
<body>
<?php include '../includes/header.php'; ?>
<main>
    <h1>Editar Usuario</h1>
    <?php if ($mensaje): ?>
        <p role="alert" tabindex="-1" id="mensaje" class="<?php echo strpos($mensaje, 'Error') === false ? 'mensaje-exito' : 'mensaje-error'; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </p>
        <script>
            document.getElementById('mensaje').focus();
        </script>
    <?php endif; ?>
    <form method="POST" aria-label="Formulario para editar usuario" id="editUserForm">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($usuario['nombre']); ?>" />

        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" required value="<?php echo htmlspecialchars($usuario['correo']); ?>" />

        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="usuario" <?php echo $usuario['rol'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
            <option value="admin" <?php echo $usuario['rol'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
        </select>

        <button type="submit" name="actualizar">Guardar Cambios</button>
        <a href="usuarios.php" class="button-link">Cancelar</a>
    </form>
    <form method="post" action="" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
        <input type="hidden" name="eliminar" value="1">
        <button type="submit" style="background:#e53935;color:#fff;margin-top:1em;">Eliminar Usuario</button>
    </form>
</main>
</body>
</html>

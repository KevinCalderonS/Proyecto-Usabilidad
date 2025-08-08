<?php
include '../includes/auth.php';
include '../includes/db_connect.php';
verificarSesion();

$usuario = $_SESSION['usuario'] ?? null;
if (!$usuario) {
    header("Location: ../views/login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: contenidos.php");
    exit();
}

// Obtener contenido
$stmt = $conn->prepare("SELECT id, titulo, descripcion, archivo_url, accesibilidad, user_id FROM contenidos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: contenidos.php");
    exit();
}
$contenido = $result->fetch_assoc();

// Verificar permisos: usuario propietario o admin
if ($usuario['rol'] !== 'admin' && $contenido['user_id'] != $usuario['id']) {
    header("Location: contenidos.php");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_contenido']) && $_POST['delete_contenido'] == '1') {
        // Eliminar contenido completo
        $stmt = $conn->prepare("DELETE FROM contenidos WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: contenidos.php?msg=deleted");
            exit();
        } else {
            $mensaje = "Error al eliminar el contenido: " . $conn->error;
        }
    } else {
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $descripcion = $conn->real_escape_string($_POST['descripcion']);
        $accesibilidad = $conn->real_escape_string($_POST['accesibilidad']);

        if (isset($_POST['eliminar_archivo']) && $_POST['eliminar_archivo'] == '1') {
            // Eliminar archivo del servidor FTP si existe
            if (!empty($contenido['archivo_url'])) {
                $ftp_host = '145.223.104.13';
                $ftp_user = 'u599795228.6bg2251';
                $ftp_pass = 'G2-6b251';

                $parsed_url = parse_url($contenido['archivo_url']);
                $file_path = basename($parsed_url['path']);

                $ftp_conn = ftp_connect($ftp_host, 21, 30);
                if ($ftp_conn) {
                    $login = ftp_login($ftp_conn, $ftp_user, $ftp_pass);
                    if ($login) {
                        ftp_pasv($ftp_conn, true);
                        ftp_delete($ftp_conn, $file_path);
                    }
                    ftp_close($ftp_conn);
                }
            }
            $archivo_url = '';
        } else {
            $archivo_url = $conn->real_escape_string($_POST['archivo_url']);
        }

        // Opcional: manejar subida de archivo si se desea

        $sql = "UPDATE contenidos SET titulo = ?, descripcion = ?, archivo_url = ?, accesibilidad = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $titulo, $descripcion, $archivo_url, $accesibilidad, $id);

        if ($stmt->execute()) {
            $mensaje = "Contenido actualizado correctamente.";
            // Refrescar datos
            $contenido['titulo'] = $titulo;
            $contenido['descripcion'] = $descripcion;
            $contenido['archivo_url'] = $archivo_url;
            $contenido['accesibilidad'] = $accesibilidad;
        } else {
            $mensaje = "Error al actualizar el contenido: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Contenido</title>
    <link rel="stylesheet" href="../assets/css/estilos.css" />
</head>
<body>
<?php include '../includes/header.php'; ?>
<main>
    <h1>Editar Contenido</h1>
    <?php if ($mensaje): ?>
        <p role="alert" tabindex="-1" id="mensaje" class="<?php echo strpos($mensaje, 'Error') === false ? 'mensaje-exito' : 'mensaje-error'; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </p>
        <script>
            document.getElementById('mensaje').focus();
        </script>
    <?php endif; ?>
    <form method="POST" aria-label="Formulario para editar contenido" id="editForm">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" required value="<?php echo htmlspecialchars($contenido['titulo']); ?>" />

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($contenido['descripcion']); ?></textarea>

        <label for="archivo_url">URL del archivo:</label>
        <input type="url" id="archivo_url" name="archivo_url" value="<?php echo htmlspecialchars($contenido['archivo_url']); ?>" />
        <?php if (!empty($contenido['archivo_url'])): ?>
            <div>
                <input type="checkbox" id="eliminar_archivo" name="eliminar_archivo" value="1" />
                <label for="eliminar_archivo">Eliminar archivo actual</label>
            </div>
        <?php endif; ?>

        <label for="accesibilidad">Accesibilidad:</label>
        <textarea id="accesibilidad" name="accesibilidad"><?php echo htmlspecialchars($contenido['accesibilidad']); ?></textarea>

        <button type="submit">Guardar Cambios</button>
        <a href="contenidos.php" class="button-link">Cancelar</a>
    </form>

    <form method="POST" id="deleteForm" style="margin-top: 1em;">
        <input type="hidden" name="delete_contenido" value="1" />
        <button type="button" id="deleteButton" style="background-color: #d9534f; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer;">
            🗑️ Eliminar Contenido
        </button>
    </form>

    <script>
        document.getElementById('deleteButton').addEventListener('click', function() {
            if (confirm('¿Está seguro de eliminar este contenido? Esta acción no se puede deshacer.')) {
                document.getElementById('deleteForm').submit();
            }
        });
    </script>
</main>
</body>
</html>

<?php

include '../includes/auth.php';
include '../includes/db_connect.php';
verificarSesion();

$ftp_host = '145.223.104.13';
$ftp_user = 'u599795228.6bg2251';
$ftp_pass = 'G2-6b251';
$ftp_dir = '/domains/sisti.site/public_html/6b251/g2';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $accesibilidad = $conn->real_escape_string($_POST['accesibilidad']);
    $archivo_url = '';
    $mensaje = '';
    $user_id = $_SESSION['usuario']['id'];
    $user_email = $_SESSION['usuario']['email'] ?? '';

    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == UPLOAD_ERR_OK) {
        $archivo_tmp = $_FILES['archivo']['tmp_name'];
        $archivo_nombre = basename($_FILES['archivo']['name']);

        $ftp_conn = ftp_connect($ftp_host, 21, 30); // 30 segundos timeout
        if (!$ftp_conn) {
            $mensaje = "No se pudo conectar al servidor FTP.";
        } else {
            $login = ftp_login($ftp_conn, $ftp_user, $ftp_pass);
            if (!$login) {
                $mensaje = "No se pudo iniciar sesión en el servidor FTP.";
                ftp_close($ftp_conn);
            } else {
                ftp_pasv($ftp_conn, true); // Activar modo pasivo
                if (ftp_put($ftp_conn, $archivo_nombre, $archivo_tmp, FTP_BINARY)) {
                    $base_url = 'https://sisti.site/6b251/g2/';
                    $archivo_url = $base_url . $archivo_nombre;
                } else {
                    $mensaje = "Error al subir el archivo al servidor FTP.";
                }
                ftp_close($ftp_conn);
            }
        }
    } else {
        $archivo_url = $conn->real_escape_string($_POST['archivo_url']);
    }

    if (empty($mensaje)) {
        $sql = "INSERT INTO contenidos (titulo, descripcion, archivo_url, accesibilidad, user_id, user_email) VALUES ('$titulo', '$descripcion', '$archivo_url', '$accesibilidad', $user_id, '$user_email')";
        if ($conn->query($sql) === TRUE) {
            // Redirigir para evitar reenvío del formulario
            header("Location: contenidos.php?msg=success");
            exit();
        } else {
            $mensaje = "Error al insertar en la base de datos: " . $conn->error;
        }
    }
}

$usuario = $_SESSION['usuario'] ?? null;
if ($usuario && $usuario['rol'] === 'admin') {
    $sql = "SELECT c.*, u.correo AS correo_usuario
            FROM contenidos c
            LEFT JOIN usuarios u ON c.user_id = u.id
            ORDER BY c.fecha_subida DESC";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        error_log("DEBUG: Primer user_email: " . ($row['user_email'] ?? 'NULL'));
        $result->data_seek(0);
    }
} else {
    $user_id = $usuario['id'];
    $stmt = $conn->prepare("SELECT id, titulo, descripcion, archivo_url, accesibilidad, fecha_subida, user_id FROM contenidos WHERE user_id = ? ORDER BY fecha_subida DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contenidos</title>
    <link rel="stylesheet" href="../assets/css/estilos.css" />
</head>
<body>
    <nav role="navigation" aria-label="Menú principal">
        <ul>
            <li><a href="menu_principal.php" accesskey="m">Menú Principal</a></li>
            <li><a href="usuarios.php" accesskey="u">Usuarios</a></li>
            <li><a href="../logout.php">Cerrar Sesión</a></li>
        </ul>
    </nav>
    <main>
        <h1>Contenidos</h1>
        <?php
        $msg = '';
        $class = '';
        $icon = '';
        if (isset($_GET['msg'])) {
            if ($_GET['msg'] === 'success') {
                $msg = 'Contenido agregado correctamente.';
                $class = 'mensaje-exito';
                $icon = '✅';
            } elseif ($_GET['msg'] === 'deleted') {
                $msg = 'Contenido eliminado correctamente.';
                $class = 'mensaje-exito';
                $icon = '✅';
            } elseif ($_GET['msg'] === 'error') {
                $msg = 'Error al procesar la solicitud.';
                $class = 'mensaje-error';
                $icon = '❌';
            }
        } elseif (isset($mensaje)) {
            $msg = $mensaje;
            $isError = stripos($mensaje, 'error') !== false;
            $icon = $isError ? '❌' : '✅';
            $class = $isError ? 'mensaje-error' : 'mensaje-exito';
        }
        if ($msg !== ''):
        ?>
            <p role="alert" tabindex="-1" id="mensaje" class="<?php echo $class; ?>"><?php echo $icon . ' ' . htmlspecialchars($msg); ?></p>
            <script>
                document.getElementById('mensaje').focus();
            </script>
        <?php endif; ?>

        <section class="dashboard-section">
            <h2>
                <button class="toggle-section" aria-expanded="true" aria-controls="formulario-contenido">Agregar Contenido ▼</button>
            </h2>
            <form id="formulario-contenido" method="post" enctype="multipart/form-data" aria-label="Formulario para agregar contenido">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required />
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required></textarea>
                <label for="archivo">Archivo:</label>
                <input type="file" id="archivo" name="archivo" />
                <label for="archivo_url">URL del archivo (si no sube archivo):</label>
                <input type="url" id="archivo_url" name="archivo_url" />
                <label for="accesibilidad">Accesibilidad:</label>
                <textarea id="accesibilidad" name="accesibilidad"></textarea>
                <input type="hidden" id="user_email" name="user_email" value="<?php echo htmlspecialchars($_SESSION['usuario']['email'] ?? ''); ?>" />
                <button type="submit">Agregar Contenido</button>
            </form>
        </section>

        <section class="dashboard-section">
            <h2>
                <button class="toggle-section" aria-expanded="true" aria-controls="lista-contenidos">Lista de Contenidos ▼</button>
            </h2>
            <table id="lista-contenidos" border="1" aria-describedby="contenidos_desc">
                <caption id="contenidos_desc">Lista de contenidos subidos</caption>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Archivo URL</th>
                        <th>Accesibilidad</th>
                <th>Fecha de Subida</th>
                <?php if ($usuario && $usuario['rol'] === 'admin'): ?>
                <th>Correo Usuario</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($result): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
            <td><?php echo htmlspecialchars($row['titulo']); ?></td>
            <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
            <td><a href="<?php echo htmlspecialchars($row['archivo_url']); ?>" target="_blank" rel="noopener noreferrer">Ver archivo</a></td>
            <td><?php echo nl2br(htmlspecialchars($row['accesibilidad'])); ?></td>
            <td><?php echo htmlspecialchars($row['fecha_subida']); ?></td>
            <?php if ($usuario && $usuario['rol'] === 'admin'): ?>
            <td><?php echo htmlspecialchars($row['correo_usuario']); ?></td>
            <?php endif; ?>
            <td>
                <?php if ($usuario['rol'] === 'admin' || $row['user_id'] == $usuario['id']): ?>
                    <a href="edit_contenido.php?id=<?php echo $row['id']; ?>">Editar</a>
                <?php endif; ?>
            </td>
        </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr><td colspan="6">No hay contenidos disponibles.</td></tr>
            <?php endif; ?>
        </tbody>
            </table>
        </section>
    </main>
    <?php include '../includes/accesibilidad.php'; ?>

    <script>
        document.querySelectorAll('.toggle-section').forEach(button => {
            button.addEventListener('click', () => {
                const expanded = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', !expanded);
                const content = document.getElementById(button.getAttribute('aria-controls'));
                if (content) {
                    if (expanded) {
                        content.style.display = 'none';
                        button.textContent = button.textContent.replace('▼', '►');
                    } else {
                        content.style.display = 'block';
                        button.textContent = button.textContent.replace('►', '▼');
                    }
                }
            });
        });
    </script>
</body>
</html>

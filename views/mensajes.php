<?php
include '../includes/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'usuario') {
    header('Location: contenidos.php');
    exit;
}

$user_id = $_SESSION['usuario']['id'];
// Suponiendo que tienes una tabla 'respuestas_contacto' con los mensajes respondidos
$sql = "SELECT c.id, c.asunto, c.mensaje, r.respuesta, r.fecha_respuesta
        FROM contacto c
        LEFT JOIN respuestas_contacto r ON c.id = r.contacto_id
        WHERE c.user_id = $user_id AND r.respuesta IS NOT NULL
        ORDER BY r.fecha_respuesta DESC";
$result = $conn->query($sql);
?>

<main>
    <h1>Mensajes Recibidos</h1>
    <?php if ($result && $result->num_rows > 0): ?>
        <table class="table-contacto">
            <thead>
                <tr>
                    <th>Asunto</th>
                    <th>Mensaje</th>
                    <th>Respuesta</th>
                    <th>Fecha de Respuesta</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr onclick="mostrarModal('<?php echo htmlspecialchars(addslashes($row['respuesta'])); ?>')">
                    <td><?php echo htmlspecialchars($row['asunto']); ?></td>
                    <td><?php echo htmlspecialchars($row['mensaje']); ?></td>
                    <td><?php echo htmlspecialchars(mb_strimwidth($row['respuesta'], 0, 30, '...')); ?></td>
                    <td><?php echo htmlspecialchars($row['fecha_respuesta']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No tienes mensajes respondidos aún.</p>
    <?php endif; ?>
</main>

<!-- Modal para mostrar respuesta completa -->
<div id="modalMensaje" class="modal" style="display:none;">
  <div class="modal-content" style="background:#fff; padding:2em; border-radius:10px; max-width:400px; margin:auto; box-shadow:0 2px 16px rgba(0,0,0,0.2);">
    <h2>Respuesta del Administrador</h2>
    <p id="modalTexto"></p>
    <button onclick="cerrarModal()" style="margin-top:1em;">Cerrar</button>
  </div>
</div>

<script>
function mostrarModal(respuesta) {
    document.getElementById('modalMensaje').style.display = 'block';
    document.getElementById('modalTexto').textContent = respuesta;
}
function cerrarModal() {
    document.getElementById('modalMensaje').style.display = 'none';
}
</script>

<style>
/* Modal básico */
.modal {
    position: fixed;
    z-index: 9999;
    left: 0; top: 0; width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.3);
    display: flex; align-items: center; justify-content: center;
}
.modal-content {
    animation: modalIn 0.2s;
}
@keyframes modalIn {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.table-contacto tr { cursor: pointer; }
</style>
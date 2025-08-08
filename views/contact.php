<?php
include '../includes/db_connect.php';

$mensaje = "";
$enviado = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $asunto = $conn->real_escape_string($_POST['asunto']);
    $mensaje_texto = $conn->real_escape_string($_POST['mensaje']);

    $sql = "INSERT INTO contact_messages (nombre, correo, asunto, mensaje) VALUES ('$nombre', '$correo', '$asunto', '$mensaje_texto')";
    if ($conn->query($sql) === TRUE) {
        $enviado = true;
        $mensaje = "Mensaje enviado correctamente.";
    } else {
        $mensaje = "Error al enviar el mensaje: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contacto</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
    <main>
        <h1>Contacto</h1>
        <p>Por favor, complete el siguiente formulario para contactarnos. Responderemos a la brevedad.</p>
        <?php if ($mensaje) echo "<p role='alert' aria-live='assertive' class='" . ($enviado ? "mensaje-exito" : "mensaje-error") . "'>$mensaje</p>"; ?>
        <form method="POST" id="contactForm" novalidate>
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required aria-required="true" aria-describedby="nombreHelp" autocomplete="name">
            <small id="nombreHelp" class="input-help" aria-live="polite"></small>

            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required aria-required="true" aria-describedby="correoHelp" autocomplete="email">
            <small id="correoHelp" class="input-help" aria-live="polite"></small>

            <label for="asunto">Asunto:</label>
            <input type="text" id="asunto" name="asunto" required aria-required="true" aria-describedby="asuntoHelp">
            <small id="asuntoHelp" class="input-help" aria-live="polite"></small>

            <label for="mensaje">Mensaje:</label>
            <textarea id="mensaje" name="mensaje" rows="6" required aria-required="true" aria-describedby="mensajeHelp"></textarea>
            <small id="mensajeHelp" class="input-help" aria-live="polite"></small>

            <!-- Placeholder para captcha o verificación antispam -->

            <button type="submit" id="submitBtn">Enviar</button>
            <button type="button" id="cancelBtn">Cancelar</button>
        </form>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contactForm');
            const nombre = document.getElementById('nombre');
            const correo = document.getElementById('correo');
            const asunto = document.getElementById('asunto');
            const mensaje = document.getElementById('mensaje');
            const submitBtn = document.getElementById('submitBtn');
            const cancelBtn = document.getElementById('cancelBtn');

            const nombreHelp = document.getElementById('nombreHelp');
            const correoHelp = document.getElementById('correoHelp');
            const asuntoHelp = document.getElementById('asuntoHelp');
            const mensajeHelp = document.getElementById('mensajeHelp');

            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            function validateField() {
                let valid = true;

                if (nombre.value.trim() === '') {
                    nombreHelp.textContent = 'El nombre es obligatorio.';
                    nombre.setAttribute('aria-invalid', 'true');
                    valid = false;
                } else {
                    nombreHelp.textContent = '';
                    nombre.removeAttribute('aria-invalid');
                }

                if (!validateEmail(correo.value)) {
                    correoHelp.textContent = 'Por favor, ingrese un correo válido.';
                    correo.setAttribute('aria-invalid', 'true');
                    valid = false;
                } else {
                    correoHelp.textContent = '';
                    correo.removeAttribute('aria-invalid');
                }

                if (asunto.value.trim() === '') {
                    asuntoHelp.textContent = 'El asunto es obligatorio.';
                    asunto.setAttribute('aria-invalid', 'true');
                    valid = false;
                } else {
                    asuntoHelp.textContent = '';
                    asunto.removeAttribute('aria-invalid');
                }

                if (mensaje.value.trim() === '') {
                    mensajeHelp.textContent = 'El mensaje es obligatorio.';
                    mensaje.setAttribute('aria-invalid', 'true');
                    valid = false;
                } else {
                    mensajeHelp.textContent = '';
                    mensaje.removeAttribute('aria-invalid');
                }

                submitBtn.disabled = !valid;
                return valid;
            }

            nombre.addEventListener('input', validateField);
            correo.addEventListener('input', validateField);
            asunto.addEventListener('input', validateField);
            mensaje.addEventListener('input', validateField);

            form.addEventListener('submit', function(e) {
                if (!validateField()) {
                    e.preventDefault();
                } else {
                    submitBtn.disabled = true;
                }
            });

            cancelBtn.addEventListener('click', function() {
                form.reset();
                nombreHelp.textContent = '';
                correoHelp.textContent = '';
                asuntoHelp.textContent = '';
                mensajeHelp.textContent = '';
                submitBtn.disabled = false;
            });

            validateField();
        });
        </script>
    </main>
    <?php include '../includes/accesibilidad.php'; ?>
</body>
</html>

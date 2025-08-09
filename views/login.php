<?php
include '../includes/db_connect.php';

// Evitar error de sesión ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = "";

// --- Prevención de intentos múltiples ---
if (!isset($_SESSION['intentos_login'])) {
    $_SESSION['intentos_login'] = 0;
}
if (!isset($_SESSION['bloqueo_login'])) {
    $_SESSION['bloqueo_login'] = 0;
}

$max_intentos = 3;
$tiempo_bloqueo = 10; // segundos

if ($_SESSION['bloqueo_login'] > time()) {
    $mensaje = "Demasiados intentos fallidos. Intente nuevamente en " . ($_SESSION['bloqueo_login'] - time()) . " segundos.";
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    $sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($contrasena, $usuario['contrasena'])) {
            $_SESSION['usuario'] = $usuario;
            $_SESSION['intentos_login'] = 0; // reset al éxito
            header("Location: menu_principal.php");
            exit();
        } else {
            $_SESSION['intentos_login']++;
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $_SESSION['intentos_login']++;
        $mensaje = "Usuario no encontrado.";
    }

    if ($_SESSION['intentos_login'] >= $max_intentos) {
        $_SESSION['bloqueo_login'] = time() + $tiempo_bloqueo;
        $mensaje = "Demasiados intentos fallidos. Intente nuevamente en $tiempo_bloqueo segundos.";
    }
}

// Solo aquí incluye el header (después de procesar el login)
include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <main>
        <h1>Iniciar Sesión</h1>
        <?php if ($mensaje) echo "<p id='bloqueoMensaje' role='alert' aria-live='assertive' class='mensaje-error'>$mensaje</p>"; ?>
        <p id="mensajeGeneral" role="alert" aria-live="assertive" class="mensaje-error" style="display:none;"></p>
        <form method="POST" id="loginForm" novalidate <?php if ($_SESSION['bloqueo_login'] > time()) echo 'style="pointer-events:none;opacity:0.6;"'; ?>>
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required aria-required="true" aria-describedby="correoHelp" autofocus>
            <small id="correoHelp" class="input-help" aria-live="polite"></small>

            <label for="contrasena">Contraseña:</label>
            <div class="password-wrapper">
                <input type="password" id="contrasena" name="contrasena" required aria-required="true" aria-describedby="contrasenaHelp">
                <button type="button" id="togglePassword" aria-label="Mostrar contraseña">👁️</button>
            </div>
            <small id="contrasenaHelp" class="input-help" aria-live="polite"></small>

            <button type="submit" id="submitBtn">Ingresar</button>
            <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
        </form>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const correo = document.getElementById('correo');
            const contrasena = document.getElementById('contrasena');
            const togglePassword = document.getElementById('togglePassword');
            const submitBtn = document.getElementById('submitBtn');
            const correoHelp = document.getElementById('correoHelp');
            const contrasenaHelp = document.getElementById('contrasenaHelp');
            const mensajeGeneral = document.getElementById('mensajeGeneral');
            const bloqueoMensaje = document.getElementById('bloqueoMensaje');

            // --- Bloqueo visual con contador ---
            <?php if ($_SESSION['bloqueo_login'] > time()): ?>
                let tiempoRestante = <?php echo $_SESSION['bloqueo_login'] - time(); ?>;
                form.style.pointerEvents = "none";
                form.style.opacity = "0.6";
                submitBtn.disabled = true;

                function actualizarBloqueo() {
                    if (tiempoRestante > 0) {
                        if (bloqueoMensaje) {
                            bloqueoMensaje.textContent = "Demasiados intentos fallidos. Intente nuevamente en " + tiempoRestante + " segundos.";
                        }
                        tiempoRestante--;
                        setTimeout(actualizarBloqueo, 1000);
                    } else {
                        // Quitar bloqueo visual y mensaje
                        form.style.pointerEvents = "";
                        form.style.opacity = "";
                        submitBtn.disabled = false;
                        if (bloqueoMensaje) {
                            bloqueoMensaje.style.display = "none";
                        }
                    }
                }
                actualizarBloqueo();
            <?php endif; ?>

            togglePassword.addEventListener('click', function() {
                const type = contrasena.getAttribute('type') === 'password' ? 'text' : 'password';
                contrasena.setAttribute('type', type);
                this.setAttribute('aria-label', type === 'password' ? 'Mostrar contraseña' : 'Ocultar contraseña');
            });

            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            function validateField() {
                let valid = true;

                if (!validateEmail(correo.value)) {
                    correoHelp.textContent = 'Por favor, ingrese un correo válido.';
                    correo.setAttribute('aria-invalid', 'true');
                    valid = false;
                } else {
                    correoHelp.textContent = '';
                    correo.removeAttribute('aria-invalid');
                }

                if (contrasena.value.trim() === '') {
                    contrasenaHelp.textContent = 'La contraseña es obligatoria.';
                    contrasena.setAttribute('aria-invalid', 'true');
                    valid = false;
                } else {
                    contrasenaHelp.textContent = '';
                    contrasena.removeAttribute('aria-invalid');
                }

                submitBtn.disabled = !valid;
                return valid;
            }

            correo.addEventListener('input', function() {
                validateField();
                mensajeGeneral.style.display = 'none';
            });
            contrasena.addEventListener('input', function() {
                validateField();
                mensajeGeneral.style.display = 'none';
            });

            form.addEventListener('submit', function(e) {
                if (!validateField()) {
                    e.preventDefault();
                    mensajeGeneral.textContent = 'Datos Incorrectos';
                    mensajeGeneral.style.display = 'block';
                } else {
                    submitBtn.disabled = true;
                    mensajeGeneral.style.display = 'none';
                }
            });

            validateField();
        });
        </script>
    </main>
    <?php include '../includes/accesibilidad.php'; ?>
</body>
</html>

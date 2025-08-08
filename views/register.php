<?php
include '../includes/db_connect.php';
include '../includes/accesibilidad.php'; 

$mensaje = "";
$mensajeClase = "mensaje-error";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $rol = 'usuario';

    $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES ('$nombre', '$correo', '$contrasena', '$rol')";
    if ($conn->query($sql) === TRUE) {
        $mensaje = "Usuario registrado.";
        $mensajeClase = "mensaje-exito";
    } else {
        $mensaje = "Error: " . $conn->error;
        $mensajeClase = "mensaje-error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <style>
        .mensaje-exito {
            background: #c8f7c5;
            color: #256029;
            border: 1px solid #6fdc8c;
            padding: 1em;
            border-radius: 6px;
            margin-bottom: 1em;
            width: fit-content;
        }
    </style>
</head>
<body>
    <main>
        <h1>Registrar Usuario</h1>
        <?php if ($mensaje) echo "<p role='alert' aria-live='assertive' class='$mensajeClase'>$mensaje</p>"; ?>
        <form method="POST" id="registerForm" novalidate>
            <fieldset>
                <legend>Datos Personales</legend>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required aria-required="true" autocomplete="name" aria-describedby="nombreHelp">
                <small id="nombreHelp" class="input-help" aria-live="polite"></small>
            </fieldset>
            <fieldset>
                <legend>Credenciales</legend>
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" required aria-required="true" autocomplete="email" aria-describedby="correoHelp">
                <small id="correoHelp" class="input-help" aria-live="polite"></small>

                <label for="contrasena">Contraseña:</label>
                <div class="password-wrapper">
                    <input type="password" id="contrasena" name="contrasena" required aria-required="true" aria-describedby="contrasenaHelp" autocomplete="new-password">
                    <button type="button" id="togglePassword" aria-label="Mostrar contraseña">👁️</button>
                </div>
                <small id="contrasenaHelp" class="input-help" aria-live="polite"></small>
                <div id="passwordStrength" aria-live="polite" class="password-strength"></div>
            </fieldset>
            <button type="submit" id="submitBtn">Registrarse</button>
            <button type="button" id="cancelBtn">Cancelar</button>
            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
        </form>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const nombre = document.getElementById('nombre');
            const correo = document.getElementById('correo');
            const contrasena = document.getElementById('contrasena');
            const togglePassword = document.getElementById('togglePassword');
            const submitBtn = document.getElementById('submitBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const nombreHelp = document.getElementById('nombreHelp');
            const correoHelp = document.getElementById('correoHelp');
            const contrasenaHelp = document.getElementById('contrasenaHelp');
            const passwordStrength = document.getElementById('passwordStrength');

            togglePassword.addEventListener('click', function() {
                const type = contrasena.getAttribute('type') === 'password' ? 'text' : 'password';
                contrasena.setAttribute('type', type);
                this.setAttribute('aria-label', type === 'password' ? 'Mostrar contraseña' : 'Ocultar contraseña');
            });

            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            function checkPasswordStrength(password) {
                let strength = 0;
                if (password.length >= 8) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[\W]/.test(password)) strength++;
                return strength;
            }

            function updatePasswordStrength(password) {
                const strength = checkPasswordStrength(password);
                const strengthLabels = ['Muy débil', 'Débil', 'Moderada', 'Fuerte', 'Muy fuerte'];
                passwordStrength.textContent = password ? `Fortaleza: ${strengthLabels[strength]}` : '';
                passwordStrength.className = 'password-strength strength-' + strength;
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

                if (contrasena.value.trim() === '') {
                    contrasenaHelp.textContent = 'La contraseña es obligatoria.';
                    contrasena.setAttribute('aria-invalid', 'true');
                    valid = false;
                } else {
                    contrasenaHelp.textContent = '';
                    contrasena.removeAttribute('aria-invalid');
                }

                updatePasswordStrength(contrasena.value);

                submitBtn.disabled = !valid;
                return valid;
            }

            nombre.addEventListener('input', validateField);
            correo.addEventListener('input', validateField);
            contrasena.addEventListener('input', validateField);

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
                contrasenaHelp.textContent = '';
                passwordStrength.textContent = '';
                submitBtn.disabled = false;
            });

            validateField();
        });
        </script>
    </main>
</body>
</html>
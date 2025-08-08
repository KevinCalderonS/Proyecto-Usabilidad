<?php
include '../includes/auth.php';
include '../includes/db_connect.php';
include '../includes/accesibilidad.php'; 
verificarSesion();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Menú Principal</title>
    <link rel="stylesheet" href="../assets/css/estilos.css" />
    <style>
        /* Menú responsive hamburguesa */
        .main-navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #333;
            color: #fff;
            padding: 0.5em 1em;
        }
        .main-navbar .logo {
            font-weight: bold;
            font-size: 1.2em;
        }
        .main-navbar .menu-toggle {
            display: none;
            font-size: 2em;
            background: none;
            border: none;
            color: #fff;
            cursor: pointer;
        }
        .main-navbar .nav-links {
            display: flex;
            list-style: none;
            gap: 1em;
            margin: 0;
            padding: 0;
        }
        .main-navbar .nav-links li {
            display: flex;
            align-items: center;
        }
        .main-navbar .nav-links li a {
            color: #fff;
            text-decoration: none;
            padding: 0.5em 0.8em;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 0.5em;
            transition: background 0.2s;
        }
        .main-navbar .nav-links li a:hover,
        .main-navbar .nav-links li a:focus {
            background: #444;
            outline: none;
        }
        .main-navbar .nav-links li .icon {
            font-size: 1.1em;
        }
        @media (max-width: 768px) {
            .main-navbar .nav-links {
                display: none;
                flex-direction: column;
                background: #333;
                position: absolute;
                top: 60px;
                right: 0;
                width: 200px;
                padding: 1em;
                z-index: 100;
            }
            .main-navbar .nav-links.active {
                display: flex;
            }
            .main-navbar .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
<?php
    include '../includes/header.php';
    $usuario = $_SESSION['usuario'] ?? null;
?>

    <!-- Menú principal responsive con iconos y texto -->
    <nav class="main-navbar" aria-label="Navegación principal">
        <div class="logo">Menú</div>
        <button class="menu-toggle" aria-label="Abrir menú">&#9776;</button>
        <ul class="nav-links">
            <li><a href="contenidos.php"><span class="icon">📚</span> <span>Mis Contenidos</span></a></li>
            <li><a href="usuarios.php"><span class="icon">👤</span> <span>Gestión de Usuarios</span></a></li>
            <li><a href="menu_principal.php"><span class="icon">🏠</span> <span>Menú Principal</span></a></li>
            <li><a href="../logout.php"><span class="icon">🚪</span> <span>Cerrar Sesión</span></a></li>
        </ul>
    </nav>

    <main>
        <h1>Bienvenido al Menú Principal</h1>
        <?php if ($usuario): ?>
            <p>Hola, <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong>. Utilice el menú para navegar por las secciones.</p>
        <?php else: ?>
            <p>Utilice el menú para navegar por las secciones.</p>
        <?php endif; ?>

        <section class="dashboard-section">
            <h2>
                <button class="toggle-section" aria-expanded="true" aria-controls="cursos-destacados">Cursos Destacados ▼</button>
            </h2>
            <div id="cursos-destacados" class="cards-container">
                <article class="card">
                    <h3>Matemáticas Avanzadas</h3>
                    <p>Explora conceptos avanzados de álgebra, cálculo y geometría.</p>
                    <a href="#">Ver curso</a>
                </article>
                <article class="card">
                    <h3>Historia Universal</h3>
                    <p>Un recorrido por los eventos más importantes de la historia mundial.</p>
                    <a href="#">Ver curso</a>
                </article>
                <article class="card">
                    <h3>Programación en Python</h3>
                    <p>Aprende a programar desde cero con Python.</p>
                    <a href="#">Ver curso</a>
                </article>
            </div>
        </section>

        <section class="dashboard-section">
            <h2>
                <button class="toggle-section" aria-expanded="true" aria-controls="noticias-anuncios">Noticias y Anuncios ▼</button>
            </h2>
            <ul id="noticias-anuncios" class="news-list">
                <li><strong>01/07/2025:</strong> Nuevo curso de Inteligencia Artificial disponible.</li>
                <li><strong>15/06/2025:</strong> Mantenimiento programado el 20 de junio.</li>
                <li><strong>10/06/2025:</strong> Actualización de la plataforma con nuevas funcionalidades.</li>
            </ul>
        </section>

        <section class="dashboard-section">
            <h2>
                <button class="toggle-section" aria-expanded="true" aria-controls="recursos-educativos">Recursos Educativos ▼</button>
            </h2>
            <ul id="recursos-educativos" class="resources-list">
                <li><a href="#">Biblioteca Digital</a></li>
                <li><a href="#">Guías de Estudio</a></li>
                <li><a href="#">Videos Tutoriales</a></li>
                <li><a href="#">Foro de Discusión</a></li>
            </ul>
        </section>

        <section class="dashboard-section">
            <h2>
                <button class="toggle-section" aria-expanded="true" aria-controls="enlaces-rapidos">Enlaces Rápidos ▼</button>
            </h2>
            <ul id="enlaces-rapidos" class="quick-links">
                <li><a href="contenidos.php"><span class="icon">📚</span> <span>Mis Contenidos</span></a></li>
                <li><a href="usuarios.php"><span class="icon">👤</span> <span>Gestión de Usuarios</span></a></li>
                <li><a href="menu_principal.php"><span class="icon">🏠</span> <span>Menú Principal</span></a></li>
                <li><a href="../logout.php"><span class="icon">🚪</span> <span>Cerrar Sesión</span></a></li>
            </ul>
        </section>
    </main>

    <script>
        // Menú hamburguesa responsive
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });

        // Secciones colapsables
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
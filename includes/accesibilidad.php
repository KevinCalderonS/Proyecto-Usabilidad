<!-- Módulo de Accesibilidad Universal -->
<style>
/* Animación para el menú */
@keyframes slideIn {
    from { opacity: 0; transform: translateX(100%); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes slideOut {
    from { opacity: 1; transform: translateX(0); }
    to { opacity: 0; transform: translateX(100%); }
}
.accesibilidad-menu {
    max-height: 80vh;
    overflow-y: auto;
    box-sizing: border-box;
}
.accesibilidad-menu.visible {
    display: flex;
    animation: slideIn 0.3s forwards;
}
.accesibilidad-menu.hiding {
    animation: slideOut 0.3s forwards;
}
</style>

<div id="accesibilidad-toggle" onclick="toggleMenuAccesibilidad()" aria-label="Accesibilidad" tabindex="0" role="button" aria-pressed="false">
    ⚙️
</div>

<div id="accesibilidad-menu" class="accesibilidad-menu" role="dialog" aria-label="Opciones de accesibilidad" aria-modal="true" hidden>
    <button onclick="toggleContrast()" aria-pressed="false">🌓 Contraste Alto</button>
    <button onclick="toggleMonochrome()" aria-pressed="false">⬜ Modo Monocromático</button>
    <button onclick="toggleInvertColors()" aria-pressed="false">🔄 Invertir Colores</button>
    <button onclick="cambiarTamano('grande')" aria-pressed="false">🔠 Texto +</button>
    <button onclick="cambiarTamano('normal')" aria-pressed="false">🔤 Texto Normal</button>
    <button onclick="cambiarTamano('pequeno')" aria-pressed="false">🔡 Texto -</button>
    <button onclick="ajustarEspaciado('linea')" aria-pressed="false">↕️ Espaciado Línea</button>
    <button onclick="ajustarEspaciado('palabra')" aria-pressed="false">↔️ Espaciado Palabra</button>
    <button onclick="ajustarEspaciado('caracter')" aria-pressed="false">🔡 Espaciado Carácter</button>
    <button onclick="activarTipografiaDislexia()" aria-pressed="false">🔤 Tipografía Dislexia</button>
    <button onclick="toggleZoom()" aria-pressed="false">🔍 Zoom Página</button>
    <button onclick="toggleMagnifier()" aria-pressed="false">🔎 Lupa Puntual</button>
    <button onclick="resaltarFoco()" aria-pressed="false">🎯 Resaltar Foco y Enlaces</button>
    <button onclick="subrayarLinks()" aria-pressed="false">🔗 Subrayar enlaces</button>
    <button onclick="activarLecturaFacil()" aria-pressed="false">📖 Lectura fácil</button>
    <button onclick="toggleCursor()" aria-pressed="false">🖱️ Cursor grande</button>
    <button onclick="toggleAnimaciones()" aria-pressed="false">⏸️ Pausar animaciones</button>
    <button onclick="leerPagina()" aria-pressed="false">🔊 Lector de pantalla integrado</button>
    <button onclick="detenerLectura()" aria-pressed="false">⏹️ Detener lectura</button>
    <button onclick="toggleAudioDescription()" aria-pressed="false">🎧 Descripción de audio</button>
    <button onclick="toggleClosedCaptions()" aria-pressed="false">🎞️ Subtítulos cerrados</button>
    <button onclick="toggleLiveCaptions()" aria-pressed="false">📡 Subtítulos en directo</button>
    <button onclick="descargarTranscripcion()" aria-pressed="false">📄 Transcripción descargable</button>
    <button onclick="toggleAudioControls()" aria-pressed="false">🔈 Controles de audio</button>
    <button onclick="toggleVisualAlerts()" aria-pressed="false">⚠️ Alertas visuales</button>
</div>

<a href="#main-content" class="skip-link" tabindex="0">Saltar al contenido principal</a>

<script>
const toggleButton = document.getElementById("accesibilidad-toggle");
const menu = document.getElementById("accesibilidad-menu");

toggleButton.addEventListener('keydown', function(event) {
    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        toggleMenuAccesibilidad();
    }
});

menu.addEventListener('keydown', function(event) {
    const focusableButtons = Array.from(menu.querySelectorAll('button'));
    const index = focusableButtons.indexOf(document.activeElement);
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        const nextIndex = (index + 1) % focusableButtons.length;
        focusableButtons[nextIndex].focus();
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        const prevIndex = (index - 1 + focusableButtons.length) % focusableButtons.length;
        focusableButtons[prevIndex].focus();
    } else if (event.key === 'Escape') {
        event.preventDefault();
        toggleMenuAccesibilidad();
        toggleButton.focus();
    }
});

function toggleMenuAccesibilidad() {
    if (menu.hasAttribute("hidden")) {
        menu.removeAttribute("hidden");
        menu.classList.add("visible");
        menu.classList.remove("hiding");
        toggleButton.setAttribute("aria-pressed", "true");
        // Focus first button when menu opens
        const firstButton = menu.querySelector('button');
        if (firstButton) firstButton.focus();
    } else {
        menu.classList.add("hiding");
        menu.classList.remove("visible");
        toggleButton.setAttribute("aria-pressed", "false");
        setTimeout(() => {
            menu.setAttribute("hidden", "");
            menu.classList.remove("hiding");
        }, 300);
    }
}

function toggleContrast() {
    document.body.classList.toggle("contraste-alto");
    toggleAriaPressed(event.target);
}
function toggleMonochrome() {
    document.body.classList.toggle("modo-monocromatico");
    toggleAriaPressed(event.target);
}
function toggleInvertColors() {
    document.body.classList.toggle("invertir-colores");
    toggleAriaPressed(event.target);
}
function cambiarTamano(tamano) {
    document.body.classList.remove("texto-grande", "texto-pequeno");
    if (tamano === "grande") {
        document.body.classList.add("texto-grande");
    } else if (tamano === "pequeno") {
        document.body.classList.add("texto-pequeno");
    }
    toggleAriaPressed(event.target);
}
function ajustarEspaciado(tipo) {
    document.body.classList.remove("espaciado-linea", "espaciado-palabra", "espaciado-caracter");
    if (tipo === "linea") {
        document.body.classList.add("espaciado-linea");
    } else if (tipo === "palabra") {
        document.body.classList.add("espaciado-palabra");
    } else if (tipo === "caracter") {
        document.body.classList.add("espaciado-caracter");
    }
    toggleAriaPressed(event.target);
}
function activarTipografiaDislexia() {
    document.body.classList.toggle("tipografia-dislexia");
    toggleAriaPressed(event.target);
}
function toggleZoom() {
    document.body.classList.toggle("zoom-pagina");
    toggleAriaPressed(event.target);
}
function toggleMagnifier() {
    document.body.classList.toggle("lupa-puntual");
    toggleAriaPressed(event.target);
}
function resaltarFoco() {
    document.body.classList.toggle("resaltar-foco");
    toggleAriaPressed(event.target);
}
function subrayarLinks() {
    document.body.classList.toggle("subrayar-links");
    toggleAriaPressed(event.target);
}
function activarLecturaFacil() {
    document.body.classList.toggle("lectura-facil");
    toggleAriaPressed(event.target);
}
function toggleCursor() {
    document.body.classList.toggle("cursor-grande");
    toggleAriaPressed(event.target);
}
function toggleAnimaciones() {
    document.body.classList.toggle("pausar-animaciones");
    toggleAriaPressed(event.target);
}
function leerPagina() {
    if (!('speechSynthesis' in window)) {
        alert("Lo siento, tu navegador no soporta la lectura en voz alta.");
        return;
    }
    const texto = document.body.innerText;
    const utterance = new SpeechSynthesisUtterance(texto);
    utterance.lang = 'es-ES';
    speechSynthesis.cancel();
    speechSynthesis.speak(utterance);
    toggleAriaPressed(event.target);
}
function detenerLectura() {
    if ('speechSynthesis' in window) {
        speechSynthesis.cancel();
        const btn = event ? event.target : null;
        if (btn) {
            btn.setAttribute("aria-pressed", "false");
        }
    }
}
function toggleAudioDescription() {
    document.body.classList.toggle("descripcion-audio");
    toggleAriaPressed(event.target);
}
function toggleClosedCaptions() {
    document.body.classList.toggle("subtitulos-cerrados");
    toggleAriaPressed(event.target);
}
function toggleLiveCaptions() {
    document.body.classList.toggle("subtitulos-directo");
    toggleAriaPressed(event.target);
}
function descargarTranscripcion() {
    alert("Función de descarga de transcripción no implementada aún.");
}
function toggleAudioControls() {
    document.body.classList.toggle("controles-audio");
    toggleAriaPressed(event.target);
}
function toggleVisualAlerts() {
    document.body.classList.toggle("alertas-visuales");
    toggleAriaPressed(event.target);
}
function toggleAriaPressed(button) {
    if (button.getAttribute("aria-pressed") === "true") {
        button.setAttribute("aria-pressed", "false");
    } else {
        button.setAttribute("aria-pressed", "true");
    }
}
</script>

<!-- Removed duplicate script block to fix redeclaration error -->
</create_file>

<?php
// Recibir la plataforma desde la URL, ej: ?origen=perfeccionamiento
$plataforma = isset($_GET['origen']) ? $_GET['origen'] : 'general';

// Archivo donde se guardarán los conteos
$archivoContador = 'contadores.json';

// Si el archivo no existe, crearlo con un JSON vacío
if (!file_exists($archivoContador)) {
    file_put_contents($archivoContador, json_encode([]));
}

// Leer los datos actuales del archivo JSON
$datosJson = file_get_contents($archivoContador);
$contadores = json_decode($datosJson, true);

// Si la plataforma actual no existe en el JSON, inicializarla en 0
if (!isset($contadores[$plataforma])) {
    $contadores[$plataforma] = 0;
}

// Lógica para incrementar el contador mediante Fetch/AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'incrementar') {
    $contadores[$plataforma]++;
    
    // Guardar el nuevo valor en el archivo JSON
    file_put_contents($archivoContador, json_encode($contadores, JSON_PRETTY_PRINT));
    
    // Devolver el nuevo valor al frontend
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'nuevoValor' => $contadores[$plataforma]]);
    exit; // Terminar la ejecución aquí si es una petición POST
}

// Valor actual para mostrar al cargar la página
$vistasActuales = $contadores[$plataforma];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="cenpecar-logo.png">
  <title>Titulo del video</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --primary: #f0f4f8;
      --secondary: #e2e8f0;
      --accent: #00732C;
      --accent-hover: #046530;
      --accent-alt: #C99616;
      --text-primary: #1a202c;
      --text-secondary: #4a5568;
      --surface: rgba(255, 255, 255, 0.7);
      --surface-border: rgba(255, 255, 255, 0.5);
      --shadow: rgba(6, 135, 64, 0.08); /* Sombra verde sutil */
    }

    body {
      font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: var(--primary);
      background-attachment: fixed;
      color: var(--text-primary);
      min-height: 100vh;
      line-height: 1.6;
      overflow-x: hidden;
      position: relative;
    }

    /* Elementos decorativos de fondo (Blobs) */
    .bg-blob {
      position: fixed;
      width: 500px;
      height: 500px;
      border-radius: 50%;
      filter: blur(100px);
      z-index: -1;
      opacity: 0.15;
      pointer-events: none;
      animation: pulse 10s ease-in-out infinite alternate;
      will-change: opacity;
    }

    .blob-1 {
      background: var(--accent);
      top: -100px;
      left: -100px;
    }

    .blob-2 {
      background: var(--accent-alt);
      bottom: -100px;
      right: -100px;
      animation-delay: -5s;
    }

    /* Animations */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
      0%, 100% { opacity: 0.15; }
      50% { opacity: 0.25; }
    }

    @keyframes countUp {
      0% { transform: scale(1); color: var(--text-primary); }
      50% { transform: scale(1.2); color: var(--accent); }
      100% { transform: scale(1); color: var(--text-primary); }
    }

    @keyframes glow {
      0%, 100% { box-shadow: 0 0 20px rgba(6, 135, 64, 0.3); }
      50% { box-shadow: 0 0 40px rgba(6, 135, 64, 0.5); }
    }

    /* Container */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 24px;
    }

    /* Header Section */
    .header {
      padding: 50px 0 40px; 
      text-align: center;
      position: relative;
      z-index: 10;
      overflow: hidden;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
    }

    .header-logo {
      max-width: 90px; /* Ajustado a 120px */
      width: 100%;     /* Permite que se achique en móviles */
      height: auto;    /* Mantiene la proporción */
      object-fit: contain;
      margin-bottom: 20px;
      opacity: 0;
      animation: fadeIn 1s ease-out 0s forwards;
    }

    .header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 50%;
      transform: translateX(-50%);
      width: min(600px, 100vw);
      height: 600px;
      background: radial-gradient(circle, rgb(0 251 96 / 17%) 0%, transparent 70%);
      pointer-events: none;
    }

    .hero-title {
      font-size: clamp(2.5rem, 8vw, 4.5rem);
      font-weight: 800;
      letter-spacing: -0.04em;  
      color: var(--accent); /* Nombra principal Verde */
      opacity: 0;
      animation: fadeIn 1s ease-out 0.2s forwards;
      text-shadow: 0 4px 12px rgba(6, 135, 64, 0.15);
      will-change: transform, opacity;
    }

    .hero-subtitle {
      font-size: clamp(1.1rem, 3vw, 1.4rem);
      color: var(--accent-alt); /* Subtítulo Amarillo */
      max-width: 600px;
      margin: 0 auto;
      opacity: 0;
      animation: fadeIn 1s ease-out 0.5s forwards;
      font-weight: 600;
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }

    /* Video Section */
    .video-section {
      padding: 80px 0 60px; /* Aumentado top a 80px */
      opacity: 0;
      animation: slideUp 1s cubic-bezier(0.16, 1, 0.3, 1) 0.8s forwards;
      will-change: transform, opacity;
    }

    .video-wrapper {
      position: relative;
      max-width: 900px;
      margin: 0 auto;
      border-radius: 24px;
      overflow: hidden;
      background: rgba(255, 255, 255, 0.4);
      backdrop-filter: blur(25px) saturate(180%);
      -webkit-backdrop-filter: blur(25px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.6);
      box-shadow: 0 25px 50px -12px var(--shadow), 0 0 0 1px rgba(255,255,255,1) inset;
      transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease;
      padding: 12px; /* Marco estilo TV moderna */
    }

    .video-wrapper:hover {
      transform: translateY(-6px) scale(1.01);
      box-shadow: 0 30px 50px -12px rgba(6, 135, 64, 0.15), 0 0 0 1px rgba(255,255,255,0.9) inset;
    }

    .video-wrapper:focus-within {
      animation: glow 3s ease-in-out infinite alternate;
    }

    .video-player {
      width: 100%;
      height: auto;
      display: block;
      aspect-ratio: 16 / 9;
      background: #222;
      border-radius: 16px; /* Borde interno del video */
    }

    /* Info Panel */
    .info-section {
      padding: 40px 0 100px;
      opacity: 0;
      animation: slideUp 1s cubic-bezier(0.16, 1, 0.3, 1) 1.1s forwards;
      will-change: transform, opacity;
    }

    .info-panel {
      max-width: 420px;
      margin: 0 auto;
      background: rgba(255, 255, 255, 0.4);
      backdrop-filter: blur(30px) saturate(200%);
      -webkit-backdrop-filter: blur(30px) saturate(200%);
      border-radius: 24px;
      padding: 36px 40px;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.7);
      box-shadow: 0 20px 40px -10px var(--shadow), 0 0 0 1px rgba(255,255,255,1) inset;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .info-panel:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 45px -5px rgba(171, 153, 25, 0.15), 0 0 0 1px rgba(255,255,255,0.8) inset;
    }

    .info-label {
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.15em;
      color: var(--accent-alt); /* Amarillo */
      margin-bottom: 12px;
      font-weight: 800;
    }

    .counter-icon {
      width: 36px;
      height: 36px;
      fill: var(--accent); /* Verde */
      filter: drop-shadow(0 4px 6px rgba(6, 135, 64, 0.2));
    }

    .counter-number {
      font-size: 3.5rem;
      font-weight: 800;
      color: var(--accent); /* Verde */
      font-variant-numeric: tabular-nums;
      transition: transform 0.3s ease, color 0.3s ease;
      letter-spacing: -0.02em;
    }

    .counter-number.animating {
      animation: countUp 0.5s ease-out;
    }

    /* Footer */
    .footer {
      padding: 40px 0;
      text-align: center;
      border-top: 1px solid rgba(255,255,255,0.3);
      opacity: 0;
      animation: fadeIn 1s ease-out 1.4s forwards;
      background: linear-gradient(0deg, var(--surface) 0%, transparent 100%);
    }

    .footer p {
      color: var(--text-secondary);
      font-size: 0.9rem;
      font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .bg-blob { display: none; }
      .container { padding: 0 20px; width: 100%; box-sizing: border-box; }
      .header { padding: 40px 0 30px; }
      .header-logo { max-width: 80px; }
      .hero-title { font-size: 2rem; margin-bottom: 12px; }
      .hero-subtitle { font-size: 0.85rem; padding: 0 10px; }
      .video-section { padding: 40px 0 20px; } /* Más separación del header en móviles */
      .video-wrapper { 
        border-radius: 20px; 
        margin: 0 auto; 
        padding: 8px;
        width: 100%;
        max-width: 100%;
      }
      .info-section { padding: 20px 0 60px; }
      .info-panel { 
        border-radius: 20px; 
        padding: 30px 20px; 
        width: 100%; 
        max-width: 350px; /* Evita que crezca demasiado pero se ajusta */
      }
      .counter-number { font-size: 2.5rem; }
    }

    @media (max-width: 480px) {
      .container { padding: 0 16px; }
      .header { padding: 30px 0 20px; }
      .header-logo { max-width: 65px; }
      .hero-title { font-size: 1.7rem; }
      .video-wrapper { border-radius: 16px; padding: 6px; }
      .info-panel { padding: 25px 15px; }
      .counter-number { font-size: 2rem; }
      .counter-icon { width: 30px; height: 30px; }
    }
  </style>
</head>
<body>
  <!-- Fondos Vibrantes (Glass Blobs) -->
  <div class="bg-blob blob-1"></div>
  <div class="bg-blob blob-2"></div>

  <!-- Header Section -->
  <header class="header">
    <div class="container">
      <img src="cenpecar-logo.png" alt="Cenpecar Logo" class="header-logo">
      <h1 class="hero-title">Criterios ESG</h1>
      <p class="hero-subtitle">Ambientales, Sociales y de Gobernanza</p>
    </div>
  </header>

  <!-- Video Section -->
  <section class="video-section">
    <div class="container">
      <div class="video-wrapper">
        <video 
          class="video-player" 
          id="mainVideo"
          controls
          poster="portada.png"
        >
          <source src="https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4" type="video/mp4">
          Su navegador no soporta la etiqueta de vídeo.
        </video>
      </div>
    </div>
  </section>

  <!-- Info Panel Section -->
  <section class="info-section">
    <div class="container">
      <div class="info-panel">
        <p class="info-label">Vistas globales</p>
        <div class="view-counter">
          <svg class="counter-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
          </svg>
          <span class="counter-number" id="viewCounter"><?php echo $vistasActuales; ?></span>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <p>&copy; 2026 Cenpecar. Todos los derechos reservados.</p>
    </div>
  </footer>

  <script>
    (function() {
      // DOM Elements
      const video = document.getElementById('mainVideo');
      const counterElement = document.getElementById('viewCounter');
      
      // Obtener la plataforma actual de PHP
      const plataforma = "<?php echo htmlspecialchars($plataforma); ?>";
      
      // Función para actualizar y animar el contador en pantalla
      function updateCounterDisplay(count) {
        counterElement.textContent = count.toLocaleString();
        
        // Trigger animation
        counterElement.classList.remove('animating');
        void counterElement.offsetWidth; // Force reflow
        counterElement.classList.add('animating');
      }
      
      // Función para incrementar en el servidor (via fetch)
      async function incrementarServidor() {
        try {
            // Hacemos una petición POST al mismo archivo PHP actual
            const response = await fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'action': 'incrementar'
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                updateCounterDisplay(data.nuevoValor);
            }
        } catch (error) {
            console.error('Error al actualizar contador: ', error);
        }
      }

      // Track if we've already counted this play session
      let hasCountedThisSession = false;
      
      // Detectar cuando el video se reproduce
      video.addEventListener('play', function() {
        if (!hasCountedThisSession) {
          // Ya no guardamos en LocalStorage. Llamamos a PHP
          incrementarServidor();
          hasCountedThisSession = true;
        }
      });
      
      // Reiniciar seguridad cuando termina
      video.addEventListener('ended', function() {
        hasCountedThisSession = false;
      });
      
      // Reiniciar si se retrocede al inicio
      video.addEventListener('seeked', function() {
        if (video.currentTime === 0) {
          hasCountedThisSession = false;
        }
      });
    })();
  </script>
</body>
</html>

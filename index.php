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
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --primary: #f8fafc;
      --secondary: #e2e8f0;
      --accent: #068740;
      --accent-hover: #046530;
      --text-primary: #068740;
      --text-secondary: #AB9919;
      --surface: #ffffff;
      --border: #cbd5e1;
      --shadow: rgba(0, 0, 0, 0.1);
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
      background: var(--primary);
      color: var(--text-primary);
      min-height: 100vh;
      line-height: 1.6;
    }

    /* Animations */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
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
      padding: 50px 0 30px; /* Reducido un poco para dar espacio al logo */
      text-align: center;
      background: linear-gradient(180deg, var(--secondary) 0%, var(--primary) 100%);
      position: relative;
      overflow: hidden;
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
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgb(0 251 96 / 17%) 0%, transparent 70%);
      pointer-events: none;
    }

    .hero-title {
      font-size: clamp(2.5rem, 8vw, 4.5rem);
      font-weight: 800;
      letter-spacing: -0.02em;
      
      opacity: 0;
      animation: fadeIn 1s ease-out 0.2s forwards;
      background: linear-gradient(135deg, var(--text-primary) 0%, var(--text-secondary) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero-subtitle {
      font-size: clamp(1.1rem, 3vw, 1.5rem);
      color: var(--text-secondary);
      max-width: 600px;
      margin: 0 auto;
      opacity: 0;
      animation: fadeIn 1s ease-out 0.5s forwards;
      font-weight: 400;
      text-transform: capitalize;
    }

    /* Video Section */
    .video-section {
      padding: 60px 0;
      opacity: 0;
      animation: slideUp 1s ease-out 0.8s forwards;
    }

    .video-wrapper {
      position: relative;
      max-width: 900px;
      margin: 0 auto;
      border-radius: 20px;
      overflow: hidden;
      background: var(--surface);
      box-shadow: 0 25px 50px -12px var(--shadow), 0 0 0 1px var(--border);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .video-wrapper:hover {
      transform: translateY(-4px);
      box-shadow: 0 35px 60px -15px var(--shadow), 0 0 0 1px var(--border);
    }

    .video-wrapper:focus-within {
      animation: glow 2s ease-in-out infinite;
    }

    .video-player {
      width: 100%;
      height: auto;
      display: block;
      aspect-ratio: 16 / 9;
      background: var(--secondary);
    }

    /* Info Panel */
    .info-section {
      padding: 40px 0 100px;
      opacity: 0;
      animation: slideUp 1s ease-out 1.1s forwards;
    }

    .info-panel {
      max-width: 400px;
      margin: 0 auto;
      background: var(--surface);
      border-radius: 16px;
      padding: 32px 40px;
      text-align: center;
      box-shadow: 0 10px 40px -10px var(--shadow), 0 0 0 1px var(--border);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .info-panel:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 50px -10px var(--shadow), 0 0 0 1px var(--accent);
    }

    .info-label {
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      color: var(--text-secondary);
      margin-bottom: 12px;
      font-weight: 600;
    }

    .view-counter {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
    }

    .counter-icon {
      width: 32px;
      height: 32px;
      fill: var(--accent);
    }

    .counter-number {
      font-size: 3rem;
      font-weight: 700;
      font-variant-numeric: tabular-nums;
      transition: transform 0.3s ease, color 0.3s ease;
    }

    .counter-number.animating {
      animation: countUp 0.5s ease-out;
    }

    /* Footer */
    .footer {
      padding: 40px 0;
      text-align: center;
      border-top: 1px solid var(--border);
      opacity: 0;
      animation: fadeIn 1s ease-out 1.4s forwards;
    }

    .footer p {
      color: var(--text-secondary);
      font-size: 0.875rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .header { padding: 80px 0 60px; }
      .header-logo { max-width: 70px; }
      .video-section { padding: 40px 0; }
      .video-wrapper { border-radius: 16px; margin: 0 16px; }
      .info-panel { margin: 0 16px; padding: 24px 32px; }
      .counter-number { font-size: 2.5rem; }
      .info-section { padding: 30px 0 80px; }
    }

    @media (max-width: 480px) {
      .header { padding: 60px 0 40px; }
      .header-logo { max-width: 60px; }
      .hero-subtitle { padding: 0 16px; }
      .video-wrapper { border-radius: 12px; }
      .info-panel { border-radius: 12px; padding: 20px 24px; }
      .counter-number { font-size: 2rem; }
      .counter-icon { width: 24px; height: 24px; }
    }
  </style>
</head>
<body>
  <!-- Header Section -->
  <header class="header">
    <div class="container">
      <img src="cenpecar-logo.png" alt="Cenpecar Logo" class="header-logo">
      <h1 class="hero-title">Criterios ESG</h1>
      <p class="hero-subtitle">Posible subtitulo del video</p>
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

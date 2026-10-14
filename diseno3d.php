<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$inventario = getInventario();
?>

<!-- Contenedor Principal del Estudio 3D -->
<main class="flex-1 max-w-7xl w-full mx-auto px-3 sm:px-6 lg:px-8 py-5 space-y-5">

  <!-- Encabezado del Módulo Core 3D -->
  <div class="bg-gradient-to-r from-[#103E4A] via-[#0D4F60] to-[#103E4A] border border-[#0D4F60] p-4 sm:p-6 rounded-2xl shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#09252D]/80 border border-[#ED991F]/30 text-[#ED991F] text-xs font-semibold mb-2">
        <i data-lucide="box" class="w-3.5 h-3.5 text-[#ED991F]"></i>
        Core Domain: Diseño Paramétrico & Modelado 3D (WebGL / Three.js)
      </div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-[#FFFFFF] tracking-tight font-display">
        Estudio 3D & Configurador de Muebles a Medida
      </h1>
      <p class="text-[#F0EEE8]/80 text-xs sm:text-sm mt-1 max-w-2xl">
        Modela muebles paramétricos en tiempo real, simula texturas de maderas, explora el ensamble despiezado y obtén volumetría y cotización instantánea.
      </p>
    </div>

    <!-- Controles Rápidos de Vista 3D -->
    <div class="flex flex-wrap items-center gap-2">
      <button onclick="resetearCamara3D()" class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-[#09252D] hover:bg-[#0D4F60] text-[#F7F5EF] border border-[#0D4F60] text-xs font-semibold transition-all">
        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-[#ED991F]"></i>
        <span>Restablecer Cámara</span>
      </button>
      <button onclick="tomarCaptura3D()" class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-[#09252D] hover:bg-[#0D4F60] text-[#F7F5EF] border border-[#0D4F60] text-xs font-semibold transition-all">
        <i data-lucide="camera" class="w-3.5 h-3.5 text-[#ED991F]"></i>
        <span>Captura 3D</span>
      </button>
      <button onclick="abrirModalCarpinterIA()" class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-[#0D4F60] hover:bg-[#1A677B] text-[#ED991F] border border-[#ED991F]/40 text-xs font-bold transition-all">
        <i data-lucide="sparkles" class="w-3.5 h-3.5 animate-pulse"></i>
        <span>Asesoría CarpinterIA</span>
      </button>
    </div>
  </div>

  <!-- Workspace 3D: Grid de Visor y Panel Paramétrico -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
    
    <!-- 1. Canvas 3D Interactivo (8 Columnas en desktop) -->
    <div class="lg:col-span-8 bg-[#103E4A]/90 border border-[#0D4F60] rounded-2xl overflow-hidden shadow-2xl flex flex-col relative">
      
      <!-- Barra Superior de Herramientas del Visor -->
      <div class="p-3 bg-[#09252D]/80 border-b border-[#0D4F60] flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2">
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#0D4F60] text-[#ED991F] font-bold font-mono">
            <span class="w-2 h-2 rounded-full bg-[#ED991F] animate-ping"></span>
            WebGL Activo
          </span>
          <span id="labelCotasActuales" class="text-[#F0EEE8]/80 font-mono hidden sm:inline">
            Alto: 210cm × Ancho: 180cm × Fondo: 60cm
          </span>
        </div>

        <div class="flex items-center gap-2">
          <!-- Toggle Modo Estructura / Alambre -->
          <button id="btnAlambre" onclick="toggleModoAlambre()" class="px-2.5 py-1 rounded-lg bg-[#09252D] hover:bg-[#0D4F60] text-[#F0EEE8] border border-[#0D4F60] text-[11px] font-semibold transition-colors flex items-center gap-1">
            <i data-lucide="grid" class="w-3.5 h-3.5 text-[#ED991F]"></i>
            <span id="textAlambre">Ver Estructura</span>
          </button>
          <!-- Botón de Rotación Automática -->
          <button id="btnAutoRotar" onclick="toggleAutoRotacion()" class="px-2.5 py-1 rounded-lg bg-[#09252D] hover:bg-[#0D4F60] text-[#F0EEE8] border border-[#0D4F60] text-[11px] font-semibold transition-colors flex items-center gap-1">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-[#ED991F]"></i>
            <span id="textRotar">Giro Auto</span>
          </button>
        </div>
      </div>

      <!-- Contenedor del Viewport Three.js -->
      <div id="contenedorCanvas3D" class="w-full h-[460px] sm:h-[520px] bg-[#071d24] relative cursor-grab active:cursor-grabbing">
        <!-- Indicador de carga -->
        <div id="loading3D" class="absolute inset-0 flex flex-col items-center justify-center bg-[#09252D] z-10 space-y-3">
          <div class="w-10 h-10 border-4 border-[#ED991F] border-t-transparent rounded-full animate-spin"></div>
          <p class="text-xs text-[#F7F5EF] font-semibold">Inicializando Motor 3D Three.js...</p>
        </div>

        <!-- Coto / Overlay Flotante de Controles Táctiles / Mouse -->
        <div class="absolute bottom-3 left-3 bg-[#09252D]/85 backdrop-blur-md border border-[#0D4F60] px-3 py-1.5 rounded-xl text-[11px] text-[#F0EEE8]/80 pointer-events-none flex items-center gap-2">
          <i data-lucide="mouse-pointer" class="w-3.5 h-3.5 text-[#ED991F]"></i>
          <span>Clic + Arrastrar: Rotar | Scroll: Zoom | Clic Der: Desplazar</span>
        </div>
      </div>

      <!-- Barra Inferior: Animación de Puertas y Vista Explotada -->
      <div class="p-3.5 bg-[#09252D]/90 border-t border-[#0D4F60] grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
        <div>
          <div class="flex items-center justify-between mb-1.5 font-semibold text-[#F7F5EF]">
            <span class="flex items-center gap-1.5">
              <i data-lucide="maximize-2" class="w-3.5 h-3.5 text-[#ED991F]"></i>
              Vista Explotada (Despiece en Banco)
            </span>
            <span id="valExplotado" class="text-[#ED991F] font-mono font-bold">0%</span>
          </div>
          <input type="range" id="sliderExplotado" min="0" max="100" value="0" step="1" oninput="actualizarVistaExplotada(this.value)" class="w-full accent-[#ED991F] cursor-pointer">
        </div>

        <div>
          <div class="flex items-center justify-between mb-1.5 font-semibold text-[#F7F5EF]">
            <span class="flex items-center gap-1.5">
              <i data-lucide="door-open" class="w-3.5 h-3.5 text-[#ED991F]"></i>
              Apertura de Puertas / Gavetas
            </span>
            <span id="valApertura" class="text-[#ED991F] font-mono font-bold">Cerrado (0°)</span>
          </div>
          <input type="range" id="sliderApertura" min="0" max="100" value="0" step="1" oninput="actualizarAperturaPuertas(this.value)" class="w-full accent-[#ED991F] cursor-pointer">
        </div>
      </div>

    </div>

    <!-- 2. Panel Paramétrico de Configuración (4 Columnas en desktop) -->
    <div class="lg:col-span-4 space-y-4">

      <!-- Caja de Parámetros Geométricos (Fondo Blanco y Alto Contraste) -->
      <div class="bento-card p-4 sm:p-5 shadow-xl space-y-4 bg-white text-[#103E4A]">
        
        <div class="flex items-center justify-between border-b border-[#0D4F60]/15 pb-2.5">
          <h2 class="text-sm font-bold text-[#103E4A] font-display flex items-center gap-2">
            <i data-lucide="sliders" class="w-4 h-4 text-[#ED991F]"></i>
            Especificaciones Paramétricas
          </h2>
          <span class="text-[10px] font-bold text-[#E58D17] uppercase tracking-wider bg-[#ED991F]/15 px-2 py-0.5 rounded border border-[#ED991F]/30">
            En Vivo
          </span>
        </div>

        <!-- Tipo de Mueble -->
        <div>
          <label class="block text-xs font-bold text-[#103E4A] mb-1">Tipo de Mueble</label>
          <select id="tipoMuebleSelect" onchange="cambiarTipoMueble(this.value)" class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] font-semibold focus:outline-none focus:border-[#ED991F]">
            <option value="closet" selected>Clóset Vestidor / Módulo Ropero</option>
            <option value="cocina">Módulo Bajo de Cocina con Cajones</option>
            <option value="mesa">Mesa de Comedor Maciza (Patas en X)</option>
            <option value="tv">Centro de TV con Panel Flotante</option>
          </select>
        </div>

        <!-- Material y Textura de Acabado -->
        <div>
          <label class="block text-xs font-bold text-[#103E4A] mb-1">Madera / Acabado Superficial</label>
          <select id="materialSelect" onchange="cambiarTexturaMaterial(this.value)" class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] font-semibold focus:outline-none focus:border-[#ED991F]">
            <option value="roble_sinatra" selected>Melamina Pelíkano Roble Sinatra 18mm</option>
            <option value="blanco_mate">Melamina Blanco Mate 18mm</option>
            <option value="grafito">Melamina Gris Grafito Texturizado 18mm</option>
            <option value="roble_macizo">Madera Maciza Roble Americano 2"</option>
            <option value="cedro_macizo">Madera Maciza Cedro Caquetá 3"</option>
            <option value="teca_cultivo">Madera Maciza Teca Natural</option>
          </select>
        </div>

        <!-- Dimensiones Paramétricas (Sliders + Inputs Numéricos) -->
        <div class="space-y-3 pt-1">
          <!-- Alto -->
          <div>
            <div class="flex justify-between text-xs text-[#103E4A] font-bold mb-1">
              <span>Alto Total</span>
              <span id="lblAlto" class="text-[#E58D17] font-mono font-extrabold">210 cm</span>
            </div>
            <input type="range" id="inputAlto" min="70" max="260" value="210" step="5" oninput="actualizarDimensiones()" class="w-full accent-[#ED991F] cursor-pointer">
          </div>

          <!-- Ancho -->
          <div>
            <div class="flex justify-between text-xs text-[#103E4A] font-bold mb-1">
              <span>Ancho Frontal</span>
              <span id="lblAncho" class="text-[#E58D17] font-mono font-extrabold">180 cm</span>
            </div>
            <input type="range" id="inputAncho" min="60" max="300" value="180" step="5" oninput="actualizarDimensiones()" class="w-full accent-[#ED991F] cursor-pointer">
          </div>

          <!-- Fondo -->
          <div>
            <div class="flex justify-between text-xs text-[#103E4A] font-bold mb-1">
              <span>Profundidad (Fondo)</span>
              <span id="lblFondo" class="text-[#E58D17] font-mono font-extrabold">60 cm</span>
            </div>
            <input type="range" id="inputFondo" min="35" max="110" value="60" step="5" oninput="actualizarDimensiones()" class="w-full accent-[#ED991F] cursor-pointer">
          </div>
        </div>

        <!-- Elementos Interiores (Repisas y Cajones) -->
        <div class="grid grid-cols-2 gap-3 pt-1 border-t border-[#0D4F60]/15">
          <div>
            <label class="block text-[11px] font-bold text-[#103E4A] mb-1">Repisas</label>
            <select id="selectRepisas" onchange="actualizarEstructuraInterna()" class="w-full px-2.5 py-1.5 bg-[#F8FAFC] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] font-semibold">
              <option value="1">1 Repisa</option>
              <option value="2">2 Repisas</option>
              <option value="3" selected>3 Repisas</option>
              <option value="4">4 Repisas</option>
              <option value="5">5 Repisas</option>
            </select>
          </div>
          <div>
            <label class="block text-[11px] font-bold text-[#103E4A] mb-1">Cajones</label>
            <select id="selectCajones" onchange="actualizarEstructuraInterna()" class="w-full px-2.5 py-1.5 bg-[#F8FAFC] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] font-semibold">
              <option value="0">Sin cajones</option>
              <option value="2" selected>2 Cajones</option>
              <option value="3">3 Cajones</option>
              <option value="4">4 Cajones</option>
            </select>
          </div>
        </div>

      </div>

      <!-- 3. Motor de Cotización & Volumetría en Tiempo Real (Core Domain 2) -->
      <div class="bento-card p-4 sm:p-5 shadow-xl space-y-3 bg-white text-[#103E4A]">
        <div class="flex items-center justify-between border-b border-[#0D4F60]/15 pb-2">
          <span class="text-xs font-bold uppercase text-[#103E4A] tracking-wider">Cálculo de Volumetría & Costos</span>
          <i data-lucide="calculator" class="w-4 h-4 text-[#ED991F]"></i>
        </div>

        <div class="grid grid-cols-2 gap-2 text-[11px]">
          <div class="bg-[#F8FAFC] p-2.5 rounded-xl border border-[#0D4F60]/15">
            <span class="text-[#0D4F60]/70 block font-medium">Área Tableros</span>
            <span id="metricArea" class="font-extrabold text-[#103E4A] font-mono text-xs">8.64 m²</span>
          </div>
          <div class="bg-[#F8FAFC] p-2.5 rounded-xl border border-[#0D4F60]/15">
            <span class="text-[#0D4F60]/70 block font-medium">Planchas Estimadas</span>
            <span id="metricPlanchas" class="font-extrabold text-[#E58D17] font-mono text-xs">2.2 planchas</span>
          </div>
          <div class="bg-[#F8FAFC] p-2.5 rounded-xl border border-[#0D4F60]/15">
            <span class="text-[#0D4F60]/70 block font-medium">Tapacanto PVC 2mm</span>
            <span id="metricCanto" class="font-extrabold text-[#103E4A] font-mono text-xs">28.5 m</span>
          </div>
          <div class="bg-[#F8FAFC] p-2.5 rounded-xl border border-[#0D4F60]/15">
            <span class="text-[#0D4F60]/70 block font-medium">Herrajes Clave</span>
            <span id="metricHerrajes" class="font-extrabold text-[#103E4A] font-mono text-xs">6 bis. + 2 corr.</span>
          </div>
        </div>

        <!-- Precio Total Cotizado -->
        <div class="pt-2 border-t border-[#0D4F60]/15 flex items-center justify-between">
          <div>
            <span class="text-[10px] uppercase font-bold text-[#0D4F60]/70 block">Precio Venta Taller</span>
            <span id="lblPrecioTotal" class="text-xl sm:text-2xl font-black text-[#E58D17] font-mono">$ 3.850.000</span>
          </div>
          <span class="text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-300 px-2 py-0.5 rounded font-bold">
            Margen 45%
          </span>
        </div>

        <!-- Botón de Transferencia a Pedidos en Taller -->
        <form action="api/guardar_pedido_3d.php" method="POST" id="formGuardar3D">
          <input type="hidden" name="proyecto" id="formProyecto" value="Clóset Vestidor Paramétrico 3D">
          <input type="hidden" name="categoria" id="formCategoria" value="Closet">
          <input type="hidden" name="madera" id="formMadera" value="Melamina Roble Sinatra 18mm">
          <input type="hidden" name="alto" id="formAlto" value="210">
          <input type="hidden" name="ancho" id="formAncho" value="180">
          <input type="hidden" name="fondo" id="formFondo" value="60">
          <input type="hidden" name="precio" id="formPrecio" value="3850000">

          <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-[#ED991F] to-[#E58D17] hover:from-[#F3B14F] hover:to-[#ED991F] text-[#103E4A] font-extrabold text-xs flex items-center justify-center gap-2 shadow-lg shadow-orange-950/40 transition-all cursor-pointer active:scale-95">
            <i data-lucide="plus-circle" class="w-4 h-4 text-[#103E4A]"></i>
            <span>Transferir a Pedido en Taller</span>
          </button>
        </form>

      </div>

    </div>

  </div>

</main>

<!-- Bibliotecas Three.js y OrbitControls vía CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

<script>
  // ========================================================
  // MOTOR 3D THREE.JS PARAMÉTRICO - CARPINTERSOFT
  // ========================================================

  let escena, camara, renderizador, controles;
  let grupoMueble;
  let piezasMueble = [];
  let partesMoviles = []; // Puertas y gavetas para animar apertura
  let autoRotar = false;
  let modoAlambre = false;

  // Estado Paramétrico Actual
  const modeloActual = {
    tipo: 'closet',
    material: 'roble_sinatra',
    alto: 210, // cm
    ancho: 180, // cm
    fondo: 60, // cm
    repisas: 3,
    cajones: 2,
    espesorTablero: 1.8, // 18mm estándar
    apertura: 0, // 0 a 1
    explotado: 0 // 0 a 1
  };

  // Texturas Procedurales en Canvas para renderizado 100% fiable sin CORS
  function crearTexturaMaderaProcedural(tipo) {
    const canvas = document.createElement('canvas');
    canvas.width = 512;
    canvas.height = 512;
    const ctx = canvas.getContext('2d');

    let baseColor = '#b5793e'; // Roble cálido
    let lineaColor = '#804e21';

    if (tipo === 'blanco_mate') {
      baseColor = '#f5f5f7';
      lineaColor = '#e1e1e6';
    } else if (tipo === 'grafito') {
      baseColor = '#2b2f33';
      lineaColor = '#1e2124';
    } else if (tipo === 'cedro_macizo') {
      baseColor = '#9e472a'; // Rojizo caoba
      lineaColor = '#6d2b17';
    } else if (tipo === 'teca_cultivo') {
      baseColor = '#a87537';
      lineaColor = '#704618';
    }

    ctx.fillStyle = baseColor;
    ctx.fillRect(0, 0, 512, 512);

    // Dibujar vetas de madera
    ctx.fillStyle = lineaColor;
    for (let i = 0; i < 50; i++) {
      ctx.globalAlpha = 0.08 + Math.random() * 0.12;
      const y = Math.random() * 512;
      const h = 2 + Math.random() * 8;
      ctx.fillRect(0, y, 512, h);
    }
    ctx.globalAlpha = 1.0;

    const texture = new THREE.CanvasTexture(canvas);
    texture.wrapS = THREE.RepeatWrapping;
    texture.wrapT = THREE.RepeatWrapping;
    texture.repeat.set(2, 2);
    return texture;
  }

  // Inicializar Escena Three.js
  function init3D() {
    const contenedor = document.getElementById('contenedorCanvas3D');
    const width = contenedor.clientWidth;
    const height = contenedor.clientHeight;

    // Escena
    escena = new THREE.Scene();
    escena.background = new THREE.Color(0x071d24);

    // Niebla sutil
    escena.fog = new THREE.FogExp2(0x071d24, 0.002);

    // Cámara
    camara = new THREE.PerspectiveCamera(45, width / height, 1, 3000);
    camara.position.set(220, 190, 320);

    // Renderizador WebGL
    renderizador = new THREE.WebGLRenderer({ antialias: true, preserveDrawingBuffer: true });
    renderizador.setSize(width, height);
    renderizador.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderizador.shadowMap.enabled = true;
    renderizador.shadowMap.type = THREE.PCFSoftShadowMap;
    contenedor.appendChild(renderizador.domElement);

    // Controles de Órbita
    controles = new THREE.OrbitControls(camara, renderizador.domElement);
    controles.enableDamping = true;
    controles.dampingFactor = 0.05;
    controles.maxPolarAngle = Math.PI / 2 + 0.05; // No atravesar el suelo
    controles.minDistance = 80;
    controles.maxDistance = 800;
    controles.target.set(0, 80, 0);

    // Iluminación Profesional de Estudio
    const luzAmbiente = new THREE.AmbientLight(0xffffff, 0.65);
    escena.add(luzAmbiente);

    const luzDireccional = new THREE.DirectionalLight(0xfff5e6, 0.85);
    luzDireccional.position.set(200, 350, 200);
    luzDireccional.castShadow = true;
    luzDireccional.shadow.mapSize.width = 2048;
    luzDireccional.shadow.mapSize.height = 2048;
    escena.add(luzDireccional);

    const luzRelleno = new THREE.DirectionalLight(0x88bbcc, 0.35);
    luzRelleno.position.set(-200, 150, -150);
    escena.add(luzRelleno);

    // Suelo con Grid de Taller
    const gridHelper = new THREE.GridHelper(600, 30, 0x0D4F60, 0x103E4A);
    gridHelper.position.y = 0;
    escena.add(gridHelper);

    const planoSueloGeo = new THREE.PlaneGeometry(600, 600);
    const planoSueloMat = new THREE.ShadowMaterial({ opacity: 0.3 });
    const planoSuelo = new THREE.Mesh(planoSueloGeo, planoSueloMat);
    planoSuelo.rotation.x = -Math.PI / 2;
    planoSuelo.receiveShadow = true;
    escena.add(planoSuelo);

    // Grupo donde se construyen los muebles
    grupoMueble = new THREE.Group();
    escena.add(grupoMueble);

    // Ocultar Loader
    document.getElementById('loading3D').style.display = 'none';

    // Construir Mueble Inicial
    construirMueble3D();

    // Redimensionado de ventana
    window.addEventListener('resize', onWindowResize);

    // Ciclo de Renderizado
    animar();
  }

  // Redimensionamiento Responsivo
  function onWindowResize() {
    const contenedor = document.getElementById('contenedorCanvas3D');
    if (!contenedor || !camara || !renderizador) return;
    const width = contenedor.clientWidth;
    const height = contenedor.clientHeight;
    camara.aspect = width / height;
    camara.updateProjectionMatrix();
    renderizador.setSize(width, height);
  }

  // Material de la madera
  function obtenerMaterialActual() {
    const texture = crearTexturaMaderaProcedural(modeloActual.material);
    return new THREE.MeshStandardMaterial({
      map: texture,
      roughness: 0.55,
      metalness: 0.08,
      wireframe: modoAlambre
    });
  }

  // Material metálico para herrajes
  function obtenerMaterialHerraje() {
    return new THREE.MeshStandardMaterial({
      color: 0x1a1a1a,
      roughness: 0.3,
      metalness: 0.9,
      wireframe: modoAlambre
    });
  }

  // Función constructora Builder: Ensambla el mueble pieza por pieza
  function construirMueble3D() {
    // Limpiar mueble anterior
    while (grupoMueble.children.length > 0) {
      const obj = grupoMueble.children[0];
      grupoMueble.remove(obj);
      if (obj.geometry) obj.geometry.dispose();
    }
    piezasMueble = [];
    partesMoviles = [];

    const matMadera = obtenerMaterialActual();
    const matHerraje = obtenerMaterialHerraje();

    const H = modeloActual.alto;
    const W = modeloActual.ancho;
    const D = modeloActual.fondo;
    const T = modeloActual.espesorTablero;

    if (modeloActual.tipo === 'closet' || modeloActual.tipo === 'cocina') {
      // 1. Laterales (Izquierdo y Derecho)
      crearPieza(T, H, D, -W / 2 + T / 2, H / 2, 0, matMadera, { x: -1, y: 0, z: 0 }, 'Lateral Izquierdo');
      crearPieza(T, H, D, W / 2 - T / 2, H / 2, 0, matMadera, { x: 1, y: 0, z: 0 }, 'Lateral Derecho');

      // 2. Techo y Base inferior
      crearPieza(W - 2 * T, T, D, 0, H - T / 2, 0, matMadera, { x: 0, y: 1, z: 0 }, 'Techo Superior');
      crearPieza(W - 2 * T, T, D, 0, T / 2, 0, matMadera, { x: 0, y: -1, z: 0 }, 'Base Inferior');

      // 3. Fondo Trasero (MDF 3mm)
      const matFondo = matMadera.clone();
      matFondo.color.setHex(0xd0d0d5);
      crearPieza(W - T, H - T, 0.4, 0, H / 2, -D / 2 + 0.2, matFondo, { x: 0, y: 0, z: -1 }, 'Fondo Trasero');

      // 4. Repisas Intermedias
      const numRepisas = parseInt(modeloActual.repisas);
      const espaciadoY = (H - 2 * T) / (numRepisas + 1);
      for (let i = 1; i <= numRepisas; i++) {
        const posY = T + i * espaciadoY;
        crearPieza(W - 2 * T, T, D - 2, 0, posY, 0, matMadera, { x: 0, y: 0.3 * (i - 2), z: 0 }, `Repisa #${i}`);
      }

      // 5. Puertas Batientes Animables
      const anchoPuerta = (W - 1) / 2;
      const altoPuerta = H - 2;

      // Puerta Izquierda
      const grupoPuertaIzq = new THREE.Group();
      grupoPuertaIzq.position.set(-W / 2 + 0.5, 1, D / 2);
      
      const geoPuertaIzq = new THREE.BoxGeometry(anchoPuerta, altoPuerta, T);
      geoPuertaIzq.translate(anchoPuerta / 2, altoPuerta / 2, T / 2);
      const meshPuertaIzq = new THREE.Mesh(geoPuertaIzq, matMadera);
      meshPuertaIzq.castShadow = true;
      meshPuertaIzq.receiveShadow = true;
      grupoPuertaIzq.add(meshPuertaIzq);

      // Tirador Puerta Izq
      const tiradorIzq = new THREE.Mesh(new THREE.CylinderGeometry(0.8, 0.8, 18, 16), matHerraje);
      tiradorIzq.position.set(anchoPuerta - 4, altoPuerta / 2, T + 2);
      grupoPuertaIzq.add(tiradorIzq);

      grupoMueble.add(grupoPuertaIzq);
      partesMoviles.push({ tipo: 'puerta_izq', obj: grupoPuertaIzq, anguloMax: -Math.PI / 2 });

      // Puerta Derecha
      const grupoPuertaDer = new THREE.Group();
      grupoPuertaDer.position.set(W / 2 - 0.5, 1, D / 2);

      const geoPuertaDer = new THREE.BoxGeometry(anchoPuerta, altoPuerta, T);
      geoPuertaDer.translate(-anchoPuerta / 2, altoPuerta / 2, T / 2);
      const meshPuertaDer = new THREE.Mesh(geoPuertaDer, matMadera);
      meshPuertaDer.castShadow = true;
      meshPuertaDer.receiveShadow = true;
      grupoPuertaDer.add(meshPuertaDer);

      // Tirador Puerta Der
      const tiradorDer = new THREE.Mesh(new THREE.CylinderGeometry(0.8, 0.8, 18, 16), matHerraje);
      tiradorDer.position.set(-anchoPuerta + 4, altoPuerta / 2, T + 2);
      grupoPuertaDer.add(tiradorDer);

      grupoMueble.add(grupoPuertaDer);
      partesMoviles.push({ tipo: 'puerta_der', obj: grupoPuertaDer, anguloMax: Math.PI / 2 });

    } else if (modeloActual.tipo === 'mesa') {
      // Mesa de Comedor Maciza
      const grosorSobre = 5; // 5cm (2 pulgadas)
      // Cubierta Superior
      crearPieza(W, grosorSobre, D, 0, H - grosorSobre / 2, 0, matMadera, { x: 0, y: 1, z: 0 }, 'Cubierta Maciza');

      // Patas en X Estructurales
      const matPatas = matMadera.clone();
      const anchoPata = 10;
      const altoPata = H - grosorSobre;

      // Pata Izquierda Delantera/Trasera
      crearPieza(anchoPata, altoPata, anchoPata, -W / 2 + 15, altoPata / 2, -D / 2 + 10, matPatas, { x: -1, y: 0, z: -1 }, 'Pata Izq Trasera');
      crearPieza(anchoPata, altoPata, anchoPata, -W / 2 + 15, altoPata / 2, D / 2 - 10, matPatas, { x: -1, y: 0, z: 1 }, 'Pata Izq Delantera');

      // Pata Derecha Delantera/Trasera
      crearPieza(anchoPata, altoPata, anchoPata, W / 2 - 15, altoPata / 2, -D / 2 + 10, matPatas, { x: 1, y: 0, z: -1 }, 'Pata Der Trasera');
      crearPieza(anchoPata, altoPata, anchoPata, W / 2 - 15, altoPata / 2, D / 2 - 10, matPatas, { x: 1, y: 0, z: 1 }, 'Pata Der Delantera');

      // Travesaño Central de Refuerzo
      crearPieza(W - 40, 6, 8, 0, H - grosorSobre - 4, 0, matPatas, { x: 0, y: -0.5, z: 0 }, 'Travesaño');

    } else if (modeloActual.tipo === 'tv') {
      // Centro de Entretenimiento TV
      // Panel Trasero Alistonado
      crearPieza(W, H, 4, 0, H / 2, -D / 2 + 2, matMadera, { x: 0, y: 0, z: -1 }, 'Panel Alistonado');

      // Mueble Flotante Inferior
      const altoMueble = 40;
      const fondoMueble = D;
      const yMueble = 50;

      crearPieza(W - 20, T, fondoMueble, 0, yMueble + altoMueble, 0, matMadera, { x: 0, y: 1, z: 0 }, 'Tapa Flotante');
      crearPieza(W - 20, T, fondoMueble, 0, yMueble, 0, matMadera, { x: 0, y: -1, z: 0 }, 'Base Flotante');
      crearPieza(T, altoMueble, fondoMueble, -W / 2 + 10, yMueble + altoMueble / 2, 0, matMadera, { x: -1, y: 0, z: 0 }, 'Lateral Izq Flotante');
      crearPieza(T, altoMueble, fondoMueble, W / 2 - 10, yMueble + altoMueble / 2, 0, matMadera, { x: 1, y: 0, z: 0 }, 'Lateral Der Flotante');
    }

    // Centrar objetivo de controles
    controles.target.set(0, H / 2, 0);
  }

  // Función auxiliar para agregar piezas y registrar su vector de explosión
  function crearPieza(ancho, alto, fondo, x, y, z, material, dirExplosion, nombre) {
    const geo = new THREE.BoxGeometry(ancho, alto, fondo);
    const mesh = new THREE.Mesh(geo, material);
    mesh.position.set(x, y, z);
    mesh.castShadow = true;
    mesh.receiveShadow = true;

    grupoMueble.add(mesh);

    piezasMueble.push({
      mesh: mesh,
      posOriginal: new THREE.Vector3(x, y, z),
      dirExplosion: dirExplosion || { x: 0, y: 0, z: 0 },
      nombre: nombre
    });
  }

  // Slider de Vista Explotada
  function actualizarVistaExplotada(valor) {
    const factor = valor / 100;
    document.getElementById('valExplotado').textContent = `${valor}%`;
    const distanciaMax = 45; // cm de separación

    piezasMueble.forEach(p => {
      p.mesh.position.x = p.posOriginal.x + p.dirExplosion.x * distanciaMax * factor;
      p.mesh.position.y = p.posOriginal.y + p.dirExplosion.y * distanciaMax * factor;
      p.mesh.position.z = p.posOriginal.z + p.dirExplosion.z * distanciaMax * factor;
    });
  }

  // Slider de Apertura de Puertas / Gavetas
  function actualizarAperturaPuertas(valor) {
    const factor = valor / 100;
    const grados = Math.round(factor * 90);
    document.getElementById('valApertura').textContent = factor > 0 ? `Abierto (${grados}°)` : 'Cerrado (0°)';

    partesMoviles.forEach(pm => {
      pm.obj.rotation.y = pm.anguloMax * factor;
    });
  }

  // Cambiar dimensiones desde Sliders
  function actualizarDimensiones() {
    modeloActual.alto = parseFloat(document.getElementById('inputAlto').value);
    modeloActual.ancho = parseFloat(document.getElementById('inputAncho').value);
    modeloActual.fondo = parseFloat(document.getElementById('inputFondo').value);

    document.getElementById('lblAlto').textContent = `${modeloActual.alto} cm`;
    document.getElementById('lblAncho').textContent = `${modeloActual.ancho} cm`;
    document.getElementById('lblFondo').textContent = `${modeloActual.fondo} cm`;
    document.getElementById('labelCotasActuales').textContent = `Alto: ${modeloActual.alto}cm × Ancho: ${modeloActual.ancho}cm × Fondo: ${modeloActual.fondo}cm`;

    // Actualizar campos ocultos de formulario
    document.getElementById('formAlto').value = modeloActual.alto;
    document.getElementById('formAncho').value = modeloActual.ancho;
    document.getElementById('formFondo').value = modeloActual.fondo;

    construirMueble3D();
    calcularCostosEnTiempoReal();
  }

  // Cambiar Tipo de Mueble
  function cambiarTipoMueble(tipo) {
    modeloActual.tipo = tipo;
    const selectMat = document.getElementById('materialSelect');
    
    if (tipo === 'mesa') {
      selectMat.value = 'roble_macizo';
      modeloActual.material = 'roble_macizo';
      document.getElementById('inputAlto').value = 76;
      document.getElementById('inputAncho').value = 220;
      document.getElementById('inputFondo').value = 100;
      document.getElementById('formCategoria').value = 'Mesa';
      document.getElementById('formProyecto').value = 'Mesa de Comedor Nórdica Roble Macizo';
    } else if (tipo === 'cocina') {
      selectMat.value = 'roble_sinatra';
      modeloActual.material = 'roble_sinatra';
      document.getElementById('inputAlto').value = 90;
      document.getElementById('inputAncho').value = 160;
      document.getElementById('inputFondo').value = 60;
      document.getElementById('formCategoria').value = 'Cocina';
      document.getElementById('formProyecto').value = 'Módulo Bajo Cocina Integral con Gavetas';
    } else if (tipo === 'tv') {
      selectMat.value = 'grafito';
      modeloActual.material = 'grafito';
      document.getElementById('inputAlto').value = 200;
      document.getElementById('inputAncho').value = 240;
      document.getElementById('inputFondo').value = 40;
      document.getElementById('formCategoria').value = 'Especial';
      document.getElementById('formProyecto').value = 'Centro de TV con Panel Alistonado';
    } else {
      selectMat.value = 'roble_sinatra';
      modeloActual.material = 'roble_sinatra';
      document.getElementById('inputAlto').value = 210;
      document.getElementById('inputAncho').value = 180;
      document.getElementById('inputFondo').value = 60;
      document.getElementById('formCategoria').value = 'Closet';
      document.getElementById('formProyecto').value = 'Clóset Vestidor Paramétrico 3D';
    }

    actualizarDimensiones();
  }

  // Cambiar Textura / Material
  function cambiarTexturaMaterial(mat) {
    modeloActual.material = mat;
    document.getElementById('formMadera').value = document.getElementById('materialSelect').selectedOptions[0].text;
    construirMueble3D();
    calcularCostosEnTiempoReal();
  }

  function actualizarEstructuraInterna() {
    modeloActual.repisas = parseInt(document.getElementById('selectRepisas').value);
    modeloActual.cajones = parseInt(document.getElementById('selectCajones').value);
    construirMueble3D();
    calcularCostosEnTiempoReal();
  }

  // Motor de Precios y Volumetría (Core Domain 2)
  function calcularCostosEnTiempoReal() {
    const H = modeloActual.alto / 100; // m
    const W = modeloActual.ancho / 100; // m
    const D = modeloActual.fondo / 100; // m

    // Estimación de área superficial de tableros
    const areaLaterales = 2 * (H * D);
    const areaTechoBase = 2 * (W * D);
    const areaFondo = H * W;
    const areaRepisas = modeloActual.repisas * (W * D);
    const areaTotalM2 = (areaLaterales + areaTechoBase + areaFondo + areaRepisas).toFixed(2);

    // Planchas de 2.44 x 1.83m = 4.46 m²
    const planchas = (areaTotalM2 / 4.46 * 1.15).toFixed(1); // 15% desperdicio
    const metrosCanto = Math.round((H * 4 + W * 6));

    document.getElementById('metricArea').textContent = `${areaTotalM2} m²`;
    document.getElementById('metricPlanchas').textContent = `${planchas} planchas`;
    document.getElementById('metricCanto').textContent = `${metrosCanto} m`;

    let costoBaseM2 = 95000;
    if (modeloActual.material.includes('macizo')) {
      costoBaseM2 = 240000;
    } else if (modeloActual.material === 'grafito') {
      costoBaseM2 = 115000;
    }

    const costoMateriales = areaTotalM2 * costoBaseM2;
    const costoHerrajes = (modeloActual.cajones * 45000) + 75000;
    const costoManoObra = 650000;
    const precioVenta = Math.round((costoMateriales + costoHerrajes + costoManoObra) * 1.45 / 10000) * 10000;

    const formatoCOP = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(precioVenta);
    document.getElementById('lblPrecioTotal').textContent = formatoCOP;
    document.getElementById('formPrecio').value = precioVenta;
  }

  // Controles del Visor
  function resetearCamara3D() {
    camara.position.set(220, 190, 320);
    controles.target.set(0, modeloActual.alto / 2, 0);
    controles.update();
  }

  function toggleModoAlambre() {
    modoAlambre = !modoAlambre;
    document.getElementById('textAlambre').textContent = modoAlambre ? 'Ver Sólido' : 'Ver Estructura';
    construirMueble3D();
  }

  function toggleAutoRotacion() {
    autoRotar = !autoRotar;
    document.getElementById('textRotar').textContent = autoRotar ? 'Pausar Giro' : 'Giro Auto';
  }

  function tomarCaptura3D() {
    renderizador.render(escena, camara);
    const dataURL = renderizador.domElement.toDataURL('image/png');
    const a = document.createElement('a');
    a.download = `CarpinterSoft-Modelo3D-${modeloActual.tipo}.png`;
    a.href = dataURL;
    a.click();
  }

  // Bucle de Animación
  function animar() {
    requestAnimationFrame(animar);

    if (autoRotar && grupoMueble) {
      grupoMueble.rotation.y += 0.005;
    }

    controles.update();
    renderizador.render(escena, camara);
  }

  // Iniciar al cargar el DOM
  window.addEventListener('DOMContentLoaded', () => {
    init3D();
    calcularCostosEnTiempoReal();
  });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

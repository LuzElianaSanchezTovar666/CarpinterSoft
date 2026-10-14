<?php
$pedidos = getPedidos();
$inventario = getInventario();

$pedidosActivosCount = count(array_filter($pedidos, fn($p) => $p['estado'] !== 'entregado'));
$stockBajoCount = count(array_filter($inventario, fn($i) => $i['stock'] <= $i['min_stock']));

// Agrupación lógica de navegación para evitar corrimiento y garantizar orden visual
$esDisenoActivo = in_array($paginaActual, ['diseno3d', 'despiece', 'catalogo']);
$esTallerActivo = in_array($paginaActual, ['pedidos', 'inventario', 'clientes']);
?>

<header class="no-print bg-white/95 backdrop-blur-md border-b border-[#0D4F60]/15 sticky top-0 z-40 transition-all shadow-sm">
  <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16 gap-2 sm:gap-4">
      
      <!-- 1. Logo y Branding (Fondo Blanco + Contraste Azul Oscuro y Naranja) -->
      <a href="index.php" class="flex items-center gap-2.5 sm:gap-3 flex-shrink-0 group">
        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-[#ED991F] to-[#E58D17] flex items-center justify-center shadow-md shadow-orange-500/25 border border-[#ED991F]/40 text-white group-hover:scale-105 transition-transform flex-shrink-0">
          <i data-lucide="hammer" class="w-5 h-5 text-white font-extrabold"></i>
        </div>
        <div class="flex flex-col min-w-0">
          <div class="flex items-center gap-1.5">
            <span class="font-extrabold text-lg sm:text-xl tracking-tight text-[#103E4A] font-display whitespace-nowrap">
              Carpinter<span class="text-[#ED991F]">Soft</span>
            </span>
            <span class="text-[9px] sm:text-[10px] font-bold tracking-wider uppercase px-1.5 py-0.5 rounded bg-[#F0EEE8] text-[#103E4A] border border-[#0D4F60]/20 hidden xs:inline-block">
              Pro
            </span>
            <?php if (!Database::isUsingFallback()): ?>
              <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-300 items-center gap-1 hidden xl:inline-flex" title="Base de Datos MySQL Conectada (Puerto <?= Database::getActivePort() ?>)">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                MySQL Live
              </span>
            <?php endif; ?>
          </div>
          <p class="text-[10px] text-[#0D4F60]/75 font-semibold hidden 2xl:block leading-none">
            Gestión, Despiece & Modelado 3D
          </p>
        </div>
      </a>

      <!-- 2. Menú de Navegación Compacto: Agrupación Lógica en Menús Desplegables -->
      <!-- Este diseño de 3 botones principales evita corrimientos en tablets (768px-1024px) y laptops -->
      <nav class="hidden md:flex items-center gap-1.5 bg-[#F7F5EF] p-1 rounded-xl border border-[#0D4F60]/15 flex-shrink-0">
        
        <!-- Vista Directa: Panel Principal -->
        <a 
          href="index.php" 
          class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold transition-all <?= ($paginaActual === 'index' || $paginaActual === '') ? 'bg-[#103E4A] text-white shadow-sm' : 'text-[#0D4F60] hover:text-[#ED991F] hover:bg-white/80' ?>"
        >
          <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
          <span>Panel</span>
        </a>

        <!-- MENÚ DESPLEGABLE 1: ÁREA DE DISEÑO & MODELADO 3D -->
        <!-- Agrupa herramientas técnicas: Estudio 3D, Calculadora de Despiece y Catálogo de Muebles -->
        <div class="relative" id="dropdownDisenoContainer">
          <button 
            type="button" 
            id="btnDropdownDiseno"
            onclick="toggleDropdown('diseno', event)"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer select-none <?= $esDisenoActivo ? 'bg-[#103E4A] text-white shadow-sm' : 'text-[#0D4F60] hover:text-[#ED991F] hover:bg-white/80' ?>"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <i data-lucide="box" class="w-4 h-4 text-[#ED991F]"></i>
            <span>Diseño & 3D</span>
            <span class="px-1 text-[9px] font-extrabold bg-[#ED991F]/20 text-[#ED991F] rounded">
              3D
            </span>
            <i data-lucide="chevron-down" id="chevronDiseno" class="w-3.5 h-3.5 transition-transform duration-200"></i>
          </button>

          <!-- Tarjeta Flotante Desplegable: Diseño & Ingeniería -->
          <div 
            id="menuDropdownDiseno" 
            class="hidden absolute left-0 mt-2 w-72 bg-white border border-[#0D4F60]/20 rounded-2xl shadow-xl p-2 z-50 animate-in fade-in zoom-in-95 duration-150"
          >
            <div class="px-3 py-1.5 text-[10px] font-mono font-bold text-[#0D4F60]/60 uppercase tracking-wider border-b border-slate-100 mb-1 flex items-center justify-between">
              <span>Ingeniería & Modelado</span>
              <span class="text-[9px] text-[#ED991F] font-semibold">Técnico</span>
            </div>

            <!-- Opción: Estudio 3D WebGL -->
            <a 
              href="diseno3d.php" 
              class="flex items-start gap-2.5 p-2.5 rounded-xl transition-all group <?= $paginaActual === 'diseno3d' ? 'bg-[#103E4A] text-white' : 'text-[#103E4A] hover:bg-[#F7F5EF]' ?>"
            >
              <div class="p-2 rounded-lg <?= $paginaActual === 'diseno3d' ? 'bg-[#0D4F60] text-[#ED991F]' : 'bg-[#F7F5EF] text-[#ED991F] group-hover:bg-white group-hover:shadow-xs' ?>">
                <i data-lucide="box" class="w-4 h-4"></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-bold font-display">Estudio 3D (WebGL)</span>
                  <span class="text-[9px] font-extrabold px-1 py-0.2 rounded bg-[#ED991F]/20 text-[#E58D17]">
                    Interactivo
                  </span>
                </div>
                <p class="text-[11px] <?= $paginaActual === 'diseno3d' ? 'text-white/80' : 'text-[#103E4A]/65' ?> line-clamp-1 mt-0.5">
                  Modelador paramétrico y despiece 3D en vivo
                </p>
              </div>
            </a>

            <!-- Opción: Calculadora de Despiece & Planos -->
            <a 
              href="despiece.php" 
              class="flex items-start gap-2.5 p-2.5 rounded-xl transition-all group <?= $paginaActual === 'despiece' ? 'bg-[#103E4A] text-white' : 'text-[#103E4A] hover:bg-[#F7F5EF]' ?>"
            >
              <div class="p-2 rounded-lg <?= $paginaActual === 'despiece' ? 'bg-[#0D4F60] text-[#ED991F]' : 'bg-[#F7F5EF] text-[#ED991F] group-hover:bg-white group-hover:shadow-xs' ?>">
                <i data-lucide="scissors" class="w-4 h-4"></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-bold font-display">Calculadora de Despiece</span>
                  <span class="text-[9px] font-bold px-1 py-0.2 rounded bg-slate-100 text-slate-600">
                    CNC
                  </span>
                </div>
                <p class="text-[11px] <?= $paginaActual === 'despiece' ? 'text-white/80' : 'text-[#103E4A]/65' ?> line-clamp-1 mt-0.5">
                  Planos de corte exactos y rendimiento de tableros
                </p>
              </div>
            </a>

            <!-- Opción: Catálogo de Muebles Base -->
            <a 
              href="catalogo.php" 
              class="flex items-start gap-2.5 p-2.5 rounded-xl transition-all group <?= $paginaActual === 'catalogo' ? 'bg-[#103E4A] text-white' : 'text-[#103E4A] hover:bg-[#F7F5EF]' ?>"
            >
              <div class="p-2 rounded-lg <?= $paginaActual === 'catalogo' ? 'bg-[#0D4F60] text-[#ED991F]' : 'bg-[#F7F5EF] text-[#ED991F] group-hover:bg-white group-hover:shadow-xs' ?>">
                <i data-lucide="image" class="w-4 h-4"></i>
              </div>
              <div class="flex-1 min-w-0">
                <span class="text-xs font-bold font-display block">Catálogo de Mobiliario</span>
                <p class="text-[11px] <?= $paginaActual === 'catalogo' ? 'text-white/80' : 'text-[#103E4A]/65' ?> line-clamp-1 mt-0.5">
                  Showroom de muebles estándar y cotización rápida
                </p>
              </div>
            </a>
          </div>
        </div>

        <!-- MENÚ DESPLEGABLE 2: ÁREA DE TALLER, PEDIDOS & GESTIÓN -->
        <!-- Agrupa operaciones de negocio: Tablero Kanban, Inventario y Directorio de Clientes -->
        <div class="relative" id="dropdownTallerContainer">
          <button 
            type="button" 
            id="btnDropdownTaller"
            onclick="toggleDropdown('taller', event)"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold transition-all cursor-pointer select-none <?= $esTallerActivo ? 'bg-[#103E4A] text-white shadow-sm' : 'text-[#0D4F60] hover:text-[#ED991F] hover:bg-white/80' ?>"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <i data-lucide="clipboard-list" class="w-4 h-4 text-[#ED991F]"></i>
            <span>Taller & Pedidos</span>
            <?php if ($pedidosActivosCount > 0): ?>
              <span class="px-1.5 py-0.2 text-[10px] font-extrabold bg-[#ED991F] text-[#103E4A] rounded-full">
                <?= $pedidosActivosCount ?>
              </span>
            <?php elseif ($stockBajoCount > 0): ?>
              <span class="w-2 h-2 rounded-full bg-[#ED991F] animate-pulse"></span>
            <?php endif; ?>
            <i data-lucide="chevron-down" id="chevronTaller" class="w-3.5 h-3.5 transition-transform duration-200"></i>
          </button>

          <!-- Tarjeta Flotante Desplegable: Taller & Gestión -->
          <div 
            id="menuDropdownTaller" 
            class="hidden absolute right-0 mt-2 w-72 bg-white border border-[#0D4F60]/20 rounded-2xl shadow-xl p-2 z-50 animate-in fade-in zoom-in-95 duration-150"
          >
            <div class="px-3 py-1.5 text-[10px] font-mono font-bold text-[#0D4F60]/60 uppercase tracking-wider border-b border-slate-100 mb-1 flex items-center justify-between">
              <span>Producción & Operaciones</span>
              <span class="text-[9px] text-[#ED991F] font-semibold">Taller</span>
            </div>

            <!-- Opción: Tablero Kanban de Pedidos -->
            <a 
              href="pedidos.php" 
              class="flex items-start gap-2.5 p-2.5 rounded-xl transition-all group <?= $paginaActual === 'pedidos' ? 'bg-[#103E4A] text-white' : 'text-[#103E4A] hover:bg-[#F7F5EF]' ?>"
            >
              <div class="p-2 rounded-lg <?= $paginaActual === 'pedidos' ? 'bg-[#0D4F60] text-[#ED991F]' : 'bg-[#F7F5EF] text-[#ED991F] group-hover:bg-white group-hover:shadow-xs' ?>">
                <i data-lucide="clipboard-list" class="w-4 h-4"></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-bold font-display">Tablero de Pedidos</span>
                  <?php if ($pedidosActivosCount > 0): ?>
                    <span class="px-1.5 py-0.2 text-[10px] font-extrabold bg-[#ED991F] text-[#103E4A] rounded-full">
                      <?= $pedidosActivosCount ?> en curso
                    </span>
                  <?php endif; ?>
                </div>
                <p class="text-[11px] <?= $paginaActual === 'pedidos' ? 'text-white/80' : 'text-[#103E4A]/65' ?> line-clamp-1 mt-0.5">
                  Flujo Kanban en 7 fases con anticipos y entregas
                </p>
              </div>
            </a>

            <!-- Opción: Inventario & Stock -->
            <a 
              href="inventario.php" 
              class="flex items-start gap-2.5 p-2.5 rounded-xl transition-all group <?= $paginaActual === 'inventario' ? 'bg-[#103E4A] text-white' : 'text-[#103E4A] hover:bg-[#F7F5EF]' ?>"
            >
              <div class="p-2 rounded-lg <?= $paginaActual === 'inventario' ? 'bg-[#0D4F60] text-[#ED991F]' : 'bg-[#F7F5EF] text-[#ED991F] group-hover:bg-white group-hover:shadow-xs' ?>">
                <i data-lucide="boxes" class="w-4 h-4"></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-bold font-display">Inventario & Stock</span>
                  <?php if ($stockBajoCount > 0): ?>
                    <span class="px-1.5 py-0.5 text-[9px] font-bold bg-[#ED991F]/20 text-[#E58D17] border border-[#ED991F]/40 rounded-full flex items-center gap-0.5">
                      <i data-lucide="alert-triangle" class="w-2.5 h-2.5"></i>
                      <?= $stockBajoCount ?> crítico
                    </span>
                  <?php endif; ?>
                </div>
                <p class="text-[11px] <?= $paginaActual === 'inventario' ? 'text-white/80' : 'text-[#103E4A]/65' ?> line-clamp-1 mt-0.5">
                  Stock de tableros, maderas macizas y herrajes
                </p>
              </div>
            </a>

            <!-- Opción: Clientes & CRM -->
            <a 
              href="clientes.php" 
              class="flex items-start gap-2.5 p-2.5 rounded-xl transition-all group <?= $paginaActual === 'clientes' ? 'bg-[#103E4A] text-white' : 'text-[#103E4A] hover:bg-[#F7F5EF]' ?>"
            >
              <div class="p-2 rounded-lg <?= $paginaActual === 'clientes' ? 'bg-[#0D4F60] text-[#ED991F]' : 'bg-[#F7F5EF] text-[#ED991F] group-hover:bg-white group-hover:shadow-xs' ?>">
                <i data-lucide="users" class="w-4 h-4"></i>
              </div>
              <div class="flex-1 min-w-0">
                <span class="text-xs font-bold font-display block">Directorio de Clientes</span>
                <p class="text-[11px] <?= $paginaActual === 'clientes' ? 'text-white/80' : 'text-[#103E4A]/65' ?> line-clamp-1 mt-0.5">
                  Historial de compras y contacto directo por WhatsApp
                </p>
              </div>
            </a>
          </div>
        </div>

      </nav>

      <!-- 3. Botones de Acción Rápida (Alto Contraste y Adaptabilidad) -->
      <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
        
        <!-- Botón CarpinterIA -->
        <button 
          onclick="abrirModalCarpinterIA()" 
          class="flex items-center gap-1.5 px-2.5 sm:px-3 py-2 rounded-xl text-xs font-bold bg-[#F0EEE8] text-[#103E4A] border border-[#0D4F60]/20 hover:bg-[#E2E8F0] transition-all cursor-pointer"
          title="Asistente de Inteligencia Artificial para Ebanistería"
        >
          <i data-lucide="bot" class="w-4 h-4 text-[#ED991F] animate-pulse"></i>
          <span class="hidden sm:inline">CarpinterIA</span>
        </button>

        <!-- Botón Nuevo Pedido Naranja Vibrante -->
        <button 
          onclick="abrirModalNuevoPedido()" 
          class="flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl text-xs font-extrabold bg-gradient-to-r from-[#ED991F] to-[#E58D17] hover:from-[#F3B14F] hover:to-[#ED991F] text-white transition-all shadow-md shadow-orange-500/30 cursor-pointer active:scale-95"
        >
          <i data-lucide="plus-circle" class="w-4 h-4 text-white"></i>
          <span class="hidden lg:inline">Nuevo Pedido</span>
          <span class="lg:hidden">+ Pedido</span>
        </button>

        <!-- Botón Menú Hamburguesa para Móvil (< 768px) -->
        <button 
          id="btnToggleMenuMovil"
          onclick="toggleMenuMovil()"
          type="button" 
          class="md:hidden p-2 rounded-xl bg-[#F0EEE8] border border-[#0D4F60]/20 text-[#103E4A] hover:bg-[#E2E8F0] transition-colors focus:outline-none"
          aria-label="Abrir menú de navegación"
        >
          <i data-lucide="menu" id="iconoMenuMovil" class="w-5 h-5"></i>
        </button>

      </div>

    </div>

    <!-- 4. Menú Móvil Desplegable: Estructura Limpia por Categorías -->
    <div id="panelMenuMovil" class="hidden md:hidden pb-4 pt-2 border-t border-[#0D4F60]/15 animate-in slide-in-from-top-2 duration-200">
      
      <!-- Panel General -->
      <a 
        href="index.php" 
        class="flex items-center justify-between p-3 mb-3 rounded-xl border <?= ($paginaActual === 'index' || $paginaActual === '') ? 'bg-[#103E4A] text-white border-[#103E4A] font-bold' : 'bg-[#F7F5EF] text-[#103E4A] border-[#0D4F60]/15 font-semibold' ?>"
      >
        <div class="flex items-center gap-2.5">
          <i data-lucide="layout-dashboard" class="w-4 h-4 text-[#ED991F]"></i>
          <span class="text-xs">Panel Central & KPIs</span>
        </div>
        <i data-lucide="chevron-right" class="w-4 h-4 text-[#103E4A]/40"></i>
      </a>

      <!-- Sección 1: Diseño & Modelado 3D -->
      <div class="mb-3">
        <div class="text-[10px] font-mono font-bold text-[#0D4F60]/70 uppercase tracking-wider mb-1.5 px-1 flex items-center gap-1.5">
          <i data-lucide="box" class="w-3.5 h-3.5 text-[#ED991F]"></i>
          <span>Ingeniería & Diseño 3D</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1.5 text-xs">
          <a href="diseno3d.php" class="flex items-center justify-between p-2.5 rounded-xl border <?= $paginaActual === 'diseno3d' ? 'bg-[#103E4A] text-[#ED991F] border-[#ED991F]/50 font-bold' : 'bg-white text-[#103E4A] border-[#0D4F60]/15 font-medium' ?>">
            <div class="flex items-center gap-2">
              <i data-lucide="box" class="w-4 h-4 text-[#ED991F]"></i>
              <span>Estudio 3D (WebGL)</span>
            </div>
            <span class="px-1 text-[9px] font-extrabold bg-[#ED991F]/20 text-[#E58D17] rounded">3D</span>
          </a>
          <a href="despiece.php" class="flex items-center gap-2 p-2.5 rounded-xl border <?= $paginaActual === 'despiece' ? 'bg-[#103E4A] text-[#ED991F] border-[#ED991F]/50 font-bold' : 'bg-white text-[#103E4A] border-[#0D4F60]/15 font-medium' ?>">
            <i data-lucide="scissors" class="w-4 h-4 text-[#ED991F]"></i>
            <span>Calculadora Despiece</span>
          </a>
          <a href="catalogo.php" class="flex items-center gap-2 p-2.5 rounded-xl border <?= $paginaActual === 'catalogo' ? 'bg-[#103E4A] text-[#ED991F] border-[#ED991F]/50 font-bold' : 'bg-white text-[#103E4A] border-[#0D4F60]/15 font-medium' ?>">
            <i data-lucide="image" class="w-4 h-4 text-[#ED991F]"></i>
            <span>Catálogo Mobiliario</span>
          </a>
        </div>
      </div>

      <!-- Sección 2: Taller & Operaciones -->
      <div>
        <div class="text-[10px] font-mono font-bold text-[#0D4F60]/70 uppercase tracking-wider mb-1.5 px-1 flex items-center gap-1.5">
          <i data-lucide="hammer" class="w-3.5 h-3.5 text-[#ED991F]"></i>
          <span>Taller & Operaciones</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1.5 text-xs">
          <a href="pedidos.php" class="flex items-center justify-between p-2.5 rounded-xl border <?= $paginaActual === 'pedidos' ? 'bg-[#103E4A] text-[#ED991F] border-[#ED991F]/50 font-bold' : 'bg-white text-[#103E4A] border-[#0D4F60]/15 font-medium' ?>">
            <div class="flex items-center gap-2">
              <i data-lucide="clipboard-list" class="w-4 h-4 text-[#ED991F]"></i>
              <span>Tablero Pedidos</span>
            </div>
            <?php if ($pedidosActivosCount > 0): ?>
              <span class="px-1.5 py-0.2 text-[10px] font-extrabold bg-[#ED991F] text-white rounded-full"><?= $pedidosActivosCount ?></span>
            <?php endif; ?>
          </a>
          <a href="inventario.php" class="flex items-center justify-between p-2.5 rounded-xl border <?= $paginaActual === 'inventario' ? 'bg-[#103E4A] text-[#ED991F] border-[#ED991F]/50 font-bold' : 'bg-white text-[#103E4A] border-[#0D4F60]/15 font-medium' ?>">
            <div class="flex items-center gap-2">
              <i data-lucide="boxes" class="w-4 h-4 text-[#ED991F]"></i>
              <span>Inventario Stock</span>
            </div>
            <?php if ($stockBajoCount > 0): ?>
              <span class="px-1.5 py-0.5 text-[9px] font-bold bg-[#ED991F]/20 text-[#E58D17] border border-[#ED991F]/40 rounded-full"><?= $stockBajoCount ?></span>
            <?php endif; ?>
          </a>
          <a href="clientes.php" class="flex items-center gap-2 p-2.5 rounded-xl border <?= $paginaActual === 'clientes' ? 'bg-[#103E4A] text-[#ED991F] border-[#ED991F]/50 font-bold' : 'bg-white text-[#103E4A] border-[#0D4F60]/15 font-medium' ?>">
            <i data-lucide="users" class="w-4 h-4 text-[#ED991F]"></i>
            <span>Clientes & CRM</span>
          </a>
        </div>
      </div>

    </div>

  </div>
</header>

<script>
  // Control interactivo de los menús desplegables agrupados
  function toggleDropdown(tipo, e) {
    if (e) e.stopPropagation();
    
    const menuDiseno = document.getElementById('menuDropdownDiseno');
    const chevronDiseno = document.getElementById('chevronDiseno');
    const btnDiseno = document.getElementById('btnDropdownDiseno');

    const menuTaller = document.getElementById('menuDropdownTaller');
    const chevronTaller = document.getElementById('chevronTaller');
    const btnTaller = document.getElementById('btnDropdownTaller');

    if (tipo === 'diseno') {
      const estaOculto = menuDiseno.classList.contains('hidden');
      // Cerrar taller si está abierto
      if (menuTaller && !menuTaller.classList.contains('hidden')) {
        menuTaller.classList.add('hidden');
        if (chevronTaller) chevronTaller.classList.remove('rotate-180');
        if (btnTaller) btnTaller.setAttribute('aria-expanded', 'false');
      }
      // Alternar diseño
      if (estaOculto) {
        menuDiseno.classList.remove('hidden');
        if (chevronDiseno) chevronDiseno.classList.add('rotate-180');
        if (btnDiseno) btnDiseno.setAttribute('aria-expanded', 'true');
      } else {
        menuDiseno.classList.add('hidden');
        if (chevronDiseno) chevronDiseno.classList.remove('rotate-180');
        if (btnDiseno) btnDiseno.setAttribute('aria-expanded', 'false');
      }
    } else if (tipo === 'taller') {
      const estaOculto = menuTaller.classList.contains('hidden');
      // Cerrar diseño si está abierto
      if (menuDiseno && !menuDiseno.classList.contains('hidden')) {
        menuDiseno.classList.add('hidden');
        if (chevronDiseno) chevronDiseno.classList.remove('rotate-180');
        if (btnDiseno) btnDiseno.setAttribute('aria-expanded', 'false');
      }
      // Alternar taller
      if (estaOculto) {
        menuTaller.classList.remove('hidden');
        if (chevronTaller) chevronTaller.classList.add('rotate-180');
        if (btnTaller) btnTaller.setAttribute('aria-expanded', 'true');
      } else {
        menuTaller.classList.add('hidden');
        if (chevronTaller) chevronTaller.classList.remove('rotate-180');
        if (btnTaller) btnTaller.setAttribute('aria-expanded', 'false');
      }
    }
  }

  // Control del Menú Móvil
  function toggleMenuMovil() {
    const panel = document.getElementById('panelMenuMovil');
    const icono = document.getElementById('iconoMenuMovil');
    const estaOculto = panel.classList.contains('hidden');
    
    if (estaOculto) {
      panel.classList.remove('hidden');
      icono.setAttribute('data-lucide', 'x');
    } else {
      panel.classList.add('hidden');
      icono.setAttribute('data-lucide', 'menu');
    }
    if (window.lucide) {
      lucide.createIcons();
    }
  }

  // Cerrar menús desplegables al hacer clic fuera
  document.addEventListener('click', function(e) {
    const containerDiseno = document.getElementById('dropdownDisenoContainer');
    const containerTaller = document.getElementById('dropdownTallerContainer');
    const menuDiseno = document.getElementById('menuDropdownDiseno');
    const menuTaller = document.getElementById('menuDropdownTaller');
    const chevronDiseno = document.getElementById('chevronDiseno');
    const chevronTaller = document.getElementById('chevronTaller');

    if (containerDiseno && !containerDiseno.contains(e.target)) {
      if (menuDiseno && !menuDiseno.classList.contains('hidden')) {
        menuDiseno.classList.add('hidden');
        if (chevronDiseno) chevronDiseno.classList.remove('rotate-180');
      }
    }
    if (containerTaller && !containerTaller.contains(e.target)) {
      if (menuTaller && !menuTaller.classList.contains('hidden')) {
        menuTaller.classList.add('hidden');
        if (chevronTaller) chevronTaller.classList.remove('rotate-180');
      }
    }
  });
</script>

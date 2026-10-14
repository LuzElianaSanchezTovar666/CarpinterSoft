<?php
$pedidos = getPedidos();
$inventario = getInventario();

$pedidosActivosCount = count(array_filter($pedidos, fn($p) => $p['estado'] !== 'entregado'));
$stockBajoCount = count(array_filter($inventario, fn($i) => $i['stock'] <= $i['min_stock']));
$esSubmenuActivo = in_array($paginaActual, ['inventario', 'catalogo', 'clientes']);
?>

<header class="no-print bg-[#103E4A]/95 backdrop-blur-md border-b border-[#0D4F60] sticky top-0 z-40 transition-all shadow-lg">
  <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16 gap-2">
      
      <!-- 1. Logo y Branding Responsivo -->
      <a href="index.php" class="flex items-center gap-2.5 sm:gap-3 flex-shrink-0 group">
        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-[#ED991F] to-[#E58D17] flex items-center justify-center shadow-md shadow-orange-950/40 border border-[#ED991F]/40 text-[#103E4A] group-hover:scale-105 transition-transform">
          <i data-lucide="hammer" class="w-5 h-5 text-[#103E4A] font-extrabold"></i>
        </div>
        <div class="flex flex-col">
          <div class="flex items-center gap-1.5">
            <span class="font-extrabold text-lg sm:text-xl tracking-tight text-[#FFFFFF] font-display">
              Carpinter<span class="text-[#ED991F]">Soft</span>
            </span>
            <span class="text-[9px] sm:text-[10px] font-semibold tracking-wider uppercase px-1.5 py-0.5 rounded bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30 hidden xs:inline-block">
              Pro
            </span>
          </div>
          <p class="text-[10px] sm:text-[11px] text-[#F7F5EF]/80 font-medium hidden lg:block leading-none">
            Gestión & Despiece de Carpintería
          </p>
        </div>
      </a>

      <!-- 2. Menú de Navegación Principal (Optimizado con Menú Desplegable) -->
      <nav class="hidden md:flex items-center gap-1 bg-[#09252D]/85 p-1 rounded-xl border border-[#0D4F60] flex-shrink-0">
        
        <!-- Vista Directa: Panel -->
        <a href="index.php" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all <?= ($paginaActual === 'index' || $paginaActual === '') ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/40 shadow-sm' : 'text-[#F0EEE8]/80 hover:text-[#FFFFFF] hover:bg-[#0D4F60]/50' ?>">
          <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
          <span>Panel</span>
        </a>

        <!-- Vista Directa: Pedidos & Taller -->
        <a href="pedidos.php" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all relative <?= $paginaActual === 'pedidos' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/40 shadow-sm' : 'text-[#F0EEE8]/80 hover:text-[#FFFFFF] hover:bg-[#0D4F60]/50' ?>">
          <i data-lucide="clipboard-list" class="w-4 h-4"></i>
          <span>Pedidos</span>
          <?php if ($pedidosActivosCount > 0): ?>
            <span class="px-1.5 py-0.2 text-[10px] font-extrabold bg-[#ED991F] text-[#103E4A] rounded-full">
              <?= $pedidosActivosCount ?>
            </span>
          <?php endif; ?>
        </a>

        <!-- Vista Directa: Despiece -->
        <a href="despiece.php" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all <?= $paginaActual === 'despiece' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/40 shadow-sm' : 'text-[#F0EEE8]/80 hover:text-[#FFFFFF] hover:bg-[#0D4F60]/50' ?>">
          <i data-lucide="scissors" class="w-4 h-4"></i>
          <span>Despiece</span>
        </a>

        <!-- CASILLAS EN MENÚ DESPLEGABLE (Evita desbordamiento y mantiene diseño armónico) -->
        <div class="relative" id="dropdownGestionContainer">
          <button 
            type="button" 
            id="btnDropdownGestion"
            onclick="toggleDropdownGestion(event)"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer select-none <?= $esSubmenuActivo ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/40 shadow-sm' : 'text-[#F0EEE8]/80 hover:text-[#FFFFFF] hover:bg-[#0D4F60]/50' ?>"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <i data-lucide="layers" class="w-4 h-4 text-[#ED991F]"></i>
            <span>Gestión</span>
            <?php if ($stockBajoCount > 0): ?>
              <span class="w-2 h-2 rounded-full bg-[#ED991F] animate-pulse"></span>
            <?php endif; ?>
            <i data-lucide="chevron-down" id="chevronGestion" class="w-3.5 h-3.5 transition-transform duration-200"></i>
          </button>

          <!-- Menú Flotante Desplegable -->
          <div 
            id="menuDropdownGestion" 
            class="hidden absolute right-0 mt-2 w-60 bg-[#103E4A] border border-[#0D4F60] rounded-2xl shadow-2xl p-2 z-50 backdrop-blur-xl animate-in fade-in zoom-in-95 duration-150"
          >
            <div class="px-3 py-1.5 text-[10px] font-bold text-[#F0EEE8]/60 uppercase tracking-wider border-b border-[#0D4F60]/70 mb-1">
              Módulos del Taller
            </div>

            <!-- Opción: Inventario -->
            <a href="inventario.php" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all <?= $paginaActual === 'inventario' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30' : 'text-[#F7F5EF] hover:bg-[#0D4F60]/60 hover:text-[#ED991F]' ?>">
              <div class="flex items-center gap-2.5">
                <i data-lucide="boxes" class="w-4 h-4 text-[#ED991F]"></i>
                <span>Inventario & Stock</span>
              </div>
              <?php if ($stockBajoCount > 0): ?>
                <span class="px-1.5 py-0.5 text-[10px] font-bold bg-[#E58D17]/20 text-[#ED991F] border border-[#ED991F]/40 rounded-full flex items-center gap-0.5">
                  <i data-lucide="alert-triangle" class="w-2.5 h-2.5"></i>
                  <?= $stockBajoCount ?>
                </span>
              <?php endif; ?>
            </a>

            <!-- Opción: Catálogo -->
            <a href="catalogo.php" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all <?= $paginaActual === 'catalogo' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30' : 'text-[#F7F5EF] hover:bg-[#0D4F60]/60 hover:text-[#ED991F]' ?>">
              <i data-lucide="image" class="w-4 h-4 text-[#ED991F]"></i>
              <span>Catálogo de Muebles</span>
            </a>

            <!-- Opción: Clientes -->
            <a href="clientes.php" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all <?= $paginaActual === 'clientes' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30' : 'text-[#F7F5EF] hover:bg-[#0D4F60]/60 hover:text-[#ED991F]' ?>">
              <i data-lucide="users" class="w-4 h-4 text-[#ED991F]"></i>
              <span>Clientes & Contactos</span>
            </a>
          </div>
        </div>

      </nav>

      <!-- 3. Botones de Acción y Toggle Móvil -->
      <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
        
        <!-- Botón CarpinterIA -->
        <button onclick="abrirModalCarpinterIA()" class="flex items-center gap-1.5 px-2.5 sm:px-3.5 py-2 rounded-xl text-xs font-bold bg-[#0D4F60] text-[#F7F5EF] border border-[#ED991F]/40 hover:bg-[#1A677B] transition-all cursor-pointer">
          <i data-lucide="bot" class="w-4 h-4 text-[#ED991F] animate-pulse"></i>
          <span class="hidden sm:inline">CarpinterIA</span>
        </button>

        <!-- Botón Nuevo Pedido -->
        <button onclick="abrirModalNuevoPedido()" class="flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-[#ED991F] to-[#E58D17] text-[#103E4A] hover:opacity-90 transition-all shadow-md shadow-orange-950/40 cursor-pointer active:scale-95">
          <i data-lucide="plus-circle" class="w-4 h-4 text-[#103E4A]"></i>
          <span class="hidden sm:inline">Nuevo Pedido</span>
          <span class="sm:hidden">Nuevo</span>
        </button>

        <!-- Botón Menú Hamburguesa para Móvil -->
        <button 
          id="btnToggleMenuMovil"
          onclick="toggleMenuMovil()"
          type="button" 
          class="md:hidden p-2 rounded-xl bg-[#0D4F60] border border-[#ED991F]/30 text-[#F7F5EF] hover:bg-[#1A677B] transition-colors focus:outline-none"
          aria-label="Abrir menú de navegación"
        >
          <i data-lucide="menu" id="iconoMenuMovil" class="w-5 h-5"></i>
        </button>

      </div>

    </div>

    <!-- 4. Menú Desplegable Móvil (Expandible y Justificado con Media Queries) -->
    <div id="panelMenuMovil" class="hidden md:hidden pb-4 pt-2 border-t border-[#0D4F60]/80 animate-in slide-in-from-top-2 duration-200">
      <div class="grid grid-cols-2 gap-2 text-xs">
        
        <a href="index.php" class="flex items-center gap-2 p-2.5 rounded-xl border <?= ($paginaActual === 'index' || $paginaActual === '') ? 'bg-[#0D4F60] text-[#ED991F] border-[#ED991F]/50 font-bold' : 'bg-[#09252D]/90 text-[#F0EEE8] border-[#0D4F60]' ?>">
          <i data-lucide="layout-dashboard" class="w-4 h-4 text-[#ED991F]"></i>
          <span>Panel General</span>
        </a>

        <a href="pedidos.php" class="flex items-center justify-between p-2.5 rounded-xl border <?= $paginaActual === 'pedidos' ? 'bg-[#0D4F60] text-[#ED991F] border-[#ED991F]/50 font-bold' : 'bg-[#09252D]/90 text-[#F0EEE8] border-[#0D4F60]' ?>">
          <div class="flex items-center gap-2">
            <i data-lucide="clipboard-list" class="w-4 h-4 text-[#ED991F]"></i>
            <span>Pedidos</span>
          </div>
          <?php if ($pedidosActivosCount > 0): ?>
            <span class="px-1.5 py-0.2 text-[10px] font-extrabold bg-[#ED991F] text-[#103E4A] rounded-full">
              <?= $pedidosActivosCount ?>
            </span>
          <?php endif; ?>
        </a>

        <a href="despiece.php" class="flex items-center gap-2 p-2.5 rounded-xl border <?= $paginaActual === 'despiece' ? 'bg-[#0D4F60] text-[#ED991F] border-[#ED991F]/50 font-bold' : 'bg-[#09252D]/90 text-[#F0EEE8] border-[#0D4F60]' ?>">
          <i data-lucide="scissors" class="w-4 h-4 text-[#ED991F]"></i>
          <span>Calculadora</span>
        </a>

        <a href="inventario.php" class="flex items-center justify-between p-2.5 rounded-xl border <?= $paginaActual === 'inventario' ? 'bg-[#0D4F60] text-[#ED991F] border-[#ED991F]/50 font-bold' : 'bg-[#09252D]/90 text-[#F0EEE8] border-[#0D4F60]' ?>">
          <div class="flex items-center gap-2">
            <i data-lucide="boxes" class="w-4 h-4 text-[#ED991F]"></i>
            <span>Inventario</span>
          </div>
          <?php if ($stockBajoCount > 0): ?>
            <span class="px-1.5 py-0.5 text-[9px] font-bold bg-[#E58D17]/20 text-[#ED991F] border border-[#ED991F]/40 rounded-full">
              <?= $stockBajoCount ?>
            </span>
          <?php endif; ?>
        </a>

        <a href="catalogo.php" class="flex items-center gap-2 p-2.5 rounded-xl border <?= $paginaActual === 'catalogo' ? 'bg-[#0D4F60] text-[#ED991F] border-[#ED991F]/50 font-bold' : 'bg-[#09252D]/90 text-[#F0EEE8] border-[#0D4F60]' ?>">
          <i data-lucide="image" class="w-4 h-4 text-[#ED991F]"></i>
          <span>Catálogo</span>
        </a>

        <a href="clientes.php" class="flex items-center gap-2 p-2.5 rounded-xl border <?= $paginaActual === 'clientes' ? 'bg-[#0D4F60] text-[#ED991F] border-[#ED991F]/50 font-bold' : 'bg-[#09252D]/90 text-[#F0EEE8] border-[#0D4F60]' ?>">
          <i data-lucide="users" class="w-4 h-4 text-[#ED991F]"></i>
          <span>Clientes</span>
        </a>

      </div>
    </div>

  </div>
</header>

<script>
  // Control del Menú Desplegable "Gestión"
  function toggleDropdownGestion(e) {
    e.stopPropagation();
    const menu = document.getElementById('menuDropdownGestion');
    const chevron = document.getElementById('chevronGestion');
    const btn = document.getElementById('btnDropdownGestion');
    
    const estaOculto = menu.classList.contains('hidden');
    if (estaOculto) {
      menu.classList.remove('hidden');
      chevron.classList.add('rotate-180');
      btn.setAttribute('aria-expanded', 'true');
    } else {
      menu.classList.add('hidden');
      chevron.classList.remove('rotate-180');
      btn.setAttribute('aria-expanded', 'false');
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

  // Cerrar menú desplegable al hacer clic fuera
  document.addEventListener('click', function(e) {
    const container = document.getElementById('dropdownGestionContainer');
    const menu = document.getElementById('menuDropdownGestion');
    const chevron = document.getElementById('chevronGestion');
    const btn = document.getElementById('btnDropdownGestion');
    
    if (container && !container.contains(e.target)) {
      if (menu && !menu.classList.contains('hidden')) {
        menu.classList.add('hidden');
        if (chevron) chevron.classList.remove('rotate-180');
        if (btn) btn.setAttribute('aria-expanded', 'false');
      }
    }
  });
</script>

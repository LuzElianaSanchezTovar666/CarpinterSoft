<?php
$pedidos = getPedidos();
$inventario = getInventario();

$pedidosActivosCount = count(array_filter($pedidos, fn($p) => $p['estado'] !== 'entregado'));
$stockBajoCount = count(array_filter($inventario, fn($i) => $i['stock'] <= $i['min_stock']));
?>

<header class="no-print bg-[#103E4A]/95 backdrop-blur-md border-b border-[#0D4F60] sticky top-0 z-40 transition-all shadow-lg">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div style="display: flex; align-items: center; justify-content: space-between; height: 4rem;">
      
      <!-- Logo y Branding -->
      <a href="index.php" class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#ED991F] to-[#E58D17] flex items-center justify-center shadow-lg shadow-orange-950/40 border border-[#ED991F]/40 text-[#103E4A]">
          <i data-lucide="hammer" class="w-5 h-5 text-[#103E4A] font-extrabold"></i>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <span class="font-extrabold text-xl tracking-tight text-[#FFFFFF] font-display">
              Carpinter<span class="text-[#ED991F]">Soft</span>
            </span>
            <span class="text-[10px] font-semibold tracking-wider uppercase px-2 py-0.5 rounded bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30">
              Pro Taller
            </span>
          </div>
          <p class="text-[11px] text-[#F7F5EF]/80 font-medium">Gestión & Despiece de Carpintería</p>
        </div>
      </a>

      <!-- Menú de Navegación Principal -->
      <nav class="hidden md:flex items-center gap-1 bg-[#09252D]/80 p-1.5 rounded-xl border border-[#0D4F60]">
        <a href="index.php" class="flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold transition-all <?= ($paginaActual === 'index' || $paginaActual === '') ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/40 shadow-sm' : 'text-[#F0EEE8]/80 hover:text-[#FFFFFF] hover:bg-[#0D4F60]/50' ?>">
          <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
          <span>Panel</span>
        </a>

        <a href="pedidos.php" class="flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold transition-all relative <?= $paginaActual === 'pedidos' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/40 shadow-sm' : 'text-[#F0EEE8]/80 hover:text-[#FFFFFF] hover:bg-[#0D4F60]/50' ?>">
          <i data-lucide="clipboard-list" class="w-4 h-4"></i>
          <span>Pedidos & Taller</span>
          <?php if ($pedidosActivosCount > 0): ?>
            <span class="px-1.5 py-0.2 text-[10px] font-extrabold bg-[#ED991F] text-[#103E4A] rounded-full">
              <?= $pedidosActivosCount ?>
            </span>
          <?php endif; ?>
        </a>

        <a href="despiece.php" class="flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold transition-all <?= $paginaActual === 'despiece' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/40 shadow-sm' : 'text-[#F0EEE8]/80 hover:text-[#FFFFFF] hover:bg-[#0D4F60]/50' ?>">
          <i data-lucide="scissors" class="w-4 h-4"></i>
          <span>Calculadora & Despiece</span>
        </a>

        <a href="inventario.php" class="flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold transition-all relative <?= $paginaActual === 'inventario' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/40 shadow-sm' : 'text-[#F0EEE8]/80 hover:text-[#FFFFFF] hover:bg-[#0D4F60]/50' ?>">
          <i data-lucide="boxes" class="w-4 h-4"></i>
          <span>Inventario</span>
          <?php if ($stockBajoCount > 0): ?>
            <span class="px-1.5 py-0.2 text-[10px] font-bold bg-[#E58D17]/20 text-[#ED991F] border border-[#ED991F]/40 rounded-full flex items-center gap-0.5">
              <i data-lucide="alert-triangle" class="w-2.5 h-2.5"></i>
              <?= $stockBajoCount ?>
            </span>
          <?php endif; ?>
        </a>

        <a href="catalogo.php" class="flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold transition-all <?= $paginaActual === 'catalogo' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/40 shadow-sm' : 'text-[#F0EEE8]/80 hover:text-[#FFFFFF] hover:bg-[#0D4F60]/50' ?>">
          <i data-lucide="image" class="w-4 h-4"></i>
          <span>Catálogo</span>
        </a>

        <a href="clientes.php" class="flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold transition-all <?= $paginaActual === 'clientes' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/40 shadow-sm' : 'text-[#F0EEE8]/80 hover:text-[#FFFFFF] hover:bg-[#0D4F60]/50' ?>">
          <i data-lucide="users" class="w-4 h-4"></i>
          <span>Clientes</span>
        </a>
      </nav>

      <!-- Botones de Acción -->
      <div class="flex items-center gap-2.5">
        <button onclick="abrirModalCarpinterIA()" class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-[#0D4F60] text-[#F7F5EF] border border-[#ED991F]/40 hover:bg-[#1A677B] transition-all cursor-pointer">
          <i data-lucide="bot" class="w-4 h-4 text-[#ED991F] animate-pulse"></i>
          <span class="hidden sm:inline">CarpinterIA</span>
        </button>

        <button onclick="abrirModalNuevoPedido()" class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-[#ED991F] to-[#E58D17] text-[#103E4A] hover:opacity-90 transition-all shadow-md shadow-orange-950/40 cursor-pointer active:scale-95">
          <i data-lucide="plus-circle" class="w-4 h-4 text-[#103E4A]"></i>
          <span class="hidden sm:inline">Nuevo Pedido</span>
          <span class="sm:hidden">Nuevo</span>
        </button>
      </div>

    </div>

    <!-- Navegación Móvil -->
    <div class="md:hidden flex items-center justify-around py-2 border-t border-[#0D4F60] overflow-x-auto no-scrollbar">
      <a href="index.php" class="flex flex-col items-center gap-1 p-1 text-[11px] <?= ($paginaActual === 'index' || $paginaActual === '') ? 'text-[#ED991F] font-bold' : 'text-[#F0EEE8]/70' ?>">
        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
        <span>Panel</span>
      </a>
      <a href="pedidos.php" class="flex flex-col items-center gap-1 p-1 text-[11px] <?= $paginaActual === 'pedidos' ? 'text-[#ED991F] font-bold' : 'text-[#F0EEE8]/70' ?>">
        <i data-lucide="clipboard-list" class="w-4 h-4"></i>
        <span>Pedidos</span>
      </a>
      <a href="despiece.php" class="flex flex-col items-center gap-1 p-1 text-[11px] <?= $paginaActual === 'despiece' ? 'text-[#ED991F] font-bold' : 'text-[#F0EEE8]/70' ?>">
        <i data-lucide="scissors" class="w-4 h-4"></i>
        <span>Despiece</span>
      </a>
      <a href="inventario.php" class="flex flex-col items-center gap-1 p-1 text-[11px] <?= $paginaActual === 'inventario' ? 'text-[#ED991F] font-bold' : 'text-[#F0EEE8]/70' ?>">
        <i data-lucide="boxes" class="w-4 h-4"></i>
        <span>Stock</span>
      </a>
      <a href="catalogo.php" class="flex flex-col items-center gap-1 p-1 text-[11px] <?= $paginaActual === 'catalogo' ? 'text-[#ED991F] font-bold' : 'text-[#F0EEE8]/70' ?>">
        <i data-lucide="image" class="w-4 h-4"></i>
        <span>Catálogo</span>
      </a>
      <a href="clientes.php" class="flex flex-col items-center gap-1 p-1 text-[11px] <?= $paginaActual === 'clientes' ? 'text-[#ED991F] font-bold' : 'text-[#F0EEE8]/70' ?>">
        <i data-lucide="users" class="w-4 h-4"></i>
        <span>Clientes</span>
      </a>
    </div>
  </div>
</header>

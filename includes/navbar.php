<?php
$pedidos = getPedidos();
$inventario = getInventario();

$pedidosActivosCount = count(array_filter($pedidos, fn($p) => $p['estado'] !== 'entregado'));
$stockBajoCount = count(array_filter($inventario, fn($i) => $i['stock'] <= $i['min_stock']));
?>

<header class="no-print bg-stone-900/90 backdrop-blur-md border-b border-stone-800 sticky top-0 z-40 transition-all">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div className="flex items-center justify-between h-16" style="display: flex; align-items: center; justify-content: space-between; height: 4rem;">
      
      <!-- Logo y Branding -->
      <a href="index.php" class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-600 via-amber-700 to-stone-800 flex items-center justify-center shadow-lg shadow-amber-900/40 border border-amber-500/30">
          <i data-lucide="hammer" class="w-5 h-5 text-amber-200"></i>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <span class="font-extrabold text-xl tracking-tight text-white font-display">
              Carpinter<span class="text-amber-500">Soft</span>
            </span>
            <span class="text-[10px] font-semibold tracking-wider uppercase px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20">
              PHP Pro
            </span>
          </div>
          <p class="text-[11px] text-stone-400 font-medium">Gestión & Despiece de Carpintería</p>
        </div>
      </a>

      <!-- Menú de Navegación Principal -->
      <nav class="hidden md:flex items-center gap-1 bg-stone-950/60 p-1.5 rounded-xl border border-stone-800/80">
        <a href="index.php" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition-all <?= ($paginaActual === 'index' || $paginaActual === '') ? 'bg-amber-600/20 text-amber-400 border border-amber-500/30 shadow-sm' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' ?>">
          <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
          <span>Panel</span>
        </a>

        <a href="pedidos.php" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition-all relative <?= $paginaActual === 'pedidos' ? 'bg-amber-600/20 text-amber-400 border border-amber-500/30 shadow-sm' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' ?>">
          <i data-lucide="clipboard-list" class="w-4 h-4"></i>
          <span>Pedidos & Taller</span>
          <?php if ($pedidosActivosCount > 0): ?>
            <span class="px-1.5 py-0.2 text-[10px] font-bold bg-amber-600 text-white rounded-full">
              <?= $pedidosActivosCount ?>
            </span>
          <?php endif; ?>
        </a>

        <a href="despiece.php" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition-all <?= $paginaActual === 'despiece' ? 'bg-amber-600/20 text-amber-400 border border-amber-500/30 shadow-sm' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' ?>">
          <i data-lucide="scissors" class="w-4 h-4"></i>
          <span>Calculadora & Despiece</span>
        </a>

        <a href="inventario.php" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition-all relative <?= $paginaActual === 'inventario' ? 'bg-amber-600/20 text-amber-400 border border-amber-500/30 shadow-sm' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' ?>">
          <i data-lucide="boxes" class="w-4 h-4"></i>
          <span>Inventario</span>
          <?php if ($stockBajoCount > 0): ?>
            <span class="px-1.5 py-0.2 text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30 rounded-full flex items-center gap-0.5">
              <i data-lucide="alert-triangle" class="w-2.5 h-2.5"></i>
              <?= $stockBajoCount ?>
            </span>
          <?php endif; ?>
        </a>

        <a href="catalogo.php" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition-all <?= $paginaActual === 'catalogo' ? 'bg-amber-600/20 text-amber-400 border border-amber-500/30 shadow-sm' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' ?>">
          <i data-lucide="image" class="w-4 h-4"></i>
          <span>Catálogo</span>
        </a>

        <a href="clientes.php" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition-all <?= $paginaActual === 'clientes' ? 'bg-amber-600/20 text-amber-400 border border-amber-500/30 shadow-sm' : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50' ?>">
          <i data-lucide="users" class="w-4 h-4"></i>
          <span>Clientes</span>
        </a>
      </nav>

      <!-- Botones de Acción -->
      <div class="flex items-center gap-2.5">
        <button onclick="abrirModalCarpinterIA()" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold bg-gradient-to-r from-amber-600/30 to-amber-700/20 text-amber-300 border border-amber-500/40 hover:bg-amber-600/40 transition-all cursor-pointer">
          <i data-lucide="bot" class="w-4 h-4 text-amber-400 animate-pulse"></i>
          <span class="hidden sm:inline">CarpinterIA</span>
        </button>

        <button onclick="abrirModalNuevoPedido()" class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-amber-500 to-amber-600 text-stone-950 hover:from-amber-400 hover:to-amber-500 transition-all shadow-md shadow-amber-950/40 cursor-pointer active:scale-95">
          <i data-lucide="plus-circle" class="w-4 h-4 text-stone-950"></i>
          <span class="hidden sm:inline">Nuevo Pedido</span>
          <span class="sm:hidden">Nuevo</span>
        </button>
      </div>

    </div>

    <!-- Navegación Móvil -->
    <div class="md:hidden flex items-center justify-around py-2 border-t border-stone-800/60 overflow-x-auto no-scrollbar">
      <a href="index.php" class="flex flex-col items-center gap-1 p-1 text-[11px] <?= ($paginaActual === 'index' || $paginaActual === '') ? 'text-amber-400 font-bold' : 'text-stone-400' ?>">
        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
        <span>Panel</span>
      </a>
      <a href="pedidos.php" class="flex flex-col items-center gap-1 p-1 text-[11px] <?= $paginaActual === 'pedidos' ? 'text-amber-400 font-bold' : 'text-stone-400' ?>">
        <i data-lucide="clipboard-list" class="w-4 h-4"></i>
        <span>Pedidos</span>
      </a>
      <a href="despiece.php" class="flex flex-col items-center gap-1 p-1 text-[11px] <?= $paginaActual === 'despiece' ? 'text-amber-400 font-bold' : 'text-stone-400' ?>">
        <i data-lucide="scissors" class="w-4 h-4"></i>
        <span>Despiece</span>
      </a>
      <a href="inventario.php" class="flex flex-col items-center gap-1 p-1 text-[11px] <?= $paginaActual === 'inventario' ? 'text-amber-400 font-bold' : 'text-stone-400' ?>">
        <i data-lucide="boxes" class="w-4 h-4"></i>
        <span>Stock</span>
      </a>
      <a href="catalogo.php" class="flex flex-col items-center gap-1 p-1 text-[11px] <?= $paginaActual === 'catalogo' ? 'text-amber-400 font-bold' : 'text-stone-400' ?>">
        <i data-lucide="image" class="w-4 h-4"></i>
        <span>Catálogo</span>
      </a>
      <a href="clientes.php" class="flex flex-col items-center gap-1 p-1 text-[11px] <?= $paginaActual === 'clientes' ? 'text-amber-400 font-bold' : 'text-stone-400' ?>">
        <i data-lucide="users" class="w-4 h-4"></i>
        <span>Clientes</span>
      </a>
    </div>
  </div>
</header>

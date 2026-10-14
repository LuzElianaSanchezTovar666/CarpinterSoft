<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$pedidos = getPedidos();

// Filtros
$categoriaFiltro = $_GET['categoria'] ?? 'todos';
$prioridadFiltro = $_GET['prioridad'] ?? 'todos';
$busqueda = strtolower($_GET['q'] ?? '');

$pedidosFiltrados = array_filter($pedidos, function($p) use ($categoriaFiltro, $prioridadFiltro, $busqueda) {
    if ($categoriaFiltro !== 'todos' && ($p['categoria'] ?? '') !== $categoriaFiltro) return false;
    if ($prioridadFiltro !== 'todos' && ($p['prioridad'] ?? '') !== $prioridadFiltro) return false;
    if ($busqueda !== '') {
        $texto = strtolower($p['codigo'] . ' ' . $p['proyecto'] . ' ' . $p['cliente'] . ' ' . $p['madera']);
        if (strpos($texto, $busqueda) === false) return false;
    }
    return true;
});

$fases = [
    'cotizacion' => ['label' => 'Cotización', 'icon' => 'clock', 'color' => 'text-amber-400', 'bg' => 'border-amber-500/30 bg-amber-500/5'],
    'diseno'     => ['label' => 'Diseño & Planos', 'icon' => 'ruler', 'color' => 'text-indigo-400', 'bg' => 'border-indigo-500/30 bg-indigo-500/5'],
    'corte'      => ['label' => 'En Corte', 'icon' => 'scissors', 'color' => 'text-sky-400', 'bg' => 'border-sky-500/30 bg-sky-500/5'],
    'ensamble'   => ['label' => 'Ensamble', 'icon' => 'hammer', 'color' => 'text-orange-400', 'bg' => 'border-orange-500/30 bg-orange-500/5'],
    'acabado'    => ['label' => 'Acabado / Laca', 'icon' => 'palette', 'color' => 'text-purple-400', 'bg' => 'border-purple-500/30 bg-purple-500/5'],
    'listo'      => ['label' => 'Listo Entrega', 'icon' => 'package-check', 'color' => 'text-emerald-400', 'bg' => 'border-emerald-500/30 bg-emerald-500/5'],
    'entregado'  => ['label' => 'Instalado', 'icon' => 'check-circle-2', 'color' => 'text-stone-400', 'bg' => 'border-stone-700 bg-stone-800/40'],
];

$ordenFases = array_keys($fases);
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">

  <!-- Barra de Filtros y Búsqueda -->
  <div class="bg-stone-900/80 border border-stone-800 p-4 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
    
    <form method="GET" action="pedidos.php" class="relative flex-1 max-w-md">
      <i data-lucide="search" class="w-4 h-4 text-stone-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
      <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Buscar por código, cliente, mueble o madera..." class="w-full pl-10 pr-4 py-2 bg-stone-950/70 border border-stone-800 rounded-xl text-xs sm:text-sm text-stone-200 placeholder-stone-500 focus:outline-none focus:border-amber-500">
    </form>

    <div class="flex flex-wrap items-center gap-2.5">
      <form method="GET" action="pedidos.php" class="flex items-center gap-2">
        <select name="categoria" onchange="this.form.submit()" class="px-3 py-2 bg-stone-950/70 border border-stone-800 rounded-xl text-xs text-stone-300 focus:outline-none focus:border-amber-500 cursor-pointer">
          <option value="todos" <?= $categoriaFiltro === 'todos' ? 'selected' : '' ?>>Todas las Categorías</option>
          <option value="Cocina" <?= $categoriaFiltro === 'Cocina' ? 'selected' : '' ?>>Cocinas</option>
          <option value="Closet" <?= $categoriaFiltro === 'Closet' ? 'selected' : '' ?>>Clósets</option>
          <option value="Mesa" <?= $categoriaFiltro === 'Mesa' ? 'selected' : '' ?>>Mesas</option>
          <option value="Puerta" <?= $categoriaFiltro === 'Puerta' ? 'selected' : '' ?>>Puertas</option>
          <option value="Oficina" <?= $categoriaFiltro === 'Oficina' ? 'selected' : '' ?>>Oficinas</option>
          <option value="Especial" <?= $categoriaFiltro === 'Especial' ? 'selected' : '' ?>>Especiales</option>
        </select>

        <select name="prioridad" onchange="this.form.submit()" class="px-3 py-2 bg-stone-950/70 border border-stone-800 rounded-xl text-xs text-stone-300 focus:outline-none focus:border-amber-500 cursor-pointer">
          <option value="todos" <?= $prioridadFiltro === 'todos' ? 'selected' : '' ?>>Toda Prioridad</option>
          <option value="urgente" <?= $prioridadFiltro === 'urgente' ? 'selected' : '' ?>>Urgente</option>
          <option value="alta" <?= $prioridadFiltro === 'alta' ? 'selected' : '' ?>>Alta</option>
          <option value="media" <?= $prioridadFiltro === 'media' ? 'selected' : '' ?>>Media</option>
          <option value="baja" <?= $prioridadFiltro === 'baja' ? 'selected' : '' ?>>Baja</option>
        </select>
      </form>

      <button onclick="abrirModalNuevoPedido()" class="flex items-center gap-1.5 px-3.5 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-stone-950 font-bold text-xs rounded-xl shadow-md transition-all cursor-pointer">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Nuevo Pedido</span>
      </button>
    </div>

  </div>

  <!-- Tablero Kanban de Taller -->
  <div class="overflow-x-auto pb-4">
    <div class="flex gap-4 min-w-[1300px]">
      <?php foreach ($fases as $faseKey => $faseInfo): 
          $pedidosEnFase = array_filter($pedidosFiltrados, fn($p) => $p['estado'] === $faseKey);
          $faseIdx = array_search($faseKey, $ordenFases);
          $prevFase = $faseIdx > 0 ? $ordenFases[$faseIdx - 1] : null;
          $nextFase = $faseIdx < count($ordenFases) - 1 ? $ordenFases[$faseIdx + 1] : null;
      ?>
        <div class="w-80 flex-shrink-0 bg-stone-900/60 border border-stone-800/80 rounded-2xl flex flex-col h-[750px] shadow-lg">
          <!-- Cabecera de Columna -->
          <div class="p-3.5 border-b border-stone-800 flex items-center justify-between rounded-t-2xl <?= $faseInfo['bg'] ?>">
            <div class="flex items-center gap-2">
              <i data-lucide="<?= $faseInfo['icon'] ?>" class="w-4 h-4 <?= $faseInfo['color'] ?>"></i>
              <span class="font-bold text-xs text-white uppercase tracking-wider font-display">
                <?= $faseInfo['label'] ?>
              </span>
            </div>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-stone-900 text-stone-300 border border-stone-700">
              <?= count($pedidosEnFase) ?>
            </span>
          </div>

          <!-- Tarjetas de la Columna -->
          <div class="p-3 space-y-3 overflow-y-auto flex-1 no-scrollbar">
            <?php if (empty($pedidosEnFase)): ?>
              <div class="h-32 flex items-center justify-center text-xs text-stone-500 border border-dashed border-stone-800 rounded-xl">
                Sin pedidos en esta fase
              </div>
            <?php else: ?>
              <?php foreach ($pedidosEnFase as $p): 
                  $porcentajePago = min(100, round(($p['anticipo'] / $p['precio']) * 100));
              ?>
                <div class="bg-stone-950/80 border border-stone-800/90 hover:border-amber-500/40 rounded-xl p-3.5 space-y-3 transition-all hover:shadow-xl">
                  
                  <div class="flex items-center justify-between">
                    <span class="text-[11px] font-mono font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                      <?= htmlspecialchars($p['codigo']) ?>
                    </span>
                    <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full <?= $p['prioridad'] === 'urgente' ? 'bg-rose-500/20 text-rose-300' : 'bg-stone-800 text-stone-400' ?>">
                      <?= $p['prioridad'] ?>
                    </span>
                  </div>

                  <div>
                    <h4 class="text-xs sm:text-sm font-bold text-white hover:text-amber-400 transition-colors">
                      <?= htmlspecialchars($p['proyecto']) ?>
                    </h4>
                    <p class="text-[11px] text-stone-400 flex items-center gap-1 mt-0.5">
                      <i data-lucide="user" class="w-3 h-3 text-stone-500"></i>
                      <?= htmlspecialchars($p['cliente']) ?>
                    </p>
                  </div>

                  <div class="bg-stone-900/90 rounded-lg p-2 text-[11px] space-y-1 text-stone-300 border border-stone-800/60">
                    <div class="flex items-center justify-between">
                      <span class="text-stone-400">Medidas:</span>
                      <span class="font-mono text-stone-200"><?= $p['alto'] ?>x<?= $p['ancho'] ?>x<?= $p['fondo'] ?> cm</span>
                    </div>
                    <div class="text-[10px] text-stone-400 truncate">
                      Madera: <span class="text-stone-300"><?= htmlspecialchars($p['madera']) ?></span>
                    </div>
                  </div>

                  <div class="flex items-center justify-between text-[11px] text-stone-400">
                    <div class="flex items-center gap-1">
                      <i data-lucide="calendar" class="w-3 h-3 text-amber-400"></i>
                      <span><?= $p['fecha_entrega'] ?></span>
                    </div>
                    <span class="text-[10px] text-stone-500 truncate max-w-[110px]"><?= $p['carpintero'] ?></span>
                  </div>

                  <!-- Barra de Anticipo -->
                  <div>
                    <div class="flex justify-between text-[10px] text-stone-400 mb-1">
                      <span>Anticipo: <?= $porcentajePago ?>%</span>
                      <span class="font-semibold text-stone-200"><?= formatCOP($p['precio']) ?></span>
                    </div>
                    <div class="w-full bg-stone-900 h-1.5 rounded-full overflow-hidden">
                      <div class="h-full rounded-full <?= $porcentajePago === 100 ? 'bg-emerald-500' : 'bg-amber-500' ?>" style="width: <?= $porcentajePago ?>%"></div>
                    </div>
                  </div>

                  <!-- Acciones Rápidas -->
                  <div class="pt-2 border-t border-stone-800/80 flex items-center justify-between gap-1">
                    <?php if ($prevFase): ?>
                      <a href="api/actualizar_estado.php?id=<?= $p['id'] ?>&estado=<?= $prevFase ?>" class="p-1.5 rounded-lg bg-stone-900 hover:bg-stone-800 text-stone-400 hover:text-stone-200" title="Retroceder fase">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                      </a>
                    <?php else: ?><div class="w-7"></div><?php endif; ?>

                    <a href="imprimir_orden.php?id=<?= $p['id'] ?>" target="_blank" class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-stone-900 hover:bg-stone-800 text-amber-400 text-[11px] font-semibold border border-stone-800">
                      <i data-lucide="printer" class="w-3 h-3"></i>
                      <span>Ficha</span>
                    </a>

                    <?php if ($nextFase): ?>
                      <a href="api/actualizar_estado.php?id=<?= $p['id'] ?>&estado=<?= $nextFase ?>" class="p-1.5 rounded-lg bg-amber-500/20 hover:bg-amber-500 text-amber-400 hover:text-stone-950 border border-amber-500/30" title="Avanzar fase">
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                      </a>
                    <?php else: ?>
                      <span class="p-1 text-emerald-400"><i data-lucide="check-circle-2" class="w-4 h-4"></i></span>
                    <?php endif; ?>
                  </div>

                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

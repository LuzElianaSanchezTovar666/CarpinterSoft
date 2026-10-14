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
    'cotizacion' => ['label' => 'Cotización', 'icon' => 'clock', 'color' => 'text-[#ED991F]', 'bg' => 'border-[#ED991F]/30 bg-[#ED991F]/10'],
    'diseno'     => ['label' => 'Diseño & Planos', 'icon' => 'ruler', 'color' => 'text-[#F7F5EF]', 'bg' => 'border-[#0D4F60] bg-[#0D4F60]/40'],
    'corte'      => ['label' => 'En Corte', 'icon' => 'scissors', 'color' => 'text-[#ED991F]', 'bg' => 'border-[#ED991F]/40 bg-[#0D4F60]/50'],
    'ensamble'   => ['label' => 'Ensamble', 'icon' => 'hammer', 'color' => 'text-[#E58D17]', 'bg' => 'border-[#E58D17]/40 bg-[#0D4F60]/60'],
    'acabado'    => ['label' => 'Acabado / Laca', 'icon' => 'palette', 'color' => 'text-[#F0EEE8]', 'bg' => 'border-[#0D4F60] bg-[#0D4F60]/40'],
    'listo'      => ['label' => 'Listo Entrega', 'icon' => 'package-check', 'color' => 'text-[#ED991F]', 'bg' => 'border-[#ED991F]/50 bg-[#ED991F]/15'],
    'entregado'  => ['label' => 'Instalado', 'icon' => 'check-circle-2', 'color' => 'text-[#F0EEE8]/60', 'bg' => 'border-[#103E4A] bg-[#103E4A]/40'],
];

$ordenFases = array_keys($fases);
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">

  <!-- Barra de Filtros y Búsqueda -->
  <div class="bg-white border border-[#0D4F60]/20 p-4 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
    
    <form method="GET" action="pedidos.php" class="relative flex-1 max-w-md">
      <i data-lucide="search" class="w-4 h-4 text-[#103E4A]/50 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
      <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Buscar por código, cliente, mueble o madera..." class="w-full pl-10 pr-4 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-xs sm:text-sm text-[#103E4A] placeholder-[#103E4A]/50 focus:bg-white focus:outline-none focus:border-[#ED991F] transition-all">
    </form>

    <div class="flex flex-wrap items-center gap-2.5">
      <form method="GET" action="pedidos.php" class="flex items-center gap-2">
        <select name="categoria" onchange="this.form.submit()" class="px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] font-medium focus:bg-white focus:outline-none focus:border-[#ED991F] cursor-pointer">
          <option value="todos" <?= $categoriaFiltro === 'todos' ? 'selected' : '' ?>>Todas las Categorías</option>
          <option value="Cocina" <?= $categoriaFiltro === 'Cocina' ? 'selected' : '' ?>>Cocinas</option>
          <option value="Closet" <?= $categoriaFiltro === 'Closet' ? 'selected' : '' ?>>Clósets</option>
          <option value="Mesa" <?= $categoriaFiltro === 'Mesa' ? 'selected' : '' ?>>Mesas</option>
          <option value="Puerta" <?= $categoriaFiltro === 'Puerta' ? 'selected' : '' ?>>Puertas</option>
          <option value="Oficina" <?= $categoriaFiltro === 'Oficina' ? 'selected' : '' ?>>Oficinas</option>
          <option value="Especial" <?= $categoriaFiltro === 'Especial' ? 'selected' : '' ?>>Especiales</option>
        </select>

        <select name="prioridad" onchange="this.form.submit()" class="px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] font-medium focus:bg-white focus:outline-none focus:border-[#ED991F] cursor-pointer">
          <option value="todos" <?= $prioridadFiltro === 'todos' ? 'selected' : '' ?>>Toda Prioridad</option>
          <option value="urgente" <?= $prioridadFiltro === 'urgente' ? 'selected' : '' ?>>Urgente</option>
          <option value="alta" <?= $prioridadFiltro === 'alta' ? 'selected' : '' ?>>Alta</option>
          <option value="media" <?= $prioridadFiltro === 'media' ? 'selected' : '' ?>>Media</option>
          <option value="baja" <?= $prioridadFiltro === 'baja' ? 'selected' : '' ?>>Baja</option>
        </select>
      </form>

      <button onclick="abrirModalNuevoPedido()" class="flex items-center gap-1.5 px-3.5 py-2 bg-gradient-to-r from-[#ED991F] to-[#E58D17] text-[#103E4A] font-extrabold text-xs rounded-xl shadow-md transition-all cursor-pointer hover:opacity-90">
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
        <div class="w-80 flex-shrink-0 bg-white border border-[#0D4F60]/20 rounded-2xl flex flex-col h-[750px] shadow-sm">
          <!-- Cabecera de Columna -->
          <div class="p-3.5 border-b border-[#0D4F60]/20 flex items-center justify-between rounded-t-2xl bg-[#103E4A] text-white">
            <div class="flex items-center gap-2">
              <i data-lucide="<?= $faseInfo['icon'] ?>" class="w-4 h-4 text-[#ED991F]"></i>
              <span class="font-bold text-xs uppercase tracking-wider font-display">
                <?= $faseInfo['label'] ?>
              </span>
            </div>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30">
              <?= count($pedidosEnFase) ?>
            </span>
          </div>

          <!-- Tarjetas de la Columna -->
          <div class="p-3 space-y-3 overflow-y-auto flex-1 no-scrollbar bg-[#F7F5EF]/60">
            <?php if (empty($pedidosEnFase)): ?>
              <div class="h-32 flex items-center justify-center text-xs text-[#103E4A]/50 border border-dashed border-[#0D4F60]/20 rounded-xl bg-white/50">
                Sin pedidos en esta fase
              </div>
            <?php else: ?>
              <?php foreach ($pedidosEnFase as $p): 
                  $porcentajePago = min(100, round(($p['anticipo'] / $p['precio']) * 100));
              ?>
                <div class="bg-white border border-[#0D4F60]/15 hover:border-[#ED991F] rounded-xl p-3.5 space-y-3 transition-all hover:shadow-md">
                  
                  <div class="flex items-center justify-between">
                    <span class="text-[11px] font-mono font-bold text-[#103E4A] bg-[#ED991F] px-2 py-0.5 rounded">
                      <?= htmlspecialchars($p['codigo']) ?>
                    </span>
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full <?= $p['prioridad'] === 'urgente' ? 'bg-[#ED991F]/20 text-[#E58D17] border border-[#ED991F]/40' : 'bg-[#103E4A] text-white' ?>">
                      <?= $p['prioridad'] ?>
                    </span>
                  </div>

                  <div>
                    <h4 class="text-xs sm:text-sm font-bold text-[#103E4A] hover:text-[#ED991F] transition-colors font-display">
                      <?= htmlspecialchars($p['proyecto']) ?>
                    </h4>
                    <p class="text-[11px] text-[#103E4A]/70 flex items-center gap-1 mt-0.5">
                      <i data-lucide="user" class="w-3 h-3 text-[#ED991F]"></i>
                      <?= htmlspecialchars($p['cliente']) ?>
                    </p>
                  </div>

                  <div class="bg-[#F7F5EF] rounded-lg p-2 text-[11px] space-y-1 text-[#103E4A] border border-[#0D4F60]/15">
                    <div class="flex items-center justify-between">
                      <span class="text-[#103E4A]/70">Medidas:</span>
                      <span class="font-mono font-semibold text-[#103E4A]"><?= $p['alto'] ?>x<?= $p['ancho'] ?>x<?= $p['fondo'] ?> cm</span>
                    </div>
                    <div class="text-[10px] text-[#103E4A]/70 truncate">
                      Madera: <span class="font-medium text-[#103E4A]"><?= htmlspecialchars($p['madera']) ?></span>
                    </div>
                  </div>

                  <div class="flex items-center justify-between text-[11px] text-[#103E4A]/70">
                    <div class="flex items-center gap-1">
                      <i data-lucide="calendar" class="w-3 h-3 text-[#ED991F]"></i>
                      <span><?= $p['fecha_entrega'] ?></span>
                    </div>
                    <span class="text-[10px] text-[#103E4A]/80 font-medium truncate max-w-[110px]"><?= $p['carpintero'] ?></span>
                  </div>

                  <!-- Barra de Anticipo -->
                  <div>
                    <div class="flex justify-between text-[10px] text-[#103E4A]/70 mb-1">
                      <span>Anticipo: <?= $porcentajePago ?>%</span>
                      <span class="font-bold text-[#103E4A] font-mono"><?= formatCOP($p['precio']) ?></span>
                    </div>
                    <div class="w-full bg-[#E5E1D8] h-1.5 rounded-full overflow-hidden">
                      <div class="h-full rounded-full <?= $porcentajePago === 100 ? 'bg-[#ED991F]' : 'bg-[#E58D17]' ?>" style="width: <?= $porcentajePago ?>%"></div>
                    </div>
                  </div>

                  <!-- Acciones Rápidas -->
                  <div class="pt-2 border-t border-[#0D4F60]/15 flex items-center justify-between gap-1">
                    <?php if ($prevFase): ?>
                      <a href="api/actualizar_estado.php?id=<?= $p['id'] ?>&estado=<?= $prevFase ?>" class="p-1.5 rounded-lg bg-[#F0EEE8] hover:bg-[#103E4A] hover:text-white text-[#103E4A] transition-all" title="Retroceder fase">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                      </a>
                    <?php else: ?><div class="w-7"></div><?php endif; ?>

                    <a href="imprimir_orden.php?id=<?= $p['id'] ?>" target="_blank" class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#103E4A] hover:bg-[#0D4F60] text-white text-[11px] font-semibold transition-all">
                      <i data-lucide="printer" class="w-3 h-3 text-[#ED991F]"></i>
                      <span>Ficha</span>
                    </a>

                    <?php if ($nextFase): ?>
                      <a href="api/actualizar_estado.php?id=<?= $p['id'] ?>&estado=<?= $nextFase ?>" class="p-1.5 rounded-lg bg-[#ED991F] hover:bg-[#E58D17] text-[#103E4A] font-bold shadow-sm transition-all" title="Avanzar fase">
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                      </a>
                    <?php else: ?>
                      <span class="p-1 text-[#ED991F]"><i data-lucide="check-circle-2" class="w-4 h-4"></i></span>
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

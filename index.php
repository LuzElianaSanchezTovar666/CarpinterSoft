<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$pedidos = getPedidos();
$inventario = getInventario();

// Estadísticas de Taller
$pedidosActivos = array_filter($pedidos, fn($p) => $p['estado'] !== 'entregado');
$totalVentas = array_sum(array_column($pedidos, 'precio'));
$totalAnticipos = array_sum(array_column($pedidos, 'anticipo'));
$saldoPendiente = $totalVentas - $totalAnticipos;

$materialesBajoStock = array_filter($inventario, fn($i) => $i['stock'] <= $i['min_stock']);

// Conteo por Fases de Taller
$estados = [
    'cotizacion' => ['label' => 'Cotización', 'icon' => 'clock', 'color' => 'text-amber-400 border-amber-500/30 bg-amber-500/5'],
    'diseno' => ['label' => 'Diseño & Planos', 'icon' => 'ruler', 'color' => 'text-indigo-400 border-indigo-500/30 bg-indigo-500/5'],
    'corte' => ['label' => 'En Corte', 'icon' => 'scissors', 'color' => 'text-sky-400 border-sky-500/30 bg-sky-500/5'],
    'ensamble' => ['label' => 'Ensamble', 'icon' => 'hammer', 'color' => 'text-orange-400 border-orange-500/30 bg-orange-500/5'],
    'acabado' => ['label' => 'Acabado / Laca', 'icon' => 'palette', 'color' => 'text-purple-400 border-purple-500/30 bg-purple-500/5'],
    'listo' => ['label' => 'Listo Entrega', 'icon' => 'package-check', 'color' => 'text-emerald-400 border-emerald-500/30 bg-emerald-500/5'],
    'entregado' => ['label' => 'Instalado', 'icon' => 'check-circle-2', 'color' => 'text-stone-400 border-stone-700 bg-stone-800/40'],
];

$conteoEstados = [];
foreach ($estados as $key => $val) {
    $conteoEstados[$key] = count(array_filter($pedidos, fn($p) => $p['estado'] === $key));
}

// Ordenar por fecha de entrega más próxima
usort($pedidosActivos, fn($a, $b) => strtotime($a['fecha_entrega']) - strtotime($b['fecha_entrega']));
$pedidosUrgentes = array_slice($pedidosActivos, 0, 4);
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8">

  <!-- Banner Principal -->
  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-stone-900 via-amber-950/40 to-stone-900 border border-stone-800 p-6 md:p-8 shadow-xl">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="space-y-2">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold">
          <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
          Taller de Carpintería & Ebanistería en Operación (PHP)
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight font-display">
          Control de Producción & Taller
        </h1>
        <p class="text-stone-400 text-sm max-w-2xl leading-relaxed">
          Supervisa el avance de corte, ensamble y laqueado de cada proyecto, optimiza el consumo de tableros de melamina y madera maciza, y programa entregas puntuales.
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <a href="despiece.php" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-stone-800 hover:bg-stone-700/80 text-stone-200 border border-stone-700 text-xs sm:text-sm font-semibold transition-all hover:border-amber-500/30">
          <i data-lucide="scissors" class="w-4 h-4 text-amber-400"></i>
          <span>Cotizar con Despiece</span>
        </a>
        <button onclick="abrirModalNuevoPedido()" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-stone-950 font-bold text-xs sm:text-sm shadow-lg shadow-amber-950/50 transition-all cursor-pointer">
          <i data-lucide="plus" class="w-4 h-4"></i>
          <span>Nuevo Proyecto</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Métricas Principales -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Muebles en Fabricación -->
    <div class="bg-stone-900/70 border border-stone-800 rounded-2xl p-5 hover:border-amber-500/30 transition-all">
      <div class="flex items-center justify-between">
        <span class="text-xs font-medium text-stone-400 uppercase tracking-wider">Muebles en Fabricación</span>
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
          <i data-lucide="hammer" class="w-5 h-5"></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-3xl font-extrabold text-white font-display">
          <?= count($pedidosActivos) ?>
        </div>
        <p class="text-xs text-stone-400 mt-1">
          <span class="text-amber-400 font-semibold"><?= $conteoEstados['corte'] + $conteoEstados['ensamble'] + $conteoEstados['acabado'] ?></span> en banco de trabajo
        </p>
      </div>
    </div>

    <!-- Facturación de Pedidos -->
    <div class="bg-stone-900/70 border border-stone-800 rounded-2xl p-5 hover:border-amber-500/30 transition-all">
      <div class="flex items-center justify-between">
        <span class="text-xs font-medium text-stone-400 uppercase tracking-wider">Valor de Pedidos</span>
        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
          <i data-lucide="dollar-sign" class="w-5 h-5"></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-2xl font-extrabold text-white font-display truncate">
          <?= formatCOP($totalVentas) ?>
        </div>
        <p class="text-xs text-stone-400 mt-1">
          Recaudado: <span class="text-emerald-400 font-medium"><?= formatCOP($totalAnticipos) ?></span>
        </p>
      </div>
    </div>

    <!-- Saldo por Cobrar -->
    <div class="bg-stone-900/70 border border-stone-800 rounded-2xl p-5 hover:border-amber-500/30 transition-all">
      <div class="flex items-center justify-between">
        <span class="text-xs font-medium text-stone-400 uppercase tracking-wider">Saldo Contra Entrega</span>
        <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
          <i data-lucide="clipboard-check" class="w-5 h-5"></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-2xl font-extrabold text-white font-display truncate">
          <?= formatCOP($saldoPendiente) ?>
        </div>
        <p class="text-xs text-stone-400 mt-1">
          Por cobrar a la instalación
        </p>
      </div>
    </div>

    <!-- Alerta de Inventario -->
    <a href="inventario.php" class="bg-stone-900/70 border border-stone-800 rounded-2xl p-5 hover:border-rose-500/40 transition-all group block">
      <div class="flex items-center justify-between">
        <span class="text-xs font-medium text-stone-400 uppercase tracking-wider">Stock Mínimo Alerta</span>
        <div class="w-10 h-10 rounded-xl <?= count($materialesBajoStock) > 0 ? 'bg-rose-500/10 border border-rose-500/30 text-rose-400' : 'bg-stone-800 text-stone-400' ?> flex items-center justify-center">
          <i data-lucide="alert-triangle" class="w-5 h-5"></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-3xl font-extrabold text-white font-display">
          <?= count($materialesBajoStock) ?>
        </div>
        <p class="text-xs text-rose-400 mt-1 font-medium flex items-center justify-between">
          <span><?= count($materialesBajoStock) > 0 ? 'Materiales críticos' : 'Stock en niveles óptimos' ?></span>
          <i data-lucide="arrow-right" class="w-3.5 h-3.5 opacity-60 group-hover:translate-x-1 transition-transform"></i>
        </p>
      </div>
    </a>
  </div>

  <!-- Embudo de Producción de Taller -->
  <div class="bg-stone-900/80 border border-stone-800 rounded-2xl p-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-base font-bold text-white font-display">Embudo de Producción en Taller</h2>
        <p class="text-xs text-stone-400">Distribución de muebles por etapa de fabricación</p>
      </div>
      <a href="pedidos.php" class="text-xs text-amber-400 hover:text-amber-300 font-medium flex items-center gap-1">
        Ver tablero Kanban <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
      </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
      <?php foreach ($estados as $key => $st): ?>
        <a href="pedidos.php?estado=<?= $key ?>" class="p-3.5 rounded-xl border <?= $st['color'] ?> flex flex-col justify-between hover:scale-[1.02] transition-transform">
          <div class="flex items-center justify-between text-xs font-medium">
            <span class="opacity-90"><?= $st['label'] ?></span>
            <i data-lucide="<?= $st['icon'] ?>" class="w-4 h-4 opacity-75"></i>
          </div>
          <div class="mt-3 text-2xl font-black text-white font-display">
            <?= $conteoEstados[$key] ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Grid Inferior: Proyectos Próximos & Materiales Críticos -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Proyectos Próximos a Entregar -->
    <div class="lg:col-span-2 bg-stone-900/80 border border-stone-800 rounded-2xl p-6">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-base font-bold text-white font-display">Proyectos Próximos a Entregar</h2>
          <p class="text-xs text-stone-400">Compromisos de entrega ordenados por fecha límite</p>
        </div>
        <span class="text-xs text-stone-500"><?= count($pedidosUrgentes) ?> proyectos</span>
      </div>

      <div class="divide-y divide-stone-800/80">
        <?php foreach ($pedidosUrgentes as $p): ?>
          <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-stone-800/30 p-2 rounded-xl transition-colors">
            <div class="space-y-1">
              <div class="flex items-center gap-2">
                <span class="text-xs font-mono font-bold text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded">
                  <?= htmlspecialchars($p['codigo']) ?>
                </span>
                <span class="text-[11px] font-semibold uppercase px-2 py-0.5 rounded-full <?= $p['prioridad'] === 'urgente' ? 'bg-rose-500/20 text-rose-300' : 'bg-amber-500/20 text-amber-300' ?>">
                  <?= $p['prioridad'] ?>
                </span>
                <span class="text-xs text-stone-400 hidden sm:inline">
                  • <?= htmlspecialchars($p['cliente']) ?>
                </span>
              </div>
              <div class="font-semibold text-stone-200 text-sm">
                <?= htmlspecialchars($p['proyecto']) ?>
              </div>
              <div class="text-xs text-stone-400">
                Material: <span class="text-stone-300"><?= htmlspecialchars($p['madera']) ?></span>
              </div>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-5">
              <div class="text-left sm:text-right">
                <div class="flex items-center gap-1.5 text-xs text-stone-300">
                  <i data-lucide="calendar" class="w-3.5 h-3.5 text-amber-400"></i>
                  <span><?= $p['fecha_entrega'] ?></span>
                </div>
                <div class="text-xs text-stone-400 mt-0.5">
                  <?= formatCOP($p['precio']) ?>
                </div>
              </div>
              <a href="imprimir_orden.php?id=<?= $p['id'] ?>" target="_blank" class="p-2 rounded-xl bg-stone-800 hover:bg-amber-600 text-stone-400 hover:text-stone-950 transition-colors" title="Imprimir Hoja de Taller">
                <i data-lucide="printer" class="w-4 h-4"></i>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Alertas de Stock Crítico -->
    <div class="bg-stone-900/80 border border-stone-800 rounded-2xl p-6 flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-base font-bold text-white font-display flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-400"></i>
            Alertas de Stock
          </h2>
          <a href="inventario.php" class="text-xs text-amber-400 hover:text-amber-300 font-medium">Gestionar</a>
        </div>

        <p class="text-xs text-stone-400 mb-4">Materiales que requieren reposición para evitar paradas:</p>

        <div class="space-y-3">
          <?php if (empty($materialesBajoStock)): ?>
            <div class="p-4 rounded-xl bg-stone-800/40 border border-stone-800 text-center text-xs text-stone-400">
              No hay materiales con stock crítico actualmente.
            </div>
          <?php else: ?>
            <?php foreach (array_slice($materialesBajoStock, 0, 4) as $mat): ?>
              <div class="p-3 rounded-xl bg-stone-800/50 border border-stone-700/60 flex items-center justify-between gap-2">
                <div>
                  <div class="text-xs font-semibold text-stone-200"><?= htmlspecialchars($mat['nombre']) ?></div>
                  <div class="text-[11px] text-stone-400 mt-0.5"><?= htmlspecialchars($mat['ubicacion']) ?></div>
                </div>
                <div class="text-right">
                  <div class="text-xs font-bold text-rose-400"><?= $mat['stock'] ?> <?= $mat['unidad'] ?></div>
                  <div class="text-[10px] text-stone-400">Mín: <?= $mat['min_stock'] ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <div class="mt-6 pt-4 border-t border-stone-800">
        <a href="inventario.php" class="w-full py-2.5 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-200 font-semibold text-xs flex items-center justify-center gap-2 transition-colors">
          <span>Ver Inventario Completo</span>
          <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
        </a>
      </div>
    </div>

  </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

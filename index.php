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
    'cotizacion' => ['label' => 'Cotización', 'icon' => 'clock', 'color' => 'text-[#ED991F] border-[#ED991F]/30 bg-[#ED991F]/10'],
    'diseno'     => ['label' => 'Diseño & Planos', 'icon' => 'ruler', 'color' => 'text-[#F7F5EF] border-[#0D4F60] bg-[#0D4F60]/40'],
    'corte'      => ['label' => 'En Corte', 'icon' => 'scissors', 'color' => 'text-[#ED991F] border-[#ED991F]/40 bg-[#0D4F60]/50'],
    'ensamble'   => ['label' => 'Ensamble', 'icon' => 'hammer', 'color' => 'text-[#E58D17] border-[#E58D17]/40 bg-[#0D4F60]/60'],
    'acabado'    => ['label' => 'Acabado / Laca', 'icon' => 'palette', 'color' => 'text-[#F0EEE8] border-[#0D4F60] bg-[#0D4F60]/40'],
    'listo'      => ['label' => 'Listo Entrega', 'icon' => 'package-check', 'color' => 'text-[#ED991F] border-[#ED991F]/50 bg-[#ED991F]/15'],
    'entregado'  => ['label' => 'Instalado', 'icon' => 'check-circle-2', 'color' => 'text-[#F0EEE8]/60 border-[#103E4A] bg-[#103E4A]/40'],
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
  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#103E4A] via-[#0D4F60] to-[#103E4A] border border-[#0D4F60] p-6 md:p-8 shadow-2xl">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="space-y-2">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#09252D]/80 border border-[#ED991F]/30 text-[#ED991F] text-xs font-semibold">
          <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#ED991F]"></i>
          Taller de Carpintería & Ebanistería en Operación (PHP)
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#FFFFFF] tracking-tight font-display">
          Control de Producción & Taller
        </h1>
        <p class="text-[#F0EEE8]/90 text-sm max-w-2xl leading-relaxed">
          Supervisa el avance de corte, ensamble y laqueado de cada proyecto, optimiza el consumo de tableros de melamina y madera maciza, y programa entregas puntuales.
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <a href="despiece.php" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#09252D] hover:bg-[#0D4F60] text-[#F7F5EF] border border-[#0D4F60] text-xs sm:text-sm font-semibold transition-all hover:border-[#ED991F]/40 shadow">
          <i data-lucide="scissors" class="w-4 h-4 text-[#ED991F]"></i>
          <span>Cotizar con Despiece</span>
        </a>
        <button onclick="abrirModalNuevoPedido()" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#ED991F] to-[#E58D17] text-[#103E4A] font-extrabold text-xs sm:text-sm shadow-lg shadow-orange-950/40 transition-all cursor-pointer hover:opacity-95 active:scale-95">
          <i data-lucide="plus" class="w-4 h-4 text-[#103E4A]"></i>
          <span>Nuevo Proyecto</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Métricas Principales -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Muebles en Fabricación -->
    <div class="bg-[#103E4A]/80 border border-[#0D4F60] rounded-2xl p-5 hover:border-[#ED991F]/40 transition-all shadow-md">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-[#F0EEE8]/80 uppercase tracking-wider">Muebles en Fabricación</span>
        <div class="w-10 h-10 rounded-xl bg-[#0D4F60] border border-[#ED991F]/30 flex items-center justify-center text-[#ED991F]">
          <i data-lucide="hammer" class="w-5 h-5 text-[#ED991F]"></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-3xl font-extrabold text-[#FFFFFF] font-display">
          <?= count($pedidosActivos) ?>
        </div>
        <p class="text-xs text-[#F0EEE8]/80 mt-1">
          <span class="text-[#ED991F] font-bold"><?= $conteoEstados['corte'] + $conteoEstados['ensamble'] + $conteoEstados['acabado'] ?></span> en banco de trabajo
        </p>
      </div>
    </div>

    <!-- Facturación de Pedidos -->
    <div class="bg-[#103E4A]/80 border border-[#0D4F60] rounded-2xl p-5 hover:border-[#ED991F]/40 transition-all shadow-md">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-[#F0EEE8]/80 uppercase tracking-wider">Valor de Pedidos</span>
        <div class="w-10 h-10 rounded-xl bg-[#0D4F60] border border-[#ED991F]/30 flex items-center justify-center text-[#ED991F]">
          <i data-lucide="dollar-sign" class="w-5 h-5 text-[#ED991F]"></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-2xl font-extrabold text-[#FFFFFF] font-display truncate">
          <?= formatCOP($totalVentas) ?>
        </div>
        <p class="text-xs text-[#F0EEE8]/80 mt-1">
          Recaudado: <span class="text-[#ED991F] font-semibold"><?= formatCOP($totalAnticipos) ?></span>
        </p>
      </div>
    </div>

    <!-- Saldo por Cobrar -->
    <div class="bg-[#103E4A]/80 border border-[#0D4F60] rounded-2xl p-5 hover:border-[#ED991F]/40 transition-all shadow-md">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-[#F0EEE8]/80 uppercase tracking-wider">Saldo Contra Entrega</span>
        <div class="w-10 h-10 rounded-xl bg-[#0D4F60] border border-[#ED991F]/30 flex items-center justify-center text-[#F7F5EF]">
          <i data-lucide="clipboard-check" class="w-5 h-5 text-[#F7F5EF]"></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-2xl font-extrabold text-[#FFFFFF] font-display truncate">
          <?= formatCOP($saldoPendiente) ?>
        </div>
        <p class="text-xs text-[#F0EEE8]/80 mt-1">
          Por cobrar a la instalación
        </p>
      </div>
    </div>

    <!-- Alerta de Inventario -->
    <a href="inventario.php" class="bg-[#103E4A]/80 border border-[#0D4F60] rounded-2xl p-5 hover:border-[#ED991F]/60 transition-all group block shadow-md">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-[#F0EEE8]/80 uppercase tracking-wider">Stock Mínimo Alerta</span>
        <div class="w-10 h-10 rounded-xl <?= count($materialesBajoStock) > 0 ? 'bg-[#E58D17]/20 border border-[#ED991F]/50 text-[#ED991F]' : 'bg-[#0D4F60] text-[#F0EEE8]' ?> flex items-center justify-center">
          <i data-lucide="alert-triangle" class="w-5 h-5"></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-3xl font-extrabold text-[#FFFFFF] font-display">
          <?= count($materialesBajoStock) ?>
        </div>
        <p class="text-xs text-[#ED991F] mt-1 font-semibold flex items-center justify-between">
          <span><?= count($materialesBajoStock) > 0 ? 'Materiales críticos' : 'Stock en niveles óptimos' ?></span>
          <i data-lucide="arrow-right" class="w-3.5 h-3.5 opacity-70 group-hover:translate-x-1 transition-transform"></i>
        </p>
      </div>
    </a>
  </div>

  <!-- Embudo de Producción de Taller -->
  <div class="bg-[#103E4A]/70 border border-[#0D4F60] rounded-2xl p-6 shadow-xl">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-base font-bold text-[#FFFFFF] font-display">Embudo de Producción en Taller</h2>
        <p class="text-xs text-[#F0EEE8]/70">Distribución de muebles por etapa de fabricación</p>
      </div>
      <a href="pedidos.php" class="text-xs text-[#ED991F] hover:text-[#E58D17] font-semibold flex items-center gap-1">
        Ver tablero Kanban <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
      </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
      <?php foreach ($estados as $key => $st): ?>
        <a href="pedidos.php?estado=<?= $key ?>" class="p-3.5 rounded-xl border <?= $st['color'] ?> flex flex-col justify-between hover:scale-[1.02] transition-transform shadow-sm">
          <div class="flex items-center justify-between text-xs font-semibold">
            <span class="opacity-95"><?= $st['label'] ?></span>
            <i data-lucide="<?= $st['icon'] ?>" class="w-4 h-4 opacity-85"></i>
          </div>
          <div class="mt-3 text-2xl font-black text-[#FFFFFF] font-display">
            <?= $conteoEstados[$key] ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Grid Inferior: Proyectos Próximos & Materiales Críticos -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Proyectos Próximos a Entregar -->
    <div class="lg:col-span-2 bg-[#103E4A]/70 border border-[#0D4F60] rounded-2xl p-6 shadow-xl">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-base font-bold text-[#FFFFFF] font-display">Proyectos Próximos a Entregar</h2>
          <p class="text-xs text-[#F0EEE8]/70">Compromisos de entrega ordenados por fecha límite</p>
        </div>
        <span class="text-xs text-[#F7F5EF]/70"><?= count($pedidosUrgentes) ?> proyectos</span>
      </div>

      <div class="divide-y divide-[#0D4F60]">
        <?php foreach ($pedidosUrgentes as $p): ?>
          <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#0D4F60]/40 p-2.5 rounded-xl transition-colors">
            <div class="space-y-1">
              <div class="flex items-center gap-2">
                <span class="text-xs font-mono font-bold text-[#103E4A] bg-[#ED991F] px-2 py-0.5 rounded">
                  <?= htmlspecialchars($p['codigo']) ?>
                </span>
                <span class="text-[11px] font-bold uppercase px-2 py-0.5 rounded-full <?= $p['prioridad'] === 'urgente' ? 'bg-[#E58D17]/30 text-[#ED991F] border border-[#ED991F]/40' : 'bg-[#0D4F60] text-[#F7F5EF]' ?>">
                  <?= $p['prioridad'] ?>
                </span>
                <span class="text-xs text-[#F0EEE8]/80 hidden sm:inline">
                  • <?= htmlspecialchars($p['cliente']) ?>
                </span>
              </div>
              <div class="font-bold text-[#FFFFFF] text-sm">
                <?= htmlspecialchars($p['proyecto']) ?>
              </div>
              <div class="text-xs text-[#F0EEE8]/70">
                Material: <span class="text-[#F7F5EF] font-medium"><?= htmlspecialchars($p['madera']) ?></span>
              </div>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-5">
              <div class="text-left sm:text-right">
                <div class="flex items-center gap-1.5 text-xs text-[#F7F5EF]">
                  <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#ED991F]"></i>
                  <span><?= $p['fecha_entrega'] ?></span>
                </div>
                <div class="text-xs text-[#ED991F] font-bold font-mono mt-0.5">
                  <?= formatCOP($p['precio']) ?>
                </div>
              </div>
              <a href="imprimir_orden.php?id=<?= $p['id'] ?>" target="_blank" class="p-2 rounded-xl bg-[#0D4F60] hover:bg-[#ED991F] text-[#F7F5EF] hover:text-[#103E4A] transition-colors border border-[#0D4F60]" title="Imprimir Hoja de Taller">
                <i data-lucide="printer" class="w-4 h-4"></i>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Alertas de Stock Crítico -->
    <div class="bg-[#103E4A]/70 border border-[#0D4F60] rounded-2xl p-6 flex flex-col justify-between shadow-xl">
      <div>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-base font-bold text-[#FFFFFF] font-display flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-[#ED991F]"></i>
            Alertas de Stock
          </h2>
          <a href="inventario.php" class="text-xs text-[#ED991F] hover:text-[#E58D17] font-semibold">Gestionar</a>
        </div>

        <p class="text-xs text-[#F0EEE8]/70 mb-4">Materiales que requieren reposición para evitar paradas:</p>

        <div class="space-y-3">
          <?php if (empty($materialesBajoStock)): ?>
            <div class="p-4 rounded-xl bg-[#09252D]/60 border border-[#0D4F60] text-center text-xs text-[#F0EEE8]/70">
              No hay materiales con stock crítico actualmente.
            </div>
          <?php else: ?>
            <?php foreach (array_slice($materialesBajoStock, 0, 4) as $mat): ?>
              <div class="p-3 rounded-xl bg-[#0D4F60]/50 border border-[#0D4F60] flex items-center justify-between gap-2">
                <div>
                  <div class="text-xs font-semibold text-[#FFFFFF]"><?= htmlspecialchars($mat['nombre']) ?></div>
                  <div class="text-[11px] text-[#F0EEE8]/70 mt-0.5"><?= htmlspecialchars($mat['ubicacion']) ?></div>
                </div>
                <div class="text-right">
                  <div class="text-xs font-bold text-[#ED991F] font-mono"><?= $mat['stock'] ?> <?= $mat['unidad'] ?></div>
                  <div class="text-[10px] text-[#F0EEE8]/60">Mín: <?= $mat['min_stock'] ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <div class="mt-6 pt-4 border-t border-[#0D4F60]">
        <a href="inventario.php" class="w-full py-2.5 rounded-xl bg-[#0D4F60] hover:bg-[#1A677B] text-[#FFFFFF] font-bold text-xs flex items-center justify-center gap-2 transition-colors border border-[#0D4F60]">
          <span>Ver Inventario Completo</span>
          <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-[#ED991F]"></i>
        </a>
      </div>
    </div>

  </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

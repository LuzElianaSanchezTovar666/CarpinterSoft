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

// Conteo por Fases de Taller (Máquina de Estados)
$estados = [
    'cotizacion' => ['label' => 'Cotización', 'icon' => 'clock', 'badge' => 'text-[#ED991F] bg-[#ED991F]/10 border-[#ED991F]/30'],
    'diseno'     => ['label' => 'Diseño & 3D', 'icon' => 'ruler', 'badge' => 'text-[#F7F5EF] bg-[#0D4F60]/40 border-[#0D4F60]'],
    'corte'      => ['label' => 'En Corte', 'icon' => 'scissors', 'badge' => 'text-[#ED991F] bg-[#0D4F60]/50 border-[#ED991F]/40'],
    'ensamble'   => ['label' => 'Ensamble', 'icon' => 'hammer', 'badge' => 'text-[#E58D17] bg-[#0D4F60]/60 border-[#E58D17]/40'],
    'acabado'    => ['label' => 'Laca / Acabado', 'icon' => 'palette', 'badge' => 'text-[#F0EEE8] bg-[#0D4F60]/40 border-[#0D4F60]'],
    'listo'      => ['label' => 'Listo Entrega', 'icon' => 'package-check', 'badge' => 'text-[#ED991F] bg-[#ED991F]/15 border-[#ED991F]/50'],
    'entregado'  => ['label' => 'Instalado', 'icon' => 'check-circle-2', 'badge' => 'text-[#F0EEE8]/60 bg-[#103E4A]/40 border-[#103E4A]'],
];

$conteoEstados = [];
foreach ($estados as $key => $val) {
    $conteoEstados[$key] = count(array_filter($pedidos, fn($p) => $p['estado'] === $key));
}

// Ordenar por fecha de entrega más próxima
usort($pedidosActivos, fn($a, $b) => strtotime($a['fecha_entrega']) - strtotime($b['fecha_entrega']));
$pedidosUrgentes = array_slice($pedidosActivos, 0, 4);
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-3 sm:px-6 lg:px-8 py-5 space-y-5">

  <!-- ========================================================
       BENTO GRID SUPERIOR: CONSOLA RETRO-FUTURISTA & ESTUDIO 3D
       ======================================================== -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">
    
    <!-- Bento 1: Consola Central de Control de Taller (Col 8) -->
    <div class="lg:col-span-8 bento-card corner-mark p-6 flex flex-col justify-between shadow-2xl relative overflow-hidden group">
      
      <!-- Fondo decorativo técnico sutil -->
      <div class="absolute -right-16 -top-16 w-56 h-56 bg-[#ED991F]/5 rounded-full blur-3xl pointer-events-none"></div>
      
      <div class="space-y-3 relative z-10">
        <!-- Telemetría Retro-Tech -->
        <div class="flex flex-wrap items-center gap-2">
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-[#09252D] text-[#ED991F] text-[10px] font-mono font-bold border border-[#ED991F]/40 tracking-wider uppercase">
            <span class="w-1.5 h-1.5 rounded-full bg-[#ED991F] animate-pulse"></span>
            SYS//OP-2026 • TALLER EN VIVO
          </span>
          <span class="text-[11px] font-mono text-[#F0EEE8]/60 hidden sm:inline">
            BANCO PRINCIPAL: CALIBRADO (12.4% HUMEDAD)
          </span>
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#FFFFFF] tracking-tight font-display">
          Consola Central de Producción & Ebanistería
        </h1>
        <p class="text-[#F0EEE8]/80 text-xs sm:text-sm max-w-2xl leading-relaxed">
          Supervisa el ciclo de vida de cada mueble a medida, optimiza el consumo volumétrico de tableros y acelera la entrega de órdenes con trazabilidad en tiempo real.
        </p>
      </div>

      <!-- Barra de Acciones Directas Retro-Futuristas (Simples y Claras) -->
      <div class="pt-5 mt-4 border-t border-[#0D4F60]/80 flex flex-wrap items-center justify-between gap-3 relative z-10">
        <div class="flex flex-wrap items-center gap-2.5">
          <a href="diseno3d.php" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#ED991F] to-[#E58D17] text-[#103E4A] font-extrabold text-xs sm:text-sm shadow-lg shadow-orange-950/40 hover:opacity-95 transition-all active:scale-95 cursor-pointer">
            <i data-lucide="box" class="w-4 h-4 text-[#103E4A]"></i>
            <span>Estudio 3D (WebGL)</span>
          </a>
          <a href="despiece.php" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#09252D] hover:bg-[#0D4F60] text-[#F7F5EF] border border-[#0D4F60] text-xs sm:text-sm font-semibold transition-all hover:border-[#ED991F]/50">
            <i data-lucide="scissors" class="w-4 h-4 text-[#ED991F]"></i>
            <span>Calculadora & Despiece</span>
          </a>
        </div>

        <button onclick="abrirModalNuevoPedido()" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-[#0D4F60]/70 hover:bg-[#0D4F60] text-[#F7F5EF] border border-[#ED991F]/30 text-xs font-bold transition-all cursor-pointer">
          <i data-lucide="plus-circle" class="w-4 h-4 text-[#ED991F]"></i>
          <span>+ Nuevo Proyecto</span>
        </button>
      </div>

    </div>

    <!-- Bento 2: Módulo Destacado 3D & Simulación (Col 4) -->
    <a href="diseno3d.php" class="lg:col-span-4 bento-card corner-mark p-6 flex flex-col justify-between group hover:border-[#ED991F] transition-all shadow-xl block cursor-pointer">
      <div>
        <div class="flex items-center justify-between mb-3">
          <span class="text-[10px] font-mono uppercase font-bold text-[#ED991F] bg-[#09252D] px-2.5 py-1 rounded-md border border-[#ED991F]/30 tracking-wider">
            CORE//01 • 3D ENGINE
          </span>
          <div class="w-9 h-9 rounded-xl bg-[#0D4F60] border border-[#ED991F]/40 flex items-center justify-center text-[#ED991F] group-hover:scale-110 transition-transform">
            <i data-lucide="box" class="w-5 h-5 text-[#ED991F]"></i>
          </div>
        </div>

        <h3 class="text-base sm:text-lg font-bold text-[#FFFFFF] font-display group-hover:text-[#ED991F] transition-colors">
          Modelador Paramétrico 3D
        </h3>
        <p class="text-xs text-[#F0EEE8]/70 mt-1 leading-relaxed">
          Simula aperturas de puertas, vista explotada de despiece en banco y acabados de roble o melamina en tiempo real con Three.js.
        </p>
      </div>

      <!-- Mini Preview Gráfico Retro-Tech -->
      <div class="mt-4 pt-3 border-t border-[#0D4F60]/80 flex items-center justify-between text-xs text-[#ED991F] font-bold">
        <span class="flex items-center gap-1.5 font-mono text-[11px]">
          <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
          Lanzar Visor 3D WebGL
        </span>
        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1.5 transition-transform"></i>
      </div>
    </a>

  </div>

  <!-- ========================================================
       BENTO GRID DE MÉTRICAS (KPIs TIPO INSTRUMENTO ANALÓGICO-DIGITAL)
       ======================================================== -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    
    <!-- Bento KPI 1: Muebles en Banco -->
    <div class="bento-card p-5 flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <span class="text-[10px] font-mono uppercase font-bold text-[#F0EEE8]/60 tracking-wider">
          MTR//01 • FABRICACIÓN
        </span>
        <div class="w-8 h-8 rounded-lg bg-[#0D4F60] border border-[#ED991F]/30 flex items-center justify-center text-[#ED991F]">
          <i data-lucide="hammer" class="w-4 h-4 text-[#ED991F]"></i>
        </div>
      </div>
      <div class="mt-3">
        <div class="text-3xl font-extrabold text-[#FFFFFF] font-display flex items-baseline gap-2">
          <span><?= count($pedidosActivos) ?></span>
          <span class="text-xs font-normal text-[#F0EEE8]/60 font-sans">muebles activos</span>
        </div>
        <div class="w-full bg-[#09252D] h-1.5 rounded-full mt-3 overflow-hidden">
          <div class="bg-gradient-to-r from-[#ED991F] to-[#E58D17] h-full rounded-full" style="width: <?= min(100, (count($pedidosActivos) / 10) * 100) ?>%"></div>
        </div>
        <p class="text-[11px] text-[#F0EEE8]/70 mt-2 font-mono">
          <span class="text-[#ED991F] font-bold"><?= $conteoEstados['corte'] + $conteoEstados['ensamble'] + $conteoEstados['acabado'] ?></span> en banco de trabajo
        </p>
      </div>
    </div>

    <!-- Bento KPI 2: Valor Total de Órdenes -->
    <div class="bento-card p-5 flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <span class="text-[10px] font-mono uppercase font-bold text-[#F0EEE8]/60 tracking-wider">
          MTR//02 • FACTURACIÓN
        </span>
        <div class="w-8 h-8 rounded-lg bg-[#0D4F60] border border-[#ED991F]/30 flex items-center justify-center text-[#ED991F]">
          <i data-lucide="dollar-sign" class="w-4 h-4 text-[#ED991F]"></i>
        </div>
      </div>
      <div class="mt-3">
        <div class="text-2xl font-extrabold text-[#FFFFFF] font-display truncate">
          <?= formatCOP($totalVentas) ?>
        </div>
        <div class="w-full bg-[#09252D] h-1.5 rounded-full mt-3 overflow-hidden">
          <div class="bg-gradient-to-r from-emerald-500 to-emerald-400 h-full rounded-full" style="width: <?= $totalVentas > 0 ? min(100, ($totalAnticipos / $totalVentas) * 100) : 0 ?>%"></div>
        </div>
        <p class="text-[11px] text-[#F0EEE8]/70 mt-2 font-mono">
          Recaudado: <span class="text-emerald-400 font-bold"><?= formatCOP($totalAnticipos) ?></span>
        </p>
      </div>
    </div>

    <!-- Bento KPI 3: Saldo por Cobrar -->
    <div class="bento-card p-5 flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <span class="text-[10px] font-mono uppercase font-bold text-[#F0EEE8]/60 tracking-wider">
          MTR//03 • POR COBRAR
        </span>
        <div class="w-8 h-8 rounded-lg bg-[#0D4F60] border border-[#ED991F]/30 flex items-center justify-center text-[#F7F5EF]">
          <i data-lucide="receipt" class="w-4 h-4 text-[#F7F5EF]"></i>
        </div>
      </div>
      <div class="mt-3">
        <div class="text-2xl font-extrabold text-[#ED991F] font-display truncate">
          <?= formatCOP($saldoPendiente) ?>
        </div>
        <div class="w-full bg-[#09252D] h-1.5 rounded-full mt-3 overflow-hidden">
          <div class="bg-gradient-to-r from-[#ED991F] to-[#E58D17] h-full rounded-full" style="width: 70%"></div>
        </div>
        <p class="text-[11px] text-[#F0EEE8]/70 mt-2 font-mono">
          Pendiente a instalación final
        </p>
      </div>
    </div>

    <!-- Bento KPI 4: Alerta de Inventario Crítico -->
    <a href="inventario.php" class="bento-card p-5 flex flex-col justify-between group block">
      <div class="flex items-center justify-between">
        <span class="text-[10px] font-mono uppercase font-bold text-[#F0EEE8]/60 tracking-wider">
          MTR//04 • ALMACÉN
        </span>
        <div class="w-8 h-8 rounded-lg <?= count($materialesBajoStock) > 0 ? 'bg-[#E58D17]/20 border border-[#ED991F]/50 text-[#ED991F]' : 'bg-[#0D4F60] text-[#F0EEE8]' ?> flex items-center justify-center">
          <i data-lucide="alert-triangle" class="w-4 h-4"></i>
        </div>
      </div>
      <div class="mt-3">
        <div class="text-3xl font-extrabold text-[#FFFFFF] font-display flex items-baseline gap-2">
          <span><?= count($materialesBajoStock) ?></span>
          <span class="text-xs font-normal text-[#F0EEE8]/60 font-sans">críticos</span>
        </div>
        <div class="w-full bg-[#09252D] h-1.5 rounded-full mt-3 overflow-hidden">
          <div class="bg-gradient-to-r from-[#E58D17] to-rose-500 h-full rounded-full" style="width: <?= min(100, count($materialesBajoStock) * 25) ?>%"></div>
        </div>
        <p class="text-[11px] text-[#ED991F] mt-2 font-semibold flex items-center justify-between">
          <span><?= count($materialesBajoStock) > 0 ? 'Reposición requerida' : 'Stock en niveles óptimos' ?></span>
          <i data-lucide="arrow-right" class="w-3.5 h-3.5 opacity-70 group-hover:translate-x-1 transition-transform"></i>
        </p>
      </div>
    </a>

  </div>

  <!-- ========================================================
       BENTO: PIPELINE DE PRODUCCIÓN (MÁQUINA DE ESTADOS 7 FASES)
       ======================================================== -->
  <div class="bento-card corner-mark p-5 shadow-xl">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 border-b border-[#0D4F60]/80 pb-3">
      <div>
        <div class="flex items-center gap-2">
          <span class="text-[10px] font-mono uppercase font-bold text-[#ED991F] bg-[#09252D] px-2 py-0.5 rounded border border-[#ED991F]/30">
            WORKFLOW//07
          </span>
          <h2 class="text-base font-bold text-[#FFFFFF] font-display">
            Pipeline de Manufactura en Taller
          </h2>
        </div>
        <p class="text-xs text-[#F0EEE8]/70 mt-0.5">Avance de pedidos ordenados por estación de trabajo</p>
      </div>
      <a href="pedidos.php" class="text-xs text-[#ED991F] hover:text-[#E58D17] font-semibold flex items-center gap-1 self-start sm:self-auto">
        <span>Abrir Tablero Kanban</span>
        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
      </a>
    </div>

    <!-- Riel de 7 Fases Bento -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2.5">
      <?php foreach ($estados as $key => $st): ?>
        <a href="pedidos.php?estado=<?= $key ?>" class="p-3 rounded-xl border <?= $st['badge'] ?> flex flex-col justify-between hover:scale-[1.03] transition-all group shadow-sm bg-[#09252D]/80">
          <div class="flex items-center justify-between text-[11px] font-semibold">
            <span class="truncate"><?= $st['label'] ?></span>
            <i data-lucide="<?= $st['icon'] ?>" class="w-3.5 h-3.5 opacity-80 group-hover:text-[#ED991F] transition-colors"></i>
          </div>
          <div class="mt-3 text-2xl font-black text-[#FFFFFF] font-mono group-hover:text-[#ED991F] transition-colors">
            <?= $conteoEstados[$key] ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ========================================================
       BENTO INFERIOR: RADAR DE PROYECTOS & ASESOR TÉCNICO
       ======================================================== -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
    
    <!-- Bento 1: Radar de Proyectos Próximos (Col 8) -->
    <div class="lg:col-span-8 bento-card corner-mark p-5 shadow-xl space-y-4">
      <div class="flex items-center justify-between border-b border-[#0D4F60]/80 pb-3">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-[10px] font-mono uppercase font-bold text-[#ED991F] bg-[#09252D] px-2 py-0.5 rounded border border-[#ED991F]/30">
              QUEUE//RADAR
            </span>
            <h2 class="text-base font-bold text-[#FFFFFF] font-display">
              Compromisos & Fechas de Entrega
            </h2>
          </div>
          <p class="text-xs text-[#F0EEE8]/70 mt-0.5">Órdenes con fecha límite más próxima</p>
        </div>
        <span class="text-xs font-mono text-[#ED991F] font-bold"><?= count($pedidosUrgentes) ?> proyectos</span>
      </div>

      <div class="divide-y divide-[#0D4F60]/70">
        <?php foreach ($pedidosUrgentes as $p): ?>
          <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#0D4F60]/30 p-2.5 rounded-xl transition-colors">
            <div class="space-y-1">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-mono font-bold text-[#103E4A] bg-[#ED991F] px-2 py-0.5 rounded shadow-sm">
                  <?= htmlspecialchars($p['codigo']) ?>
                </span>
                <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded-full <?= $p['prioridad'] === 'urgente' ? 'bg-[#E58D17]/30 text-[#ED991F] border border-[#ED991F]/40' : 'bg-[#0D4F60] text-[#F7F5EF]' ?>">
                  <?= $p['prioridad'] ?>
                </span>
                <span class="text-xs text-[#F0EEE8]/80 font-medium">
                  <?= htmlspecialchars($p['cliente']) ?>
                </span>
              </div>
              <div class="font-bold text-[#FFFFFF] text-sm">
                <?= htmlspecialchars($p['proyecto']) ?>
              </div>
              <div class="text-[11px] text-[#F0EEE8]/70">
                Material: <span class="text-[#F7F5EF] font-medium"><?= htmlspecialchars($p['madera']) ?></span>
              </div>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-4 flex-shrink-0">
              <div class="text-left sm:text-right">
                <div class="flex items-center gap-1.5 text-xs text-[#F7F5EF]">
                  <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#ED991F]"></i>
                  <span class="font-mono"><?= $p['fecha_entrega'] ?></span>
                </div>
                <div class="text-xs text-[#ED991F] font-bold font-mono mt-0.5">
                  <?= formatCOP($p['precio']) ?>
                </div>
              </div>
              <a href="imprimir_orden.php?id=<?= $p['id'] ?>" target="_blank" class="p-2.5 rounded-xl bg-[#0D4F60] hover:bg-[#ED991F] text-[#F7F5EF] hover:text-[#103E4A] transition-colors border border-[#0D4F60]" title="Imprimir Hoja de Taller">
                <i data-lucide="printer" class="w-4 h-4"></i>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Bento 2: Terminal CarpinterIA & Alertas de Stock (Col 4) -->
    <div class="lg:col-span-4 space-y-4">
      
      <!-- Terminal Rápido CarpinterIA -->
      <div class="bento-card p-4 sm:p-5 shadow-xl space-y-3">
        <div class="flex items-center justify-between border-b border-[#0D4F60]/80 pb-2.5">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-[#ED991F] to-[#E58D17] flex items-center justify-center text-[#103E4A]">
              <i data-lucide="bot" class="w-4 h-4 text-[#103E4A]"></i>
            </div>
            <h3 class="text-xs font-bold text-[#FFFFFF] font-display">CarpinterIA Terminal</h3>
          </div>
          <span class="text-[10px] font-mono text-[#ED991F] bg-[#09252D] px-2 py-0.5 rounded border border-[#ED991F]/30">
            ONLINE
          </span>
        </div>

        <p class="text-xs text-[#F0EEE8]/80 leading-relaxed">
          Consulta al instante holguras de correderas, descuentos de tapacanto y compatibilidad de maderas para el taller.
        </p>

        <button onclick="abrirModalCarpinterIA()" class="w-full py-2 px-3 rounded-xl bg-[#0D4F60] hover:bg-[#1A677B] text-[#ED991F] border border-[#ED991F]/30 text-xs font-bold flex items-center justify-center gap-2 transition-colors cursor-pointer">
          <i data-lucide="terminal" class="w-3.5 h-3.5"></i>
          <span>Abrir Asesor Técnico</span>
        </button>
      </div>

      <!-- Diagnóstico de Materiales Críticos -->
      <div class="bento-card p-4 sm:p-5 shadow-xl space-y-3">
        <div class="flex items-center justify-between border-b border-[#0D4F60]/80 pb-2.5">
          <div class="flex items-center gap-1.5">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-[#ED991F]"></i>
            <h3 class="text-xs font-bold text-[#FFFFFF] font-display">Stock Crítico</h3>
          </div>
          <a href="inventario.php" class="text-[11px] font-semibold text-[#ED991F] hover:underline">Ver todo</a>
        </div>

        <div class="space-y-2">
          <?php if (empty($materialesBajoStock)): ?>
            <div class="p-3 rounded-xl bg-[#09252D]/70 text-center text-xs text-[#F0EEE8]/70">
              Almacén sin faltantes críticos.
            </div>
          <?php else: ?>
            <?php foreach (array_slice($materialesBajoStock, 0, 3) as $mat): ?>
              <div class="p-2.5 rounded-xl bg-[#09252D]/80 border border-[#0D4F60] flex items-center justify-between text-xs">
                <div>
                  <div class="font-semibold text-[#FFFFFF] truncate max-w-[140px]"><?= htmlspecialchars($mat['nombre']) ?></div>
                  <div class="text-[10px] text-[#F0EEE8]/60"><?= htmlspecialchars($mat['ubicacion']) ?></div>
                </div>
                <div class="text-right font-mono">
                  <div class="font-bold text-[#ED991F]"><?= $mat['stock'] ?> <?= $mat['unidad'] ?></div>
                  <div class="text-[10px] text-[#F0EEE8]/50">Mín: <?= $mat['min_stock'] ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

    </div>

  </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

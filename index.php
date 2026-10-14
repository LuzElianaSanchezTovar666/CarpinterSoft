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
    'cotizacion' => ['label' => 'Cotización', 'icon' => 'clock', 'badge' => 'text-[#ED991F] bg-[#ED991F]/10 border-[#ED991F]/40'],
    'diseno'     => ['label' => 'Diseño & 3D', 'icon' => 'ruler', 'badge' => 'text-[#103E4A] bg-[#F0EEE8] border-[#0D4F60]/20'],
    'corte'      => ['label' => 'En Corte', 'icon' => 'scissors', 'badge' => 'text-[#E58D17] bg-[#ED991F]/15 border-[#ED991F]/40'],
    'ensamble'   => ['label' => 'Ensamble', 'icon' => 'hammer', 'badge' => 'text-[#103E4A] bg-[#F7F5EF] border-[#0D4F60]/30'],
    'acabado'    => ['label' => 'Laca / Acabado', 'icon' => 'palette', 'badge' => 'text-[#0D4F60] bg-[#F0EEE8] border-[#0D4F60]/20'],
    'listo'      => ['label' => 'Listo Entrega', 'icon' => 'package-check', 'badge' => 'text-[#ED991F] bg-[#ED991F]/15 border-[#ED991F]/50'],
    'entregado'  => ['label' => 'Instalado', 'icon' => 'check-circle-2', 'badge' => 'text-slate-500 bg-slate-100 border-slate-200'],
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
       (FONDO BLANCO, ALTO CONTRASTE AZUL OSCURO Y NARANJA)
       ======================================================== -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">
    
    <!-- Bento 1: Consola Central de Control de Taller (Col 8) -->
    <div class="lg:col-span-8 bento-card corner-mark p-6 flex flex-col justify-between shadow-xl relative overflow-hidden group">
      
      <!-- Fondo decorativo técnico sutil -->
      <div class="absolute -right-16 -top-16 w-56 h-56 bg-[#ED991F]/10 rounded-full blur-3xl pointer-events-none"></div>
      
      <div class="space-y-3 relative z-10">
        <!-- Telemetría Retro-Tech en Alto Contraste -->
        <div class="flex flex-wrap items-center gap-2">
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-[#103E4A] text-[#ED991F] text-[10px] font-mono font-bold tracking-wider uppercase shadow-sm">
            <span class="w-1.5 h-1.5 rounded-full bg-[#ED991F] animate-pulse"></span>
            SYS//OP-2026 • TALLER ACTIVO
          </span>
          <span class="text-[11px] font-mono font-semibold text-[#0D4F60] bg-[#F7F5EF] px-2 py-0.5 rounded border border-[#0D4F60]/15 hidden sm:inline">
            BANCO PRINCIPAL: CALIBRADO (12.4% HUMEDAD)
          </span>
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#103E4A] tracking-tight font-display">
          Consola Central de Producción & Ebanistería
        </h1>
        <p class="text-[#0D4F60]/80 text-xs sm:text-sm max-w-2xl leading-relaxed font-medium">
          Supervisa el ciclo de vida de cada mueble a medida, optimiza el consumo volumétrico de tableros y acelera la entrega de órdenes con trazabilidad en tiempo real.
        </p>
      </div>

      <!-- Barra de Acciones Directas Retro-Futuristas (Simples, Claras y de Alto Contraste) -->
      <div class="pt-5 mt-4 border-t border-[#0D4F60]/15 flex flex-wrap items-center justify-between gap-3 relative z-10">
        <div class="flex flex-wrap items-center gap-2.5">
          <!-- Botón Primario Naranja -->
          <a href="diseno3d.php" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#ED991F] to-[#E58D17] hover:from-[#F3B14F] hover:to-[#ED991F] text-white font-extrabold text-xs sm:text-sm shadow-md shadow-orange-500/25 transition-all active:scale-95 cursor-pointer">
            <i data-lucide="box" class="w-4 h-4 text-white"></i>
            <span>Estudio 3D (WebGL)</span>
          </a>
          <!-- Botón Secundario Azul Oscuro / Crema -->
          <a href="despiece.php" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#F7F5EF] hover:bg-[#E2E8F0] text-[#103E4A] border border-[#0D4F60]/20 text-xs sm:text-sm font-bold transition-all shadow-sm">
            <i data-lucide="scissors" class="w-4 h-4 text-[#ED991F]"></i>
            <span>Calculadora & Despiece</span>
          </a>
        </div>

        <!-- Botón Azul Oscuro Sólido -->
        <button onclick="abrirModalNuevoPedido()" class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-[#103E4A] hover:bg-[#0D4F60] text-white text-xs font-bold transition-all shadow-sm cursor-pointer">
          <i data-lucide="plus-circle" class="w-4 h-4 text-[#ED991F]"></i>
          <span>+ Nuevo Proyecto</span>
        </button>
      </div>

    </div>

    <!-- Bento 2: Módulo Destacado 3D & Simulación (Col 4) -->
    <a href="diseno3d.php" class="lg:col-span-4 bento-card corner-mark p-6 flex flex-col justify-between group hover:border-[#ED991F] transition-all shadow-xl block cursor-pointer bg-white">
      <div>
        <div class="flex items-center justify-between mb-3">
          <span class="text-[10px] font-mono uppercase font-bold text-[#E58D17] bg-[#ED991F]/15 px-2.5 py-1 rounded-md border border-[#ED991F]/30 tracking-wider">
            CORE//01 • 3D ENGINE
          </span>
          <div class="w-9 h-9 rounded-xl bg-[#103E4A] text-[#ED991F] flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm">
            <i data-lucide="box" class="w-5 h-5 text-[#ED991F]"></i>
          </div>
        </div>

        <h3 class="text-base sm:text-lg font-bold text-[#103E4A] font-display group-hover:text-[#ED991F] transition-colors">
          Modelador Paramétrico 3D
        </h3>
        <p class="text-xs text-[#0D4F60]/80 mt-1 leading-relaxed font-medium">
          Simula aperturas de puertas, vista explotada de despiece en banco y texturas de roble o melamina en tiempo real con Three.js.
        </p>
      </div>

      <!-- Mini Preview Gráfico Retro-Tech -->
      <div class="mt-4 pt-3 border-t border-[#0D4F60]/15 flex items-center justify-between text-xs text-[#E58D17] font-bold">
        <span class="flex items-center gap-1.5 font-mono text-[11px]">
          <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#ED991F]"></i>
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
    <div class="bento-card p-5 flex flex-col justify-between bg-white">
      <div class="flex items-center justify-between">
        <span class="text-[10px] font-mono uppercase font-bold text-[#0D4F60]/70 tracking-wider">
          MTR//01 • FABRICACIÓN
        </span>
        <div class="w-8 h-8 rounded-lg bg-[#F0EEE8] border border-[#0D4F60]/20 flex items-center justify-center text-[#103E4A]">
          <i data-lucide="hammer" class="w-4 h-4 text-[#ED991F]"></i>
        </div>
      </div>
      <div class="mt-3">
        <div class="text-3xl font-extrabold text-[#103E4A] font-display flex items-baseline gap-2">
          <span><?= count($pedidosActivos) ?></span>
          <span class="text-xs font-semibold text-[#0D4F60]/70 font-sans">muebles activos</span>
        </div>
        <div class="w-full bg-[#F0EEE8] h-2 rounded-full mt-3 overflow-hidden">
          <div class="bg-gradient-to-r from-[#ED991F] to-[#E58D17] h-full rounded-full" style="width: <?= min(100, (count($pedidosActivos) / 10) * 100) ?>%"></div>
        </div>
        <p class="text-[11px] text-[#0D4F60] mt-2 font-mono font-medium">
          <span class="text-[#E58D17] font-bold"><?= $conteoEstados['corte'] + $conteoEstados['ensamble'] + $conteoEstados['acabado'] ?></span> en banco de trabajo
        </p>
      </div>
    </div>

    <!-- Bento KPI 2: Valor Total de Órdenes -->
    <div class="bento-card p-5 flex flex-col justify-between bg-white">
      <div class="flex items-center justify-between">
        <span class="text-[10px] font-mono uppercase font-bold text-[#0D4F60]/70 tracking-wider">
          MTR//02 • FACTURACIÓN
        </span>
        <div class="w-8 h-8 rounded-lg bg-[#F0EEE8] border border-[#0D4F60]/20 flex items-center justify-center text-[#103E4A]">
          <i data-lucide="dollar-sign" class="w-4 h-4 text-[#ED991F]"></i>
        </div>
      </div>
      <div class="mt-3">
        <div class="text-2xl font-extrabold text-[#103E4A] font-display truncate">
          <?= formatCOP($totalVentas) ?>
        </div>
        <div class="w-full bg-[#F0EEE8] h-2 rounded-full mt-3 overflow-hidden">
          <div class="bg-gradient-to-r from-emerald-500 to-emerald-400 h-full rounded-full" style="width: <?= $totalVentas > 0 ? min(100, ($totalAnticipos / $totalVentas) * 100) : 0 ?>%"></div>
        </div>
        <p class="text-[11px] text-[#0D4F60] mt-2 font-mono font-medium">
          Recaudado: <span class="text-emerald-700 font-bold"><?= formatCOP($totalAnticipos) ?></span>
        </p>
      </div>
    </div>

    <!-- Bento KPI 3: Saldo por Cobrar -->
    <div class="bento-card p-5 flex flex-col justify-between bg-white">
      <div class="flex items-center justify-between">
        <span class="text-[10px] font-mono uppercase font-bold text-[#0D4F60]/70 tracking-wider">
          MTR//03 • POR COBRAR
        </span>
        <div class="w-8 h-8 rounded-lg bg-[#F0EEE8] border border-[#0D4F60]/20 flex items-center justify-center text-[#103E4A]">
          <i data-lucide="receipt" class="w-4 h-4 text-[#ED991F]"></i>
        </div>
      </div>
      <div class="mt-3">
        <div class="text-2xl font-extrabold text-[#E58D17] font-display truncate">
          <?= formatCOP($saldoPendiente) ?>
        </div>
        <div class="w-full bg-[#F0EEE8] h-2 rounded-full mt-3 overflow-hidden">
          <div class="bg-gradient-to-r from-[#ED991F] to-[#E58D17] h-full rounded-full" style="width: 70%"></div>
        </div>
        <p class="text-[11px] text-[#0D4F60] mt-2 font-mono font-medium">
          Liquidación contra entrega
        </p>
      </div>
    </div>

    <!-- Bento KPI 4: Alerta de Inventario Crítico -->
    <a href="inventario.php" class="bento-card p-5 flex flex-col justify-between group block bg-white">
      <div class="flex items-center justify-between">
        <span class="text-[10px] font-mono uppercase font-bold text-[#0D4F60]/70 tracking-wider">
          MTR//04 • ALMACÉN
        </span>
        <div class="w-8 h-8 rounded-lg <?= count($materialesBajoStock) > 0 ? 'bg-amber-100 text-[#E58D17] border border-amber-300' : 'bg-[#F0EEE8] text-[#103E4A]' ?> flex items-center justify-center">
          <i data-lucide="alert-triangle" class="w-4 h-4"></i>
        </div>
      </div>
      <div class="mt-3">
        <div class="text-3xl font-extrabold text-[#103E4A] font-display flex items-baseline gap-2">
          <span><?= count($materialesBajoStock) ?></span>
          <span class="text-xs font-semibold text-[#0D4F60]/70 font-sans">críticos</span>
        </div>
        <div class="w-full bg-[#F0EEE8] h-2 rounded-full mt-3 overflow-hidden">
          <div class="bg-gradient-to-r from-[#E58D17] to-rose-500 h-full rounded-full" style="width: <?= min(100, count($materialesBajoStock) * 25) ?>%"></div>
        </div>
        <p class="text-[11px] text-[#E58D17] mt-2 font-bold flex items-center justify-between">
          <span><?= count($materialesBajoStock) > 0 ? 'Reposición requerida' : 'Stock en niveles óptimos' ?></span>
          <i data-lucide="arrow-right" class="w-3.5 h-3.5 opacity-70 group-hover:translate-x-1 transition-transform"></i>
        </p>
      </div>
    </a>

  </div>

  <!-- ========================================================
       BENTO: PIPELINE DE PRODUCCIÓN (MÁQUINA DE ESTADOS 7 FASES)
       ======================================================== -->
  <div class="bento-card corner-mark p-5 shadow-xl bg-white">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 border-b border-[#0D4F60]/15 pb-3">
      <div>
        <div class="flex items-center gap-2">
          <span class="text-[10px] font-mono uppercase font-bold text-[#E58D17] bg-[#ED991F]/15 px-2 py-0.5 rounded border border-[#ED991F]/30">
            WORKFLOW//07
          </span>
          <h2 class="text-base font-bold text-[#103E4A] font-display">
            Pipeline de Manufactura en Taller
          </h2>
        </div>
        <p class="text-xs text-[#0D4F60]/80 mt-0.5 font-medium">Avance de pedidos ordenados por estación de trabajo</p>
      </div>
      <a href="pedidos.php" class="text-xs text-[#E58D17] hover:text-[#ED991F] font-bold flex items-center gap-1 self-start sm:self-auto">
        <span>Abrir Tablero Kanban</span>
        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
      </a>
    </div>

    <!-- Riel de 7 Fases Bento en Fondo Claro -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2.5">
      <?php foreach ($estados as $key => $st): ?>
        <a href="pedidos.php?estado=<?= $key ?>" class="p-3.5 rounded-xl border <?= $st['badge'] ?> flex flex-col justify-between hover:scale-[1.03] transition-all group shadow-sm bg-white">
          <div class="flex items-center justify-between text-[11px] font-bold">
            <span class="truncate"><?= $st['label'] ?></span>
            <i data-lucide="<?= $st['icon'] ?>" class="w-3.5 h-3.5 opacity-80 group-hover:text-[#ED991F] transition-colors"></i>
          </div>
          <div class="mt-3 text-2xl font-black text-[#103E4A] font-mono group-hover:text-[#ED991F] transition-colors">
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
    <div class="lg:col-span-8 bento-card corner-mark p-5 shadow-xl space-y-4 bg-white">
      <div class="flex items-center justify-between border-b border-[#0D4F60]/15 pb-3">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-[10px] font-mono uppercase font-bold text-[#E58D17] bg-[#ED991F]/15 px-2 py-0.5 rounded border border-[#ED991F]/30">
              QUEUE//RADAR
            </span>
            <h2 class="text-base font-bold text-[#103E4A] font-display">
              Compromisos & Fechas de Entrega
            </h2>
          </div>
          <p class="text-xs text-[#0D4F60]/80 mt-0.5 font-medium">Órdenes con fecha límite más próxima</p>
        </div>
        <span class="text-xs font-mono text-[#E58D17] font-bold"><?= count($pedidosUrgentes) ?> proyectos</span>
      </div>

      <div class="divide-y divide-slate-100">
        <?php foreach ($pedidosUrgentes as $p): ?>
          <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#F7F5EF] p-2.5 rounded-xl transition-colors">
            <div class="space-y-1">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-mono font-bold text-white bg-[#103E4A] px-2 py-0.5 rounded shadow-sm">
                  <?= htmlspecialchars($p['codigo']) ?>
                </span>
                <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded-full <?= $p['prioridad'] === 'urgente' ? 'bg-amber-100 text-[#E58D17] border border-amber-300 font-bold' : 'bg-[#F0EEE8] text-[#103E4A] font-semibold' ?>">
                  <?= $p['prioridad'] ?>
                </span>
                <span class="text-xs text-[#103E4A] font-bold">
                  <?= htmlspecialchars($p['cliente']) ?>
                </span>
              </div>
              <div class="font-bold text-[#103E4A] text-sm">
                <?= htmlspecialchars($p['proyecto']) ?>
              </div>
              <div class="text-[11px] text-[#0D4F60]/80">
                Material: <span class="text-[#103E4A] font-semibold"><?= htmlspecialchars($p['madera']) ?></span>
              </div>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-4 flex-shrink-0">
              <div class="text-left sm:text-right">
                <div class="flex items-center gap-1.5 text-xs text-[#103E4A] font-medium">
                  <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#ED991F]"></i>
                  <span class="font-mono"><?= $p['fecha_entrega'] ?></span>
                </div>
                <div class="text-xs text-[#E58D17] font-bold font-mono mt-0.5">
                  <?= formatCOP($p['precio']) ?>
                </div>
              </div>
              <a href="imprimir_orden.php?id=<?= $p['id'] ?>" target="_blank" class="p-2.5 rounded-xl bg-[#F0EEE8] hover:bg-[#103E4A] text-[#103E4A] hover:text-white transition-colors border border-[#0D4F60]/20" title="Imprimir Hoja de Taller">
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
      <div class="bento-card p-4 sm:p-5 shadow-xl space-y-3 bg-white">
        <div class="flex items-center justify-between border-b border-[#0D4F60]/15 pb-2.5">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-[#ED991F] to-[#E58D17] flex items-center justify-center text-white">
              <i data-lucide="bot" class="w-4 h-4 text-white"></i>
            </div>
            <h3 class="text-xs font-bold text-[#103E4A] font-display">CarpinterIA Terminal</h3>
          </div>
          <span class="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-300">
            ONLINE
          </span>
        </div>

        <p class="text-xs text-[#0D4F60]/80 leading-relaxed font-medium">
          Consulta al instante holguras de correderas, descuentos de tapacanto y tolerancias de ensamble para el taller.
        </p>

        <button onclick="abrirModalCarpinterIA()" class="w-full py-2 px-3 rounded-xl bg-[#F7F5EF] hover:bg-[#E2E8F0] text-[#103E4A] border border-[#0D4F60]/20 text-xs font-bold flex items-center justify-center gap-2 transition-colors cursor-pointer">
          <i data-lucide="terminal" class="w-3.5 h-3.5 text-[#ED991F]"></i>
          <span>Abrir Asesor Técnico</span>
        </button>
      </div>

      <!-- Diagnóstico de Materiales Críticos -->
      <div class="bento-card p-4 sm:p-5 shadow-xl space-y-3 bg-white">
        <div class="flex items-center justify-between border-b border-[#0D4F60]/15 pb-2.5">
          <div class="flex items-center gap-1.5">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-[#ED991F]"></i>
            <h3 class="text-xs font-bold text-[#103E4A] font-display">Stock Crítico</h3>
          </div>
          <a href="inventario.php" class="text-[11px] font-bold text-[#E58D17] hover:underline">Ver todo</a>
        </div>

        <div class="space-y-2">
          <?php if (empty($materialesBajoStock)): ?>
            <div class="p-3 rounded-xl bg-[#F8FAFC] text-center text-xs text-[#0D4F60]/70 font-medium">
              Almacén sin faltantes críticos.
            </div>
          <?php else: ?>
            <?php foreach (array_slice($materialesBajoStock, 0, 3) as $mat): ?>
              <div class="p-2.5 rounded-xl bg-[#F8FAFC] border border-[#0D4F60]/15 flex items-center justify-between text-xs">
                <div>
                  <div class="font-bold text-[#103E4A] truncate max-w-[140px]"><?= htmlspecialchars($mat['nombre']) ?></div>
                  <div class="text-[10px] text-[#0D4F60]/70"><?= htmlspecialchars($mat['ubicacion']) ?></div>
                </div>
                <div class="text-right font-mono">
                  <div class="font-bold text-[#E58D17]"><?= $mat['stock'] ?> <?= $mat['unidad'] ?></div>
                  <div class="text-[10px] text-slate-500">Mín: <?= $mat['min_stock'] ?></div>
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

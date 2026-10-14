<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Valores por defecto o recibidos por POST/GET
$categoria = $_POST['categoria'] ?? ($_GET['categoria'] ?? 'Cocina');
$proyecto = $_POST['proyecto'] ?? ($_GET['proyecto'] ?? 'Módulo de Cocina Integral Bajo');
$madera = $_POST['madera'] ?? ($_GET['madera'] ?? 'Melamina Pelíkano Roble Sinatra 18mm');
$alto = floatval($_POST['alto'] ?? 90);
$ancho = floatval($_POST['ancho'] ?? 120);
$fondo = floatval($_POST['fondo'] ?? 60);
$puertas = intval($_POST['puertas'] ?? 2);
$gavetas = intval($_POST['gavetas'] ?? 3);
$repisas = intval($_POST['repisas'] ?? 2);
$tapacanto = $_POST['tapacanto'] ?? 'PVC 2mm en 4 cantos';
$margen = intval($_POST['margen'] ?? 40);

// CÁLCULO DE DESPIECE MATEMÁTICO DE CARPINTERÍA
$piezas = [];
$espesor = 18; // mm

if ($categoria === 'Mesa') {
    $piezas[] = ['nombre' => 'Cubierta Superior de Mesa Principal', 'cantidad' => 1, 'largo' => $ancho, 'ancho' => $fondo, 'espesor' => 38, 'material' => $madera, 'canto' => 'Biselado madera maciza', 'veta' => 'largo'];
    $piezas[] = ['nombre' => 'Patas Estructurales Reforzadas', 'cantidad' => 4, 'largo' => max(40, $alto - 4), 'ancho' => 10, 'espesor' => 75, 'material' => $madera, 'canto' => 'Aristas suavizadas', 'veta' => 'largo'];
    $piezas[] = ['nombre' => 'Faldones y Travesaños de Amarre', 'cantidad' => 4, 'largo' => max(20, $ancho - 25), 'ancho' => 12, 'espesor' => 25, 'material' => $madera, 'canto' => 'Cepillado 4 caras', 'veta' => 'largo'];
} elseif ($categoria === 'Puerta') {
    $piezas[] = ['nombre' => 'Montantes Verticales Estructurales', 'cantidad' => 2, 'largo' => $alto, 'ancho' => 14, 'espesor' => 45, 'material' => $madera, 'canto' => 'Rebaje para batiente', 'veta' => 'largo'];
    $piezas[] = ['nombre' => 'Peinazos Horizontales', 'cantidad' => 3, 'largo' => max(20, $ancho - 28), 'ancho' => 16, 'espesor' => 45, 'material' => $madera, 'canto' => 'Espigas dobles', 'veta' => 'ancho'];
    $piezas[] = ['nombre' => 'Tableros Realzados Moldurados', 'cantidad' => 2, 'largo' => max(20, ($alto - 45) / 2), 'ancho' => max(20, $ancho - 30), 'espesor' => 25, 'material' => $madera, 'canto' => 'Moldura perimetral', 'veta' => 'largo'];
} else {
    // Muebles modulares
    $anchoInterno = max(10, $ancho - (2 * ($espesor / 10)));
    $piezas[] = ['nombre' => 'Laterales Estructurales (Costados)', 'cantidad' => 2, 'largo' => $alto, 'ancho' => $fondo, 'espesor' => $espesor, 'material' => $madera, 'canto' => $tapacanto, 'veta' => 'largo'];
    $piezas[] = ['nombre' => 'Techo y Suelo (Bases)', 'cantidad' => 2, 'largo' => round($anchoInterno, 1), 'ancho' => $fondo, 'espesor' => $espesor, 'material' => $madera, 'canto' => $tapacanto, 'veta' => 'ancho'];
    
    if ($repisas > 0) {
        $piezas[] = ['nombre' => 'Entrepaños / Repisas Interiores', 'cantidad' => $repisas, 'largo' => round($anchoInterno - 0.2, 1), 'ancho' => max(10, $fondo - 2), 'espesor' => $espesor, 'material' => $madera, 'canto' => $tapacanto, 'veta' => 'ancho'];
    }
    
    $piezas[] = ['nombre' => 'Fondo Trasero Soporte', 'cantidad' => 1, 'largo' => max(10, $alto - 2), 'ancho' => max(10, $ancho - 2), 'espesor' => 3, 'material' => 'MDF 3mm Revestido', 'canto' => 'Sin canto', 'veta' => 'indistinto'];
    
    if ($puertas > 0) {
        $anchoPuerta = round(($ancho - 0.4) / $puertas, 1);
        $piezas[] = ['nombre' => 'Puertas Batientes Frontales', 'cantidad' => $puertas, 'largo' => round($alto - 0.4, 1), 'ancho' => $anchoPuerta, 'espesor' => $espesor, 'material' => $madera, 'canto' => 'PVC 2mm en 4 cantos', 'veta' => 'largo'];
    }
    
    if ($gavetas > 0) {
        $altoFrente = round(($alto * 0.4) / $gavetas, 1);
        $piezas[] = ['nombre' => 'Frentes de Gaveta', 'cantidad' => $gavetas, 'largo' => $altoFrente, 'ancho' => round($anchoInterno - 0.4, 1), 'espesor' => $espesor, 'material' => $madera, 'canto' => 'PVC 2mm en 4 cantos', 'veta' => 'ancho'];
        $piezas[] = ['nombre' => 'Laterales Interiores Gaveta', 'cantidad' => $gavetas * 2, 'largo' => max(30, $fondo - 5), 'ancho' => max(10, $altoFrente - 5), 'espesor' => 15, 'material' => 'MDF / Melamina 15mm', 'canto' => 'Canto delgado 0.5mm', 'veta' => 'largo'];
    }
}

// Superficie y Metraje de Cantos
$totalAreaM2 = 0;
$metrosCanto = 0;
foreach ($piezas as $pz) {
    $areaPz = ($pz['largo'] / 100) * ($pz['ancho'] / 100) * $pz['cantidad'];
    $totalAreaM2 += $areaPz;
    if (strpos($pz['canto'], '4 cantos') !== false) {
        $metrosCanto += (($pz['largo'] * 2 + $pz['ancho'] * 2) / 100) * $pz['cantidad'];
    } else {
        $metrosCanto += (($pz['largo'] * 2) / 100) * $pz['cantidad'];
    }
}

$areaAprovechable = 4.46 * 0.8;
$planchasRequeridas = max(1, ceil($totalAreaM2 / $areaAprovechable));

$precioPlancha = 285000;
if (stripos($madera, 'roble') !== false) $precioPlancha = 310000;
if (stripos($madera, 'cedro') !== false) $precioPlancha = 330000;

$costoMaterial = $planchasRequeridas * $precioPlancha;
$costoCanto = round($metrosCanto * 1800);
$costoHerrajes = ($puertas * 2 * 9500) + ($gavetas * 42000) + (($puertas + $gavetas) * 14500);
$costoInsumos = round(($costoMaterial + $costoHerrajes) * 0.08);
$horasManoObra = max(8, count($piezas) * 2.5 + $gavetas * 2 + $puertas * 1.5);
$costoManoObra = round($horasManoObra * 28000);

$costoTotal = $costoMaterial + $costoCanto + $costoHerrajes + $costoInsumos + $costoManoObra;
$multiplicadorMargen = 1 / (1 - ($margen / 100));
$precioSugerido = round(($costoTotal * $multiplicadorMargen) / 10000) * 10000;
$utilidad = $precioSugerido - $costoTotal;
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8">

  <!-- Encabezado -->
  <div class="bg-white border border-[#0D4F60]/20 p-6 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#103E4A] text-[#ED991F] text-xs font-semibold border border-[#ED991F]/30 mb-2">
        <i data-lucide="scissors" class="w-3.5 h-3.5"></i>
        Optimizador & Cotizador Paramétrico (PHP Engine)
      </div>
      <h2 class="text-xl sm:text-2xl font-black text-[#103E4A] font-display">Calculadora de Muebles & Despiece</h2>
      <p class="text-[#103E4A]/70 text-xs sm:text-sm mt-1">
        Ingresa las medidas finales del mueble y el motor PHP calculará el despiece exacto para escuadradora o corte CNC, rendimiento de tableros y cotización.
      </p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    
    <!-- Formulario de Parámetros (5 cols) -->
    <div class="lg:col-span-5 space-y-6">
      <form method="POST" action="despiece.php" class="bg-white border border-[#0D4F60]/20 p-5 rounded-2xl space-y-4 shadow-sm">
        <h3 class="text-sm font-bold text-[#103E4A] uppercase tracking-wider font-display flex items-center gap-2 border-b border-[#0D4F60]/15 pb-3">
          <i data-lucide="settings-2" class="w-4 h-4 text-[#ED991F]"></i>
          1. Especificaciones del Proyecto
        </h3>

        <div>
          <label class="block text-xs font-semibold text-[#103E4A] mb-1">Nombre del Proyecto</label>
          <input type="text" name="proyecto" value="<?= htmlspecialchars($proyecto) ?>" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] focus:bg-white focus:outline-none focus:border-[#ED991F]">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-[#103E4A] mb-1">Tipo de Mueble</label>
            <select name="categoria" onchange="this.form.submit()" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] focus:bg-white focus:border-[#ED991F]">
              <option value="Cocina" <?= $categoria === 'Cocina' ? 'selected' : '' ?>>Cocina Integral</option>
              <option value="Closet" <?= $categoria === 'Closet' ? 'selected' : '' ?>>Clóset / Armario</option>
              <option value="Mesa" <?= $categoria === 'Mesa' ? 'selected' : '' ?>>Mesa de Comedor</option>
              <option value="Puerta" <?= $categoria === 'Puerta' ? 'selected' : '' ?>>Puerta Principal</option>
              <option value="Bano" <?= $categoria === 'Bano' ? 'selected' : '' ?>>Mueble de Baño</option>
              <option value="Oficina" <?= $categoria === 'Oficina' ? 'selected' : '' ?>>Escritorio / Oficina</option>
              <option value="Especial" <?= $categoria === 'Especial' ? 'selected' : '' ?>>Mueble Especial</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-[#103E4A] mb-1">Madera / Tablero</label>
            <select name="madera" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] focus:bg-white focus:border-[#ED991F]">
              <option value="Melamina Pelíkano Roble Sinatra 18mm" <?= $madera === 'Melamina Pelíkano Roble Sinatra 18mm' ? 'selected' : '' ?>>Melamina Roble 18mm</option>
              <option value="Melamina Blanco Lino Mate 18mm" <?= $madera === 'Melamina Blanco Lino Mate 18mm' ? 'selected' : '' ?>>Melamina Blanco Mate 18mm</option>
              <option value="Madera Roble Americano Macizo 2 pulgadas" <?= $madera === 'Madera Roble Americano Macizo 2 pulgadas' ? 'selected' : '' ?>>Roble Macizo</option>
              <option value="Madera Cedro Caquetá Macizo 3 pulgadas" <?= $madera === 'Madera Cedro Caquetá Macizo 3 pulgadas' ? 'selected' : '' ?>>Cedro Macizo</option>
              <option value="Madera Teca de Cultivo" <?= $madera === 'Madera Teca de Cultivo' ? 'selected' : '' ?>>Teca de Cultivo</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-[#103E4A] mb-1 flex items-center justify-between">
            <span>Dimensiones Finales (cm)</span>
            <span class="text-[11px] text-[#ED991F] font-mono font-bold">Alto x Ancho x Fondo</span>
          </label>
          <div class="grid grid-cols-3 gap-2">
            <div>
              <input type="number" name="alto" value="<?= $alto ?>" class="w-full px-2.5 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] text-center font-mono font-bold focus:bg-white focus:border-[#ED991F]">
              <span class="block text-[10px] text-[#103E4A]/70 text-center mt-1">Alto (cm)</span>
            </div>
            <div>
              <input type="number" name="ancho" value="<?= $ancho ?>" class="w-full px-2.5 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] text-center font-mono font-bold focus:bg-white focus:border-[#ED991F]">
              <span class="block text-[10px] text-[#103E4A]/70 text-center mt-1">Ancho (cm)</span>
            </div>
            <div>
              <input type="number" name="fondo" value="<?= $fondo ?>" class="w-full px-2.5 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] text-center font-mono font-bold focus:bg-white focus:border-[#ED991F]">
              <span class="block text-[10px] text-[#103E4A]/70 text-center mt-1">Fondo (cm)</span>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-[#0D4F60]/15">
          <div>
            <label class="block text-[11px] font-semibold text-[#103E4A] mb-1 text-center">Puertas</label>
            <input type="number" name="puertas" min="0" max="8" value="<?= $puertas ?>" class="w-full px-2 py-1.5 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] text-center font-bold focus:bg-white focus:border-[#ED991F]">
          </div>
          <div>
            <label class="block text-[11px] font-semibold text-[#103E4A] mb-1 text-center">Gavetas</label>
            <input type="number" name="gavetas" min="0" max="8" value="<?= $gavetas ?>" class="w-full px-2 py-1.5 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] text-center font-bold focus:bg-white focus:border-[#ED991F]">
          </div>
          <div>
            <label class="block text-[11px] font-semibold text-[#103E4A] mb-1 text-center">Repisas</label>
            <input type="number" name="repisas" min="0" max="10" value="<?= $repisas ?>" class="w-full px-2 py-1.5 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-xs text-[#103E4A] text-center font-bold focus:bg-white focus:border-[#ED991F]">
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-[#103E4A] mb-1">Margen de Ganancia del Taller: <span class="font-bold text-[#ED991F]"><?= $margen ?>%</span></label>
          <input type="range" name="margen" min="15" max="75" step="5" value="<?= $margen ?>" onchange="this.form.submit()" class="w-full accent-[#ED991F]">
        </div>

        <button type="submit" class="w-full py-2.5 rounded-xl bg-[#103E4A] hover:bg-[#0D4F60] text-white font-bold text-xs border border-[#103E4A] transition-all shadow-xs cursor-pointer">
          Recalcular Despiece
        </button>
      </form>
    </div>

    <!-- Resultados y Plano de Corte (7 cols) -->
    <div class="lg:col-span-7 space-y-6">
      
      <!-- Resumen Económico -->
      <div class="bg-white border border-[#0D4F60]/20 p-6 rounded-2xl shadow-sm">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-6 border-b border-[#0D4F60]/15">
          <div class="bg-[#F7F5EF] p-3 rounded-xl border border-[#0D4F60]/15">
            <span class="text-[10px] uppercase font-bold text-[#103E4A]/70 block">Área Estimada</span>
            <span class="text-xl font-black text-[#103E4A] font-mono"><?= round($totalAreaM2, 2) ?> m²</span>
            <span class="text-[10px] text-[#103E4A]/60 block"><?= count($piezas) ?> piezas</span>
          </div>
          <div class="bg-[#F7F5EF] p-3 rounded-xl border border-[#0D4F60]/15">
            <span class="text-[10px] uppercase font-bold text-[#103E4A]/70 block">Tableros 2.44x1.83</span>
            <span class="text-xl font-black text-[#103E4A] font-mono"><?= $planchasRequeridas ?> planchas</span>
            <span class="text-[10px] text-[#103E4A]/60 block">rendimiento ~80%</span>
          </div>
          <div class="bg-[#F7F5EF] p-3 rounded-xl border border-[#0D4F60]/15">
            <span class="text-[10px] uppercase font-bold text-[#103E4A]/70 block">Tapacanto PVC</span>
            <span class="text-xl font-black text-[#103E4A] font-mono"><?= round($metrosCanto, 1) ?> m</span>
            <span class="text-[10px] text-[#103E4A]/60 block">lineales requeridos</span>
          </div>
          <div class="bg-[#F7F5EF] p-3 rounded-xl border border-[#0D4F60]/15">
            <span class="text-[10px] uppercase font-bold text-[#103E4A]/70 block">Herrajes</span>
            <span class="text-xl font-black text-[#ED991F] font-mono"><?= $puertas * 2 ?>B / <?= $gavetas ?>C</span>
            <span class="text-[10px] text-[#103E4A]/60 block">bisagras/correderas</span>
          </div>
        </div>

        <div class="py-4 space-y-2 border-b border-[#0D4F60]/15 text-xs text-[#103E4A]">
          <div class="flex justify-between text-[#103E4A]/80">
            <span>Materia prima (tableros):</span>
            <span class="font-mono font-semibold text-[#103E4A]"><?= formatCOP($costoMaterial) ?></span>
          </div>
          <div class="flex justify-between text-[#103E4A]/80">
            <span>Herrajes y tiradores:</span>
            <span class="font-mono font-semibold text-[#103E4A]"><?= formatCOP($costoHerrajes) ?></span>
          </div>
          <div class="flex justify-between text-[#103E4A]/80">
            <span>Mano de obra (corte y ensamble):</span>
            <span class="font-mono font-semibold text-[#103E4A]"><?= formatCOP($costoManoObra) ?></span>
          </div>
          <div class="flex justify-between text-[#103E4A] font-bold pt-1 border-t border-[#0D4F60]/15">
            <span>Costo Total Directo del Taller:</span>
            <span class="font-mono text-[#103E4A] font-bold"><?= formatCOP($costoTotal) ?></span>
          </div>
        </div>

        <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <span class="text-[11px] font-semibold text-[#103E4A]/70 uppercase tracking-wider">Precio Sugerido al Cliente:</span>
            <div class="text-3xl font-black text-[#ED991F] font-display"><?= formatCOP($precioSugerido) ?></div>
            <p class="text-xs text-[#103E4A] font-medium">Utilidad estimada: <span class="text-[#ED991F] font-bold"><?= formatCOP($utilidad) ?></span> (<?= $margen ?>%)</p>
          </div>

          <form action="api/guardar_pedido.php" method="POST">
            <input type="hidden" name="proyecto" value="<?= htmlspecialchars($proyecto) ?>">
            <input type="hidden" name="categoria" value="<?= htmlspecialchars($categoria) ?>">
            <input type="hidden" name="madera" value="<?= htmlspecialchars($madera) ?>">
            <input type="hidden" name="acabado" value="<?= htmlspecialchars($tapacanto) ?>">
            <input type="hidden" name="alto" value="<?= $alto ?>">
            <input type="hidden" name="ancho" value="<?= $ancho ?>">
            <input type="hidden" name="fondo" value="<?= $fondo ?>">
            <input type="hidden" name="precio" value="<?= $precioSugerido ?>">
            <input type="hidden" name="anticipo" value="<?= round($precioSugerido * 0.5) ?>">
            <input type="hidden" name="cliente" value="Cliente de Cotizador">
            <input type="hidden" name="fecha_entrega" value="<?= date('Y-m-d', strtotime('+15 days')) ?>">
            <input type="hidden" name="prioridad" value="media">
            <button type="submit" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#ED991F] to-[#E58D17] text-[#103E4A] font-extrabold text-xs sm:text-sm shadow-md hover:opacity-90 cursor-pointer transition-all">
              <i data-lucide="plus-circle" class="w-4 h-4"></i>
              <span>Convertir a Pedido</span>
            </button>
          </form>
        </div>
      </div>

      <!-- Tabla de Despiece para Escuadradora -->
      <div class="bg-white border border-[#0D4F60]/20 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 bg-[#103E4A] border-b border-[#0D4F60]/20 flex items-center justify-between">
          <div>
            <h4 class="text-xs font-bold text-white uppercase tracking-wider font-display flex items-center gap-2">
              <i data-lucide="file-text" class="w-4 h-4 text-[#ED991F]"></i>
              Plano de Despiece de Corte (<?= count($piezas) ?> piezas)
            </h4>
          </div>
          <span class="text-xs font-mono text-[#103E4A] font-bold bg-[#ED991F] px-2 py-0.5 rounded">
            Medidas en cm
          </span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-[#103E4A] font-mono">
            <thead class="bg-[#F7F5EF] text-[#103E4A] text-[10px] uppercase font-bold tracking-wider border-b border-[#0D4F60]/15">
              <tr>
                <th class="p-3">Pieza</th>
                <th class="p-3 text-center">Cant.</th>
                <th class="p-3 text-center">Largo (cm)</th>
                <th class="p-3 text-center">Ancho (cm)</th>
                <th class="p-3 text-center">Espesor</th>
                <th class="p-3">Tapacanto</th>
                <th class="p-3">Veta</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#0D4F60]/10">
              <?php foreach ($piezas as $pz): ?>
                <tr class="hover:bg-[#F7F5EF]/60 transition-colors">
                  <td class="p-3 font-sans font-semibold text-[#103E4A]"><?= htmlspecialchars($pz['nombre']) ?></td>
                  <td class="p-3 text-center font-bold text-[#ED991F]"><?= $pz['cantidad'] ?></td>
                  <td class="p-3 text-center text-[#103E4A] font-semibold"><?= $pz['largo'] ?></td>
                  <td class="p-3 text-center text-[#103E4A] font-semibold"><?= $pz['ancho'] ?></td>
                  <td class="p-3 text-center text-[#103E4A]/70"><?= $pz['espesor'] ?> mm</td>
                  <td class="p-3 font-sans text-[11px] text-[#103E4A]/80"><?= htmlspecialchars($pz['canto']) ?></td>
                  <td class="p-3 font-sans text-[11px] text-[#ED991F] font-bold capitalize"><?= $pz['veta'] ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>

  </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

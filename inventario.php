<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$inventario = getInventario();

$categoriaSel = $_GET['cat'] ?? 'todas';
$q = strtolower($_GET['q'] ?? '');

$filtrado = array_filter($inventario, function($item) use ($categoriaSel, $q) {
    if ($categoriaSel !== 'todas' && $item['categoria'] !== $categoriaSel) return false;
    if ($q !== '') {
        $txt = strtolower($item['nombre'] . ' ' . $item['codigo'] . ' ' . $item['proveedor'] . ' ' . $item['ubicacion']);
        if (strpos($txt, $q) === false) return false;
    }
    return true;
});

$totalValor = array_reduce($inventario, fn($sum, $i) => $sum + ($i['stock'] * $i['costo']), 0);
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">

  <!-- Banner -->
  <div class="bg-white border border-[#0D4F60]/20 p-6 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#103E4A] text-[#ED991F] text-xs font-semibold border border-[#ED991F]/30 mb-2">
        <i data-lucide="boxes" class="w-3.5 h-3.5"></i>
        Almacén & Materias Primas
      </div>
      <h2 class="text-xl sm:text-2xl font-black text-[#103E4A] font-display">Control de Inventario de Carpintería</h2>
      <p class="text-[#103E4A]/70 text-xs sm:text-sm mt-1">Stock de maderas macizas, tableros melamínicos, herrajes de cazoleta y acabados.</p>
    </div>

    <div class="flex flex-wrap items-center gap-4">
      <div class="bg-[#F7F5EF] p-3 rounded-xl border border-[#0D4F60]/15">
        <span class="text-[10px] uppercase font-bold text-[#103E4A]/70 block">Valorización Almacén</span>
        <span class="text-lg font-black text-[#103E4A] font-mono"><?= formatCOP($totalValor) ?></span>
      </div>
      <button onclick="document.getElementById('modalMaterial').classList.remove('hidden'); document.getElementById('modalMaterial').classList.add('flex');" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#ED991F] to-[#E58D17] text-[#103E4A] font-extrabold text-xs sm:text-sm shadow-md hover:opacity-90 transition-all cursor-pointer">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Agregar Material</span>
      </button>
    </div>
  </div>

  <!-- Controles y Pestañas -->
  <div class="bg-white border border-[#0D4F60]/20 p-4 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
    <form method="GET" action="inventario.php" class="relative flex-1 max-w-md">
      <i data-lucide="search" class="w-4 h-4 text-[#103E4A]/50 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
      <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Buscar material por código, nombre o proveedor..." class="w-full pl-10 pr-4 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-xs sm:text-sm text-[#103E4A] placeholder-[#103E4A]/50 focus:bg-white focus:outline-none focus:border-[#ED991F] transition-all">
    </form>

    <div class="flex flex-wrap gap-1.5 bg-[#F7F5EF] p-1 rounded-xl border border-[#0D4F60]/15">
      <a href="inventario.php?cat=todas" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $categoriaSel === 'todas' ? 'bg-[#103E4A] text-white shadow-xs' : 'text-[#103E4A]/70 hover:text-[#103E4A]' ?>">Todos</a>
      <a href="inventario.php?cat=madera" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $categoriaSel === 'madera' ? 'bg-[#103E4A] text-white shadow-xs' : 'text-[#103E4A]/70 hover:text-[#103E4A]' ?>">🌲 Maderas</a>
      <a href="inventario.php?cat=tablero" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $categoriaSel === 'tablero' ? 'bg-[#103E4A] text-white shadow-xs' : 'text-[#103E4A]/70 hover:text-[#103E4A]' ?>">🪵 Tableros</a>
      <a href="inventario.php?cat=herraje" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $categoriaSel === 'herraje' ? 'bg-[#103E4A] text-white shadow-xs' : 'text-[#103E4A]/70 hover:text-[#103E4A]' ?>">🔩 Herrajes</a>
      <a href="inventario.php?cat=quimico" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $categoriaSel === 'quimico' ? 'bg-[#103E4A] text-white shadow-xs' : 'text-[#103E4A]/70 hover:text-[#103E4A]' ?>">🧪 Químicos</a>
    </div>
  </div>

  <!-- Grid de Materiales -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($filtrado as $item): 
        $critico = $item['stock'] <= $item['min_stock'];
        $ratio = min(100, round(($item['stock'] / ($item['min_stock'] * 2)) * 100));
    ?>
      <div class="bg-white border <?= $critico ? 'border-[#E58D17]' : 'border-[#0D4F60]/20' ?> rounded-2xl p-4 flex flex-col justify-between shadow-sm hover:shadow-md transition-all">
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-[10px] font-mono font-bold text-[#103E4A] bg-[#ED991F] px-2 py-0.5 rounded">
              <?= htmlspecialchars($item['codigo']) ?>
            </span>
            <?php if ($critico): ?>
              <span class="text-[10px] font-bold text-[#E58D17] bg-[#ED991F]/20 px-2 py-0.5 rounded-full border border-[#ED991F]/40">
                Stock Crítico
              </span>
            <?php else: ?>
              <span class="text-[10px] font-semibold text-[#103E4A] bg-[#F7F5EF] px-2 py-0.5 rounded-full border border-[#0D4F60]/20">
                Disponible
              </span>
            <?php endif; ?>
          </div>

          <div>
            <h4 class="text-sm font-bold text-[#103E4A] font-display line-clamp-1"><?= htmlspecialchars($item['nombre']) ?></h4>
            <p class="text-xs text-[#103E4A]/70 mt-0.5"><?= htmlspecialchars($item['descripcion'] ?? '') ?></p>
          </div>

          <div class="p-2 rounded-xl bg-[#F7F5EF] border border-[#0D4F60]/15 space-y-1 text-[11px] text-[#103E4A]/80">
            <div>Ubicación: <span class="font-medium text-[#103E4A]"><?= htmlspecialchars($item['ubicacion']) ?></span></div>
            <div>Proveedor: <span class="font-medium text-[#103E4A]"><?= htmlspecialchars($item['proveedor']) ?></span></div>
          </div>

          <div>
            <div class="flex justify-between text-xs mb-1 text-[#103E4A]">
              <span class="text-[#103E4A]/70">Existencias:</span>
              <span class="font-bold font-mono text-[#103E4A]"><?= $item['stock'] ?> <span class="text-[#103E4A]/70 text-[11px] font-sans"><?= $item['unidad'] ?></span></span>
            </div>
            <div class="w-full bg-[#E5E1D8] h-2 rounded-full overflow-hidden border border-[#0D4F60]/15">
              <div class="h-full rounded-full <?= $critico ? 'bg-[#E58D17]' : 'bg-[#ED991F]' ?>" style="width: <?= $ratio ?>%"></div>
            </div>
            <div class="flex justify-between text-[10px] text-[#103E4A]/70 mt-1">
              <span>Mínimo: <?= $item['min_stock'] ?></span>
              <span>Costo: <?= formatCOP($item['costo']) ?></span>
            </div>
          </div>
        </div>

        <div class="mt-4 pt-3 border-t border-[#0D4F60]/15 flex items-center justify-between">
          <span class="text-[11px] text-[#103E4A]/70">Ajustar stock:</span>
          <div class="flex items-center gap-2">
            <a href="api/actualizar_stock.php?id=<?= $item['id'] ?>&delta=-1" class="px-2.5 py-1 rounded-lg bg-[#F0EEE8] hover:bg-[#103E4A] hover:text-white text-[#103E4A] text-xs font-bold transition-all" title="Descontar 1 unidad utilizada">-1 Uso</a>
            <a href="api/actualizar_stock.php?id=<?= $item['id'] ?>&delta=1" class="px-2.5 py-1 rounded-lg bg-[#ED991F] hover:bg-[#E58D17] text-[#103E4A] text-xs font-bold shadow-xs transition-all" title="Registrar 1 unidad recibida">+1 Ingreso</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</main>

<!-- Modal Nuevo Material -->
<div id="modalMaterial" class="fixed inset-0 z-50 bg-[#103E4A]/60 backdrop-blur-sm hidden items-center justify-center p-4">
  <div class="bg-white border border-[#0D4F60]/20 rounded-2xl max-w-lg w-full p-6 shadow-2xl my-auto">
    <div class="flex items-center justify-between pb-3 border-b border-[#0D4F60]/15 mb-4">
      <div class="flex items-center gap-2">
        <i data-lucide="package-plus" class="w-5 h-5 text-[#ED991F]"></i>
        <h3 class="text-lg font-bold text-[#103E4A] font-display">Registrar Material al Almacén</h3>
      </div>
      <button type="button" onclick="document.getElementById('modalMaterial').classList.add('hidden');" class="p-1 rounded-lg text-[#103E4A]/50 hover:text-[#103E4A] hover:bg-[#F0EEE8]">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>

    <form action="api/guardar_material.php" method="POST" class="space-y-4 text-xs">
      <div>
        <label class="block text-[#103E4A] font-semibold mb-1">Nombre del Material</label>
        <input type="text" name="nombre" required placeholder="Ej: Melamina Rovere 18mm" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-[#103E4A] focus:bg-white focus:outline-none focus:border-[#ED991F]">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-[#103E4A] font-semibold mb-1">Categoría</label>
          <select name="categoria" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-[#103E4A] focus:bg-white focus:border-[#ED991F]">
            <option value="madera">Madera Maciza</option>
            <option value="tablero">Tablero / Melamina</option>
            <option value="herraje">Herraje</option>
            <option value="quimico">Químico / Acabado</option>
          </select>
        </div>
        <div>
          <label class="block text-[#103E4A] font-semibold mb-1">Unidad</label>
          <select name="unidad" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-[#103E4A] focus:bg-white focus:border-[#ED991F]">
            <option value="pie_tablar">Pies Tablares (pt)</option>
            <option value="plancha">Planchas / Láminas</option>
            <option value="par">Pares</option>
            <option value="unidad">Unidad</option>
            <option value="litro">Litro / Galón</option>
            <option value="metro">Metro lineal</option>
          </select>
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <div>
          <label class="block text-[#103E4A] font-semibold mb-1">Stock</label>
          <input type="number" name="stock" value="50" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-[#103E4A] font-mono focus:bg-white focus:border-[#ED991F]">
        </div>
        <div>
          <label class="block text-[#103E4A] font-semibold mb-1">Mínimo</label>
          <input type="number" name="min_stock" value="20" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-[#103E4A] font-mono focus:bg-white focus:border-[#ED991F]">
        </div>
        <div>
          <label class="block text-[#103E4A] font-semibold mb-1">Costo Unit. (COP)</label>
          <input type="number" name="costo" value="25000" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-[#103E4A] font-mono focus:bg-white focus:border-[#ED991F]">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-[#103E4A] font-semibold mb-1">Proveedor</label>
          <input type="text" name="proveedor" placeholder="Distribuidora Local" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-[#103E4A] focus:bg-white focus:border-[#ED991F]">
        </div>
        <div>
          <label class="block text-[#103E4A] font-semibold mb-1">Ubicación</label>
          <input type="text" name="ubicacion" placeholder="Rack B-1" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-[#103E4A] focus:bg-white focus:border-[#ED991F]">
        </div>
      </div>
      <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#0D4F60]/15">
        <button type="button" onclick="document.getElementById('modalMaterial').classList.add('hidden');" class="px-4 py-2 rounded-xl bg-[#F0EEE8] hover:bg-[#E5E1D8] text-[#103E4A] font-medium transition-all">Cancelar</button>
        <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-[#ED991F] to-[#E58D17] text-[#103E4A] font-extrabold shadow hover:opacity-90 transition-all">Guardar Material</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

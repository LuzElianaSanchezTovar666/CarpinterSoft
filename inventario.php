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
  <div class="bg-stone-900/80 border border-stone-800 p-6 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 text-xs font-semibold border border-amber-500/20 mb-2">
        <i data-lucide="boxes" class="w-3.5 h-3.5"></i>
        Almacén & Materias Primas
      </div>
      <h2 class="text-xl sm:text-2xl font-black text-white font-display">Control de Inventario de Carpintería</h2>
      <p class="text-stone-400 text-xs sm:text-sm mt-1">Stock de maderas macizas, tableros melamínicos, herrajes de cazoleta y acabados.</p>
    </div>

    <div class="flex flex-wrap items-center gap-4">
      <div class="bg-stone-950 p-3 rounded-xl border border-stone-800">
        <span class="text-[10px] uppercase font-bold text-stone-400 block">Valorización Almacén</span>
        <span class="text-lg font-black text-emerald-400 font-mono"><?= formatCOP($totalValor) ?></span>
      </div>
      <button onclick="document.getElementById('modalMaterial').classList.remove('hidden'); document.getElementById('modalMaterial').classList.add('flex');" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-stone-950 font-bold text-xs sm:text-sm shadow-md">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Agregar Material</span>
      </button>
    </div>
  </div>

  <!-- Controles y Pestañas -->
  <div class="bg-stone-900/80 border border-stone-800 p-4 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
    <form method="GET" action="inventario.php" class="relative flex-1 max-w-md">
      <i data-lucide="search" class="w-4 h-4 text-stone-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
      <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Buscar material por código, nombre o proveedor..." class="w-full pl-10 pr-4 py-2 bg-stone-950/70 border border-stone-800 rounded-xl text-xs sm:text-sm text-stone-200 focus:outline-none focus:border-amber-500">
    </form>

    <div class="flex flex-wrap gap-1.5 bg-stone-950/60 p-1 rounded-xl border border-stone-800">
      <a href="inventario.php?cat=todas" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $categoriaSel === 'todas' ? 'bg-amber-600/30 text-amber-300 border border-amber-500/30' : 'text-stone-400 hover:text-stone-200' ?>">Todos</a>
      <a href="inventario.php?cat=madera" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $categoriaSel === 'madera' ? 'bg-amber-600/30 text-amber-300 border border-amber-500/30' : 'text-stone-400 hover:text-stone-200' ?>">🌲 Maderas</a>
      <a href="inventario.php?cat=tablero" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $categoriaSel === 'tablero' ? 'bg-amber-600/30 text-amber-300 border border-amber-500/30' : 'text-stone-400 hover:text-stone-200' ?>">🪵 Tableros</a>
      <a href="inventario.php?cat=herraje" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $categoriaSel === 'herraje' ? 'bg-amber-600/30 text-amber-300 border border-amber-500/30' : 'text-stone-400 hover:text-stone-200' ?>">🔩 Herrajes</a>
      <a href="inventario.php?cat=quimico" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $categoriaSel === 'quimico' ? 'bg-amber-600/30 text-amber-300 border border-amber-500/30' : 'text-stone-400 hover:text-stone-200' ?>">🧪 Químicos</a>
    </div>
  </div>

  <!-- Grid de Materiales -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($filtrado as $item): 
        $critico = $item['stock'] <= $item['min_stock'];
        $ratio = min(100, round(($item['stock'] / ($item['min_stock'] * 2)) * 100));
    ?>
      <div class="bg-stone-900/80 border <?= $critico ? 'border-rose-500/40' : 'border-stone-800' ?> rounded-2xl p-4 flex flex-col justify-between">
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-[10px] font-mono font-bold text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded border border-amber-400/20">
              <?= htmlspecialchars($item['codigo']) ?>
            </span>
            <?php if ($critico): ?>
              <span class="text-[10px] font-bold text-rose-400 bg-rose-500/20 px-2 py-0.5 rounded-full border border-rose-500/30">
                Stock Crítico
              </span>
            <?php else: ?>
              <span class="text-[10px] font-medium text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">
                Disponible
              </span>
            <?php endif; ?>
          </div>

          <div>
            <h4 class="text-sm font-bold text-white line-clamp-1"><?= htmlspecialchars($item['nombre']) ?></h4>
            <p class="text-xs text-stone-400 mt-0.5"><?= htmlspecialchars($item['descripcion'] ?? '') ?></p>
          </div>

          <div class="p-2 rounded-xl bg-stone-950/70 border border-stone-800 space-y-1 text-[11px] text-stone-400">
            <div>Ubicación: <span class="text-stone-200"><?= htmlspecialchars($item['ubicacion']) ?></span></div>
            <div>Proveedor: <span class="text-stone-200"><?= htmlspecialchars($item['proveedor']) ?></span></div>
          </div>

          <div>
            <div class="flex justify-between text-xs mb-1">
              <span class="text-stone-400">Existencias:</span>
              <span class="font-bold font-mono text-white"><?= $item['stock'] ?> <span class="text-stone-400 text-[11px] font-sans"><?= $item['unidad'] ?></span></span>
            </div>
            <div class="w-full bg-stone-950 h-2 rounded-full overflow-hidden border border-stone-800">
              <div class="h-full rounded-full <?= $critico ? 'bg-rose-500' : 'bg-amber-500' ?>" style="width: <?= $ratio ?>%"></div>
            </div>
            <div class="flex justify-between text-[10px] text-stone-500 mt-1">
              <span>Mínimo: <?= $item['min_stock'] ?></span>
              <span>Costo: <?= formatCOP($item['costo']) ?></span>
            </div>
          </div>
        </div>

        <div class="mt-4 pt-3 border-t border-stone-800 flex items-center justify-between">
          <span class="text-[11px] text-stone-400">Ajustar stock:</span>
          <div class="flex items-center gap-2">
            <a href="api/actualizar_stock.php?id=<?= $item['id'] ?>&delta=-1" class="px-2.5 py-1 rounded-lg bg-stone-800 hover:bg-stone-700 text-stone-300 text-xs font-bold" title="Descontar 1 unidad utilizada">-1 Uso</a>
            <a href="api/actualizar_stock.php?id=<?= $item['id'] ?>&delta=1" class="px-2.5 py-1 rounded-lg bg-amber-600/30 hover:bg-amber-600 text-amber-300 hover:text-stone-950 text-xs font-bold border border-amber-500/30" title="Registrar 1 unidad recibida">+1 Ingreso</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</main>

<!-- Modal Nuevo Material -->
<div id="modalMaterial" class="fixed inset-0 z-50 bg-stone-950/80 backdrop-blur-sm hidden items-center justify-center p-4">
  <div class="bg-stone-900 border border-stone-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl my-auto">
    <h3 class="text-lg font-bold text-white font-display mb-4">Registrar Material al Almacén</h3>
    <form action="api/guardar_material.php" method="POST" class="space-y-4 text-xs">
      <div>
        <label class="block text-stone-300 font-semibold mb-1">Nombre del Material</label>
        <input type="text" name="nombre" required placeholder="Ej: Melamina Rovere 18mm" class="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-stone-300 font-semibold mb-1">Categoría</label>
          <select name="categoria" class="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-stone-200">
            <option value="madera">Madera Maciza</option>
            <option value="tablero">Tablero / Melamina</option>
            <option value="herraje">Herraje</option>
            <option value="quimico">Químico / Acabado</option>
          </select>
        </div>
        <div>
          <label class="block text-stone-300 font-semibold mb-1">Unidad</label>
          <select name="unidad" class="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-stone-200">
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
          <label class="block text-stone-300 font-semibold mb-1">Stock</label>
          <input type="number" name="stock" value="50" class="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white font-mono">
        </div>
        <div>
          <label class="block text-stone-300 font-semibold mb-1">Mínimo</label>
          <input type="number" name="min_stock" value="20" class="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white font-mono">
        </div>
        <div>
          <label class="block text-stone-300 font-semibold mb-1">Costo Unit. (COP)</label>
          <input type="number" name="costo" value="25000" class="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white font-mono">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-stone-300 font-semibold mb-1">Proveedor</label>
          <input type="text" name="proveedor" placeholder="Distribuidora Local" class="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white">
        </div>
        <div>
          <label class="block text-stone-300 font-semibold mb-1">Ubicación</label>
          <input type="text" name="ubicacion" placeholder="Rack B-1" class="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white">
        </div>
      </div>
      <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-800">
        <button type="button" onclick="document.getElementById('modalMaterial').classList.add('hidden');" class="px-4 py-2 rounded-xl bg-stone-800 text-stone-300">Cancelar</button>
        <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-stone-950 font-bold">Guardar Material</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$clientes = getClientes();
$q = strtolower($_GET['q'] ?? '');

$filtrados = array_filter($clientes, function($c) use ($q) {
    if ($q === '') return true;
    $txt = strtolower($c['nombre'] . ' ' . $c['telefono'] . ' ' . $c['email'] . ' ' . $c['ciudad']);
    return strpos($txt, $q) !== false;
});
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">

  <!-- Banner -->
  <div class="bg-stone-900/80 border border-stone-800 p-6 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 text-xs font-semibold border border-amber-500/20 mb-2">
        <i data-lucide="users" class="w-3.5 h-3.5"></i>
        Directorio & CRM de Carpintería
      </div>
      <h2 class="text-xl sm:text-2xl font-black text-white font-display">Clientes & Propietarios</h2>
      <p class="text-stone-400 text-xs sm:text-sm mt-1">Historial de compras, direcciones de instalación y comunicación directa por WhatsApp.</p>
    </div>

    <button onclick="document.getElementById('modalCliente').classList.remove('hidden'); document.getElementById('modalCliente').classList.add('flex');" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-stone-950 font-bold text-xs sm:text-sm shadow-md">
      <i data-lucide="plus" class="w-4 h-4"></i>
      <span>Nuevo Cliente</span>
    </button>
  </div>

  <!-- Buscador -->
  <div class="bg-stone-900/80 border border-stone-800 p-4 rounded-2xl">
    <form method="GET" action="clientes.php" class="relative max-w-md">
      <i data-lucide="search" class="w-4 h-4 text-stone-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
      <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Buscar por nombre, teléfono, correo o ciudad..." class="w-full pl-10 pr-4 py-2 bg-stone-950/70 border border-stone-800 rounded-xl text-xs sm:text-sm text-stone-200 focus:outline-none focus:border-amber-500">
    </form>
  </div>

  <!-- Grid de Clientes -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($filtrados as $c): 
        $telLimpio = preg_replace('/[^0-9]/', '', $c['telefono']);
        $mensajeWa = urlencode("Hola {$c['nombre']}, te saludamos de CarpinterSoft Taller de Ebanistería. Queremos consultar los detalles de tu proyecto.");
    ?>
      <div class="bg-stone-900/80 border border-stone-800 hover:border-amber-500/40 rounded-2xl p-5 flex flex-col justify-between transition-all">
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-white font-display"><?= htmlspecialchars($c['nombre']) ?></h3>
            <span class="text-[10px] font-semibold text-stone-400 bg-stone-950 px-2.5 py-1 rounded-full border border-stone-800">
              <?= $c['pedidos_count'] ?? 0 ?> pedidos
            </span>
          </div>

          <div class="space-y-1.5 text-xs text-stone-300">
            <div class="flex items-center gap-2 text-stone-400">
              <i data-lucide="phone" class="w-3.5 h-3.5 text-amber-400 flex-shrink-0"></i>
              <span><?= htmlspecialchars($c['telefono']) ?></span>
            </div>
            <?php if (!empty($c['email'])): ?>
              <div class="flex items-center gap-2 text-stone-400 truncate">
                <i data-lucide="mail" class="w-3.5 h-3.5 text-stone-500 flex-shrink-0"></i>
                <span class="truncate"><?= htmlspecialchars($c['email']) ?></span>
              </div>
            <?php endif; ?>
            <div class="flex items-center gap-2 text-stone-400">
              <i data-lucide="map-pin" class="w-3.5 h-3.5 text-stone-500 flex-shrink-0"></i>
              <span><?= htmlspecialchars($c['direccion']) ?>, <?= htmlspecialchars($c['ciudad']) ?></span>
            </div>
          </div>

          <?php if (!empty($c['notas'])): ?>
            <div class="p-2.5 rounded-xl bg-stone-950/70 border border-stone-800 text-[11px] text-stone-400 italic">
              "<?= htmlspecialchars($c['notas']) ?>"
            </div>
          <?php endif; ?>
        </div>

        <div class="mt-4 pt-3 border-t border-stone-800 flex items-center justify-between">
          <div>
            <span class="text-[10px] text-stone-500 block uppercase font-semibold">Total Facturado</span>
            <span class="text-xs font-bold text-emerald-400 font-mono"><?= formatCOP($c['total_gastado'] ?? 0) ?></span>
          </div>

          <a href="https://wa.me/<?= $telLimpio ?>?text=<?= $mensajeWa ?>" target="_blank" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 text-xs font-bold transition-all">
            <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
            <span>WhatsApp</span>
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</main>

<!-- Modal Nuevo Cliente -->
<div id="modalCliente" class="fixed inset-0 z-50 bg-stone-950/80 backdrop-blur-sm hidden items-center justify-center p-4">
  <div class="bg-stone-900 border border-stone-800 rounded-2xl max-w-md w-full p-6 shadow-2xl my-auto">
    <h3 class="text-lg font-bold text-white font-display mb-4">Registrar Nuevo Cliente</h3>
    <form action="api/guardar_cliente.php" method="POST" class="space-y-3.5 text-xs">
      <div>
        <label class="block text-stone-300 font-semibold mb-1">Nombre Completo *</label>
        <input type="text" name="nombre" required placeholder="Ej: Arq. Fernando Torres" class="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white">
      </div>
      <div>
        <label class="block text-stone-300 font-semibold mb-1">Teléfono / WhatsApp *</label>
        <input type="text" name="telefono" required placeholder="+57 312 000 0000" class="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white">
      </div>
      <div>
        <label class="block text-stone-300 font-semibold mb-1">Correo Electrónico</label>
        <input type="email" name="email" placeholder="cliente@correo.com" class="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-stone-300 font-semibold mb-1">Dirección Entrega</label>
          <input type="text" name="direccion" placeholder="Calle 45 #12-30" class="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white">
        </div>
        <div>
          <label class="block text-stone-300 font-semibold mb-1">Ciudad</label>
          <input type="text" name="ciudad" value="Bogotá, D.C." class="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white">
        </div>
      </div>
      <div>
        <label class="block text-stone-300 font-semibold mb-1">Notas / Preferencias</label>
        <textarea name="notas" rows="2" placeholder="Observaciones sobre gustos de madera..." class="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white"></textarea>
      </div>
      <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-800">
        <button type="button" onclick="document.getElementById('modalCliente').classList.add('hidden');" class="px-4 py-2 rounded-xl bg-stone-800 text-stone-300">Cancelar</button>
        <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-stone-950 font-bold">Guardar Cliente</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

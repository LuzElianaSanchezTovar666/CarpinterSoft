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
  <div class="bg-[#103E4A]/80 border border-[#0D4F60] p-6 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xl">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#09252D] text-[#ED991F] text-xs font-semibold border border-[#ED991F]/30 mb-2">
        <i data-lucide="users" class="w-3.5 h-3.5"></i>
        Directorio & CRM de Carpintería
      </div>
      <h2 class="text-xl sm:text-2xl font-black text-[#FFFFFF] font-display">Clientes & Propietarios</h2>
      <p class="text-[#F0EEE8]/80 text-xs sm:text-sm mt-1">Historial de compras, direcciones de instalación y comunicación directa por WhatsApp.</p>
    </div>

    <button onclick="document.getElementById('modalCliente').classList.remove('hidden'); document.getElementById('modalCliente').classList.add('flex');" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#ED991F] to-[#E58D17] text-[#103E4A] font-extrabold text-xs sm:text-sm shadow-md hover:opacity-90">
      <i data-lucide="plus" class="w-4 h-4"></i>
      <span>Nuevo Cliente</span>
    </button>
  </div>

  <!-- Buscador -->
  <div class="bg-[#103E4A]/80 border border-[#0D4F60] p-4 rounded-2xl shadow-md">
    <form method="GET" action="clientes.php" class="relative max-w-md">
      <i data-lucide="search" class="w-4 h-4 text-[#F0EEE8]/60 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
      <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Buscar por nombre, teléfono, correo o ciudad..." class="w-full pl-10 pr-4 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-xs sm:text-sm text-[#FFFFFF] focus:outline-none focus:border-[#ED991F]">
    </form>
  </div>

  <!-- Grid de Clientes -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($filtrados as $c): 
        $telLimpio = preg_replace('/[^0-9]/', '', $c['telefono']);
        $mensajeWa = urlencode("Hola {$c['nombre']}, te saludamos de CarpinterSoft Taller de Ebanistería. Queremos consultar los detalles de tu proyecto.");
    ?>
      <div class="bg-[#103E4A]/80 border border-[#0D4F60] hover:border-[#ED991F]/50 rounded-2xl p-5 flex flex-col justify-between transition-all shadow-md">
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-[#FFFFFF] font-display"><?= htmlspecialchars($c['nombre']) ?></h3>
            <span class="text-[10px] font-semibold text-[#F7F5EF] bg-[#0D4F60] px-2.5 py-1 rounded-full border border-[#0D4F60]">
              <?= $c['pedidos_count'] ?? 0 ?> pedidos
            </span>
          </div>

          <div class="space-y-1.5 text-xs text-[#F0EEE8]">
            <div class="flex items-center gap-2 text-[#F0EEE8]/80">
              <i data-lucide="phone" class="w-3.5 h-3.5 text-[#ED991F] flex-shrink-0"></i>
              <span><?= htmlspecialchars($c['telefono']) ?></span>
            </div>
            <?php if (!empty($c['email'])): ?>
              <div class="flex items-center gap-2 text-[#F0EEE8]/80 truncate">
                <i data-lucide="mail" class="w-3.5 h-3.5 text-[#F0EEE8]/60 flex-shrink-0"></i>
                <span class="truncate"><?= htmlspecialchars($c['email']) ?></span>
              </div>
            <?php endif; ?>
            <div class="flex items-center gap-2 text-[#F0EEE8]/80">
              <i data-lucide="map-pin" class="w-3.5 h-3.5 text-[#F0EEE8]/60 flex-shrink-0"></i>
              <span><?= htmlspecialchars($c['direccion']) ?>, <?= htmlspecialchars($c['ciudad']) ?></span>
            </div>
          </div>

          <?php if (!empty($c['notas'])): ?>
            <div class="p-2.5 rounded-xl bg-[#09252D] border border-[#0D4F60] text-[11px] text-[#F0EEE8]/70 italic">
              "<?= htmlspecialchars($c['notas']) ?>"
            </div>
          <?php endif; ?>
        </div>

        <div class="mt-4 pt-3 border-t border-[#0D4F60] flex items-center justify-between">
          <div>
            <span class="text-[10px] text-[#F0EEE8]/60 block uppercase font-semibold">Total Facturado</span>
            <span class="text-xs font-bold text-[#ED991F] font-mono"><?= formatCOP($c['total_gastado'] ?? 0) ?></span>
          </div>

          <a href="https://wa.me/<?= $telLimpio ?>?text=<?= $mensajeWa ?>" target="_blank" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#0D4F60] hover:bg-[#ED991F] text-[#ED991F] hover:text-[#103E4A] border border-[#ED991F]/40 text-xs font-bold transition-all shadow">
            <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
            <span>WhatsApp</span>
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</main>

<!-- Modal Nuevo Cliente -->
<div id="modalCliente" class="fixed inset-0 z-50 bg-[#09252D]/85 backdrop-blur-sm hidden items-center justify-center p-4">
  <div class="bg-[#103E4A] border border-[#0D4F60] rounded-2xl max-w-md w-full p-6 shadow-2xl my-auto">
    <h3 class="text-lg font-bold text-[#FFFFFF] font-display mb-4">Registrar Nuevo Cliente</h3>
    <form action="api/guardar_cliente.php" method="POST" class="space-y-3.5 text-xs">
      <div>
        <label class="block text-[#F7F5EF] font-semibold mb-1">Nombre Completo *</label>
        <input type="text" name="nombre" required placeholder="Ej: Arq. Fernando Torres" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white">
      </div>
      <div>
        <label class="block text-[#F7F5EF] font-semibold mb-1">Teléfono / WhatsApp *</label>
        <input type="text" name="telefono" required placeholder="+57 312 000 0000" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white">
      </div>
      <div>
        <label class="block text-[#F7F5EF] font-semibold mb-1">Correo Electrónico</label>
        <input type="email" name="email" placeholder="cliente@correo.com" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-[#F7F5EF] font-semibold mb-1">Dirección Entrega</label>
          <input type="text" name="direccion" placeholder="Calle 45 #12-30" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white">
        </div>
        <div>
          <label class="block text-[#F7F5EF] font-semibold mb-1">Ciudad</label>
          <input type="text" name="ciudad" value="Bogotá, D.C." class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white">
        </div>
      </div>
      <div>
        <label class="block text-[#F7F5EF] font-semibold mb-1">Notas / Preferencias</label>
        <textarea name="notas" rows="2" placeholder="Observaciones sobre gustos de madera..." class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white"></textarea>
      </div>
      <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#0D4F60]">
        <button type="button" onclick="document.getElementById('modalCliente').classList.add('hidden');" class="px-4 py-2 rounded-xl bg-[#09252D] text-[#F7F5EF]">Cancelar</button>
        <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-[#ED991F] to-[#E58D17] text-[#103E4A] font-extrabold shadow">Guardar Cliente</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

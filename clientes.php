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
  <div class="bg-white border border-[#0D4F60]/20 p-6 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#103E4A] text-[#ED991F] text-xs font-semibold border border-[#ED991F]/30 mb-2">
        <i data-lucide="users" class="w-3.5 h-3.5"></i>
        Directorio & CRM de Carpintería
      </div>
      <h2 class="text-xl sm:text-2xl font-black text-[#103E4A] font-display">Clientes & Propietarios</h2>
      <p class="text-[#103E4A]/70 text-xs sm:text-sm mt-1">Historial de compras, direcciones de instalación y comunicación directa por WhatsApp.</p>
    </div>

    <button onclick="document.getElementById('modalCliente').classList.remove('hidden'); document.getElementById('modalCliente').classList.add('flex');" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#ED991F] to-[#E58D17] text-[#103E4A] font-extrabold text-xs sm:text-sm shadow-md hover:opacity-90 transition-all cursor-pointer">
      <i data-lucide="plus" class="w-4 h-4"></i>
      <span>Nuevo Cliente</span>
    </button>
  </div>

  <!-- Buscador -->
  <div class="bg-white border border-[#0D4F60]/20 p-4 rounded-2xl shadow-sm">
    <form method="GET" action="clientes.php" class="relative max-w-md">
      <i data-lucide="search" class="w-4 h-4 text-[#103E4A]/50 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
      <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Buscar por nombre, teléfono, correo o ciudad..." class="w-full pl-10 pr-4 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-xs sm:text-sm text-[#103E4A] placeholder-[#103E4A]/50 focus:bg-white focus:outline-none focus:border-[#ED991F] transition-all">
    </form>
  </div>

  <!-- Grid de Clientes -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($filtrados as $c): 
        $telLimpio = preg_replace('/[^0-9]/', '', $c['telefono']);
        $mensajeWa = urlencode("Hola {$c['nombre']}, te saludamos de CarpinterSoft Taller de Ebanistería. Queremos consultar los detalles de tu proyecto.");
    ?>
      <div class="bg-white border border-[#0D4F60]/20 hover:border-[#ED991F] rounded-2xl p-5 flex flex-col justify-between transition-all shadow-sm hover:shadow-md">
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-[#103E4A] font-display"><?= htmlspecialchars($c['nombre']) ?></h3>
            <span class="text-[10px] font-semibold text-[#103E4A] bg-[#F7F5EF] px-2.5 py-1 rounded-full border border-[#0D4F60]/20">
              <?= $c['pedidos_count'] ?? 0 ?> pedidos
            </span>
          </div>

          <div class="space-y-1.5 text-xs text-[#103E4A]">
            <div class="flex items-center gap-2 text-[#103E4A]/80">
              <i data-lucide="phone" class="w-3.5 h-3.5 text-[#ED991F] flex-shrink-0"></i>
              <span class="font-medium"><?= htmlspecialchars($c['telefono']) ?></span>
            </div>
            <?php if (!empty($c['email'])): ?>
              <div class="flex items-center gap-2 text-[#103E4A]/80 truncate">
                <i data-lucide="mail" class="w-3.5 h-3.5 text-[#103E4A]/60 flex-shrink-0"></i>
                <span class="truncate"><?= htmlspecialchars($c['email']) ?></span>
              </div>
            <?php endif; ?>
            <div class="flex items-center gap-2 text-[#103E4A]/80">
              <i data-lucide="map-pin" class="w-3.5 h-3.5 text-[#103E4A]/60 flex-shrink-0"></i>
              <span><?= htmlspecialchars($c['direccion']) ?>, <?= htmlspecialchars($c['ciudad']) ?></span>
            </div>
          </div>

          <?php if (!empty($c['notas'])): ?>
            <div class="p-2.5 rounded-xl bg-[#F7F5EF] border border-[#0D4F60]/15 text-[11px] text-[#103E4A]/80 italic">
              "<?= htmlspecialchars($c['notas']) ?>"
            </div>
          <?php endif; ?>
        </div>

        <div class="mt-4 pt-3 border-t border-[#0D4F60]/15 flex items-center justify-between">
          <div>
            <span class="text-[10px] text-[#103E4A]/60 block uppercase font-semibold">Total Facturado</span>
            <span class="text-xs font-bold text-[#103E4A] font-mono"><?= formatCOP($c['total_gastado'] ?? 0) ?></span>
          </div>

          <a href="https://wa.me/<?= $telLimpio ?>?text=<?= $mensajeWa ?>" target="_blank" class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#103E4A] hover:bg-[#ED991F] text-white hover:text-[#103E4A] border border-[#103E4A] text-xs font-bold transition-all shadow-xs cursor-pointer">
            <i data-lucide="message-square" class="w-3.5 h-3.5 text-[#ED991F]"></i>
            <span>WhatsApp</span>
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</main>

<!-- Modal Nuevo Cliente -->
<div id="modalCliente" class="fixed inset-0 z-50 bg-[#103E4A]/60 backdrop-blur-sm hidden items-center justify-center p-4">
  <div class="bg-white border border-[#0D4F60]/20 rounded-2xl max-w-md w-full p-6 shadow-2xl my-auto">
    <div class="flex items-center justify-between pb-3 border-b border-[#0D4F60]/15 mb-4">
      <div class="flex items-center gap-2">
        <i data-lucide="user-plus" class="w-5 h-5 text-[#ED991F]"></i>
        <h3 class="text-lg font-bold text-[#103E4A] font-display">Registrar Nuevo Cliente</h3>
      </div>
      <button type="button" onclick="document.getElementById('modalCliente').classList.add('hidden');" class="p-1 rounded-lg text-[#103E4A]/50 hover:text-[#103E4A] hover:bg-[#F0EEE8]">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>

    <form action="api/guardar_cliente.php" method="POST" class="space-y-3.5 text-xs">
      <div>
        <label class="block text-[#103E4A] font-semibold mb-1">Nombre Completo *</label>
        <input type="text" name="nombre" required placeholder="Ej: Arq. Fernando Torres" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-[#103E4A] focus:bg-white focus:outline-none focus:border-[#ED991F]">
      </div>
      <div>
        <label class="block text-[#103E4A] font-semibold mb-1">Teléfono / WhatsApp *</label>
        <input type="text" name="telefono" required placeholder="+57 312 000 0000" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-[#103E4A] focus:bg-white focus:outline-none focus:border-[#ED991F]">
      </div>
      <div>
        <label class="block text-[#103E4A] font-semibold mb-1">Correo Electrónico</label>
        <input type="email" name="email" placeholder="cliente@correo.com" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-[#103E4A] focus:bg-white focus:outline-none focus:border-[#ED991F]">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-[#103E4A] font-semibold mb-1">Dirección Entrega</label>
          <input type="text" name="direccion" placeholder="Calle 45 #12-30" class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-[#103E4A] focus:bg-white focus:outline-none focus:border-[#ED991F]">
        </div>
        <div>
          <label class="block text-[#103E4A] font-semibold mb-1">Ciudad</label>
          <input type="text" name="ciudad" value="Bogotá, D.C." class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-[#103E4A] focus:bg-white focus:border-[#ED991F]">
        </div>
      </div>
      <div>
        <label class="block text-[#103E4A] font-semibold mb-1">Notas / Preferencias</label>
        <textarea name="notas" rows="2" placeholder="Observaciones sobre gustos de madera..." class="w-full px-3 py-2 bg-[#F7F5EF] border border-[#0D4F60]/20 rounded-xl text-[#103E4A] focus:bg-white focus:outline-none focus:border-[#ED991F]"></textarea>
      </div>
      <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#0D4F60]/15">
        <button type="button" onclick="document.getElementById('modalCliente').classList.add('hidden');" class="px-4 py-2 rounded-xl bg-[#F0EEE8] hover:bg-[#E5E1D8] text-[#103E4A] font-medium transition-all">Cancelar</button>
        <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-[#ED991F] to-[#E58D17] text-[#103E4A] font-extrabold shadow hover:opacity-90 transition-all">Guardar Cliente</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

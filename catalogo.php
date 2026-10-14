<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$catalogo = getCatalogo();

$catSel = $_GET['cat'] ?? 'todas';
$filtrado = array_filter($catalogo, function($m) use ($catSel) {
    return ($catSel === 'todas' || ($m['categoria'] ?? '') === $catSel);
});
?>

<main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">

  <!-- Banner -->
  <div class="bg-[#103E4A]/80 border border-[#0D4F60] p-6 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xl">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#09252D] text-[#ED991F] text-xs font-semibold border border-[#ED991F]/30 mb-2">
        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
        Showroom & Diseños de Referencia
      </div>
      <h2 class="text-xl sm:text-2xl font-black text-[#FFFFFF] font-display">Catálogo de Mobiliario & Ebanistería</h2>
      <p class="text-[#F0EEE8]/80 text-xs sm:text-sm mt-1">Haz clic en "Cotizar Este" para cargar automáticamente las dimensiones y materiales en la calculadora de despiece.</p>
    </div>
  </div>

  <!-- Filtros -->
  <div class="flex flex-wrap gap-1.5 bg-[#09252D] p-1 rounded-xl border border-[#0D4F60]">
    <a href="catalogo.php?cat=todas" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $catSel === 'todas' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30' : 'text-[#F0EEE8]/70 hover:text-[#FFFFFF]' ?>">Todos</a>
    <a href="catalogo.php?cat=Cocina" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $catSel === 'Cocina' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30' : 'text-[#F0EEE8]/70 hover:text-[#FFFFFF]' ?>">Cocinas</a>
    <a href="catalogo.php?cat=Mesa" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $catSel === 'Mesa' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30' : 'text-[#F0EEE8]/70 hover:text-[#FFFFFF]' ?>">Comedores</a>
    <a href="catalogo.php?cat=Closet" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $catSel === 'Closet' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30' : 'text-[#F0EEE8]/70 hover:text-[#FFFFFF]' ?>">Clósets</a>
    <a href="catalogo.php?cat=Puerta" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $catSel === 'Puerta' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30' : 'text-[#F0EEE8]/70 hover:text-[#FFFFFF]' ?>">Puertas</a>
    <a href="catalogo.php?cat=Especial" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $catSel === 'Especial' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30' : 'text-[#F0EEE8]/70 hover:text-[#FFFFFF]' ?>">Centros TV</a>
    <a href="catalogo.php?cat=Oficina" class="px-3 py-1.5 rounded-lg text-xs font-semibold <?= $catSel === 'Oficina' ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30' : 'text-[#F0EEE8]/70 hover:text-[#FFFFFF]' ?>">Oficinas</a>
  </div>

  <!-- Grid de Muebles -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($filtrado as $mueble): ?>
      <div class="bg-[#103E4A]/80 border border-[#0D4F60] hover:border-[#ED991F]/50 rounded-2xl overflow-hidden flex flex-col justify-between transition-all group shadow-xl">
        <div>
          <div class="relative h-56 overflow-hidden bg-[#09252D]">
            <img src="<?= htmlspecialchars($mueble['imagen']) ?>" alt="<?= htmlspecialchars($mueble['nombre']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-[#09252D] via-[#09252D]/20 to-transparent"></div>
            <div class="absolute top-3 left-3">
              <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#09252D]/90 text-[#ED991F] border border-[#ED991F]/30">
                <?= $mueble['categoria'] ?>
              </span>
            </div>
            <div class="absolute top-3 right-3">
              <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-[#09252D]/90 text-[#F7F5EF] border border-[#0D4F60] flex items-center gap-1">
                <i data-lucide="calendar" class="w-3 h-3 text-[#ED991F]"></i>
                <?= $mueble['dias'] ?> días fab.
              </span>
            </div>
            <div class="absolute bottom-3 left-3 right-3">
              <h3 class="text-base font-bold text-[#FFFFFF] font-display leading-snug drop-shadow-md">
                <?= htmlspecialchars($mueble['nombre']) ?>
              </h3>
            </div>
          </div>

          <div class="p-4 space-y-3">
            <p class="text-xs text-[#F0EEE8]/80 leading-relaxed"><?= htmlspecialchars($mueble['descripcion']) ?></p>

            <div class="p-2.5 rounded-xl bg-[#09252D] border border-[#0D4F60] space-y-1.5 text-xs">
              <div class="flex items-center justify-between">
                <span class="text-[#F0EEE8]/70 flex items-center gap-1">
                  <i data-lucide="ruler" class="w-3.5 h-3.5 text-[#ED991F]"></i>
                  Medidas ref:
                </span>
                <span class="font-mono text-[#FFFFFF] text-[11px]"><?= htmlspecialchars($mueble['dimensiones']) ?></span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-[#F0EEE8]/70 flex items-center gap-1">
                  <i data-lucide="layers" class="w-3.5 h-3.5 text-[#ED991F]"></i>
                  Material base:
                </span>
                <span class="text-[#FFFFFF] text-[11px] truncate max-w-[170px]"><?= htmlspecialchars($mueble['material']) ?></span>
              </div>
            </div>
          </div>
        </div>

        <div class="p-4 pt-3 border-t border-[#0D4F60] flex items-center justify-between bg-[#09252D]/50">
          <div>
            <span class="text-[10px] text-[#F0EEE8]/70 uppercase font-semibold block">Precio Referencial</span>
            <span class="text-base font-bold text-[#ED991F] font-mono"><?= formatCOP($mueble['precio']) ?></span>
          </div>

          <a href="despiece.php?categoria=<?= urlencode($mueble['categoria']) ?>&proyecto=<?= urlencode($mueble['nombre']) ?>&madera=<?= urlencode($mueble['material']) ?>" class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#0D4F60] hover:bg-[#ED991F] text-[#ED991F] hover:text-[#103E4A] border border-[#ED991F]/40 text-xs font-bold transition-all shadow">
            <i data-lucide="scissors" class="w-3.5 h-3.5"></i>
            <span>Cotizar Este</span>
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

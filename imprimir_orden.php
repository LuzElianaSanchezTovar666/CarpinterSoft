<?php
require_once __DIR__ . '/config/db.php';

$id = $_GET['id'] ?? null;
$pedido = null;

if ($id) {
    $pedido = getPedidoById($id);
}

if (!$pedido) {
    $pedidos = getPedidos();
    $pedido = $pedidos[0] ?? null;
}

if (!$pedido) {
    die("No se encontró el pedido solicitado.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Hoja de Taller - Orden #<?= htmlspecialchars($pedido['codigo']) ?> - CarpinterSoft</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Outfit:wght@600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .font-display { font-family: 'Outfit', sans-serif; }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
    @media print {
      .no-print { display: none !important; }
      body { background: white !important; color: black !important; padding: 0 !important; }
    }
  </style>
</head>
<body class="bg-[#F7F5EF] text-stone-900 p-4 sm:p-8 flex flex-col items-center">

  <!-- Botones de Impresión (No visibles al imprimir) -->
  <div class="no-print max-w-4xl w-full mb-6 flex items-center justify-between bg-white p-4 rounded-2xl border border-[#0D4F60]/20 text-[#103E4A] shadow-sm">
    <div class="flex items-center gap-2">
      <span class="text-xs font-bold text-[#103E4A] bg-[#ED991F] px-2 py-0.5 rounded font-mono">ORDEN #<?= htmlspecialchars($pedido['codigo']) ?></span>
      <span class="text-xs text-[#103E4A]/70">• Vista de Impresión Oficial de Taller</span>
    </div>
    <div class="flex items-center gap-3">
      <button onclick="window.print()" class="px-5 py-2 bg-gradient-to-r from-[#ED991F] to-[#E58D17] text-[#103E4A] font-extrabold text-xs rounded-xl shadow cursor-pointer hover:opacity-90 transition-all">
        🖨️ Imprimir / Guardar PDF
      </button>
      <a href="pedidos.php" class="px-4 py-2 bg-[#F0EEE8] hover:bg-[#103E4A] hover:text-white text-[#103E4A] text-xs font-semibold rounded-xl border border-[#0D4F60]/20 transition-all">
        Volver a Pedidos
      </a>
    </div>
  </div>

  <!-- Documento Físico Imprimible -->
  <div class="max-w-4xl w-full bg-[#FFFFFF] p-8 sm:p-12 rounded-3xl shadow-2xl print:shadow-none print:rounded-none">
    
    <!-- Membrete con Colores Azul Petróleo y Naranja -->
    <div class="flex justify-between items-start border-b-2 border-[#103E4A] pb-4 mb-6">
      <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-[#103E4A] text-[#ED991F] flex items-center justify-center font-black text-2xl shadow">
          🪚
        </div>
        <div>
          <h1 class="text-2xl font-black text-[#103E4A] tracking-tight font-display">
            CARPINTER<span class="text-[#ED991F]">SOFT</span>
          </h1>
          <p class="text-xs text-stone-600 font-semibold">Taller de Ebanistería & Carpintería Fina</p>
          <p class="text-[11px] text-stone-500">Diseño, Despiece de Tableros y Muebles a Medida</p>
        </div>
      </div>

      <div class="text-right">
        <div class="inline-block px-3 py-1 bg-[#F7F5EF] text-[#103E4A] font-mono font-bold text-sm rounded border border-[#0D4F60]/30">
          ORDEN #<?= htmlspecialchars($pedido['codigo']) ?>
        </div>
        <p class="text-xs text-stone-600 mt-1">Fecha Emisión: <?= date('Y-m-d') ?></p>
        <p class="text-xs font-bold text-[#E58D17]">Fecha Entrega: <?= $pedido['fecha_entrega'] ?></p>
      </div>
    </div>

    <!-- Datos Cliente & Proyecto -->
    <div class="grid grid-cols-2 gap-6 bg-[#F7F5EF] p-4 rounded-xl border border-[#F0EEE8] mb-6 text-xs">
      <div>
        <span class="font-bold text-[#103E4A] uppercase tracking-wider block mb-1">Datos del Cliente</span>
        <p class="font-bold text-sm text-stone-900"><?= htmlspecialchars($pedido['cliente']) ?></p>
        <p class="text-stone-600 mt-0.5">Teléfono: <?= htmlspecialchars($pedido['telefono'] ?? 'N/A') ?></p>
        <p class="text-stone-600">Dirección: <?= htmlspecialchars($pedido['direccion'] ?? 'Taller Central') ?></p>
      </div>

      <div>
        <span class="font-bold text-[#103E4A] uppercase tracking-wider block mb-1">Detalles del Mueble</span>
        <p class="font-bold text-sm text-stone-900"><?= htmlspecialchars($pedido['proyecto']) ?></p>
        <p class="text-stone-600 mt-0.5">
          Dimensiones: <span class="font-mono font-bold text-[#103E4A]"><?= $pedido['alto'] ?> x <?= $pedido['ancho'] ?> x <?= $pedido['fondo'] ?> cm</span>
        </p>
        <p class="text-stone-600">Madera / Tablero: <span class="font-semibold text-stone-800"><?= htmlspecialchars($pedido['madera']) ?></span></p>
        <p class="text-stone-600">Acabado: <span class="font-semibold text-stone-800"><?= htmlspecialchars($pedido['acabado']) ?></span></p>
        <p class="text-stone-600">Operario a Cargo: <span class="font-semibold text-stone-800"><?= htmlspecialchars($pedido['carpintero']) ?></span></p>
      </div>
    </div>

    <!-- Plano de Despiece de Corte para el Operario -->
    <div class="mb-6">
      <h2 class="text-xs font-bold uppercase tracking-wider text-[#103E4A] mb-2 border-b border-stone-300 pb-1">
        Plano de Despiece de Corte (Para Banco de Trabajo y Escuadradora)
      </h2>
      <table class="w-full text-left text-xs border border-stone-300">
        <thead class="bg-[#F0EEE8] text-[#103E4A] font-bold uppercase text-[10px]">
          <tr class="border-b border-stone-300">
            <th class="p-2 border-r border-stone-300">#</th>
            <th class="p-2 border-r border-stone-300">Pieza</th>
            <th class="p-2 text-center border-r border-stone-300">Cant.</th>
            <th class="p-2 text-center border-r border-stone-300">Largo (cm)</th>
            <th class="p-2 text-center border-r border-stone-300">Ancho (cm)</th>
            <th class="p-2 text-center border-r border-stone-300">Espesor</th>
            <th class="p-2 border-r border-stone-300">Material</th>
            <th class="p-2">Tapacanto</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-stone-200 font-mono text-[11px]">
          <?php if (!empty($pedido['piezas'])): ?>
            <?php foreach ($pedido['piezas'] as $idx => $pz): ?>
              <tr class="<?= $idx % 2 === 0 ? 'bg-white' : 'bg-[#F7F5EF]/60' ?>">
                <td class="p-2 border-r border-stone-200 text-stone-500"><?= $idx + 1 ?></td>
                <td class="p-2 border-r border-stone-200 font-sans font-bold text-stone-900"><?= htmlspecialchars($pz['nombre']) ?></td>
                <td class="p-2 text-center border-r border-stone-200 font-bold text-[#ED991F]"><?= $pz['cantidad'] ?></td>
                <td class="p-2 text-center border-r border-stone-200"><?= $pz['largo'] ?></td>
                <td class="p-2 text-center border-r border-stone-200"><?= $pz['ancho'] ?></td>
                <td class="p-2 text-center border-r border-stone-200"><?= $pz['espesor'] ?> mm</td>
                <td class="p-2 border-r border-stone-200 font-sans text-stone-700"><?= htmlspecialchars($pz['material']) ?></td>
                <td class="p-2 font-sans text-stone-600"><?= htmlspecialchars($pz['canto']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="p-4 text-center text-stone-500 font-sans">
                Sin lista detallada de despiece registrada.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Liquidación Económica -->
    <div class="flex justify-end mb-8">
      <div class="w-72 bg-[#F7F5EF] p-4 rounded-xl border border-stone-200 space-y-2 text-xs">
        <div class="flex justify-between">
          <span class="text-stone-600">Precio Total Cotizado:</span>
          <span class="font-bold font-mono text-stone-900"><?= formatCOP($pedido['precio']) ?></span>
        </div>
        <div class="flex justify-between text-emerald-800">
          <span>Anticipo Inicial (Abonado):</span>
          <span class="font-bold font-mono"><?= formatCOP($pedido['anticipo']) ?></span>
        </div>
        <div class="flex justify-between border-t-2 border-stone-300 pt-2 font-bold text-[#E58D17] text-sm">
          <span>Saldo Pendiente Entrega:</span>
          <span class="font-mono"><?= formatCOP($pedido['precio'] - $pedido['anticipo']) ?></span>
        </div>
      </div>
    </div>

    <!-- Firmas -->
    <div class="grid grid-cols-2 gap-12 pt-8 border-t border-stone-300 text-xs text-center text-stone-600">
      <div>
        <div class="border-b border-stone-400 mb-2 h-14"></div>
        <p class="font-bold text-stone-900"><?= htmlspecialchars($pedido['cliente']) ?></p>
        <p class="text-[11px]">Aceptación y Visto Bueno del Cliente</p>
      </div>
      <div>
        <div class="border-b border-stone-400 mb-2 h-14"></div>
        <p class="font-bold text-stone-900"><?= htmlspecialchars($pedido['carpintero'] ?? 'Maestro Ebanista') ?></p>
        <p class="text-[11px]">Control de Calidad & Taller CarpinterSoft</p>
      </div>
    </div>

  </div>

</body>
</html>

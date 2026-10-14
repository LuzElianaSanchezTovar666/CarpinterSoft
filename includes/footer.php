<footer class="no-print border-t border-[#0D4F60]/80 bg-[#09252D] py-6 mt-auto text-xs text-[#F0EEE8]/50 text-center">
  <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="flex items-center gap-2">
      <div class="w-5 h-5 rounded-md bg-[#ED991F]/20 text-[#ED991F] flex items-center justify-center">
        <i data-lucide="hammer" class="w-3 h-3"></i>
      </div>
      <span class="font-bold text-[#F7F5EF]/80 font-display">CarpinterSoft 2026</span>
      <span>• Sistema Integral de Gestión para Carpintería y Ebanistería (PHP & MySQL)</span>
    </div>
    <div class="text-[11px] text-[#F0EEE8]/40">
      Optimización de corte de tableros, control de banco de trabajo e inventario
    </div>
  </div>
</footer>

<!-- MODAL: NUEVO PEDIDO -->
<div id="modalNuevoPedido" class="fixed inset-0 z-50 bg-[#09252D]/90 backdrop-blur-sm hidden items-center justify-center p-3 sm:p-6 overflow-y-auto">
  <div class="bg-[#103E4A] border border-[#0D4F60] rounded-3xl max-w-2xl w-full max-h-[92vh] flex flex-col shadow-2xl my-auto">
    <div class="p-5 border-b border-[#0D4F60] flex items-center justify-between bg-[#09252D]/60 rounded-t-3xl">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-xl bg-[#ED991F]/10 border border-[#ED991F]/20 flex items-center justify-center text-[#ED991F]">
          <i data-lucide="plus-circle" class="w-4 h-4"></i>
        </div>
        <div>
          <h3 class="text-base font-bold text-[#FFFFFF] font-display">Registrar Nuevo Proyecto de Taller</h3>
          <p class="text-xs text-[#F0EEE8]/60">Ingresa las especificaciones del mueble y cliente</p>
        </div>
      </div>
      <button onclick="cerrarModalNuevoPedido()" class="p-1.5 rounded-xl bg-[#0D4F60] hover:bg-[#1A677B] text-[#F0EEE8]/70 hover:text-white transition-colors">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>

    <form action="api/guardar_pedido.php" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1 text-xs">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="sm:col-span-2">
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1">Nombre del Mueble / Proyecto *</label>
          <input type="text" name="proyecto" required placeholder="Ej: Clóset Principal con Zapatero" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white focus:outline-none focus:border-[#ED991F] placeholder-[#F0EEE8]/30">
        </div>
        <div>
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1">Tipo de Mueble</label>
          <select name="categoria" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-[#F7F5EF] focus:outline-none focus:border-[#ED991F]">
            <option value="Cocina">Cocina Integral</option>
            <option value="Closet">Clóset / Vestidor</option>
            <option value="Mesa">Mesa / Comedor</option>
            <option value="Puerta">Puerta Principal</option>
            <option value="Bano">Mueble de Baño</option>
            <option value="Oficina">Oficina / Escritorio</option>
            <option value="Especial">Mueble Especial</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1">Cliente *</label>
          <input type="text" name="cliente" required placeholder="Ej: Arq. Carlos López" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white focus:outline-none focus:border-[#ED991F] placeholder-[#F0EEE8]/30">
        </div>
        <div>
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1">Teléfono / WhatsApp</label>
          <input type="text" name="telefono" placeholder="+57 310 000 0000" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white focus:outline-none focus:border-[#ED991F] placeholder-[#F0EEE8]/30">
        </div>
        <div>
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1">Dirección Entrega</label>
          <input type="text" name="direccion" placeholder="Calle 100 #15-20" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white focus:outline-none focus:border-[#ED991F] placeholder-[#F0EEE8]/30">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1">Madera / Tablero</label>
          <input type="text" name="madera" value="Melamina Roble Sinatra 18mm" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white focus:outline-none focus:border-[#ED991F]">
        </div>
        <div>
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1">Acabado / Cantos</label>
          <input type="text" name="acabado" value="Canto PVC 2mm en frentes" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white focus:outline-none focus:border-[#ED991F]">
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <div>
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1 text-center">Alto (cm)</label>
          <input type="number" name="alto" value="210" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white text-center font-mono focus:outline-none focus:border-[#ED991F]">
        </div>
        <div>
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1 text-center">Ancho (cm)</label>
          <input type="number" name="ancho" value="180" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white text-center font-mono focus:outline-none focus:border-[#ED991F]">
        </div>
        <div>
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1 text-center">Fondo (cm)</label>
          <input type="number" name="fondo" value="60" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white text-center font-mono focus:outline-none focus:border-[#ED991F]">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1">Precio Venta (COP)</label>
          <input type="number" name="precio" value="3800000" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white font-mono focus:outline-none focus:border-[#ED991F]">
        </div>
        <div>
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1">Anticipo Recibido (COP)</label>
          <input type="number" name="anticipo" value="1900000" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white font-mono focus:outline-none focus:border-[#ED991F]">
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <div>
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1">Fecha Entrega</label>
          <input type="date" name="fecha_entrega" value="<?= date('Y-m-d', strtotime('+15 days')) ?>" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white focus:outline-none focus:border-[#ED991F]">
        </div>
        <div>
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1">Carpintero</label>
          <input type="text" name="carpintero" value="Maestro Carlos Echeverry" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white focus:outline-none focus:border-[#ED991F]">
        </div>
        <div>
          <label class="block text-[#F7F5EF]/80 font-semibold mb-1">Prioridad</label>
          <select name="prioridad" class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-[#F7F5EF] focus:outline-none focus:border-[#ED991F]">
            <option value="baja">Baja</option>
            <option value="media" selected>Media</option>
            <option value="alta">Alta</option>
            <option value="urgente">Urgente</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-[#F7F5EF]/80 font-semibold mb-1">Observaciones / Herrajes</label>
        <textarea name="notas" rows="2" placeholder="Ej: Requiere bisagras de 110 grados y correderas de extensión..." class="w-full px-3 py-2 bg-[#09252D] border border-[#0D4F60] rounded-xl text-white focus:outline-none focus:border-[#ED991F] placeholder-[#F0EEE8]/30"></textarea>
      </div>

      <div class="pt-4 border-t border-[#0D4F60] flex items-center justify-end gap-3">
        <button type="button" onclick="cerrarModalNuevoPedido()" class="px-4 py-2 rounded-xl bg-[#0D4F60] text-[#F7F5EF] font-semibold hover:bg-[#1A677B] transition-colors">
          Cancelar
        </button>
        <button type="submit" class="px-6 py-2 rounded-xl bg-gradient-to-r from-[#ED991F] to-[#E58D17] hover:from-[#F3B14F] hover:to-[#ED991F] text-[#103E4A] font-bold shadow-lg shadow-orange-950/40 transition-all active:scale-95">
          Crear Orden de Taller
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: ASISTENTE CARPINTERIA -->
<div id="modalCarpinterIA" class="fixed inset-0 z-50 bg-[#09252D]/90 backdrop-blur-sm hidden items-center justify-center p-3 sm:p-6 overflow-y-auto">
  <div class="bg-[#103E4A] border border-[#0D4F60] rounded-3xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl my-auto">
    <div class="p-4 sm:p-5 border-b border-[#0D4F60] flex items-center justify-between bg-[#09252D]/70 rounded-t-3xl">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#ED991F] to-[#E58D17] flex items-center justify-center text-[#103E4A] shadow-lg shadow-orange-950/40">
          <i data-lucide="bot" class="w-5 h-5 text-[#103E4A]"></i>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h3 class="text-base font-bold text-white font-display">Carpinter<span class="text-[#ED991F]">IA</span> Asesor Técnico PHP</h3>
            <span class="text-[10px] bg-[#ED991F]/10 text-[#ED991F] font-bold px-2 py-0.5 rounded-full border border-[#ED991F]/20">Experto Taller</span>
          </div>
          <p class="text-xs text-[#F0EEE8]/60">Maderas, maquinado, tolerancias de tapacanto y acabados</p>
        </div>
      </div>
      <button onclick="cerrarModalCarpinterIA()" class="p-1.5 rounded-xl bg-[#0D4F60] hover:bg-[#1A677B] text-[#F0EEE8]/70 hover:text-white transition-colors">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>

    <div id="chatCarpinterIA" class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1 text-xs">
      <div class="flex flex-col items-start">
        <div class="max-w-[85%] rounded-2xl rounded-tl-none p-4 leading-relaxed bg-[#09252D] border border-[#0D4F60] text-[#F7F5EF] space-y-2">
          <p>👋 ¡Hola Maestro! Soy <strong>CarpinterIA</strong>. Puedo resolver dudas sobre maderas (Roble, Cedro, Teca, Pino, Melaminas), holguras de correderas telescópicas, brocas de cazoleta 35mm o acabados de poliuretano 2K. ¿Qué consulta tienes para el taller hoy?</p>
        </div>
      </div>
    </div>

    <!-- Preguntas rápidas -->
    <div class="p-2.5 bg-[#09252D]/60 border-t border-[#0D4F60]/80 overflow-x-auto no-scrollbar flex gap-2">
      <button onclick="preguntarIA('¿Qué madera recomiendas para mesón de cocina resistente a humedad?')" class="text-[11px] whitespace-nowrap px-3 py-1.5 rounded-xl bg-[#0D4F60] hover:bg-[#1A677B] text-[#F7F5EF] border border-[#0D4F60]/80 transition-colors">Mesón de Cocina</button>
      <button onclick="preguntarIA('¿Cómo calcular la holgura lateral para correderas telescópicas?')" class="text-[11px] whitespace-nowrap px-3 py-1.5 rounded-xl bg-[#0D4F60] hover:bg-[#1A677B] text-[#F7F5EF] border border-[#0D4F60]/80 transition-colors">Holgura Correderas</button>
      <button onclick="preguntarIA('¿Cómo descontar el espesor del tapacanto PVC 2mm en el corte?')" class="text-[11px] whitespace-nowrap px-3 py-1.5 rounded-xl bg-[#0D4F60] hover:bg-[#1A677B] text-[#F7F5EF] border border-[#0D4F60]/80 transition-colors">Descuento Tapacanto</button>
      <button onclick="preguntarIA('¿Qué acabado es mejor para intemperie: poliuretano o aceite?')" class="text-[11px] whitespace-nowrap px-3 py-1.5 rounded-xl bg-[#0D4F60] hover:bg-[#1A677B] text-[#F7F5EF] border border-[#0D4F60]/80 transition-colors">Acabado Intemperie</button>
    </div>

    <div class="p-4 border-t border-[#0D4F60] bg-[#09252D] rounded-b-3xl">
      <div class="flex items-center gap-2">
        <input type="text" id="inputPreguntaIA" placeholder="Pregunta técnica sobre maderas, tornillos, ensambles o despiece..." class="flex-1 px-4 py-2.5 bg-[#103E4A] border border-[#0D4F60] rounded-xl text-xs text-white focus:outline-none focus:border-[#ED991F] placeholder-[#F0EEE8]/30" onkeydown="if(event.key==='Enter') enviarPreguntaIA()">
        <button onclick="enviarPreguntaIA()" class="p-2.5 rounded-xl bg-gradient-to-r from-[#ED991F] to-[#E58D17] hover:from-[#F3B14F] hover:to-[#ED991F] text-[#103E4A] font-bold transition-all active:scale-95">
          <i data-lucide="send" class="w-4 h-4"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  // Inicializar Iconos
  lucide.createIcons();

  // Funciones de Modales
  function abrirModalNuevoPedido() {
    const m = document.getElementById('modalNuevoPedido');
    m.classList.remove('hidden');
    m.classList.add('flex');
    lucide.createIcons();
  }

  function cerrarModalNuevoPedido() {
    const m = document.getElementById('modalNuevoPedido');
    m.classList.add('hidden');
    m.classList.remove('flex');
  }

  function abrirModalCarpinterIA() {
    const m = document.getElementById('modalCarpinterIA');
    m.classList.remove('hidden');
    m.classList.add('flex');
    lucide.createIcons();
  }

  function cerrarModalCarpinterIA() {
    const m = document.getElementById('modalCarpinterIA');
    m.classList.add('hidden');
    m.classList.remove('flex');
  }

  // Respuestas del Asistente Técnico en PHP/JS
  function preguntarIA(texto) {
    document.getElementById('inputPreguntaIA').value = texto;
    enviarPreguntaIA();
  }

  function enviarPreguntaIA() {
    const input = document.getElementById('inputPreguntaIA');
    const pregunta = input.value.trim();
    if (!pregunta) return;

    const chat = document.getElementById('chatCarpinterIA');
    
    // Mensaje Usuario
    chat.innerHTML += `
      <div class="flex flex-col items-end">
        <div class="max-w-[85%] rounded-2xl rounded-tr-none p-3 bg-[#ED991F] text-[#103E4A] font-semibold">
          ${pregunta}
        </div>
      </div>
    `;

    input.value = '';
    chat.scrollTop = chat.scrollHeight;

    // Simular respuesta técnica inmediata
    setTimeout(() => {
      let respuesta = "🌳 <strong>Consejo Técnico de Ebanistería:</strong> Para cualquier trabajo en taller, verifica siempre el porcentaje de humedad de la madera (10%-12%) antes del maquinado fino.";
      const q = pregunta.toLowerCase();

      if (q.includes('cocina') || q.includes('humedad') || q.includes('meson')) {
        respuesta = "🌳 <strong>Recomendación para Mesones de Cocina:</strong><br>• <strong>Madera Maciza:</strong> Teca de cultivo o Roble estufado con sellado de poliuretano catalizado 2K.<br>• <strong>Melamina:</strong> Tablero RH (Resistente a Humedad con resina verde interior) y tapacanto rígido PVC 2mm sellado con cola PUR.";
      } else if (q.includes('corredera') || q.includes('gaveta') || q.includes('cajon')) {
        respuesta = "🔩 <strong>Holgura para Correderas Telescópicas:</strong><br>• Deja siempre <strong>12.7 mm (1/2 pulgada)</strong> de luz libre a cada costado del cajón (total ancho exterior del cajón = ancho interno del mueble menos 25.4 mm).";
      } else if (q.includes('tapacanto') || q.includes('canto') || q.includes('descuento')) {
        respuesta = "🪚 <strong>Descuento de Tapacanto en Despiece:</strong><br>• Si aplicas canto de <strong>2 mm</strong> en ambos costados, debes restar exactamente <strong>4 mm</strong> a la medida de corte de la sierra para que la pieza terminada mida lo especificado en el plano.";
      } else if (q.includes('intemperie') || q.includes('exterior') || q.includes('acabado')) {
        respuesta = "🎨 <strong>Acabados para Intemperie:</strong><br>• Usa <strong>Laca marina poliuretánica con filtro UV</strong> para puertas exteriores, o <strong>Aceite de Teca</strong> con retocado anual para muebles de terraza.";
      }

      chat.innerHTML += `
        <div class="flex flex-col items-start">
          <div class="max-w-[85%] rounded-2xl rounded-tl-none p-4 bg-[#09252D] border border-[#0D4F60] text-[#F7F5EF] leading-relaxed">
            ${respuesta}
          </div>
        </div>
      `;
      chat.scrollTop = chat.scrollHeight;
      lucide.createIcons();
    }, 400);
  }
</script>

</body>
</html>

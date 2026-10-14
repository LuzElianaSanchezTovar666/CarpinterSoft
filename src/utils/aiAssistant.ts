import { GoogleGenAI } from '@google/genai';

interface AIResponse {
  answer: string;
  category: 'maderas' | 'acabados' | 'herrajes' | 'calculos' | 'general';
  tips?: string[];
}

export async function askCarpinterIA(question: string): Promise<AIResponse> {
  // Check if GEMINI_API_KEY is available
  const apiKey =
    (typeof process !== 'undefined' && process.env?.GEMINI_API_KEY) ||
    (typeof import.meta !== 'undefined' && (import.meta as any).env?.VITE_GEMINI_API_KEY);

  if (apiKey && apiKey !== 'MY_GEMINI_API_KEY') {
    try {
      const ai = new GoogleGenAI({ apiKey });
      const prompt = `Actúa como un Maestro Ebanista y Experto Técnico en Carpintería, optimización de cortes, tipos de maderas colombianas/latinoamericanas (Cedro, Roble, Pino, Teca, Melaminas 15mm y 18mm), herrajes y acabados.
Responde de forma clara, técnica, precisa y práctica a la siguiente consulta del taller de carpintería:
"${question}"
Entrega consejos concretos y medidas o especificaciones aplicables directamente al taller.`;

      const response = await ai.models.generateContent({
        model: 'gemini-2.5-flash',
        contents: prompt,
      });

      if (response && response.text) {
        return {
          answer: response.text,
          category: 'general',
          tips: [
            'Verificar siempre el sentido de la veta antes del corte primario.',
            'Dejar tolerancia de 2mm a 3mm para holguras de dilatación y cantos.',
          ],
        };
      }
    } catch (err) {
      console.warn('Gemini API query error, using offline carpentry knowledge engine:', err);
    }
  }

  // Motor offline de conocimiento experto de carpintería y ebanistería
  const q = question.toLowerCase();

  if (q.includes('madera') || q.includes('roble') || q.includes('cedro') || q.includes('teca') || q.includes('pino')) {
    return {
      category: 'maderas',
      answer: `🌳 **Guía Técnica de Maderas para Taller:**
• **Roble Americano/Andino:** Densidad alta (aprox 720-760 kg/m³). Excelente para cubiertas de comedor, estructuras y pisos. Requiere brocas afiladas y perforación guía previa para tornillos.
• **Cedro Caquetá/Real:** Densidad media (aprox 500 kg/m³). Es la madera reina para puertas principales y ventanas exteriores por su estabilidad dimensional, aroma natural y resistencia a termitas y hongos.
• **Teca de Cultivo:** Rica en resinas y aceites naturales. Insuperable para mesones de baño, cocinas y muebles de terraza/intemperie.
• **Pino Pátula:** Económico y liviano (450 kg/m³). Debe usarse estufado e inmunizado al vacío para evitar torceduras por humedad.`,
      tips: [
        'Asegurar siempre humedad entre 10% y 12% con higrómetro antes de ensamblar.',
        'En maderas duras como roble, avellanar siempre el agujero para tornillo.',
      ],
    };
  }

  if (q.includes('acabado') || q.includes('pintura') || q.includes('poliuretano') || q.includes('barniz') || q.includes('laca') || q.includes('aceite')) {
    return {
      category: 'acabados',
      answer: `🎨 **Sistemas de Acabado Profesional para Madera:**
• **Poliuretano Catalizado 2K (Altos Sólidos):** Máxima resistencia a rayones, calor y humedad. Ideal para cubiertas de comedores, barras de cocina y vanitorios.
• **Laca Nitrocelulosa Semimate:** Secado ultrarrápido, fácil de retocar. Recomendada para muebles de dormitorio, interiores de clóset y molduras decorativas.
• **Aceite Danés / Cera de Abejas con Linaza:** Deja el poro abierto con tacto sedoso y realza la veta natural. Excelente para juguetes, tablas de corte y muebles rústicos.
• **Sellador Lijable:** Aplicar mínimo 2 manos intermedias con lija #240 y #320 entre capas antes del acabado final.`,
      tips: [
        'Aplicar en ambiente con menos del 70% de humedad relativa para evitar velamiento.',
        'Lijar siempre en la dirección longitudinal de la fibra de la madera.',
      ],
    };
  }

  if (q.includes('herraje') || q.includes('bisagra') || q.includes('corredera') || q.includes('riel') || q.includes('holgura')) {
    return {
      category: 'herrajes',
      answer: `🔩 **Estándares de Herrajes y Medidas de Holgura:**
• **Bisagras de Cazoleta 35mm:**
  - Perforación a 21.5mm - 22mm del borde de la puerta.
  - Profundidad de cazoleta: 11.5mm a 12mm.
  - Puertas hasta 90cm: 2 bisagras. De 90cm a 160cm: 3 bisagras. De 160cm a 220cm: 4 bisagras.
• **Correderas Telescópicas para Gavetas:**
  - Espacio lateral libre requerido: 12.7mm (1/2 pulgada) por cada lado del cajón (total luz mueble menos 25.4mm).
  - Usar correderas pesadas de 45kg con amortiguador hidráulico en gavetas caceroleras de más de 60cm de ancho.`,
      tips: [
        'Para puertas con canto de 2mm, descontar 3mm totales en ancho y alto para evitar roces.',
        'Instalar tornillos de fijación con torque medio para no falsear el aglomerado.',
      ],
    };
  }

  if (q.includes('despiece') || q.includes('corte') || q.includes('tablero') || q.includes('optimizar') || q.includes('melamina')) {
    return {
      category: 'calculos',
      answer: `🪚 **Reglas de Oro para Despiece y Optimización de Melamina:**
1. **Dimensiones de Tablero Estándar:** 2.44m x 1.83m (o 2.44m x 1.22m). Área total: ~4.46 m².
2. **Espesor de Corte de Sierra (Kerf):** Descontar siempre entre 3.2mm y 4mm por cada pasada del disco escuadrador o incisor.
3. **Sentido de Veta:** En melaminas maderadas (ej. Roble, Nogal), los frentes, puertas y laterales deben cortarse con la veta en vertical (largo de la pieza).
4. **Descuento por Tapacanto:** Si usas canto de 2mm en ambos lados, resta 4mm a la medida de corte de la placa para que la pieza final quede exacta al plano.`,
      tips: [
        'El disco principal debe estar afilado y el disco incisor alineado para evitar astillado.',
        'Aprovechamiento recomendado del tablero: entre 80% y 88%.',
      ],
    };
  }

  // Default response
  return {
    category: 'general',
    answer: `🪵 **Asistente Técnico CarpinterSoft:**
En el taller de carpintería y ebanistería, la precisión de milímetros y la elección adecuada del ensamble garantizan muebles duraderos.
• **Estructura:** Para ensambles ocultos en melamina usa pernos Minifix con tarugo de madera estriado de 8mm.
• **Madera sólida:** Prefiere uniones de caja y espiga o espigas flotantes (Festool Domino o tarugos de guayacán) con cola PVA D3.
• **Presupuestos:** Calcula siempre un 10% adicional por desperdicio de placa y horas hombre basadas en complejidad.`,
    tips: [
      'Puedes usar la pestaña "Calculadora & Despiece" para generar planos de corte automáticos.',
      'En la pestaña "Inventario" puedes controlar stock mínimo de maderas y herrajes.',
    ],
  };
}

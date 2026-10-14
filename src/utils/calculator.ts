import { CutPiece, QuickQuoteParams } from '../types';

export interface CalculatedQuoteResult {
  pieces: CutPiece[];
  totalAreaM2: number;
  sheetsNeeded: number;
  edgeBandMeters: number;
  hingesCount: number;
  slidesCount: number;
  handlesCount: number;
  materialCost: number;
  hardwareCost: number;
  consumablesCost: number;
  laborCost: number;
  subtotalCost: number;
  suggestedSalePrice: number;
  estimatedProfit: number;
}

export function calculateFurnitureQuote(params: QuickQuoteParams): CalculatedQuoteResult {
  const {
    furnitureType,
    woodType,
    height,
    width,
    depth,
    doorsCount,
    drawersCount,
    shelvesCount,
    includeHardware,
    edgeBandType,
    profitMarginPercent,
  } = params;

  const pieces: CutPiece[] = [];
  const thickness = 18; // mm estándar

  // Generar despiece paramétrico realista según el tipo de mueble
  if (furnitureType === 'Cocina' || furnitureType === 'Closet' || furnitureType === 'Oficina' || furnitureType === 'Bano') {
    // Laterales (2 unidades)
    pieces.push({
      id: 'pc-lat',
      partName: 'Laterales Estructurales (Costados)',
      quantity: 2,
      length: height,
      width: depth,
      thickness,
      material: woodType,
      edgeBanding: edgeBandType,
      grainDirection: 'largo',
    });

    // Techo y Suelo / Base
    const innerWidth = Math.max(10, width - 2 * (thickness / 10)); // cm
    pieces.push({
      id: 'pc-bases',
      partName: 'Techo y Suelo (Bases superior/inferior)',
      quantity: 2,
      length: Number(innerWidth.toFixed(1)),
      width: depth,
      thickness,
      material: woodType,
      edgeBanding: edgeBandType,
      grainDirection: 'ancho',
    });

    // Repisas o Entrepaños
    if (shelvesCount > 0) {
      pieces.push({
        id: 'pc-repisas',
        partName: 'Entrepaños / Repisas Interiores',
        quantity: shelvesCount,
        length: Number((innerWidth - 0.2).toFixed(1)),
        width: Math.max(10, depth - 2),
        thickness,
        material: woodType,
        edgeBanding: edgeBandType,
        grainDirection: 'ancho',
      });
    }

    // Fondo (MDF 3mm)
    pieces.push({
      id: 'pc-fondo',
      partName: 'Fondo Trasero de Soporte',
      quantity: 1,
      length: Math.max(10, height - 2),
      width: Math.max(10, width - 2),
      thickness: 3,
      material: 'MDF 3mm Revestido',
      edgeBanding: 'Sin canto',
      grainDirection: 'indistinto',
    });

    // Puertas
    if (doorsCount > 0) {
      const doorWidth = Number(((width - 0.4) / doorsCount).toFixed(1));
      const doorHeight = Number((height - 0.4).toFixed(1));
      pieces.push({
        id: 'pc-puertas',
        partName: 'Puertas Batientes Frontales',
        quantity: doorsCount,
        length: doorHeight,
        width: doorWidth,
        thickness,
        material: woodType,
        edgeBanding: 'PVC 2mm en los 4 cantos',
        grainDirection: 'largo',
      });
    }

    // Gavetas / Cajones
    if (drawersCount > 0) {
      const drawerFrontHeight = Number(((height * 0.4) / drawersCount).toFixed(1));
      pieces.push({
        id: 'pc-frentes-gaveta',
        partName: 'Frentes de Gaveta',
        quantity: drawersCount,
        length: drawerFrontHeight,
        width: Number((innerWidth - 0.4).toFixed(1)),
        thickness,
        material: woodType,
        edgeBanding: 'PVC 2mm en los 4 cantos',
        grainDirection: 'ancho',
      });
      // Cajones interiores (laterales, testeros)
      pieces.push({
        id: 'pc-cajas-gaveta',
        partName: 'Laterales Interiores de Cajón',
        quantity: drawersCount * 2,
        length: Math.max(30, depth - 5),
        width: Math.max(10, drawerFrontHeight - 5),
        thickness: 15,
        material: 'MDF / Melamina Blanco 15mm',
        edgeBanding: 'Canto delgado 0.5mm',
        grainDirection: 'largo',
      });
    }
  } else if (furnitureType === 'Mesa') {
    // Cubierta Superior
    pieces.push({
      id: 'pc-tapa-mesa',
      partName: 'Cubierta Superior de Mesa Principal',
      quantity: 1,
      length: width,
      width: depth,
      thickness: 38,
      material: woodType,
      edgeBanding: 'Biselado madera maciza',
      grainDirection: 'largo',
    });
    // Patas de Mesa
    pieces.push({
      id: 'pc-patas-mesa',
      partName: 'Patas Estructurales Reforzadas',
      quantity: 4,
      length: Math.max(40, height - 4),
      width: 10,
      thickness: 75,
      material: woodType,
      edgeBanding: 'Aristas suavizadas',
      grainDirection: 'largo',
    });
    // Faldones / Travesaños tensores
    pieces.push({
      id: 'pc-travesanos',
      partName: 'Faldones y Travesaños de Amarre',
      quantity: 4,
      length: Math.max(20, width - 25),
      width: 12,
      thickness: 25,
      material: woodType,
      edgeBanding: 'Cepillado 4 caras',
      grainDirection: 'largo',
    });
  } else if (furnitureType === 'Puerta') {
    // Montantes
    pieces.push({
      id: 'pc-montantes',
      partName: 'Montantes Verticales Estructurales',
      quantity: 2,
      length: height,
      width: 14,
      thickness: 45,
      material: woodType,
      edgeBanding: 'Rebaje para batiente',
      grainDirection: 'largo',
    });
    // Peinazos
    pieces.push({
      id: 'pc-peinazos',
      partName: 'Peinazos Horizontales (Superior/Inferior)',
      quantity: 3,
      length: Math.max(20, width - 28),
      width: 16,
      thickness: 45,
      material: woodType,
      edgeBanding: 'Espigas dobles',
      grainDirection: 'ancho',
    });
    // Tableros centrales
    pieces.push({
      id: 'pc-tableros-puerta',
      partName: 'Tableros Realzados Moldurados',
      quantity: 2,
      length: Math.max(20, (height - 45) / 2),
      width: Math.max(20, width - 30),
      thickness: 25,
      material: woodType,
      edgeBanding: 'Moldura perimetral',
      grainDirection: 'largo',
    });
  } else {
    // Mueble Especial
    pieces.push({
      id: 'pc-esp-1',
      partName: 'Estructura Principal del Mueble',
      quantity: 2,
      length: height,
      width: width,
      thickness,
      material: woodType,
      edgeBanding: edgeBandType,
      grainDirection: 'largo',
    });
    pieces.push({
      id: 'pc-esp-2',
      partName: 'Repisas y Divisiones Interiores',
      quantity: shelvesCount > 0 ? shelvesCount : 3,
      length: width,
      width: depth,
      thickness,
      material: woodType,
      edgeBanding: edgeBandType,
      grainDirection: 'ancho',
    });
  }

  // Cálculos de superficie y materiales
  let totalAreaM2 = 0;
  let edgeBandMeters = 0;

  pieces.forEach((piece) => {
    // área en m²
    const areaPiece = (piece.length / 100) * (piece.width / 100) * piece.quantity;
    totalAreaM2 += areaPiece;

    // perimetro de cantos (estimado según tipo de borde)
    if (piece.edgeBanding.includes('4 cantos')) {
      edgeBandMeters += ((piece.length * 2 + piece.width * 2) / 100) * piece.quantity;
    } else if (piece.edgeBanding.includes('frontal') || piece.edgeBanding.includes('canto')) {
      edgeBandMeters += ((piece.length * 2) / 100) * piece.quantity;
    }
  });

  // Planchas de melamina o madera requeridas (área tablero estándar 2.44m x 1.83m = 4.46 m2)
  // Con factor de aprovechamiento y cortes de disco (aprox 80% rendimiento)
  const usableSheetArea = 4.46 * 0.8;
  const sheetsNeeded = Math.max(1, Math.ceil(totalAreaM2 / usableSheetArea));

  // Herrajes
  const hingesCount = doorsCount * (height > 120 ? 3 : 2);
  const slidesCount = drawersCount;
  const handlesCount = doorsCount + drawersCount;

  // Costos unitarios de referencia
  let sheetPrice = 280000;
  if (woodType.toLowerCase().includes('roble')) sheetPrice = 310000;
  if (woodType.toLowerCase().includes('cedro')) sheetPrice = 330000;
  if (woodType.toLowerCase().includes('blanco')) sheetPrice = 220000;

  const materialCost = sheetsNeeded * sheetPrice;
  const edgeBandCost = Math.round(edgeBandMeters * 1800);

  const hardwareCost = includeHardware
    ? hingesCount * 9500 + slidesCount * 42000 + handlesCount * 14500
    : 0;

  // Insumos de taller (pegante, tornillería, lija, empaque) aprox 8%
  const consumablesCost = Math.round((materialCost + hardwareCost) * 0.08);

  // Mano de obra (carpintero, despiece, enchapado y ensamble)
  const estimatedHours = Math.max(8, pieces.length * 2.5 + drawersCount * 2 + doorsCount * 1.5);
  const hourRate = 28000; // COP/hora carpintero
  const laborCost = Math.round(estimatedHours * hourRate);

  const subtotalCost = materialCost + edgeBandCost + hardwareCost + consumablesCost + laborCost;

  // Precio de venta sugerido con margen de utilidad
  const marginMultiplier = 1 / (1 - profitMarginPercent / 100);
  const suggestedSalePrice = Math.round((subtotalCost * marginMultiplier) / 10000) * 10000;
  const estimatedProfit = suggestedSalePrice - subtotalCost;

  return {
    pieces,
    totalAreaM2: Number(totalAreaM2.toFixed(2)),
    sheetsNeeded,
    edgeBandMeters: Number(edgeBandMeters.toFixed(1)),
    hingesCount,
    slidesCount,
    handlesCount,
    materialCost,
    hardwareCost,
    consumablesCost,
    laborCost,
    subtotalCost,
    suggestedSalePrice,
    estimatedProfit,
  };
}

export function formatCOP(amount: number): string {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    maximumFractionDigits: 0,
  }).format(amount);
}

import React, { useState, useMemo } from 'react';
import { QuickQuoteParams, FurnitureCategory, Order } from '../types';
import { calculateFurnitureQuote, formatCOP } from '../utils/calculator';
import { 
  Scissors, 
  Ruler, 
  Layers, 
  CheckCircle, 
  DollarSign, 
  Printer, 
  PlusCircle, 
  Sparkles, 
  Settings2, 
  ShieldCheck, 
  FileText, 
  RotateCcw,
  Hammer
} from 'lucide-react';

interface CutCalculatorViewProps {
  onConvertQuoteToOrder: (newOrder: Partial<Order>) => void;
  onPrintQuote: (quoteData: any) => void;
  initialParams?: Partial<QuickQuoteParams>;
}

export const CutCalculatorView: React.FC<CutCalculatorViewProps> = ({
  onConvertQuoteToOrder,
  onPrintQuote,
  initialParams,
}) => {
  const [params, setParams] = useState<QuickQuoteParams>({
    furnitureType: initialParams?.furnitureType || 'Cocina',
    projectName: initialParams?.projectName || 'Módulo de Cocina Integral Bajo',
    woodType: initialParams?.woodType || 'Melamina Pelíkano Roble Sinatra 18mm',
    finishType: initialParams?.finishType || 'Canto PVC 2mm en frentes y puertas',
    height: initialParams?.height || 90,
    width: initialParams?.width || 120,
    depth: initialParams?.depth || 60,
    doorsCount: initialParams?.doorsCount ?? 2,
    drawersCount: initialParams?.drawersCount ?? 3,
    shelvesCount: initialParams?.shelvesCount ?? 2,
    includeHardware: initialParams?.includeHardware ?? true,
    edgeBandType: initialParams?.edgeBandType || 'PVC 2mm en 4 cantos',
    profitMarginPercent: initialParams?.profitMarginPercent ?? 40,
  });

  const [customerName, setCustomerName] = useState('');
  const [customerPhone, setCustomerPhone] = useState('');

  // Cálculo en tiempo real
  const quoteResult = useMemo(() => {
    return calculateFurnitureQuote(params);
  }, [params]);

  const handleReset = () => {
    setParams({
      furnitureType: 'Cocina',
      projectName: 'Módulo de Cocina Integral Bajo',
      woodType: 'Melamina Pelíkano Roble Sinatra 18mm',
      finishType: 'Canto PVC 2mm en frentes y puertas',
      height: 90,
      width: 120,
      depth: 60,
      doorsCount: 2,
      drawersCount: 3,
      shelvesCount: 2,
      includeHardware: true,
      edgeBandType: 'PVC 2mm en 4 cantos',
      profitMarginPercent: 40,
    });
  };

  const handleCreateOrder = () => {
    const newOrderData: Partial<Order> = {
      projectName: params.projectName,
      customerName: customerName || 'Cliente Particular',
      customerPhone: customerPhone || '+57 300 000 0000',
      furnitureCategory: params.furnitureType,
      woodType: params.woodType,
      finishType: params.finishType,
      dimensions: {
        height: params.height,
        width: params.width,
        depth: params.depth,
      },
      status: 'cotizacion',
      priority: 'media',
      totalCost: quoteResult.subtotalCost,
      salePrice: quoteResult.suggestedSalePrice,
      depositPaid: 0,
      pieces: quoteResult.pieces,
      materials: [
        {
          id: 'mat-1',
          name: `Tableros ${params.woodType} (2.44x1.83m)`,
          quantity: quoteResult.sheetsNeeded,
          unit: 'plancha',
          unitPrice: Math.round(quoteResult.materialCost / quoteResult.sheetsNeeded),
          subtotal: quoteResult.materialCost,
        },
        {
          id: 'mat-2',
          name: `Tapacanto ${params.edgeBandType}`,
          quantity: quoteResult.edgeBandMeters,
          unit: 'metro',
          unitPrice: 1800,
          subtotal: Math.round(quoteResult.edgeBandMeters * 1800),
        },
        {
          id: 'mat-3',
          name: 'Herrajes (Bisagras, correderas y tiradores)',
          quantity: 1,
          unit: 'kit',
          unitPrice: quoteResult.hardwareCost,
          subtotal: quoteResult.hardwareCost,
        },
      ],
      notes: `Cotización paramétrica generada en CarpinterSoft. Rendimiento de tablero estimado: ${quoteResult.sheetsNeeded} planchas.`,
    };

    onConvertQuoteToOrder(newOrderData);
  };

  return (
    <div className="space-y-8 animate-fadeIn">
      {/* Título de la sección */}
      <div className="bg-stone-900/80 border border-stone-800 p-6 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 text-xs font-semibold border border-amber-500/20 mb-2">
            <Scissors className="w-3.5 h-3.5" />
            Optimizador & Cotizador Paramétrico
          </div>
          <h2 className="text-xl sm:text-2xl font-black text-white font-['Outfit']">
            Calculadora de Muebles & Despiece de Tableros
          </h2>
          <p className="text-stone-400 text-xs sm:text-sm mt-1">
            Ingresa las medidas finales del mueble y el sistema calculará el desglose de piezas para corte, cantidad de tableros, metros de tapacanto y costo total.
          </p>
        </div>

        <button
          onClick={handleReset}
          className="flex items-center gap-2 px-3 py-2 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-300 text-xs font-semibold transition-colors self-start md:self-auto cursor-pointer"
        >
          <RotateCcw className="w-3.5 h-3.5" />
          <span>Restablecer Valores</span>
        </button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {/* COLUMNA IZQUIERDA: PARÁMETROS DEL MUEBLE (4 COLS) */}
        <div className="lg:col-span-5 space-y-6">
          <div className="bg-stone-900/80 border border-stone-800 p-5 rounded-2xl space-y-4">
            <h3 className="text-sm font-bold text-white uppercase tracking-wider font-['Outfit'] flex items-center gap-2 border-b border-stone-800 pb-3">
              <Settings2 className="w-4 h-4 text-amber-400" />
              1. Especificaciones del Proyecto
            </h3>

            {/* Nombre del Proyecto */}
            <div>
              <label className="block text-xs font-semibold text-stone-300 mb-1">
                Nombre del Mueble / Proyecto
              </label>
              <input
                type="text"
                value={params.projectName}
                onChange={(e) => setParams({ ...params, projectName: e.target.value })}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500"
              />
            </div>

            {/* Categoría */}
            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="block text-xs font-semibold text-stone-300 mb-1">
                  Tipo de Mueble
                </label>
                <select
                  value={params.furnitureType}
                  onChange={(e) => setParams({ ...params, furnitureType: e.target.value as FurnitureCategory })}
                  className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-xs text-stone-200 focus:outline-none focus:border-amber-500 cursor-pointer"
                >
                  <option value="Cocina">Cocina Integral</option>
                  <option value="Closet">Clóset / Armario</option>
                  <option value="Mesa">Mesa de Comedor</option>
                  <option value="Puerta">Puerta Principal</option>
                  <option value="Bano">Mueble de Baño</option>
                  <option value="Oficina">Escritorio / Oficina</option>
                  <option value="Especial">Mueble Especial</option>
                </select>
              </div>

              {/* Material */}
              <div>
                <label className="block text-xs font-semibold text-stone-300 mb-1">
                  Madera / Tablero
                </label>
                <select
                  value={params.woodType}
                  onChange={(e) => setParams({ ...params, woodType: e.target.value })}
                  className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-xs text-stone-200 focus:outline-none focus:border-amber-500 cursor-pointer"
                >
                  <option value="Melamina Pelíkano Roble Sinatra 18mm">Melamina Roble 18mm</option>
                  <option value="Melamina Blanco Lino Mate 18mm">Melamina Blanco Mate 18mm</option>
                  <option value="Madera Roble Americano Macizo 2 pulgadas">Roble Americano Macizo</option>
                  <option value="Madera Cedro Caquetá Macizo 3 pulgadas">Cedro Caquetá Macizo</option>
                  <option value="Madera Teca de Cultivo">Teca de Cultivo</option>
                  <option value="Triplay Fenólico Calibrado 15mm">Triplay Fenólico 15mm</option>
                </select>
              </div>
            </div>

            {/* Dimensiones (Alto, Ancho, Fondo en cm) */}
            <div>
              <label className="block text-xs font-semibold text-stone-300 mb-1 flex items-center justify-between">
                <span>Dimensiones Finales (cm)</span>
                <span className="text-[11px] text-amber-400 font-mono">Alto x Ancho x Fondo</span>
              </label>
              <div className="grid grid-cols-3 gap-2">
                <div>
                  <input
                    type="number"
                    min="20"
                    max="400"
                    value={params.height}
                    onChange={(e) => setParams({ ...params, height: Number(e.target.value) || 0 })}
                    className="w-full px-2.5 py-2 bg-stone-950 border border-stone-800 rounded-xl text-xs text-white text-center font-mono focus:outline-none focus:border-amber-500"
                  />
                  <span className="block text-[10px] text-stone-400 text-center mt-1">Alto (cm)</span>
                </div>
                <div>
                  <input
                    type="number"
                    min="20"
                    max="500"
                    value={params.width}
                    onChange={(e) => setParams({ ...params, width: Number(e.target.value) || 0 })}
                    className="w-full px-2.5 py-2 bg-stone-950 border border-stone-800 rounded-xl text-xs text-white text-center font-mono focus:outline-none focus:border-amber-500"
                  />
                  <span className="block text-[10px] text-stone-400 text-center mt-1">Ancho (cm)</span>
                </div>
                <div>
                  <input
                    type="number"
                    min="15"
                    max="200"
                    value={params.depth}
                    onChange={(e) => setParams({ ...params, depth: Number(e.target.value) || 0 })}
                    className="w-full px-2.5 py-2 bg-stone-950 border border-stone-800 rounded-xl text-xs text-white text-center font-mono focus:outline-none focus:border-amber-500"
                  />
                  <span className="block text-[10px] text-stone-400 text-center mt-1">Fondo (cm)</span>
                </div>
              </div>
            </div>

            {/* Componentes internos: Puertas, Cajones, Repisas */}
            <div className="grid grid-cols-3 gap-2 pt-2 border-t border-stone-800">
              <div>
                <label className="block text-[11px] font-semibold text-stone-300 mb-1 text-center">
                  Puertas
                </label>
                <input
                  type="number"
                  min="0"
                  max="10"
                  value={params.doorsCount}
                  onChange={(e) => setParams({ ...params, doorsCount: Number(e.target.value) || 0 })}
                  className="w-full px-2 py-1.5 bg-stone-950 border border-stone-800 rounded-xl text-xs text-white text-center font-bold focus:outline-none focus:border-amber-500"
                />
              </div>

              <div>
                <label className="block text-[11px] font-semibold text-stone-300 mb-1 text-center">
                  Gavetas
                </label>
                <input
                  type="number"
                  min="0"
                  max="12"
                  value={params.drawersCount}
                  onChange={(e) => setParams({ ...params, drawersCount: Number(e.target.value) || 0 })}
                  className="w-full px-2 py-1.5 bg-stone-950 border border-stone-800 rounded-xl text-xs text-white text-center font-bold focus:outline-none focus:border-amber-500"
                />
              </div>

              <div>
                <label className="block text-[11px] font-semibold text-stone-300 mb-1 text-center">
                  Repisas
                </label>
                <input
                  type="number"
                  min="0"
                  max="15"
                  value={params.shelvesCount}
                  onChange={(e) => setParams({ ...params, shelvesCount: Number(e.target.value) || 0 })}
                  className="w-full px-2 py-1.5 bg-stone-950 border border-stone-800 rounded-xl text-xs text-white text-center font-bold focus:outline-none focus:border-amber-500"
                />
              </div>
            </div>

            {/* Tapacantos y Herrajes */}
            <div className="space-y-3 pt-2 border-t border-stone-800">
              <div>
                <label className="block text-xs font-semibold text-stone-300 mb-1">
                  Tipo de Canto / Tapacanto
                </label>
                <select
                  value={params.edgeBandType}
                  onChange={(e) => setParams({ ...params, edgeBandType: e.target.value })}
                  className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-xs text-stone-200 focus:outline-none focus:border-amber-500 cursor-pointer"
                >
                  <option value="PVC 2mm en 4 cantos">PVC 2mm Rígido (Máxima protección)</option>
                  <option value="PVC 0.5mm estándar">PVC 0.5mm Delgado</option>
                  <option value="Madera Maciza encolada">Canto Madera Maciza</option>
                  <option value="Sin canto (Madera cruda)">Sin Canto / Cepillado</option>
                </select>
              </div>

              <div className="flex items-center justify-between p-3 rounded-xl bg-stone-950 border border-stone-800">
                <div className="space-y-0.5">
                  <span className="text-xs font-semibold text-white">Incluir Kit de Herrajes</span>
                  <p className="text-[10px] text-stone-400">Bisagras cierre lento, correderas pesadas y manijas</p>
                </div>
                <input
                  type="checkbox"
                  checked={params.includeHardware}
                  onChange={(e) => setParams({ ...params, includeHardware: e.target.checked })}
                  className="w-4 h-4 accent-amber-500 rounded cursor-pointer"
                />
              </div>
            </div>

            {/* Margen de Utilidad (Slider) */}
            <div className="pt-2 border-t border-stone-800 space-y-2">
              <div className="flex items-center justify-between text-xs">
                <span className="font-semibold text-stone-300">Margen de Ganancia del Taller:</span>
                <span className="font-bold text-amber-400 font-mono text-sm">{params.profitMarginPercent}%</span>
              </div>
              <input
                type="range"
                min="15"
                max="75"
                step="5"
                value={params.profitMarginPercent}
                onChange={(e) => setParams({ ...params, profitMarginPercent: Number(e.target.value) })}
                className="w-full accent-amber-500 cursor-pointer"
              />
              <div className="flex justify-between text-[10px] text-stone-500">
                <span>15% (Costo amigos)</span>
                <span>40% (Estándar taller)</span>
                <span>75% (Alta gama)</span>
              </div>
            </div>

            {/* Datos Opcionales del Cliente para la Cotización */}
            <div className="pt-2 border-t border-stone-800 space-y-2">
              <span className="text-[11px] font-bold text-stone-400 uppercase tracking-wider">
                Datos de Cliente (Para emitir cotización):
              </span>
              <div className="grid grid-cols-2 gap-2">
                <input
                  type="text"
                  placeholder="Nombre Cliente"
                  value={customerName}
                  onChange={(e) => setCustomerName(e.target.value)}
                  className="w-full px-2.5 py-1.5 bg-stone-950 border border-stone-800 rounded-lg text-xs text-white placeholder-stone-500"
                />
                <input
                  type="text"
                  placeholder="Teléfono / WhatsApp"
                  value={customerPhone}
                  onChange={(e) => setCustomerPhone(e.target.value)}
                  className="w-full px-2.5 py-1.5 bg-stone-950 border border-stone-800 rounded-lg text-xs text-white placeholder-stone-500"
                />
              </div>
            </div>

          </div>
        </div>

        {/* COLUMNA DERECHA: RESULTADO DEL DESPIECE Y COSTOS (7 COLS) */}
        <div className="lg:col-span-7 space-y-6">
          
          {/* Tarjeta de Resumen Financiero y Métricas Rápidas */}
          <div className="bg-gradient-to-br from-stone-900 via-stone-900 to-amber-950/30 border border-amber-500/30 p-6 rounded-2xl shadow-xl">
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-6 border-b border-stone-800">
              <div className="space-y-1">
                <span className="text-[10px] uppercase font-bold text-stone-400">Área Estimada</span>
                <div className="text-xl font-black text-white font-mono">{quoteResult.totalAreaM2} m²</div>
                <span className="text-[10px] text-stone-500">{quoteResult.pieces.length} piezas corte</span>
              </div>

              <div className="space-y-1">
                <span className="text-[10px] uppercase font-bold text-stone-400">Tableros 2.44x1.83</span>
                <div className="text-xl font-black text-amber-400 font-mono">{quoteResult.sheetsNeeded} planchas</div>
                <span className="text-[10px] text-stone-500">aprovechamiento ~80%</span>
              </div>

              <div className="space-y-1">
                <span className="text-[10px] uppercase font-bold text-stone-400">Canto PVC</span>
                <div className="text-xl font-black text-sky-400 font-mono">{quoteResult.edgeBandMeters} m</div>
                <span className="text-[10px] text-stone-500">lineales requeridos</span>
              </div>

              <div className="space-y-1">
                <span className="text-[10px] uppercase font-bold text-stone-400">Herrajes</span>
                <div className="text-xl font-black text-emerald-400 font-mono">
                  {quoteResult.hingesCount}B / {quoteResult.slidesCount}C
                </div>
                <span className="text-[10px] text-stone-500">bisagras / correderas</span>
              </div>
            </div>

            {/* Desglose de Costos de Taller */}
            <div className="py-4 space-y-2 border-b border-stone-800 text-xs">
              <div className="flex justify-between text-stone-400">
                <span>Materia prima (tableros / madera maciza):</span>
                <span className="font-mono text-stone-200">{formatCOP(quoteResult.materialCost)}</span>
              </div>
              <div className="flex justify-between text-stone-400">
                <span>Herrajes (cierre lento + rieles + tiradores):</span>
                <span className="font-mono text-stone-200">{formatCOP(quoteResult.hardwareCost)}</span>
              </div>
              <div className="flex justify-between text-stone-400">
                <span>Insumos taller (tornillos, pegamento, lija):</span>
                <span className="font-mono text-stone-200">{formatCOP(quoteResult.consumablesCost)}</span>
              </div>
              <div className="flex justify-between text-stone-400">
                <span>Mano de obra (corte, enchapado y ensamble):</span>
                <span className="font-mono text-stone-200">{formatCOP(quoteResult.laborCost)}</span>
              </div>
              <div className="flex justify-between text-stone-300 font-semibold pt-1 border-t border-stone-800/80">
                <span>Costo Total Directo del Taller:</span>
                <span className="font-mono text-amber-400">{formatCOP(quoteResult.subtotalCost)}</span>
              </div>
            </div>

            {/* Precio Final Sugerido */}
            <div className="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div>
                <span className="text-[11px] font-semibold text-stone-400 uppercase tracking-wider">
                  Precio de Venta Sugerido al Cliente:
                </span>
                <div className="text-3xl font-black text-amber-400 font-['Outfit'] mt-0.5">
                  {formatCOP(quoteResult.suggestedSalePrice)}
                </div>
                <p className="text-xs text-emerald-400 font-medium">
                  Utilidad proyectada: {formatCOP(quoteResult.estimatedProfit)} ({params.profitMarginPercent}%)
                </p>
              </div>

              <div className="flex items-center gap-2">
                <button
                  onClick={handleCreateOrder}
                  className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-stone-950 font-bold text-xs sm:text-sm transition-all shadow-lg shadow-amber-950/50 cursor-pointer active:scale-95"
                >
                  <PlusCircle className="w-4 h-4" />
                  <span>Convertir a Pedido</span>
                </button>
              </div>
            </div>

          </div>

          {/* TABLA DE DESPIECE PARAMÉTRICO DE CORTE */}
          <div className="bg-stone-900/80 border border-stone-800 rounded-2xl overflow-hidden shadow-xl">
            <div className="p-4 bg-stone-950 border-b border-stone-800 flex items-center justify-between">
              <div>
                <h4 className="text-xs font-bold text-white uppercase tracking-wider font-['Outfit'] flex items-center gap-2">
                  <FileText className="w-4 h-4 text-amber-400" />
                  Despiece para Taller de Corte
                </h4>
                <p className="text-[11px] text-stone-400">Medidas exactas de cada pieza para ingresar a escuadradora o corte CNC</p>
              </div>
              <span className="text-xs font-mono text-amber-400 font-bold bg-amber-400/10 px-2 py-0.5 rounded border border-amber-400/20">
                {quoteResult.pieces.length} piezas
              </span>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-stone-300">
                <thead className="bg-stone-950/80 text-stone-400 text-[10px] uppercase font-bold tracking-wider border-b border-stone-800">
                  <tr>
                    <th className="p-3">Pieza</th>
                    <th className="p-3 text-center">Cant.</th>
                    <th className="p-3 text-center">Largo (cm)</th>
                    <th className="p-3 text-center">Ancho (cm)</th>
                    <th className="p-3 text-center">Espesor</th>
                    <th className="p-3">Tapacanto</th>
                    <th className="p-3">Veta</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-stone-800/80 font-mono">
                  {quoteResult.pieces.map((piece, index) => (
                    <tr key={piece.id || index} className="hover:bg-stone-800/40 transition-colors">
                      <td className="p-3 font-sans font-semibold text-white">
                        {piece.partName}
                      </td>
                      <td className="p-3 text-center font-bold text-amber-400">
                        {piece.quantity}
                      </td>
                      <td className="p-3 text-center text-stone-200">
                        {piece.length}
                      </td>
                      <td className="p-3 text-center text-stone-200">
                        {piece.width}
                      </td>
                      <td className="p-3 text-center text-stone-400">
                        {piece.thickness} mm
                      </td>
                      <td className="p-3 font-sans text-[11px] text-stone-300">
                        {piece.edgeBanding}
                      </td>
                      <td className="p-3 font-sans text-[11px] text-amber-400/90 capitalize">
                        {piece.grainDirection || 'Largo'}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>

        </div>

      </div>
    </div>
  );
};

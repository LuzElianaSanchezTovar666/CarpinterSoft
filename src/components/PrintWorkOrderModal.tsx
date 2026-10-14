import React from 'react';
import { Order } from '../types';
import { formatCOP } from '../utils/calculator';
import { Printer, X, Hammer } from 'lucide-react';

interface PrintWorkOrderModalProps {
  order: Order | null;
  onClose: () => void;
}

export const PrintWorkOrderModal: React.FC<PrintWorkOrderModalProps> = ({
  order,
  onClose,
}) => {
  if (!order) return null;

  const handlePrint = () => {
    window.print();
  };

  return (
    <div className="fixed inset-0 z-50 bg-stone-950/85 backdrop-blur-sm flex items-center justify-center p-2 sm:p-6 overflow-y-auto">
      <div className="bg-stone-900 border border-stone-800 rounded-3xl max-w-4xl w-full max-h-[95vh] flex flex-col shadow-2xl overflow-hidden">
        
        {/* Controles Superiores (No imprimibles) */}
        <div className="no-print p-4 bg-stone-950 border-b border-stone-800 flex items-center justify-between">
          <div className="flex items-center gap-2">
            <Printer className="w-4 h-4 text-amber-400" />
            <span className="text-xs font-bold text-white uppercase tracking-wider font-['Outfit']">
              Vista Previa de Impresión - Hoja de Taller & Cotización
            </span>
          </div>

          <div className="flex items-center gap-2">
            <button
              onClick={handlePrint}
              className="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-stone-950 font-bold text-xs rounded-xl shadow-lg cursor-pointer"
            >
              <Printer className="w-3.5 h-3.5" />
              <span>Imprimir / Guardar PDF</span>
            </button>
            <button
              onClick={onClose}
              className="p-2 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-400 hover:text-white transition-colors cursor-pointer"
            >
              <X className="w-4 h-4" />
            </button>
          </div>
        </div>

        {/* Hoja de Trabajo Imprimible (Diseñada para hoja física o PDF) */}
        <div className="p-8 sm:p-12 overflow-y-auto bg-white text-stone-900 print:p-0 print:m-0 font-sans">
          
          {/* Membrete de la Carpintería */}
          <div className="flex justify-between items-start border-b-2 border-amber-800 pb-4 mb-6">
            <div className="flex items-center gap-3">
              <div className="w-12 h-12 rounded-xl bg-amber-800 text-white flex items-center justify-center font-black">
                <Hammer className="w-7 h-7" />
              </div>
              <div>
                <h1 className="text-2xl font-black text-amber-950 tracking-tight font-['Outfit']">
                  CARPINTER<span className="text-amber-700">SOFT</span>
                </h1>
                <p className="text-xs text-stone-600 font-semibold">Taller de Ebanistería & Carpintería Fina</p>
                <p className="text-[11px] text-stone-500">Diseño, Optimización de Tableros y Muebles a Medida</p>
              </div>
            </div>

            <div className="text-right">
              <div className="inline-block px-3 py-1 bg-amber-100 text-amber-900 font-mono font-bold text-sm rounded border border-amber-300">
                ORDEN #{order.code}
              </div>
              <p className="text-xs text-stone-600 mt-1 font-medium">Fecha Emisión: {order.createdAt}</p>
              <p className="text-xs font-bold text-amber-800">Fecha Entrega: {order.deliveryDate}</p>
            </div>
          </div>

          {/* Información del Cliente y del Proyecto */}
          <div className="grid grid-cols-2 gap-6 bg-stone-50 p-4 rounded-xl border border-stone-200 mb-6 text-xs">
            <div>
              <span className="font-bold text-stone-700 uppercase tracking-wider block mb-1">
                Datos del Cliente
              </span>
              <p className="font-bold text-sm text-stone-900">{order.customerName}</p>
              <p className="text-stone-600 mt-0.5">Teléfono: {order.customerPhone}</p>
              <p className="text-stone-600">Dirección: {order.customerAddress}</p>
            </div>

            <div>
              <span className="font-bold text-stone-700 uppercase tracking-wider block mb-1">
                Detalles del Mueble
              </span>
              <p className="font-bold text-sm text-stone-900">{order.projectName}</p>
              <p className="text-stone-600 mt-0.5">
                Dimensiones: <span className="font-mono font-bold text-stone-800">{order.dimensions.height} x {order.dimensions.width} x {order.dimensions.depth} cm</span>
              </p>
              <p className="text-stone-600">Material: <span className="font-semibold text-stone-800">{order.woodType}</span></p>
              <p className="text-stone-600">Acabado: <span className="font-semibold text-stone-800">{order.finishType}</span></p>
              <p className="text-stone-600">Operario a Cargo: <span className="font-semibold text-stone-800">{order.assignedCarpenter || 'Sin asignar'}</span></p>
            </div>
          </div>

          {/* TABLA DE DESPIECE DE CORTE PARA OPERARIOS */}
          <div className="mb-6">
            <h2 className="text-xs font-bold uppercase tracking-wider text-amber-950 mb-2 border-b border-stone-300 pb-1">
              Plano de Despiece de Corte (Para Banco de Trabajo y Escuadradora)
            </h2>
            <table className="w-full text-left text-xs border border-stone-300">
              <thead className="bg-stone-100 text-stone-700 font-bold uppercase text-[10px]">
                <tr className="border-b border-stone-300">
                  <th className="p-2 border-r border-stone-300">#</th>
                  <th className="p-2 border-r border-stone-300">Pieza</th>
                  <th className="p-2 text-center border-r border-stone-300">Cant.</th>
                  <th className="p-2 text-center border-r border-stone-300">Largo (cm)</th>
                  <th className="p-2 text-center border-r border-stone-300">Ancho (cm)</th>
                  <th className="p-2 text-center border-r border-stone-300">Espesor</th>
                  <th className="p-2 border-r border-stone-300">Material</th>
                  <th className="p-2">Tapacanto</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200 font-mono text-[11px]">
                {order.pieces?.map((piece, i) => (
                  <tr key={piece.id || i} className={i % 2 === 0 ? 'bg-white' : 'bg-stone-50/60'}>
                    <td className="p-2 border-r border-stone-200 text-stone-500">{i + 1}</td>
                    <td className="p-2 border-r border-stone-200 font-sans font-bold text-stone-900">{piece.partName}</td>
                    <td className="p-2 text-center border-r border-stone-200 font-bold text-amber-900">{piece.quantity}</td>
                    <td className="p-2 text-center border-r border-stone-200">{piece.length}</td>
                    <td className="p-2 text-center border-r border-stone-200">{piece.width}</td>
                    <td className="p-2 text-center border-r border-stone-200">{piece.thickness} mm</td>
                    <td className="p-2 border-r border-stone-200 font-sans text-stone-700">{piece.material}</td>
                    <td className="p-2 font-sans text-stone-600">{piece.edgeBanding}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* LISTA DE MATERIALES E INSUMOS */}
          <div className="mb-6">
            <h2 className="text-xs font-bold uppercase tracking-wider text-amber-950 mb-2 border-b border-stone-300 pb-1">
              Materiales, Tableros & Herrajes Requeridos
            </h2>
            <table className="w-full text-left text-xs border border-stone-300">
              <thead className="bg-stone-100 text-stone-700 font-bold uppercase text-[10px]">
                <tr className="border-b border-stone-300">
                  <th className="p-2 border-r border-stone-300">Descripción Insumo</th>
                  <th className="p-2 text-center border-r border-stone-300">Cantidad</th>
                  <th className="p-2 text-right border-r border-stone-300">Valor Unitario</th>
                  <th className="p-2 text-right">Subtotal</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200 text-[11px]">
                {order.materials?.map((m, idx) => (
                  <tr key={m.id || idx}>
                    <td className="p-2 border-r border-stone-200 font-semibold">{m.name}</td>
                    <td className="p-2 text-center border-r border-stone-200 font-mono">{m.quantity} {m.unit}</td>
                    <td className="p-2 text-right border-r border-stone-200 font-mono">{formatCOP(m.unitPrice || 0)}</td>
                    <td className="p-2 text-right font-mono font-bold">{formatCOP(m.subtotal || 0)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Liquidación Económica */}
          <div className="flex justify-end mb-8">
            <div className="w-72 bg-stone-50 p-4 rounded-xl border border-stone-200 space-y-2 text-xs">
              <div className="flex justify-between">
                <span className="text-stone-600">Precio Total Cotizado:</span>
                <span className="font-bold font-mono text-stone-900">{formatCOP(order.salePrice)}</span>
              </div>
              <div className="flex justify-between text-emerald-800">
                <span>Anticipo Inicial (Abonado):</span>
                <span className="font-bold font-mono">{formatCOP(order.depositPaid)}</span>
              </div>
              <div className="flex justify-between border-t-2 border-stone-300 pt-2 font-bold text-amber-900 text-sm">
                <span>Saldo Pendiente Entrega:</span>
                <span className="font-mono">{formatCOP(order.salePrice - order.depositPaid)}</span>
              </div>
            </div>
          </div>

          {/* Firmas de Conformidad */}
          <div className="grid grid-cols-2 gap-12 pt-8 border-t border-stone-300 text-xs text-center text-stone-600">
            <div>
              <div className="border-b border-stone-400 mb-2 h-14" />
              <p className="font-bold text-stone-900">{order.customerName}</p>
              <p className="text-[11px]">Aceptación y Visto Bueno del Cliente</p>
            </div>
            <div>
              <div className="border-b border-stone-400 mb-2 h-14" />
              <p className="font-bold text-stone-900">{order.assignedCarpenter || 'Maestro Ebanista Responsable'}</p>
              <p className="text-[11px]">Control de Calidad & Taller CarpinterSoft</p>
            </div>
          </div>

        </div>

      </div>
    </div>
  );
};

import React, { useState } from 'react';
import { Order, OrderStatus } from '../types';
import { formatCOP } from '../utils/calculator';
import { 
  X, 
  Printer, 
  MessageSquare, 
  Calendar, 
  User, 
  Phone, 
  MapPin, 
  Ruler, 
  DollarSign, 
  CheckCircle, 
  FileSpreadsheet, 
  Layers, 
  Plus, 
  Hammer 
} from 'lucide-react';

interface OrderDetailModalProps {
  order: Order | null;
  onClose: () => void;
  onUpdateStatus: (orderId: string, status: OrderStatus) => void;
  onAddPayment: (orderId: string, paymentAmount: number) => void;
  onPrintOrder: (order: Order) => void;
}

export const OrderDetailModal: React.FC<OrderDetailModalProps> = ({
  order,
  onClose,
  onUpdateStatus,
  onAddPayment,
  onPrintOrder,
}) => {
  if (!order) return null;

  const [paymentInput, setPaymentInput] = useState('');
  const [showPaymentField, setShowPaymentField] = useState(false);

  const pendingBalance = order.salePrice - order.depositPaid;

  const handleRegisterPayment = (e: React.FormEvent) => {
    e.preventDefault();
    const amount = Number(paymentInput);
    if (amount > 0) {
      onAddPayment(order.id, amount);
      setPaymentInput('');
      setShowPaymentField(false);
    }
  };

  const handleSendWhatsApp = () => {
    const cleanPhone = order.customerPhone.replace(/[^0-9]/g, '');
    const message = encodeURIComponent(
      `Hola ${order.customerName}, le informamos desde CarpinterSoft que su proyecto "${order.projectName}" (Código ${order.code}) se encuentra actualmente en la fase: ${order.status.toUpperCase()}. Fecha estimada de entrega: ${order.deliveryDate}.`
    );
    window.open(`https://wa.me/${cleanPhone}?text=${message}`, '_blank');
  };

  return (
    <div className="fixed inset-0 z-50 bg-stone-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
      <div className="bg-stone-900 border border-stone-800 rounded-3xl max-w-4xl w-full max-h-[92vh] flex flex-col shadow-2xl animate-scaleUp">
        
        {/* Cabecera del Modal */}
        <div className="p-5 border-b border-stone-800 flex items-center justify-between bg-stone-950/60 rounded-t-3xl">
          <div className="flex items-center gap-3">
            <span className="font-mono font-bold text-xs text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-lg border border-amber-500/20">
              {order.code}
            </span>
            <div>
              <h3 className="text-base sm:text-lg font-bold text-white font-['Outfit']">
                {order.projectName}
              </h3>
              <p className="text-xs text-stone-400">Cliente: <span className="text-stone-200">{order.customerName}</span></p>
            </div>
          </div>

          <div className="flex items-center gap-2">
            <button
              onClick={() => onPrintOrder(order)}
              className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-200 text-xs font-semibold transition-colors cursor-pointer"
              title="Imprimir Hoja de Taller para operario"
            >
              <Printer className="w-3.5 h-3.5 text-amber-400" />
              <span className="hidden sm:inline">Imprimir Ficha</span>
            </button>

            <button
              onClick={handleSendWhatsApp}
              className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 text-xs font-semibold transition-all cursor-pointer"
              title="Notificar avance por WhatsApp"
            >
              <MessageSquare className="w-3.5 h-3.5" />
              <span className="hidden sm:inline">WhatsApp</span>
            </button>

            <button
              onClick={onClose}
              className="p-1.5 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-400 hover:text-white transition-colors cursor-pointer"
            >
              <X className="w-4 h-4" />
            </button>
          </div>
        </div>

        {/* Contenido con Scroll */}
        <div className="p-6 space-y-6 overflow-y-auto flex-1">
          
          {/* Fila de Datos Rápidos y Cambio de Fase */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            {/* Estado Actual y Selector */}
            <div className="bg-stone-950 p-4 rounded-2xl border border-stone-800 space-y-2">
              <label className="text-[10px] font-bold uppercase text-stone-400 block tracking-wider">
                Fase de Fabricación
              </label>
              <select
                value={order.status}
                onChange={(e) => onUpdateStatus(order.id, e.target.value as OrderStatus)}
                className="w-full px-3 py-2 bg-stone-900 border border-amber-500/30 rounded-xl text-xs font-bold text-amber-400 focus:outline-none cursor-pointer"
              >
                <option value="cotizacion">📝 En Cotización</option>
                <option value="diseno">📐 Diseño & Planos</option>
                <option value="corte">🪚 En Corte & Maquinado</option>
                <option value="ensamble">🔨 Ensamble en Taller</option>
                <option value="acabado">🎨 Acabado & Pintura</option>
                <option value="listo">📦 Listo para Entrega</option>
                <option value="entregado">🚚 Instalado / Entregado</option>
              </select>
              <p className="text-[10px] text-stone-500">
                Operario: <span className="text-stone-300">{order.assignedCarpenter || 'Sin asignar'}</span>
              </p>
            </div>

            {/* Datos Técnicos */}
            <div className="bg-stone-950 p-4 rounded-2xl border border-stone-800 space-y-1.5 text-xs">
              <span className="text-[10px] font-bold uppercase text-stone-400 block tracking-wider">
                Especificaciones Técnicas
              </span>
              <div className="flex justify-between">
                <span className="text-stone-500">Dimensiones:</span>
                <span className="font-mono text-stone-200">
                  {order.dimensions.height} x {order.dimensions.width} x {order.dimensions.depth} cm
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-stone-500">Madera / Tablero:</span>
                <span className="text-stone-200 truncate max-w-[150px]">{order.woodType}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-stone-500">Acabado:</span>
                <span className="text-stone-200 truncate max-w-[150px]">{order.finishType}</span>
              </div>
            </div>

            {/* Resumen Financiero y Abonos */}
            <div className="bg-stone-950 p-4 rounded-2xl border border-stone-800 space-y-1.5 text-xs">
              <span className="text-[10px] font-bold uppercase text-stone-400 block tracking-wider">
                Finanzas & Anticipos
              </span>
              <div className="flex justify-between">
                <span className="text-stone-500">Precio Total:</span>
                <span className="font-bold text-white font-mono">{formatCOP(order.salePrice)}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-stone-500">Abonado / Anticipo:</span>
                <span className="font-bold text-emerald-400 font-mono">{formatCOP(order.depositPaid)}</span>
              </div>
              <div className="flex justify-between border-t border-stone-800/80 pt-1">
                <span className="text-stone-400">Saldo Pendiente:</span>
                <span className="font-bold text-amber-400 font-mono">{formatCOP(pendingBalance)}</span>
              </div>

              {pendingBalance > 0 && !showPaymentField && (
                <button
                  onClick={() => setShowPaymentField(true)}
                  className="w-full mt-2 py-1 text-[11px] font-semibold bg-stone-900 hover:bg-stone-800 text-amber-400 rounded-lg border border-stone-800 transition-colors"
                >
                  + Registrar Nuevo Abono
                </button>
              )}

              {showPaymentField && (
                <form onSubmit={handleRegisterPayment} className="mt-2 flex gap-1.5">
                  <input
                    type="number"
                    placeholder="Monto COP"
                    value={paymentInput}
                    onChange={(e) => setPaymentInput(e.target.value)}
                    className="w-full px-2 py-1 bg-stone-900 border border-stone-700 rounded-lg text-xs text-white font-mono"
                    autoFocus
                  />
                  <button
                    type="submit"
                    className="px-2 py-1 bg-emerald-600 text-white rounded-lg text-xs font-bold"
                  >
                    Guardar
                  </button>
                </form>
              )}
            </div>

          </div>

          {/* Despiece de Piezas del Mueble para el Carpintero */}
          <div className="bg-stone-950/70 border border-stone-800 rounded-2xl overflow-hidden">
            <div className="p-3.5 bg-stone-950 border-b border-stone-800 flex items-center justify-between">
              <h4 className="text-xs font-bold text-white uppercase tracking-wider font-['Outfit'] flex items-center gap-2">
                <Layers className="w-4 h-4 text-amber-400" />
                Despiece de Corte del Mueble ({order.pieces?.length || 0} piezas)
              </h4>
              <span className="text-[10px] text-stone-400 font-mono">Medidas en Centímetros</span>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-stone-300">
                <thead className="bg-stone-950 text-stone-500 uppercase text-[10px] font-semibold tracking-wider border-b border-stone-800">
                  <tr>
                    <th className="p-3">Pieza</th>
                    <th className="p-3 text-center">Cant.</th>
                    <th className="p-3 text-center">Largo (cm)</th>
                    <th className="p-3 text-center">Ancho (cm)</th>
                    <th className="p-3 text-center">Espesor</th>
                    <th className="p-3">Material</th>
                    <th className="p-3">Tapacantos</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-stone-800/80 font-mono">
                  {order.pieces && order.pieces.length > 0 ? (
                    order.pieces.map((piece, i) => (
                      <tr key={piece.id || i} className="hover:bg-stone-800/30">
                        <td className="p-3 font-sans font-semibold text-white">{piece.partName}</td>
                        <td className="p-3 text-center font-bold text-amber-400">{piece.quantity}</td>
                        <td className="p-3 text-center text-stone-200">{piece.length}</td>
                        <td className="p-3 text-center text-stone-200">{piece.width}</td>
                        <td className="p-3 text-center text-stone-400">{piece.thickness} mm</td>
                        <td className="p-3 font-sans text-stone-300 text-[11px]">{piece.material}</td>
                        <td className="p-3 font-sans text-stone-400 text-[11px]">{piece.edgeBanding}</td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={7} className="p-4 text-center text-stone-500 font-sans">
                        No se ha generado el despiece para esta orden aún.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>

          {/* Lista de Materiales e Insumos */}
          <div className="bg-stone-950/70 border border-stone-800 rounded-2xl overflow-hidden">
            <div className="p-3.5 bg-stone-950 border-b border-stone-800 flex items-center justify-between">
              <h4 className="text-xs font-bold text-white uppercase tracking-wider font-['Outfit'] flex items-center gap-2">
                <FileSpreadsheet className="w-4 h-4 text-amber-400" />
                Materiales e Insumos Asignados
              </h4>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-stone-300">
                <thead className="bg-stone-950 text-stone-500 uppercase text-[10px] font-semibold tracking-wider border-b border-stone-800">
                  <tr>
                    <th className="p-3">Descripción Material</th>
                    <th className="p-3 text-center">Cantidad</th>
                    <th className="p-3 text-right">Costo Unitario</th>
                    <th className="p-3 text-right">Subtotal</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-stone-800/80">
                  {order.materials && order.materials.length > 0 ? (
                    order.materials.map((mat, idx) => (
                      <tr key={mat.id || idx} className="hover:bg-stone-800/30">
                        <td className="p-3 font-semibold text-white">{mat.name}</td>
                        <td className="p-3 text-center font-mono text-stone-300">{mat.quantity} {mat.unit}</td>
                        <td className="p-3 text-right font-mono text-stone-400">{formatCOP(mat.unitPrice || 0)}</td>
                        <td className="p-3 text-right font-mono font-bold text-amber-400">{formatCOP(mat.subtotal || 0)}</td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={4} className="p-4 text-center text-stone-500">
                        Sin lista de materiales asociada.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>

          {/* Notas */}
          {order.notes && (
            <div className="p-4 rounded-2xl bg-stone-950 border border-stone-800 text-xs">
              <span className="text-[10px] font-bold text-stone-400 uppercase tracking-wider block mb-1">
                Instrucciones & Notas del Taller:
              </span>
              <p className="text-stone-300 leading-relaxed">{order.notes}</p>
            </div>
          )}

        </div>

        {/* Footer del Modal */}
        <div className="p-4 border-t border-stone-800 flex items-center justify-between bg-stone-950/60 rounded-b-3xl">
          <div className="text-xs text-stone-400">
            Entrega comprometida: <span className="font-semibold text-white">{order.deliveryDate}</span>
          </div>
          <button
            onClick={onClose}
            className="px-5 py-2 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-200 text-xs font-bold transition-colors cursor-pointer"
          >
            Cerrar Ficha
          </button>
        </div>

      </div>
    </div>
  );
};

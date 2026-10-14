import React, { useState } from 'react';
import { 
  Order, 
  OrderStatus, 
  Priority, 
  FurnitureCategory 
} from '../types';
import { formatCOP } from '../utils/calculator';
import { 
  Plus, 
  Search, 
  Filter, 
  Kanban, 
  List, 
  ArrowRight, 
  ArrowLeft, 
  Calendar, 
  User, 
  Phone, 
  MapPin, 
  Ruler, 
  Sparkles, 
  Printer, 
  CheckCircle2, 
  Clock, 
  Hammer, 
  Scissors, 
  Palette, 
  PackageCheck, 
  Eye,
  FileSpreadsheet
} from 'lucide-react';

interface OrdersViewProps {
  orders: Order[];
  onSelectOrder: (order: Order) => void;
  onOpenNewOrder: () => void;
  onUpdateOrderStatus: (orderId: string, newStatus: OrderStatus) => void;
}

const STAGES: { key: OrderStatus; label: string; icon: any; color: string; bg: string }[] = [
  { key: 'cotizacion', label: 'Cotización', icon: Clock, color: 'text-amber-400', bg: 'border-amber-500/30 bg-amber-500/5' },
  { key: 'diseno', label: 'Diseño & Planos', icon: Ruler, color: 'text-indigo-400', bg: 'border-indigo-500/30 bg-indigo-500/5' },
  { key: 'corte', label: 'En Corte', icon: Scissors, color: 'text-sky-400', bg: 'border-sky-500/30 bg-sky-500/5' },
  { key: 'ensamble', label: 'Ensamble', icon: Hammer, color: 'text-orange-400', bg: 'border-orange-500/30 bg-orange-500/5' },
  { key: 'acabado', label: 'Acabado / Laca', icon: Palette, color: 'text-purple-400', bg: 'border-purple-500/30 bg-purple-500/5' },
  { key: 'listo', label: 'Listo Entrega', icon: PackageCheck, color: 'text-emerald-400', bg: 'border-emerald-500/30 bg-emerald-500/5' },
  { key: 'entregado', label: 'Instalado', icon: CheckCircle2, color: 'text-stone-400', bg: 'border-stone-700 bg-stone-800/40' },
];

export const OrdersView: React.FC<OrdersViewProps> = ({
  orders,
  onSelectOrder,
  onOpenNewOrder,
  onUpdateOrderStatus,
}) => {
  const [viewMode, setViewMode] = useState<'kanban' | 'table'>('kanban');
  const [searchTerm, setSearchTerm] = useState('');
  const [filterPriority, setFilterPriority] = useState<string>('todos');
  const [filterCategory, setFilterCategory] = useState<string>('todos');

  // Filtrado
  const filteredOrders = orders.filter((order) => {
    const matchesSearch =
      order.projectName.toLowerCase().includes(searchTerm.toLowerCase()) ||
      order.customerName.toLowerCase().includes(searchTerm.toLowerCase()) ||
      order.code.toLowerCase().includes(searchTerm.toLowerCase()) ||
      order.woodType.toLowerCase().includes(searchTerm.toLowerCase());

    const matchesPriority =
      filterPriority === 'todos' || order.priority === filterPriority;

    const matchesCategory =
      filterCategory === 'todos' || order.furnitureCategory === filterCategory;

    return matchesSearch && matchesPriority && matchesCategory;
  });

  const getNextStatus = (current: OrderStatus): OrderStatus | null => {
    const stageKeys: OrderStatus[] = ['cotizacion', 'diseno', 'corte', 'ensamble', 'acabado', 'listo', 'entregado'];
    const idx = stageKeys.indexOf(current);
    if (idx < stageKeys.length - 1) return stageKeys[idx + 1];
    return null;
  };

  const getPrevStatus = (current: OrderStatus): OrderStatus | null => {
    const stageKeys: OrderStatus[] = ['cotizacion', 'diseno', 'corte', 'ensamble', 'acabado', 'listo', 'entregado'];
    const idx = stageKeys.indexOf(current);
    if (idx > 0) return stageKeys[idx - 1];
    return null;
  };

  return (
    <div className="space-y-6 animate-fadeIn">
      {/* Barra de Controles Superiores: Búsqueda, Filtros y Switch de Vista */}
      <div className="bg-stone-900/80 border border-stone-800 p-4 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        {/* Buscador */}
        <div className="relative flex-1 max-w-md">
          <Search className="w-4 h-4 text-stone-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input
            type="text"
            placeholder="Buscar por código, cliente, mueble o madera..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full pl-10 pr-4 py-2 bg-stone-950/70 border border-stone-800 rounded-xl text-xs sm:text-sm text-stone-200 placeholder-stone-500 focus:outline-none focus:border-amber-500 transition-colors"
          />
        </div>

        {/* Filtros de Categoría y Prioridad */}
        <div className="flex flex-wrap items-center gap-2.5">
          {/* Categoría */}
          <select
            value={filterCategory}
            onChange={(e) => setFilterCategory(e.target.value)}
            className="px-3 py-2 bg-stone-950/70 border border-stone-800 rounded-xl text-xs text-stone-300 focus:outline-none focus:border-amber-500 cursor-pointer"
          >
            <option value="todos">Todas las Categorías</option>
            <option value="Cocina">Cocinas Integrales</option>
            <option value="Closet">Clósets y Vestidores</option>
            <option value="Mesa">Mesas y Comedores</option>
            <option value="Puerta">Puertas Principales</option>
            <option value="Bano">Muebles de Baño</option>
            <option value="Oficina">Oficina & Escritorios</option>
            <option value="Especial">Muebles Especiales</option>
          </select>

          {/* Prioridad */}
          <select
            value={filterPriority}
            onChange={(e) => setFilterPriority(e.target.value)}
            className="px-3 py-2 bg-stone-950/70 border border-stone-800 rounded-xl text-xs text-stone-300 focus:outline-none focus:border-amber-500 cursor-pointer"
          >
            <option value="todos">Toda Prioridad</option>
            <option value="urgente">Urgente</option>
            <option value="alta">Alta</option>
            <option value="media">Media</option>
            <option value="baja">Baja</option>
          </select>

          {/* Switch Kanban vs Tabla */}
          <div className="flex items-center bg-stone-950/70 p-1 rounded-xl border border-stone-800">
            <button
              onClick={() => setViewMode('kanban')}
              className={`p-1.5 rounded-lg text-xs font-medium transition-all ${
                viewMode === 'kanban'
                  ? 'bg-amber-600/30 text-amber-300 border border-amber-500/30 shadow-sm'
                  : 'text-stone-400 hover:text-stone-200'
              }`}
              title="Vista Tablero Kanban de Taller"
            >
              <Kanban className="w-4 h-4" />
            </button>
            <button
              onClick={() => setViewMode('table')}
              className={`p-1.5 rounded-lg text-xs font-medium transition-all ${
                viewMode === 'table'
                  ? 'bg-amber-600/30 text-amber-300 border border-amber-500/30 shadow-sm'
                  : 'text-stone-400 hover:text-stone-200'
              }`}
              title="Vista Tabla de Producción"
            >
              <List className="w-4 h-4" />
            </button>
          </div>

          {/* Botón Crear */}
          <button
            onClick={onOpenNewOrder}
            className="flex items-center gap-1.5 px-3.5 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-stone-950 font-bold text-xs rounded-xl transition-all cursor-pointer shadow-md shadow-amber-950/40"
          >
            <Plus className="w-4 h-4" />
            <span>Nuevo Pedido</span>
          </button>
        </div>

      </div>

      {/* VISTA 1: TABLERO KANBAN DE TALLER */}
      {viewMode === 'kanban' && (
        <div className="overflow-x-auto pb-4">
          <div className="flex gap-4 min-w-[1300px]">
            {STAGES.map((stage) => {
              const stageOrders = filteredOrders.filter((o) => o.status === stage.key);
              const StageIcon = stage.icon;

              return (
                <div
                  key={stage.key}
                  className="w-80 flex-shrink-0 bg-stone-900/60 border border-stone-800/80 rounded-2xl flex flex-col h-[750px] shadow-lg"
                >
                  {/* Encabezado de Columna */}
                  <div className={`p-3.5 border-b border-stone-800 flex items-center justify-between rounded-t-2xl ${stage.bg}`}>
                    <div className="flex items-center gap-2">
                      <StageIcon className={`w-4 h-4 ${stage.color}`} />
                      <span className="font-bold text-xs text-white uppercase tracking-wider font-['Outfit']">
                        {stage.label}
                      </span>
                    </div>
                    <span className="px-2 py-0.5 rounded-full text-xs font-bold bg-stone-900 text-stone-300 border border-stone-700">
                      {stageOrders.length}
                    </span>
                  </div>

                  {/* Lista de Tarjetas de la Etapa */}
                  <div className="p-3 space-y-3 overflow-y-auto flex-1 no-scrollbar">
                    {stageOrders.length === 0 ? (
                      <div className="h-32 flex items-center justify-center text-xs text-stone-500 border border-dashed border-stone-800 rounded-xl">
                        Sin pedidos en esta fase
                      </div>
                    ) : (
                      stageOrders.map((order) => {
                        const paidPercent = Math.min(
                          100,
                          Math.round((order.depositPaid / order.salePrice) * 100)
                        );
                        const next = getNextStatus(order.status);
                        const prev = getPrevStatus(order.status);

                        return (
                          <div
                            key={order.id}
                            className="bg-stone-950/80 border border-stone-800/90 hover:border-amber-500/40 rounded-xl p-3.5 space-y-3 transition-all hover:shadow-xl group"
                          >
                            {/* Cabecera Tarjeta: Código y Prioridad */}
                            <div className="flex items-center justify-between">
                              <span className="text-[11px] font-mono font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                                {order.code}
                              </span>
                              <span
                                className={`text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full ${
                                  order.priority === 'urgente'
                                    ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30'
                                    : order.priority === 'alta'
                                    ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30'
                                    : 'bg-stone-800 text-stone-400'
                                }`}
                              >
                                {order.priority}
                              </span>
                            </div>

                            {/* Nombre del Proyecto */}
                            <div>
                              <h4
                                onClick={() => onSelectOrder(order)}
                                className="text-xs sm:text-sm font-bold text-white group-hover:text-amber-400 transition-colors cursor-pointer line-clamp-2"
                              >
                                {order.projectName}
                              </h4>
                              <p className="text-[11px] text-stone-400 flex items-center gap-1 mt-0.5">
                                <User className="w-3 h-3 text-stone-500" />
                                {order.customerName}
                              </p>
                            </div>

                            {/* Medidas y Material */}
                            <div className="bg-stone-900/90 rounded-lg p-2 text-[11px] space-y-1 text-stone-300 border border-stone-800/60">
                              <div className="flex items-center justify-between">
                                <span className="text-stone-400">Medidas:</span>
                                <span className="font-mono text-stone-200">
                                  {order.dimensions.height}x{order.dimensions.width}x{order.dimensions.depth} cm
                                </span>
                              </div>
                              <div className="text-[10px] text-stone-400 truncate">
                                Madera: <span className="text-stone-300">{order.woodType}</span>
                              </div>
                            </div>

                            {/* Fecha de Entrega y Operario */}
                            <div className="flex items-center justify-between text-[11px] text-stone-400">
                              <div className="flex items-center gap-1">
                                <Calendar className="w-3 h-3 text-amber-400" />
                                <span>{order.deliveryDate}</span>
                              </div>
                              <span className="text-[10px] text-stone-500 truncate max-w-[110px]">
                                {order.assignedCarpenter || 'Sin asignar'}
                              </span>
                            </div>

                            {/* Barra de Pago (Anticipo / Total) */}
                            <div>
                              <div className="flex justify-between text-[10px] text-stone-400 mb-1">
                                <span>Anticipo: {paidPercent}%</span>
                                <span className="font-semibold text-stone-200">{formatCOP(order.salePrice)}</span>
                              </div>
                              <div className="w-full bg-stone-900 h-1.5 rounded-full overflow-hidden">
                                <div
                                  className={`h-full rounded-full ${
                                    paidPercent === 100
                                      ? 'bg-emerald-500'
                                      : paidPercent >= 50
                                      ? 'bg-amber-500'
                                      : 'bg-rose-500'
                                  }`}
                                  style={{ width: `${paidPercent}%` }}
                                />
                              </div>
                            </div>

                            {/* Botones de Acción Rápida en Tarjeta */}
                            <div className="pt-2 border-t border-stone-800/80 flex items-center justify-between gap-1">
                              {/* Retroceder de Fase */}
                              {prev ? (
                                <button
                                  onClick={() => onUpdateOrderStatus(order.id, prev)}
                                  className="p-1.5 rounded-lg bg-stone-900 hover:bg-stone-800 text-stone-400 hover:text-stone-200 transition-colors"
                                  title={`Retroceder a fase anterior`}
                                >
                                  <ArrowLeft className="w-3.5 h-3.5" />
                                </button>
                              ) : <div className="w-7" />}

                              {/* Ver Hoja de Taller */}
                              <button
                                onClick={() => onSelectOrder(order)}
                                className="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-stone-900 hover:bg-stone-800 text-amber-400 text-[11px] font-semibold border border-stone-800 transition-colors"
                              >
                                <Eye className="w-3 h-3" />
                                <span>Ficha Taller</span>
                              </button>

                              {/* Avanzar a Siguiente Fase */}
                              {next ? (
                                <button
                                  onClick={() => onUpdateOrderStatus(order.id, next)}
                                  className="p-1.5 rounded-lg bg-amber-500/20 hover:bg-amber-500 text-amber-400 hover:text-stone-950 border border-amber-500/30 transition-all"
                                  title={`Avanzar a fase siguiente`}
                                >
                                  <ArrowRight className="w-3.5 h-3.5" />
                                </button>
                              ) : (
                                <span className="p-1 text-emerald-400">
                                  <CheckCircle2 className="w-4 h-4" />
                                </span>
                              )}
                            </div>

                          </div>
                        );
                      })
                    )}
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      )}

      {/* VISTA 2: TABLA DETALLADA DE PRODUCCIÓN */}
      {viewMode === 'table' && (
        <div className="bg-stone-900/80 border border-stone-800 rounded-2xl overflow-hidden shadow-xl">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs text-stone-300">
              <thead className="bg-stone-950 text-stone-400 uppercase text-[10px] font-semibold tracking-wider border-b border-stone-800">
                <tr>
                  <th className="p-4">Código</th>
                  <th className="p-4">Proyecto / Mueble</th>
                  <th className="p-4">Cliente</th>
                  <th className="p-4">Material / Madera</th>
                  <th className="p-4">Dimensiones</th>
                  <th className="p-4">Fase Taller</th>
                  <th className="p-4">Entrega</th>
                  <th className="p-4">Precio Venta</th>
                  <th className="p-4">Anticipo</th>
                  <th className="p-4 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-800">
                {filteredOrders.length === 0 ? (
                  <tr>
                    <td colSpan={10} className="p-8 text-center text-stone-500">
                      No se encontraron pedidos con los filtros aplicados.
                    </td>
                  </tr>
                ) : (
                  filteredOrders.map((order) => {
                    const currentStage = STAGES.find((s) => s.key === order.status);
                    return (
                      <tr 
                        key={order.id} 
                        className="hover:bg-stone-800/40 transition-colors cursor-pointer"
                        onClick={() => onSelectOrder(order)}
                      >
                        <td className="p-4 font-mono font-bold text-amber-400 whitespace-nowrap">
                          {order.code}
                        </td>
                        <td className="p-4 font-semibold text-white max-w-xs truncate">
                          {order.projectName}
                        </td>
                        <td className="p-4 text-stone-300 whitespace-nowrap">
                          {order.customerName}
                        </td>
                        <td className="p-4 text-stone-400 max-w-xs truncate">
                          {order.woodType}
                        </td>
                        <td className="p-4 font-mono text-stone-300 whitespace-nowrap">
                          {order.dimensions.height}x{order.dimensions.width}x{order.dimensions.depth} cm
                        </td>
                        <td className="p-4 whitespace-nowrap">
                          <span className={`px-2.5 py-1 rounded-full text-[11px] font-semibold border ${currentStage?.bg} ${currentStage?.color}`}>
                            {currentStage?.label}
                          </span>
                        </td>
                        <td className="p-4 whitespace-nowrap text-stone-300">
                          {order.deliveryDate}
                        </td>
                        <td className="p-4 font-bold text-white whitespace-nowrap">
                          {formatCOP(order.salePrice)}
                        </td>
                        <td className="p-4 whitespace-nowrap">
                          <span className={order.depositPaid >= order.salePrice ? 'text-emerald-400' : 'text-amber-400'}>
                            {formatCOP(order.depositPaid)}
                          </span>
                        </td>
                        <td className="p-4 text-right whitespace-nowrap" onClick={(e) => e.stopPropagation()}>
                          <button
                            onClick={() => onSelectOrder(order)}
                            className="px-2.5 py-1 rounded-lg bg-stone-800 hover:bg-stone-700 text-amber-400 text-xs font-semibold transition-colors"
                          >
                            Ver Ficha
                          </button>
                        </td>
                      </tr>
                    );
                  })
                )}
              </tbody>
            </table>
          </div>
        </div>
      )}

    </div>
  );
};

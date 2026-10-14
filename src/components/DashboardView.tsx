import React from 'react';
import { 
  Order, 
  MaterialInventoryItem, 
  OrderStatus 
} from '../types';
import { formatCOP } from '../utils/calculator';
import { 
  ClipboardCheck, 
  DollarSign, 
  AlertTriangle, 
  Clock, 
  ArrowRight, 
  Hammer, 
  Layers, 
  Palette, 
  PackageCheck, 
  CheckCircle2, 
  Calendar, 
  Sparkles,
  Scissors
} from 'lucide-react';

interface DashboardViewProps {
  orders: Order[];
  inventory: MaterialInventoryItem[];
  onSelectOrder: (order: Order) => void;
  onNavigateTab: (tab: 'orders' | 'calculator' | 'inventory' | 'catalog') => void;
  onOpenNewOrder: () => void;
}

export const DashboardView: React.FC<DashboardViewProps> = ({
  orders,
  inventory,
  onSelectOrder,
  onNavigateTab,
  onOpenNewOrder,
}) => {
  // Cálculos estadísticos
  const activeOrders = orders.filter((o) => o.status !== 'entregado');
  const totalSales = orders.reduce((acc, curr) => acc + curr.salePrice, 0);
  const totalDeposits = orders.reduce((acc, curr) => acc + curr.depositPaid, 0);
  const pendingBalance = totalSales - totalDeposits;

  const lowStockItems = inventory.filter((item) => item.stock <= item.minStock);

  // Agrupación por fases de producción
  const statusCounts: Record<OrderStatus, number> = {
    cotizacion: orders.filter((o) => o.status === 'cotizacion').length,
    diseno: orders.filter((o) => o.status === 'diseno').length,
    corte: orders.filter((o) => o.status === 'corte').length,
    ensamble: orders.filter((o) => o.status === 'ensamble').length,
    acabado: orders.filter((o) => o.status === 'acabado').length,
    listo: orders.filter((o) => o.status === 'listo').length,
    entregado: orders.filter((o) => o.status === 'entregado').length,
  };

  const urgentOrders = [...activeOrders]
    .sort((a, b) => new Date(a.deliveryDate).getTime() - new Date(b.deliveryDate).getTime())
    .slice(0, 4);

  return (
    <div className="space-y-8 animate-fadeIn">
      {/* Banner de Bienvenida y Resumen del Taller */}
      <div className="relative overflow-hidden rounded-2xl bg-gradient-to-r from-stone-900 via-amber-950/40 to-stone-900 border border-stone-800 p-6 md:p-8 shadow-xl">
        <div className="absolute right-0 top-0 bottom-0 w-1/3 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-amber-500/10 via-transparent to-transparent pointer-events-none" />
        
        <div className="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
          <div className="space-y-2">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold">
              <Sparkles className="w-3.5 h-3.5" />
              Taller de Carpintería & Ebanistería en Operación
            </div>
            <h1 className="text-2xl sm:text-3xl font-extrabold text-white tracking-tight font-['Outfit']">
              Control de Producción & Taller
            </h1>
            <p className="text-stone-400 text-sm max-w-2xl leading-relaxed">
              Monitorea el avance de corte, ensamble y pintura de cada proyecto, optimiza el consumo de tableros de melamina y madera maciza, y asegura entregas a tiempo.
            </p>
          </div>

          <div className="flex flex-wrap items-center gap-3">
            <button
              onClick={() => onNavigateTab('calculator')}
              className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-stone-800 hover:bg-stone-700/80 text-stone-200 border border-stone-700 text-xs sm:text-sm font-semibold transition-all hover:border-amber-500/30 cursor-pointer"
            >
              <Scissors className="w-4 h-4 text-amber-400" />
              <span>Cotizar con Despiece</span>
            </button>
            <button
              onClick={onOpenNewOrder}
              className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-stone-950 font-bold text-xs sm:text-sm shadow-lg shadow-amber-950/50 transition-all cursor-pointer active:scale-95"
            >
              <span>+ Nuevo Proyecto</span>
            </button>
          </div>
        </div>
      </div>

      {/* Tarjetas de Métricas Principales */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {/* Métricas: Pedidos Activos */}
        <div className="bg-stone-900/70 border border-stone-800 rounded-2xl p-5 hover:border-amber-500/30 transition-all group">
          <div className="flex items-center justify-between">
            <span className="text-xs font-medium text-stone-400 uppercase tracking-wider">
              Muebles en Fabricación
            </span>
            <div className="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 group-hover:bg-amber-500 group-hover:text-stone-950 transition-all">
              <Hammer className="w-5 h-5" />
            </div>
          </div>
          <div className="mt-4">
            <div className="text-3xl font-extrabold text-white font-['Outfit']">
              {activeOrders.length}
            </div>
            <p className="text-xs text-stone-400 mt-1 flex items-center gap-1">
              <span className="text-amber-400 font-semibold">{statusCounts.corte + statusCounts.ensamble + statusCounts.acabado}</span> en banco de trabajo
            </p>
          </div>
        </div>

        {/* Métricas: Facturación Total */}
        <div className="bg-stone-900/70 border border-stone-800 rounded-2xl p-5 hover:border-amber-500/30 transition-all group">
          <div className="flex items-center justify-between">
            <span className="text-xs font-medium text-stone-400 uppercase tracking-wider">
              Valor de Pedidos
            </span>
            <div className="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:bg-emerald-500 group-hover:text-stone-950 transition-all">
              <DollarSign className="w-5 h-5" />
            </div>
          </div>
          <div className="mt-4">
            <div className="text-2xl font-extrabold text-white font-['Outfit'] truncate">
              {formatCOP(totalSales)}
            </div>
            <p className="text-xs text-stone-400 mt-1">
              Recaudado: <span className="text-emerald-400 font-medium">{formatCOP(totalDeposits)}</span>
            </p>
          </div>
        </div>

        {/* Métricas: Saldos Pendientes */}
        <div className="bg-stone-900/70 border border-stone-800 rounded-2xl p-5 hover:border-amber-500/30 transition-all group">
          <div className="flex items-center justify-between">
            <span className="text-xs font-medium text-stone-400 uppercase tracking-wider">
              Saldo Contra Entrega
            </span>
            <div className="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 group-hover:bg-blue-500 group-hover:text-stone-950 transition-all">
              <ClipboardCheck className="w-5 h-5" />
            </div>
          </div>
          <div className="mt-4">
            <div className="text-2xl font-extrabold text-white font-['Outfit'] truncate">
              {formatCOP(pendingBalance)}
            </div>
            <p className="text-xs text-stone-400 mt-1">
              Por cobrar a la instalación
            </p>
          </div>
        </div>

        {/* Métricas: Alerta de Inventario */}
        <div 
          onClick={() => onNavigateTab('inventory')}
          className="bg-stone-900/70 border border-stone-800 rounded-2xl p-5 hover:border-rose-500/40 transition-all group cursor-pointer"
        >
          <div className="flex items-center justify-between">
            <span className="text-xs font-medium text-stone-400 uppercase tracking-wider">
              Materiales en Stock Bajo
            </span>
            <div className={`w-10 h-10 rounded-xl flex items-center justify-center transition-all ${
              lowStockItems.length > 0 
                ? 'bg-rose-500/10 border border-rose-500/30 text-rose-400 group-hover:bg-rose-500 group-hover:text-white' 
                : 'bg-stone-800 text-stone-400'
            }`}>
              <AlertTriangle className="w-5 h-5" />
            </div>
          </div>
          <div className="mt-4">
            <div className="text-3xl font-extrabold text-white font-['Outfit']">
              {lowStockItems.length}
            </div>
            <p className="text-xs text-rose-400 mt-1 font-medium flex items-center justify-between">
              <span>{lowStockItems.length > 0 ? 'Maderas/Herrajes críticos' : 'Stock en niveles óptimos'}</span>
              <ArrowRight className="w-3.5 h-3.5 opacity-60 group-hover:translate-x-1 transition-transform" />
            </p>
          </div>
        </div>
      </div>

      {/* Flujo de Estados del Taller (Embudo de Producción) */}
      <div className="bg-stone-900/80 border border-stone-800 rounded-2xl p-6">
        <div className="flex items-center justify-between mb-4">
          <div>
            <h2 className="text-base font-bold text-white font-['Outfit']">
              Embudo de Producción en Taller
            </h2>
            <p className="text-xs text-stone-400">Distribución de muebles por etapa de fabricación</p>
          </div>
          <button 
            onClick={() => onNavigateTab('orders')}
            className="text-xs text-amber-400 hover:text-amber-300 font-medium flex items-center gap-1"
          >
            Ver tablero completo <ArrowRight className="w-3.5 h-3.5" />
          </button>
        </div>

        <div className="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
          {[
            { key: 'cotizacion', label: 'Cotización', count: statusCounts.cotizacion, icon: Clock, color: 'text-amber-400 border-amber-500/30 bg-amber-500/5' },
            { key: 'diseno', label: 'Diseño/Planos', count: statusCounts.diseno, icon: Layers, color: 'text-indigo-400 border-indigo-500/30 bg-indigo-500/5' },
            { key: 'corte', label: 'En Corte', count: statusCounts.corte, icon: Scissors, color: 'text-sky-400 border-sky-500/30 bg-sky-500/5' },
            { key: 'ensamble', label: 'Ensamble', count: statusCounts.ensamble, icon: Hammer, color: 'text-orange-400 border-orange-500/30 bg-orange-500/5' },
            { key: 'acabado', label: 'Acabado/Laca', count: statusCounts.acabado, icon: Palette, color: 'text-purple-400 border-purple-500/30 bg-purple-500/5' },
            { key: 'listo', label: 'Listo Entrega', count: statusCounts.listo, icon: PackageCheck, color: 'text-emerald-400 border-emerald-500/30 bg-emerald-500/5' },
            { key: 'entregado', label: 'Instalado', count: statusCounts.entregado, icon: CheckCircle2, color: 'text-stone-400 border-stone-700 bg-stone-800/40' },
          ].map((item) => {
            const Icon = item.icon;
            return (
              <div 
                key={item.key}
                onClick={() => onNavigateTab('orders')}
                className={`p-3.5 rounded-xl border ${item.color} flex flex-col justify-between cursor-pointer hover:scale-[1.02] transition-transform`}
              >
                <div className="flex items-center justify-between text-xs font-medium">
                  <span className="opacity-90">{item.label}</span>
                  <Icon className="w-4 h-4 opacity-75" />
                </div>
                <div className="mt-3 text-2xl font-black text-white font-['Outfit']">
                  {item.count}
                </div>
              </div>
            );
          })}
        </div>
      </div>

      {/* Grid Inferior: Proyectos Próximos & Materiales Críticos */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {/* Entregas Próximas / Prioritarias */}
        <div className="lg:col-span-2 bg-stone-900/80 border border-stone-800 rounded-2xl p-6">
          <div className="flex items-center justify-between mb-4">
            <div>
              <h2 className="text-base font-bold text-white font-['Outfit']">
                Proyectos Próximos a Entregar
              </h2>
              <p className="text-xs text-stone-400">Compromisos de entrega ordenados por fecha límite</p>
            </div>
            <span className="text-xs text-stone-500">
              {urgentOrders.length} proyectos listados
            </span>
          </div>

          <div className="divide-y divide-stone-800/80">
            {urgentOrders.map((order) => {
              const progressPercent = Math.min(
                100,
                Math.round((order.depositPaid / order.salePrice) * 100)
              );

              return (
                <div 
                  key={order.id}
                  onClick={() => onSelectOrder(order)}
                  className="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-stone-800/30 p-2 rounded-xl transition-colors cursor-pointer group"
                >
                  <div className="space-y-1">
                    <div className="flex items-center gap-2">
                      <span className="text-xs font-mono font-bold text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded">
                        {order.code}
                      </span>
                      <span className={`text-[11px] font-semibold uppercase px-2 py-0.5 rounded-full ${
                        order.priority === 'urgente' 
                          ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30'
                          : order.priority === 'alta'
                          ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30'
                          : 'bg-stone-800 text-stone-300'
                      }`}>
                        {order.priority}
                      </span>
                      <span className="text-xs text-stone-400 hidden sm:inline">
                        • {order.customerName}
                      </span>
                    </div>
                    <div className="font-semibold text-stone-200 text-sm group-hover:text-amber-400 transition-colors">
                      {order.projectName}
                    </div>
                    <div className="text-xs text-stone-400">
                      Material: <span className="text-stone-300">{order.woodType}</span>
                    </div>
                  </div>

                  <div className="flex items-center justify-between sm:justify-end gap-5">
                    <div className="text-left sm:text-right">
                      <div className="flex items-center gap-1.5 text-xs text-stone-300">
                        <Calendar className="w-3.5 h-3.5 text-amber-400" />
                        <span>{order.deliveryDate}</span>
                      </div>
                      <div className="text-xs text-stone-400 mt-0.5">
                        {formatCOP(order.salePrice)}
                      </div>
                    </div>

                    <div className="w-8 h-8 rounded-lg bg-stone-800 group-hover:bg-amber-600 text-stone-400 group-hover:text-stone-950 flex items-center justify-center transition-all">
                      <ArrowRight className="w-4 h-4" />
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Alerta de Insumos & Maderas Críticas */}
        <div className="bg-stone-900/80 border border-stone-800 rounded-2xl p-6 flex flex-col justify-between">
          <div>
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-base font-bold text-white font-['Outfit'] flex items-center gap-2">
                <AlertTriangle className="w-4 h-4 text-amber-400" />
                Alertas de Stock
              </h2>
              <button 
                onClick={() => onNavigateTab('inventory')}
                className="text-xs text-amber-400 hover:text-amber-300 font-medium"
              >
                Gestionar
              </button>
            </div>

            <p className="text-xs text-stone-400 mb-4">
              Materiales que requieren reposición inmediata para evitar pausas en banco de trabajo:
            </p>

            <div className="space-y-3">
              {lowStockItems.length === 0 ? (
                <div className="p-4 rounded-xl bg-stone-800/40 border border-stone-800 text-center text-xs text-stone-400">
                  No hay materiales con stock crítico actualmente.
                </div>
              ) : (
                lowStockItems.map((item) => (
                  <div 
                    key={item.id}
                    className="p-3 rounded-xl bg-stone-800/50 border border-stone-700/60 flex items-center justify-between gap-2"
                  >
                    <div>
                      <div className="text-xs font-semibold text-stone-200">
                        {item.name}
                      </div>
                      <div className="text-[11px] text-stone-400 mt-0.5">
                        Ubicación: {item.location}
                      </div>
                    </div>
                    <div className="text-right">
                      <div className="text-xs font-bold text-rose-400">
                        {item.stock} {item.unit}
                      </div>
                      <div className="text-[10px] text-stone-400">
                        Mínimo: {item.minStock}
                      </div>
                    </div>
                  </div>
                ))
              )}
            </div>
          </div>

          <div className="mt-6 pt-4 border-t border-stone-800">
            <button
              onClick={() => onNavigateTab('inventory')}
              className="w-full py-2.5 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-200 font-semibold text-xs flex items-center justify-center gap-2 transition-colors cursor-pointer"
            >
              <span>Ver Inventario Completo</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </button>
          </div>
        </div>

      </div>
    </div>
  );
};

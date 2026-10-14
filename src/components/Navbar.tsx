import React from 'react';
import { 
  Hammer, 
  LayoutDashboard, 
  ClipboardList, 
  Scissors, 
  Boxes, 
  Image as ImageIcon, 
  Users, 
  Bot, 
  AlertTriangle,
  PlusCircle,
  FileSpreadsheet
} from 'lucide-react';

export type TabType = 'dashboard' | 'orders' | 'calculator' | 'inventory' | 'catalog' | 'customers';

interface NavbarProps {
  activeTab: TabType;
  setActiveTab: (tab: TabType) => void;
  openNewOrderModal: () => void;
  openAIModal: () => void;
  lowStockCount: number;
  activeOrdersCount: number;
}

export const Navbar: React.FC<NavbarProps> = ({
  activeTab,
  setActiveTab,
  openNewOrderModal,
  openAIModal,
  lowStockCount,
  activeOrdersCount,
}) => {
  return (
    <header className="no-print bg-stone-900/90 backdrop-blur-md border-b border-stone-800 sticky top-0 z-40 transition-all">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          
          {/* Logo y Nombre de Marca */}
          <div className="flex items-center gap-3 cursor-pointer" onClick={() => setActiveTab('dashboard')}>
            <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-600 via-amber-700 to-stone-800 flex items-center justify-center shadow-lg shadow-amber-900/40 border border-amber-500/30">
              <Hammer className="w-5 h-5 text-amber-200" />
            </div>
            <div>
              <div className="flex items-center gap-2">
                <span className="font-extrabold text-xl tracking-tight text-white font-['Outfit']">
                  Carpinter<span className="text-amber-500">Soft</span>
                </span>
                <span className="text-[10px] font-semibold tracking-wider uppercase px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20">
                  Pro Taller
                </span>
              </div>
              <p className="text-[11px] text-stone-400 font-medium">Gestión & Despiece de Carpintería</p>
            </div>
          </div>

          {/* Menú de Navegación Principal */}
          <nav className="hidden md:flex items-center gap-1 bg-stone-950/60 p-1.5 rounded-xl border border-stone-800/80">
            <button
              onClick={() => setActiveTab('dashboard')}
              className={`flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all ${
                activeTab === 'dashboard'
                  ? 'bg-amber-600/20 text-amber-400 border border-amber-500/30 shadow-sm'
                  : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50'
              }`}
            >
              <LayoutDashboard className="w-4 h-4" />
              <span>Panel</span>
            </button>

            <button
              onClick={() => setActiveTab('orders')}
              className={`flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all relative ${
                activeTab === 'orders'
                  ? 'bg-amber-600/20 text-amber-400 border border-amber-500/30 shadow-sm'
                  : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50'
              }`}
            >
              <ClipboardList className="w-4 h-4" />
              <span>Pedidos & Taller</span>
              {activeOrdersCount > 0 && (
                <span className="ml-1 px-1.5 py-0.2 text-[11px] font-bold bg-amber-600 text-white rounded-full">
                  {activeOrdersCount}
                </span>
              )}
            </button>

            <button
              onClick={() => setActiveTab('calculator')}
              className={`flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all ${
                activeTab === 'calculator'
                  ? 'bg-amber-600/20 text-amber-400 border border-amber-500/30 shadow-sm'
                  : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50'
              }`}
            >
              <Scissors className="w-4 h-4" />
              <span>Calculadora & Despiece</span>
            </button>

            <button
              onClick={() => setActiveTab('inventory')}
              className={`flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all relative ${
                activeTab === 'inventory'
                  ? 'bg-amber-600/20 text-amber-400 border border-amber-500/30 shadow-sm'
                  : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50'
              }`}
            >
              <Boxes className="w-4 h-4" />
              <span>Inventario</span>
              {lowStockCount > 0 && (
                <span className="ml-1 px-1.5 py-0.2 text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30 rounded-full flex items-center gap-0.5">
                  <AlertTriangle className="w-2.5 h-2.5" />
                  {lowStockCount}
                </span>
              )}
            </button>

            <button
              onClick={() => setActiveTab('catalog')}
              className={`flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all ${
                activeTab === 'catalog'
                  ? 'bg-amber-600/20 text-amber-400 border border-amber-500/30 shadow-sm'
                  : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50'
              }`}
            >
              <ImageIcon className="w-4 h-4" />
              <span>Catálogo</span>
            </button>

            <button
              onClick={() => setActiveTab('customers')}
              className={`flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-all ${
                activeTab === 'customers'
                  ? 'bg-amber-600/20 text-amber-400 border border-amber-500/30 shadow-sm'
                  : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/50'
              }`}
            >
              <Users className="w-4 h-4" />
              <span>Clientes</span>
            </button>
          </nav>

          {/* Botones de Acción */}
          <div className="flex items-center gap-2.5">
            {/* Asistente CarpinterIA */}
            <button
              onClick={openAIModal}
              className="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold bg-gradient-to-r from-amber-600/30 to-amber-700/20 text-amber-300 border border-amber-500/40 hover:bg-amber-600/40 hover:border-amber-400 transition-all shadow-sm"
              title="Consultar Asistente Técnico CarpinterIA"
            >
              <Bot className="w-4 h-4 text-amber-400 animate-pulse" />
              <span className="hidden sm:inline">CarpinterIA</span>
            </button>

            {/* Nuevo Pedido */}
            <button
              onClick={openNewOrderModal}
              className="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold bg-gradient-to-r from-amber-500 to-amber-600 text-stone-950 hover:from-amber-400 hover:to-amber-500 transition-all shadow-md shadow-amber-900/30 active:scale-95 cursor-pointer"
            >
              <PlusCircle className="w-4 h-4 text-stone-950" />
              <span className="hidden sm:inline">Nuevo Pedido</span>
              <span className="sm:hidden">Nuevo</span>
            </button>
          </div>

        </div>

        {/* Barra de Navegación Móvil */}
        <div className="md:hidden flex items-center justify-around py-2 border-t border-stone-800/60 overflow-x-auto no-scrollbar">
          <button
            onClick={() => setActiveTab('dashboard')}
            className={`flex flex-col items-center gap-1 p-1 text-xs font-medium ${
              activeTab === 'dashboard' ? 'text-amber-400' : 'text-stone-400'
            }`}
          >
            <LayoutDashboard className="w-4 h-4" />
            <span>Panel</span>
          </button>
          <button
            onClick={() => setActiveTab('orders')}
            className={`flex flex-col items-center gap-1 p-1 text-xs font-medium relative ${
              activeTab === 'orders' ? 'text-amber-400' : 'text-stone-400'
            }`}
          >
            <ClipboardList className="w-4 h-4" />
            <span>Pedidos</span>
            {activeOrdersCount > 0 && (
              <span className="absolute -top-1 right-1 w-2 h-2 bg-amber-500 rounded-full" />
            )}
          </button>
          <button
            onClick={() => setActiveTab('calculator')}
            className={`flex flex-col items-center gap-1 p-1 text-xs font-medium ${
              activeTab === 'calculator' ? 'text-amber-400' : 'text-stone-400'
            }`}
          >
            <Scissors className="w-4 h-4" />
            <span>Despiece</span>
          </button>
          <button
            onClick={() => setActiveTab('inventory')}
            className={`flex flex-col items-center gap-1 p-1 text-xs font-medium relative ${
              activeTab === 'inventory' ? 'text-amber-400' : 'text-stone-400'
            }`}
          >
            <Boxes className="w-4 h-4" />
            <span>Stock</span>
            {lowStockCount > 0 && (
              <span className="absolute -top-1 right-1 w-2 h-2 bg-rose-500 rounded-full" />
            )}
          </button>
          <button
            onClick={() => setActiveTab('catalog')}
            className={`flex flex-col items-center gap-1 p-1 text-xs font-medium ${
              activeTab === 'catalog' ? 'text-amber-400' : 'text-stone-400'
            }`}
          >
            <ImageIcon className="w-4 h-4" />
            <span>Catálogo</span>
          </button>
          <button
            onClick={() => setActiveTab('customers')}
            className={`flex flex-col items-center gap-1 p-1 text-xs font-medium ${
              activeTab === 'customers' ? 'text-amber-400' : 'text-stone-400'
            }`}
          >
            <Users className="w-4 h-4" />
            <span>Clientes</span>
          </button>
        </div>

      </div>
    </header>
  );
};

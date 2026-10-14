import React, { useState, useRef, useEffect } from 'react';
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
  Layers, 
  ChevronDown, 
  Menu, 
  X 
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
  const [dropdownOpen, setDropdownOpen] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const dropdownRef = useRef<HTMLDivElement>(null);

  const isManagementTab = ['inventory', 'catalog', 'customers'].includes(activeTab);

  // Cerrar dropdown al hacer click fuera
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setDropdownOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleSelectTab = (tab: TabType) => {
    setActiveTab(tab);
    setDropdownOpen(false);
    setMobileMenuOpen(false);
  };

  return (
    <header className="no-print bg-[#103E4A]/95 backdrop-blur-md border-b border-[#0D4F60] sticky top-0 z-40 transition-all shadow-lg">
      <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16 gap-2">
          
          {/* Logo y Branding Responsivo */}
          <div 
            className="flex items-center gap-2.5 sm:gap-3 cursor-pointer flex-shrink-0 group" 
            onClick={() => handleSelectTab('dashboard')}
          >
            <div className="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-[#ED991F] to-[#E58D17] flex items-center justify-center shadow-md shadow-orange-950/40 border border-[#ED991F]/40 text-[#103E4A] group-hover:scale-105 transition-transform">
              <Hammer className="w-5 h-5 text-[#103E4A]" />
            </div>
            <div className="flex flex-col">
              <div className="flex items-center gap-1.5">
                <span className="font-extrabold text-lg sm:text-xl tracking-tight text-[#FFFFFF] font-['Outfit']">
                  Carpinter<span className="text-[#ED991F]">Soft</span>
                </span>
                <span className="text-[9px] sm:text-[10px] font-semibold tracking-wider uppercase px-1.5 py-0.5 rounded bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30 hidden xs:inline-block">
                  Pro
                </span>
              </div>
              <p className="text-[10px] sm:text-[11px] text-[#F7F5EF]/80 font-medium hidden lg:block leading-none">
                Gestión & Despiece de Carpintería
              </p>
            </div>
          </div>

          {/* Menú de Navegación Principal (Con Menú Desplegable) */}
          <nav className="hidden md:flex items-center gap-1 bg-[#09252D]/85 p-1 rounded-xl border border-[#0D4F60] flex-shrink-0">
            
            <button
              onClick={() => handleSelectTab('dashboard')}
              className={`flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all ${
                activeTab === 'dashboard'
                  ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/40 shadow-sm'
                  : 'text-[#F0EEE8]/80 hover:text-[#FFFFFF] hover:bg-[#0D4F60]/50'
              }`}
            >
              <LayoutDashboard className="w-4 h-4" />
              <span>Panel</span>
            </button>

            <button
              onClick={() => handleSelectTab('orders')}
              className={`flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all relative ${
                activeTab === 'orders'
                  ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/40 shadow-sm'
                  : 'text-[#F0EEE8]/80 hover:text-[#FFFFFF] hover:bg-[#0D4F60]/50'
              }`}
            >
              <ClipboardList className="w-4 h-4" />
              <span>Pedidos</span>
              {activeOrdersCount > 0 && (
                <span className="ml-0.5 px-1.5 py-0.2 text-[10px] font-extrabold bg-[#ED991F] text-[#103E4A] rounded-full">
                  {activeOrdersCount}
                </span>
              )}
            </button>

            <button
              onClick={() => handleSelectTab('calculator')}
              className={`flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all ${
                activeTab === 'calculator'
                  ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/40 shadow-sm'
                  : 'text-[#F0EEE8]/80 hover:text-[#FFFFFF] hover:bg-[#0D4F60]/50'
              }`}
            >
              <Scissors className="w-4 h-4" />
              <span>Despiece</span>
            </button>

            {/* Menú Desplegable: Gestión */}
            <div className="relative" ref={dropdownRef}>
              <button
                type="button"
                onClick={() => setDropdownOpen(!dropdownOpen)}
                className={`flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer ${
                  isManagementTab
                    ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/40 shadow-sm'
                    : 'text-[#F0EEE8]/80 hover:text-[#FFFFFF] hover:bg-[#0D4F60]/50'
                }`}
              >
                <Layers className="w-4 h-4 text-[#ED991F]" />
                <span>Gestión</span>
                {lowStockCount > 0 && (
                  <span className="w-2 h-2 rounded-full bg-[#ED991F] animate-pulse" />
                )}
                <ChevronDown className={`w-3.5 h-3.5 transition-transform duration-200 ${dropdownOpen ? 'rotate-180' : ''}`} />
              </button>

              {dropdownOpen && (
                <div className="absolute right-0 mt-2 w-60 bg-[#103E4A] border border-[#0D4F60] rounded-2xl shadow-2xl p-2 z-50 backdrop-blur-xl">
                  <div className="px-3 py-1.5 text-[10px] font-bold text-[#F0EEE8]/60 uppercase tracking-wider border-b border-[#0D4F60]/70 mb-1">
                    Módulos del Taller
                  </div>

                  <button
                    onClick={() => handleSelectTab('inventory')}
                    className={`w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all ${
                      activeTab === 'inventory'
                        ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30'
                        : 'text-[#F7F5EF] hover:bg-[#0D4F60]/60 hover:text-[#ED991F]'
                    }`}
                  >
                    <div className="flex items-center gap-2.5">
                      <Boxes className="w-4 h-4 text-[#ED991F]" />
                      <span>Inventario & Stock</span>
                    </div>
                    {lowStockCount > 0 && (
                      <span className="px-1.5 py-0.5 text-[10px] font-bold bg-[#E58D17]/20 text-[#ED991F] border border-[#ED991F]/40 rounded-full flex items-center gap-0.5">
                        <AlertTriangle className="w-2.5 h-2.5" />
                        {lowStockCount}
                      </span>
                    )}
                  </button>

                  <button
                    onClick={() => handleSelectTab('catalog')}
                    className={`w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all ${
                      activeTab === 'catalog'
                        ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30'
                        : 'text-[#F7F5EF] hover:bg-[#0D4F60]/60 hover:text-[#ED991F]'
                    }`}
                  >
                    <ImageIcon className="w-4 h-4 text-[#ED991F]" />
                    <span>Catálogo de Muebles</span>
                  </button>

                  <button
                    onClick={() => handleSelectTab('customers')}
                    className={`w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all ${
                      activeTab === 'customers'
                        ? 'bg-[#0D4F60] text-[#ED991F] border border-[#ED991F]/30'
                        : 'text-[#F7F5EF] hover:bg-[#0D4F60]/60 hover:text-[#ED991F]'
                    }`}
                  >
                    <Users className="w-4 h-4 text-[#ED991F]" />
                    <span>Clientes & Contactos</span>
                  </button>
                </div>
              )}
            </div>

          </nav>

          {/* Botones de Acción y Toggle Móvil */}
          <div className="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
            <button
              onClick={openAIModal}
              className="flex items-center gap-1.5 px-2.5 sm:px-3.5 py-2 rounded-xl text-xs font-bold bg-[#0D4F60] text-[#F7F5EF] border border-[#ED991F]/40 hover:bg-[#1A677B] transition-all cursor-pointer"
              title="Consultar Asistente Técnico CarpinterIA"
            >
              <Bot className="w-4 h-4 text-[#ED991F] animate-pulse" />
              <span className="hidden sm:inline">CarpinterIA</span>
            </button>

            <button
              onClick={openNewOrderModal}
              className="flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-[#ED991F] to-[#E58D17] text-[#103E4A] hover:opacity-90 transition-all shadow-md shadow-orange-950/40 cursor-pointer active:scale-95"
            >
              <PlusCircle className="w-4 h-4 text-[#103E4A]" />
              <span className="hidden sm:inline">Nuevo Pedido</span>
              <span className="sm:hidden">Nuevo</span>
            </button>

            <button
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="md:hidden p-2 rounded-xl bg-[#0D4F60] border border-[#ED991F]/30 text-[#F7F5EF] hover:bg-[#1A677B] transition-colors"
              aria-label="Abrir menú"
            >
              {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
            </button>
          </div>

        </div>

        {/* Panel Menú Móvil */}
        {mobileMenuOpen && (
          <div className="md:hidden pb-4 pt-2 border-t border-[#0D4F60]/80">
            <div className="grid grid-cols-2 gap-2 text-xs">
              <button
                onClick={() => handleSelectTab('dashboard')}
                className={`flex items-center gap-2 p-2.5 rounded-xl border ${
                  activeTab === 'dashboard'
                    ? 'bg-[#0D4F60] text-[#ED991F] border-[#ED991F]/50 font-bold'
                    : 'bg-[#09252D]/90 text-[#F0EEE8] border-[#0D4F60]'
                }`}
              >
                <LayoutDashboard className="w-4 h-4 text-[#ED991F]" />
                <span>Panel General</span>
              </button>

              <button
                onClick={() => handleSelectTab('orders')}
                className={`flex items-center justify-between p-2.5 rounded-xl border ${
                  activeTab === 'orders'
                    ? 'bg-[#0D4F60] text-[#ED991F] border-[#ED991F]/50 font-bold'
                    : 'bg-[#09252D]/90 text-[#F0EEE8] border-[#0D4F60]'
                }`}
              >
                <div className="flex items-center gap-2">
                  <ClipboardList className="w-4 h-4 text-[#ED991F]" />
                  <span>Pedidos</span>
                </div>
                {activeOrdersCount > 0 && (
                  <span className="px-1.5 py-0.2 text-[10px] font-extrabold bg-[#ED991F] text-[#103E4A] rounded-full">
                    {activeOrdersCount}
                  </span>
                )}
              </button>

              <button
                onClick={() => handleSelectTab('calculator')}
                className={`flex items-center gap-2 p-2.5 rounded-xl border ${
                  activeTab === 'calculator'
                    ? 'bg-[#0D4F60] text-[#ED991F] border-[#ED991F]/50 font-bold'
                    : 'bg-[#09252D]/90 text-[#F0EEE8] border-[#0D4F60]'
                }`}
              >
                <Scissors className="w-4 h-4 text-[#ED991F]" />
                <span>Calculadora</span>
              </button>

              <button
                onClick={() => handleSelectTab('inventory')}
                className={`flex items-center justify-between p-2.5 rounded-xl border ${
                  activeTab === 'inventory'
                    ? 'bg-[#0D4F60] text-[#ED991F] border-[#ED991F]/50 font-bold'
                    : 'bg-[#09252D]/90 text-[#F0EEE8] border-[#0D4F60]'
                }`}
              >
                <div className="flex items-center gap-2">
                  <Boxes className="w-4 h-4 text-[#ED991F]" />
                  <span>Inventario</span>
                </div>
                {lowStockCount > 0 && (
                  <span className="px-1.5 py-0.5 text-[9px] font-bold bg-[#E58D17]/20 text-[#ED991F] border border-[#ED991F]/40 rounded-full">
                    {lowStockCount}
                  </span>
                )}
              </button>

              <button
                onClick={() => handleSelectTab('catalog')}
                className={`flex items-center gap-2 p-2.5 rounded-xl border ${
                  activeTab === 'catalog'
                    ? 'bg-[#0D4F60] text-[#ED991F] border-[#ED991F]/50 font-bold'
                    : 'bg-[#09252D]/90 text-[#F0EEE8] border-[#0D4F60]'
                }`}
              >
                <ImageIcon className="w-4 h-4 text-[#ED991F]" />
                <span>Catálogo</span>
              </button>

              <button
                onClick={() => handleSelectTab('customers')}
                className={`flex items-center gap-2 p-2.5 rounded-xl border ${
                  activeTab === 'customers'
                    ? 'bg-[#0D4F60] text-[#ED991F] border-[#ED991F]/50 font-bold'
                    : 'bg-[#09252D]/90 text-[#F0EEE8] border-[#0D4F60]'
                }`}
              >
                <Users className="w-4 h-4 text-[#ED991F]" />
                <span>Clientes</span>
              </button>
            </div>
          </div>
        )}

      </div>
    </header>
  );
};

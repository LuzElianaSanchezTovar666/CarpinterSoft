import React from 'react';
import { Code2, Smartphone, LayoutGrid, Palette } from 'lucide-react';
import { ViewMode, DeviceFrameColor, ScreenId } from '../types';

interface TopNavBarProps {
  viewMode: ViewMode;
  onViewModeChange: (mode: ViewMode) => void;
  activeScreen: ScreenId;
  onScreenChange: (screen: ScreenId) => void;
  frameColor: DeviceFrameColor;
  onFrameColorChange: (color: DeviceFrameColor) => void;
  onOpenImageLinker: () => void;
}

export const TopNavBar: React.FC<TopNavBarProps> = ({
  viewMode,
  onViewModeChange,
  activeScreen,
  onScreenChange,
  frameColor,
  onFrameColorChange,
  onOpenImageLinker,
}) => {
  const frameColors: { id: DeviceFrameColor; label: string; colorClass: string }[] = [
    { id: 'titanium-natural', label: 'Titanio Natural', colorClass: 'bg-stone-400' },
    { id: 'titanium-black', label: 'Titanio Negro', colorClass: 'bg-zinc-800' },
    { id: 'titanium-desert', label: 'Titanio Desierto', colorClass: 'bg-amber-400' },
    { id: 'silver', label: 'Plata Lunar', colorClass: 'bg-slate-200' },
    { id: 'minimal-bezel', label: 'Sin Bordes', colorClass: 'bg-transparent border border-white/50' },
  ];

  return (
    <header className="sticky top-0 z-40 w-full bg-slate-950/90 backdrop-blur-md border-b border-slate-800/80">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
        {/* Zone 1: Brand Wordmark (Single text element in display face) */}
        <div className="flex items-center gap-3">
          <a
            href="/"
            className="text-lg font-bold tracking-tight text-white font-display flex items-center gap-2 hover:text-rose-400 transition-colors"
          >
            <div className="w-8 h-8 rounded-xl bg-gradient-to-tr from-rose-500 to-amber-500 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-rose-500/20">
              S
            </div>
            <span>ScreenCraft</span>
          </a>
        </div>

        {/* Zone 2: 4-6 Clean Text Navigation Links */}
        <nav className="hidden lg:flex items-center gap-6 text-xs font-medium text-slate-400">
          <button
            onClick={() => {
              onViewModeChange('single');
              onScreenChange('discover');
            }}
            className={`transition-colors hover:text-white ${
              viewMode === 'single' && activeScreen === 'discover'
                ? 'text-rose-400 font-semibold'
                : ''
            }`}
          >
            Explorar Feed
          </button>
          <button
            onClick={() => {
              onViewModeChange('single');
              onScreenChange('detail');
            }}
            className={`transition-colors hover:text-white ${
              viewMode === 'single' && activeScreen === 'detail'
                ? 'text-rose-400 font-semibold'
                : ''
            }`}
          >
            Detalle Experiencia
          </button>
          <button
            onClick={() => {
              onViewModeChange('single');
              onScreenChange('profile');
            }}
            className={`transition-colors hover:text-white ${
              viewMode === 'single' && activeScreen === 'profile'
                ? 'text-rose-400 font-semibold'
                : ''
            }`}
          >
            Perfil & Curaduría
          </button>
          <button
            onClick={() => {
              onViewModeChange('single');
              onScreenChange('saved');
            }}
            className={`transition-colors hover:text-white ${
              viewMode === 'single' && activeScreen === 'saved'
                ? 'text-rose-400 font-semibold'
                : ''
            }`}
          >
            Guardados
          </button>
          <button
            onClick={() => onViewModeChange('showcase')}
            className={`transition-colors hover:text-white ${
              viewMode === 'showcase' ? 'text-rose-400 font-semibold' : ''
            }`}
          >
            Visor Multi-Pantallas
          </button>
        </nav>

        {/* Zone 3: 1-2 Primary Actions & Controls */}
        <div className="flex items-center gap-3">
          {/* Frame Color Swatches */}
          <div className="hidden sm:flex items-center gap-1.5 p-1 bg-slate-900 rounded-xl border border-slate-800">
            <Palette className="w-3.5 h-3.5 text-slate-400 ml-1.5 mr-0.5" />
            {frameColors.map((f) => (
              <button
                key={f.id}
                onClick={() => onFrameColorChange(f.id)}
                title={`Marco ${f.label}`}
                className={`w-4 h-4 rounded-full ${f.colorClass} transition-transform ${
                  frameColor === f.id ? 'ring-2 ring-rose-500 scale-110' : 'opacity-70 hover:opacity-100'
                }`}
              />
            ))}
          </div>

          {/* View Mode Toggle Button */}
          <div className="flex items-center p-1 bg-slate-900 rounded-xl border border-slate-800">
            <button
              onClick={() => onViewModeChange('single')}
              className={`p-1.5 rounded-lg text-xs font-medium flex items-center gap-1 transition-colors ${
                viewMode === 'single'
                  ? 'bg-rose-500 text-white'
                  : 'text-slate-400 hover:text-white'
              }`}
              title="Modo Dispositivo Individual"
            >
              <Smartphone className="w-3.5 h-3.5" />
              <span className="hidden md:inline text-[11px]">Celular</span>
            </button>
            <button
              onClick={() => onViewModeChange('showcase')}
              className={`p-1.5 rounded-lg text-xs font-medium flex items-center gap-1 transition-colors ${
                viewMode === 'showcase'
                  ? 'bg-rose-500 text-white'
                  : 'text-slate-400 hover:text-white'
              }`}
              title="Modo Multi-Pantallas"
            >
              <LayoutGrid className="w-3.5 h-3.5" />
              <span className="hidden md:inline text-[11px]">Multi-Pantallas</span>
            </button>
          </div>

          {/* Primary Action Button: Vincular HTML */}
          <button
            onClick={onOpenImageLinker}
            className="flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 active:scale-95 text-white text-xs font-semibold shadow-md shadow-rose-500/20 transition-all whitespace-nowrap"
          >
            <Code2 className="w-4 h-4" />
            <span>Vincular Imagen HTML</span>
          </button>
        </div>
      </div>
    </header>
  );
};

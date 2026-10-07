import React from 'react';
import { Wifi, Battery, Signal } from 'lucide-react';
import { DeviceFrameColor } from '../types';

interface DeviceFrameProps {
  children: React.ReactNode;
  frameColor?: DeviceFrameColor;
  showIsland?: boolean;
  activeScreenTitle?: string;
  className?: string;
  scale?: number;
}

export const DeviceFrame: React.FC<DeviceFrameProps> = ({
  children,
  frameColor = 'titanium-natural',
  showIsland = true,
  className = '',
  scale = 1,
}) => {
  // Color configuration for phone bezel
  const colorStyles: Record<DeviceFrameColor, { outer: string; innerBorder: string; sideButton: string }> = {
    'titanium-natural': {
      outer: 'bg-gradient-to-b from-stone-400 via-stone-500 to-stone-600 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.7),0_0_0_1px_rgba(255,255,255,0.1)]',
      innerBorder: 'border-stone-800',
      sideButton: 'bg-stone-500',
    },
    'titanium-black': {
      outer: 'bg-gradient-to-b from-zinc-700 via-zinc-800 to-zinc-900 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.85),0_0_0_1px_rgba(255,255,255,0.08)]',
      innerBorder: 'border-zinc-950',
      sideButton: 'bg-zinc-700',
    },
    'titanium-desert': {
      outer: 'bg-gradient-to-b from-amber-200/80 via-amber-400/80 to-amber-700/80 shadow-[0_25px_60px_-15px_rgba(180,120,60,0.4),0_0_0_1px_rgba(255,255,255,0.2)]',
      innerBorder: 'border-amber-950',
      sideButton: 'bg-amber-400',
    },
    'silver': {
      outer: 'bg-gradient-to-b from-slate-200 via-slate-300 to-slate-400 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.6),0_0_0_1px_rgba(255,255,255,0.4)]',
      innerBorder: 'border-slate-800',
      sideButton: 'bg-slate-300',
    },
    'minimal-bezel': {
      outer: 'bg-transparent shadow-none',
      innerBorder: 'border-slate-800/80',
      sideButton: 'hidden',
    },
  };

  const style = colorStyles[frameColor];
  const isFrameless = frameColor === 'minimal-bezel';

  return (
    <div
      className={`relative inline-block transition-transform duration-300 select-none ${className}`}
      style={{ transform: scale !== 1 ? `scale(${scale})` : undefined, transformOrigin: 'top center' }}
    >
      {/* Physical Hardware Buttons (Left side: Volume buttons + Action button) */}
      {!isFrameless && (
        <>
          <div className={`absolute -left-[3px] top-[115px] w-[3px] h-[26px] rounded-l-sm ${style.sideButton}`} />
          <div className={`absolute -left-[3px] top-[155px] w-[3px] h-[48px] rounded-l-sm ${style.sideButton}`} />
          <div className={`absolute -left-[3px] top-[215px] w-[3px] h-[48px] rounded-l-sm ${style.sideButton}`} />
          {/* Right side: Power button */}
          <div className={`absolute -right-[3px] top-[170px] w-[3px] h-[75px] rounded-r-sm ${style.sideButton}`} />
        </>
      )}

      {/* Main Outer Phone Chassis */}
      <div
        className={`relative ${
          isFrameless ? 'p-0 rounded-[44px]' : 'p-[11px] rounded-[52px]'
        } ${style.outer} transition-all duration-300`}
      >
        {/* Subtle metallic bevel highlight */}
        {!isFrameless && (
          <div className="absolute inset-[1px] rounded-[50px] border border-white/20 pointer-events-none" />
        )}

        {/* Inner Screen Bezel */}
        <div
          className={`relative w-[380px] h-[780px] bg-slate-950 rounded-[42px] overflow-hidden flex flex-col border-[3px] ${
            style.innerBorder
          } shadow-inner`}
        >
          {/* Top Status Bar & Dynamic Island */}
          <div className="relative z-30 w-full pt-3 px-7 flex items-center justify-between text-slate-100 text-xs font-medium tracking-tight bg-transparent shrink-0">
            {/* Clock */}
            <span className="font-semibold text-[13px] tracking-tight">9:41</span>

            {/* Dynamic Island */}
            {showIsland && (
              <div className="absolute left-1/2 -translate-x-1/2 top-2.5 h-[28px] w-[112px] bg-black rounded-full flex items-center justify-between px-3 shadow-md border border-white/5 group hover:w-[170px] transition-all duration-300 cursor-pointer">
                <div className="flex items-center gap-1.5">
                  <div className="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse" />
                  <span className="text-[10px] text-white/70 font-mono tracking-tight hidden group-hover:inline transition-all">
                    Studio Live
                  </span>
                </div>
                <div className="w-2.5 h-2.5 rounded-full bg-slate-900 border border-slate-800" />
              </div>
            )}

            {/* Status Icons: Cellular, Wifi, Battery */}
            <div className="flex items-center gap-1.5 text-slate-300">
              <Signal className="w-3.5 h-3.5" />
              <Wifi className="w-3.5 h-3.5" />
              <div className="flex items-center gap-0.5">
                <span className="text-[10px] tabular-nums font-semibold">94%</span>
                <Battery className="w-4 h-4 text-emerald-400" />
              </div>
            </div>
          </div>

          {/* Screen Content Container with Touch-friendly Scrolling */}
          <div className="relative flex-1 w-full overflow-y-auto overflow-x-hidden no-scrollbar bg-slate-950 flex flex-col">
            {children}
          </div>

          {/* Home Bar Indicator (iOS style bar) */}
          <div className="w-full h-5 bg-transparent flex items-center justify-center pointer-events-none shrink-0 z-30">
            <div className="w-32 h-1 bg-white/40 rounded-full" />
          </div>
        </div>
      </div>
    </div>
  );
};

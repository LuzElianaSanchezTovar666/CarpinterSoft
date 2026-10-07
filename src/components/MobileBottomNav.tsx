import React from 'react';
import { Compass, Sparkles, Heart, User } from 'lucide-react';
import { ScreenId } from '../types';

interface MobileBottomNavProps {
  activeScreen: ScreenId;
  onScreenChange: (screen: ScreenId) => void;
  savedCount: number;
}

export const MobileBottomNav: React.FC<MobileBottomNavProps> = ({
  activeScreen,
  onScreenChange,
  savedCount,
}) => {
  const tabs = [
    { id: 'discover' as ScreenId, label: 'Explorar', icon: Compass },
    { id: 'detail' as ScreenId, label: 'Experiencia', icon: Sparkles },
    { id: 'saved' as ScreenId, label: 'Guardados', icon: Heart, badge: savedCount },
    { id: 'profile' as ScreenId, label: 'Perfil', icon: User },
  ];

  return (
    <div className="sticky bottom-0 inset-x-0 bg-slate-950/95 backdrop-blur-md border-t border-slate-800/90 px-4 py-2 flex items-center justify-around z-30 shrink-0">
      {tabs.map((tab) => {
        const Icon = tab.icon;
        const isActive = activeScreen === tab.id;
        return (
          <button
            key={tab.id}
            onClick={() => onScreenChange(tab.id)}
            className="flex flex-col items-center justify-center min-w-[50px] min-h-[44px] relative group transition-colors"
          >
            <div className="relative">
              <Icon
                className={`w-5 h-5 transition-transform duration-200 group-hover:scale-110 ${
                  isActive ? 'text-rose-500 fill-rose-500/20' : 'text-slate-400'
                }`}
              />
              {tab.badge !== undefined && tab.badge > 0 && (
                <span className="absolute -top-1 -right-2 px-1 min-w-[14px] h-[14px] rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center tabular-nums">
                  {tab.badge}
                </span>
              )}
            </div>
            <span
              className={`text-[10px] font-medium tracking-tight mt-1 transition-colors ${
                isActive ? 'text-rose-400 font-semibold' : 'text-slate-400'
              }`}
            >
              {tab.label}
            </span>
            {isActive && <div className="w-1 h-1 rounded-full bg-rose-500 mt-0.5" />}
          </button>
        );
      })}
    </div>
  );
};

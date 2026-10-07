import React from 'react';
import { DeviceFrame } from './DeviceFrame';
import { DiscoverScreen } from './screens/DiscoverScreen';
import { DetailScreen } from './screens/DetailScreen';
import { ProfileScreen } from './screens/ProfileScreen';
import { SavedScreen } from './screens/SavedScreen';
import { PlaceItem, Story, UserProfile, DeviceFrameColor, ScreenId } from '../types';
import { Smartphone, Maximize2 } from 'lucide-react';

interface MultiScreenShowcaseProps {
  stories: Story[];
  places: PlaceItem[];
  profile: UserProfile;
  selectedPlace: PlaceItem;
  savedPlaces: PlaceItem[];
  frameColor: DeviceFrameColor;
  onSelectPlace: (place: PlaceItem) => void;
  onToggleBookmark: (placeId: string) => void;
  onOpenImageLinker: (target: 'hero' | 'card' | 'avatar' | 'cover', placeId?: string) => void;
  onSwitchToSingleScreen: (screenId: ScreenId) => void;
}

export const MultiScreenShowcase: React.FC<MultiScreenShowcaseProps> = ({
  stories,
  places,
  profile,
  selectedPlace,
  savedPlaces,
  frameColor,
  onSelectPlace,
  onToggleBookmark,
  onOpenImageLinker,
  onSwitchToSingleScreen,
}) => {
  const screens = [
    {
      id: 'discover' as ScreenId,
      name: '01. Pantalla de Explorar',
      desc: 'Feed principal, reels de historias y tarjetas destacadas',
      component: (
        <DiscoverScreen
          stories={stories}
          places={places}
          onSelectPlace={onSelectPlace}
          onToggleBookmark={onToggleBookmark}
          onOpenImageLinker={onOpenImageLinker}
        />
      ),
    },
    {
      id: 'detail' as ScreenId,
      name: '02. Pantalla de Detalle',
      desc: 'Experiencia inmersiva, galería, anfitrión y reserva',
      component: (
        <DetailScreen
          place={selectedPlace}
          onBack={() => onSwitchToSingleScreen('discover')}
          onToggleBookmark={onToggleBookmark}
          onOpenImageLinker={onOpenImageLinker}
        />
      ),
    },
    {
      id: 'profile' as ScreenId,
      name: '03. Pantalla de Perfil',
      desc: 'Curador de arquitectura, bio, métricas y cuadrícula de fotos',
      component: (
        <ProfileScreen
          profile={profile}
          places={places}
          onSelectPlace={onSelectPlace}
          onOpenImageLinker={onOpenImageLinker}
        />
      ),
    },
    {
      id: 'saved' as ScreenId,
      name: '04. Pantalla de Guardados',
      desc: 'Colecciones personales de estancias y lugares favoritos',
      component: (
        <SavedScreen
          savedPlaces={savedPlaces}
          onSelectPlace={onSelectPlace}
          onRemoveBookmark={onToggleBookmark}
          onExploreMore={() => onSwitchToSingleScreen('discover')}
        />
      ),
    },
  ];

  return (
    <div className="w-full py-6">
      <div className="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-800/80 pb-4">
        <div>
          <span className="text-xs font-semibold text-rose-400 tracking-wider uppercase">
            Visor Multi-Pantallas
          </span>
          <h2 className="text-2xl font-bold text-white tracking-tight font-display">
            Pantallas de la Aplicación en Tiempo Real
          </h2>
          <p className="text-sm text-slate-400 mt-1 max-w-2xl">
            Cada pantalla es totalmente interactiva. Haz clic en <b className="text-slate-200">Interactuar en Vivo</b> para probarla en pantalla completa o usa los botones de vincular imagen HTML.
          </p>
        </div>

        <div className="flex items-center gap-2 text-xs text-slate-400 bg-slate-900 px-3 py-1.5 rounded-xl border border-slate-800">
          <Smartphone className="w-4 h-4 text-rose-400" />
          <span>4 Pantallas sincronizadas</span>
        </div>
      </div>

      {/* Horizontal Scrollable or Grid Screens Showcase */}
      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8 justify-items-center">
        {screens.map((screen) => (
          <div key={screen.id} className="flex flex-col items-center group w-full max-w-[380px]">
            {/* Screen Header Info */}
            <div className="w-full mb-3 flex items-center justify-between px-2">
              <div>
                <h3 className="text-sm font-bold text-white tracking-tight">{screen.name}</h3>
                <p className="text-[11px] text-slate-400 line-clamp-1">{screen.desc}</p>
              </div>
              <button
                onClick={() => onSwitchToSingleScreen(screen.id)}
                title="Ampliar y controlar esta pantalla"
                className="p-1.5 rounded-lg bg-slate-900 hover:bg-rose-500 hover:text-white border border-slate-800 text-slate-300 transition-colors flex items-center gap-1 text-[11px] font-medium"
              >
                <Maximize2 className="w-3.5 h-3.5" />
                <span className="hidden sm:inline">Probar</span>
              </button>
            </div>

            {/* Device Mockup */}
            <div className="transition-transform duration-300 group-hover:scale-[1.01]">
              <DeviceFrame frameColor={frameColor} scale={0.92} className="origin-top">
                {screen.component}
              </DeviceFrame>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

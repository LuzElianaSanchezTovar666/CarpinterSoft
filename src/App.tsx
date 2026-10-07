import { useState } from 'react';
import { DeviceFrame } from './components/DeviceFrame';
import { TopNavBar } from './components/TopNavBar';
import { MobileBottomNav } from './components/MobileBottomNav';
import { DiscoverScreen } from './components/screens/DiscoverScreen';
import { DetailScreen } from './components/screens/DetailScreen';
import { ProfileScreen } from './components/screens/ProfileScreen';
import { SavedScreen } from './components/screens/SavedScreen';
import { MultiScreenShowcase } from './components/MultiScreenShowcase';
import { ImageLinkerModal } from './components/ImageLinkerModal';
import { INITIAL_PLACES, INITIAL_STORIES, INITIAL_USER_PROFILE } from './data/mockData';
import { PlaceItem, Story, UserProfile, ScreenId, ViewMode, DeviceFrameColor } from './types';
import { Code2, Sparkles, Check, RefreshCw, MonitorSmartphone, Smartphone, ArrowRight } from 'lucide-react';

export default function App() {
  const [places, setPlaces] = useState<PlaceItem[]>(INITIAL_PLACES);
  const [stories, setStories] = useState<Story[]>(INITIAL_STORIES);
  const [profile, setProfile] = useState<UserProfile>(INITIAL_USER_PROFILE);
  const [selectedPlace, setSelectedPlace] = useState<PlaceItem>(INITIAL_PLACES[0]);
  const [activeScreen, setActiveScreen] = useState<ScreenId>('discover');
  const [viewMode, setViewMode] = useState<ViewMode>('single');
  const [frameColor, setFrameColor] = useState<DeviceFrameColor>('titanium-natural');

  // Image Linker Modal State
  const [isLinkerOpen, setIsLinkerOpen] = useState<boolean>(false);
  const [linkerTarget, setLinkerTarget] = useState<'hero' | 'card' | 'avatar' | 'cover'>('hero');
  const [linkerPlaceId, setLinkerPlaceId] = useState<string | undefined>(undefined);

  // Toast feedback
  const [toastMessage, setToastMessage] = useState<string | null>(null);

  const showToast = (msg: string) => {
    setToastMessage(msg);
    setTimeout(() => setToastMessage(null), 3000);
  };

  const handleSelectPlace = (place: PlaceItem) => {
    setSelectedPlace(place);
    setActiveScreen('detail');
  };

  const handleToggleBookmark = (placeId: string) => {
    setPlaces((prev) =>
      prev.map((p) => {
        if (p.id === placeId) {
          const nextState = !p.isBookmarked;
          showToast(nextState ? `Añadido a favoritos` : `Eliminado de favoritos`);
          return { ...p, isBookmarked: nextState };
        }
        return p;
      })
    );

    // Also update selected place if it's the one toggled
    if (selectedPlace.id === placeId) {
      setSelectedPlace((prev) => ({ ...prev, isBookmarked: !prev.isBookmarked }));
    }
  };

  const handleOpenImageLinker = (
    target: 'hero' | 'card' | 'avatar' | 'cover' = 'hero',
    placeId?: string
  ) => {
    setLinkerTarget(target);
    setLinkerPlaceId(placeId);
    setIsLinkerOpen(true);
  };

  const handleApplyImage = (
    imageUrl: string,
    target: 'hero' | 'card' | 'avatar' | 'cover',
    placeId?: string
  ) => {
    if (target === 'hero') {
      // Update first place and selected place
      setPlaces((prev) =>
        prev.map((p, idx) => (idx === 0 ? { ...p, image: imageUrl } : p))
      );
      setSelectedPlace((prev) => ({ ...prev, image: imageUrl }));
      showToast('Imagen vinculada en Hero Principal');
    } else if (target === 'card' && placeId) {
      setPlaces((prev) =>
        prev.map((p) => (p.id === placeId ? { ...p, image: imageUrl } : p))
      );
      if (selectedPlace.id === placeId) {
        setSelectedPlace((prev) => ({ ...prev, image: imageUrl }));
      }
      showToast('Imagen actualizada en la tarjeta');
    } else if (target === 'avatar') {
      setProfile((prev) => ({ ...prev, avatar: imageUrl }));
      setPlaces((prev) =>
        prev.map((p) => ({ ...p, hostAvatar: imageUrl }))
      );
      showToast('Avatar de perfil vinculado');
    } else if (target === 'cover') {
      setProfile((prev) => ({ ...prev, coverImage: imageUrl }));
      showToast('Imagen de portada vinculada');
    }
  };

  const handleResetData = () => {
    setPlaces(INITIAL_PLACES);
    setStories(INITIAL_STORIES);
    setProfile(INITIAL_USER_PROFILE);
    setSelectedPlace(INITIAL_PLACES[0]);
    showToast('Datos e imágenes restaurados');
  };

  const savedPlaces = places.filter((p) => p.isBookmarked);

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans">
      {/* Top Bar following Design Constitution */}
      <TopNavBar
        viewMode={viewMode}
        onViewModeChange={setViewMode}
        activeScreen={activeScreen}
        onScreenChange={setActiveScreen}
        frameColor={frameColor}
        onFrameColorChange={setFrameColor}
        onOpenImageLinker={() => handleOpenImageLinker('hero')}
      />

      {/* Main Studio Viewport */}
      <main className="flex-1 flex flex-col max-w-7xl w-full mx-auto px-4 sm:px-6 py-6">
        {/* Toast Notification */}
        {toastMessage && (
          <div className="fixed bottom-6 right-6 z-50 flex items-center gap-2 px-4 py-3 rounded-2xl bg-rose-500 text-white text-xs font-semibold shadow-2xl shadow-rose-500/30 animate-in fade-in slide-in-from-bottom duration-200">
            <Check className="w-4 h-4" />
            <span>{toastMessage}</span>
          </div>
        )}

        {viewMode === 'showcase' ? (
          /* Multi-Screen Side-by-Side Showcase */
          <MultiScreenShowcase
            stories={stories}
            places={places}
            profile={profile}
            selectedPlace={selectedPlace}
            savedPlaces={savedPlaces}
            frameColor={frameColor}
            onSelectPlace={handleSelectPlace}
            onToggleBookmark={handleToggleBookmark}
            onOpenImageLinker={handleOpenImageLinker}
            onSwitchToSingleScreen={(screenId) => {
              setActiveScreen(screenId);
              setViewMode('single');
            }}
          />
        ) : (
          /* Single Interactive Device Mode with Control Studio */
          <div className="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start py-2">
            {/* Left Column: Device Mockup Interactive Centerpiece */}
            <div className="lg:col-span-7 flex flex-col items-center justify-center">
              {/* Quick Screen Tab Selector */}
              <div className="flex items-center gap-1.5 p-1.5 bg-slate-900 rounded-2xl border border-slate-800 mb-6 shadow-sm">
                {[
                  { id: 'discover' as ScreenId, label: '01. Explorar' },
                  { id: 'detail' as ScreenId, label: '02. Detalle' },
                  { id: 'profile' as ScreenId, label: '03. Perfil' },
                  { id: 'saved' as ScreenId, label: '04. Guardados' },
                ].map((s) => (
                  <button
                    key={s.id}
                    onClick={() => setActiveScreen(s.id)}
                    className={`px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all ${
                      activeScreen === s.id
                        ? 'bg-rose-500 text-white shadow-md shadow-rose-500/25'
                        : 'text-slate-400 hover:text-white'
                    }`}
                  >
                    {s.label}
                  </button>
                ))}
              </div>

              {/* Realistic Mobile Device Container */}
              <DeviceFrame frameColor={frameColor}>
                {activeScreen === 'discover' && (
                  <DiscoverScreen
                    stories={stories}
                    places={places}
                    onSelectPlace={handleSelectPlace}
                    onToggleBookmark={handleToggleBookmark}
                    onOpenImageLinker={handleOpenImageLinker}
                  />
                )}
                {activeScreen === 'detail' && (
                  <DetailScreen
                    place={selectedPlace}
                    onBack={() => setActiveScreen('discover')}
                    onToggleBookmark={handleToggleBookmark}
                    onOpenImageLinker={handleOpenImageLinker}
                  />
                )}
                {activeScreen === 'profile' && (
                  <ProfileScreen
                    profile={profile}
                    places={places}
                    onSelectPlace={handleSelectPlace}
                    onOpenImageLinker={handleOpenImageLinker}
                  />
                )}
                {activeScreen === 'saved' && (
                  <SavedScreen
                    savedPlaces={savedPlaces}
                    onSelectPlace={handleSelectPlace}
                    onRemoveBookmark={handleToggleBookmark}
                    onExploreMore={() => setActiveScreen('discover')}
                  />
                )}

                {/* Persistent Ergonomic Mobile Bottom Nav */}
                <MobileBottomNav
                  activeScreen={activeScreen}
                  onScreenChange={setActiveScreen}
                  savedCount={savedPlaces.length}
                />
              </DeviceFrame>
            </div>

            {/* Right Column: Studio Tools & HTML Image Binding Control Center */}
            <div className="lg:col-span-5 space-y-6">
              {/* Card 1: Vinculador de Imágenes desde HTML */}
              <div className="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-xl space-y-4">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-2.5">
                    <div className="p-2.5 rounded-2xl bg-rose-500/10 text-rose-400 border border-rose-500/20">
                      <Code2 className="w-5 h-5" />
                    </div>
                    <div>
                      <h3 className="text-base font-bold text-white tracking-tight font-display">
                        Vincular Imágenes desde HTML
                      </h3>
                      <p className="text-xs text-slate-400">
                        Inserta tus propias etiquetas o enlaces web
                      </p>
                    </div>
                  </div>
                </div>

                <p className="text-xs text-slate-300 leading-relaxed">
                  Puedes copiar o vincular cualquier imagen usando código HTML estándar{' '}
                  <code className="text-rose-300 bg-slate-950 px-1.5 py-0.5 rounded font-mono text-[11px]">
                    &lt;img src="..." /&gt;
                  </code>{' '}
                  o URLs directas. La aplicación extraerá el recurso y lo aplicará inmediatamente al diseño de la pantalla.
                </p>

                <div className="grid grid-cols-2 gap-2.5 pt-1">
                  <button
                    onClick={() => handleOpenImageLinker('hero')}
                    className="p-3 rounded-2xl bg-slate-950 border border-slate-800 hover:border-rose-500/50 text-left transition-colors group"
                  >
                    <span className="text-[11px] text-slate-400 block">Pantalla Explorar</span>
                    <span className="text-xs font-semibold text-white group-hover:text-rose-400 transition-colors">
                      Cambiar Hero ➔
                    </span>
                  </button>

                  <button
                    onClick={() => handleOpenImageLinker('hero', selectedPlace.id)}
                    className="p-3 rounded-2xl bg-slate-950 border border-slate-800 hover:border-rose-500/50 text-left transition-colors group"
                  >
                    <span className="text-[11px] text-slate-400 block">Pantalla Detalle</span>
                    <span className="text-xs font-semibold text-white group-hover:text-rose-400 transition-colors">
                      Cambiar Foto ➔
                    </span>
                  </button>

                  <button
                    onClick={() => handleOpenImageLinker('cover')}
                    className="p-3 rounded-2xl bg-slate-950 border border-slate-800 hover:border-rose-500/50 text-left transition-colors group"
                  >
                    <span className="text-[11px] text-slate-400 block">Pantalla Perfil</span>
                    <span className="text-xs font-semibold text-white group-hover:text-rose-400 transition-colors">
                      Cambiar Portada ➔
                    </span>
                  </button>

                  <button
                    onClick={() => handleOpenImageLinker('avatar')}
                    className="p-3 rounded-2xl bg-slate-950 border border-slate-800 hover:border-rose-500/50 text-left transition-colors group"
                  >
                    <span className="text-[11px] text-slate-400 block">Anfitrión / Creador</span>
                    <span className="text-xs font-semibold text-white group-hover:text-rose-400 transition-colors">
                      Cambiar Avatar ➔
                    </span>
                  </button>
                </div>

                <button
                  onClick={() => handleOpenImageLinker('hero')}
                  className="w-full py-3 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 active:scale-98 text-white font-semibold text-xs tracking-tight shadow-md shadow-rose-500/20 transition-all flex items-center justify-center gap-2"
                >
                  <Sparkles className="w-4 h-4 text-amber-300" />
                  <span>Abrir Editor de Imágenes y HTML</span>
                </button>
              </div>

              {/* Card 2: Especificaciones del Modo de Visualización */}
              <div className="p-6 rounded-3xl bg-slate-900/80 border border-slate-800 space-y-4">
                <div className="flex items-center justify-between">
                  <h3 className="text-sm font-bold text-white tracking-tight flex items-center gap-2">
                    <MonitorSmartphone className="w-4 h-4 text-rose-400" />
                    <span>Controles de Presentación</span>
                  </h3>
                  <button
                    onClick={handleResetData}
                    className="text-xs text-slate-400 hover:text-white flex items-center gap-1 transition-colors"
                  >
                    <RefreshCw className="w-3 h-3" />
                    <span>Restablecer</span>
                  </button>
                </div>

                {/* Switch to multi-screen button */}
                <div
                  onClick={() => setViewMode('showcase')}
                  className="p-4 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 cursor-pointer transition-colors flex items-center justify-between group"
                >
                  <div>
                    <h4 className="text-xs font-semibold text-white group-hover:text-rose-400 transition-colors">
                      Ver las 4 Pantallas en Paralelo
                    </h4>
                    <p className="text-[11px] text-slate-400 mt-0.5">
                      Visualiza Explorar, Detalle, Perfil y Guardados como en una presentación de producto.
                    </p>
                  </div>
                  <div className="p-2 rounded-xl bg-slate-900 text-slate-300 group-hover:text-white group-hover:bg-rose-500 transition-colors">
                    <ArrowRight className="w-4 h-4" />
                  </div>
                </div>

                {/* Specs overview with tabular figures */}
                <div className="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-2 text-xs">
                  <div className="flex justify-between text-slate-400">
                    <span>Pantalla actual:</span>
                    <span className="text-white font-medium capitalize">{activeScreen}</span>
                  </div>
                  <div className="flex justify-between text-slate-400">
                    <span>Espacios disponibles:</span>
                    <span className="text-white font-mono tabular-nums">{places.length}</span>
                  </div>
                  <div className="flex justify-between text-slate-400">
                    <span>Colección guardada:</span>
                    <span className="text-rose-400 font-mono tabular-nums">{savedPlaces.length} ítems</span>
                  </div>
                  <div className="flex justify-between text-slate-400">
                    <span>Marco de hardware:</span>
                    <span className="text-white font-medium capitalize">{frameColor.replace('-', ' ')}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}
      </main>

      {/* Footer conforming to constitution */}
      <footer className="mt-auto border-t border-slate-800/80 py-6 text-center text-xs text-slate-400">
        <div className="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
          <span>ScreenCraft Studio · Sistema de Prototipos & Pantallas Interactivas</span>
          <div className="flex items-center gap-4 text-[11px]">
            <span>Soporte HTML &lt;img&gt;</span>
            <span aria-hidden="true">·</span>
            <span>Simulador de Hardware Móvil</span>
            <span aria-hidden="true">·</span>
            <span>Diseño Ergonómico Touch</span>
          </div>
        </div>
      </footer>

      {/* Image Linker Modal */}
      <ImageLinkerModal
        isOpen={isLinkerOpen}
        onClose={() => setIsLinkerOpen(false)}
        currentTarget={linkerTarget}
        targetPlaceId={linkerPlaceId}
        onApplyImage={handleApplyImage}
      />
    </div>
  );
}

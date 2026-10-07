import React, { useState } from 'react';
import { Search, Heart, Star, Sparkles, MapPin, SlidersHorizontal, ImagePlus } from 'lucide-react';
import { PlaceItem, Story } from '../../types';

interface DiscoverScreenProps {
  stories: Story[];
  places: PlaceItem[];
  onSelectPlace: (place: PlaceItem) => void;
  onToggleBookmark: (placeId: string) => void;
  onOpenImageLinker: (target: 'hero' | 'card' | 'avatar' | 'cover', placeId?: string) => void;
}

export const DiscoverScreen: React.FC<DiscoverScreenProps> = ({
  stories,
  places,
  onSelectPlace,
  onToggleBookmark,
  onOpenImageLinker,
}) => {
  const [activeCategory, setActiveCategory] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');

  const categories = [
    { id: 'all', label: 'Todos' },
    { id: 'villas', label: 'Villas Teak' },
    { id: 'cafes', label: 'Café de Autor' },
    { id: 'architecture', label: 'Arquitectura' },
  ];

  const filteredPlaces = places.filter((p) => {
    const matchesCat = activeCategory === 'all' || p.category === activeCategory;
    const matchesSearch =
      searchQuery.trim() === '' ||
      p.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
      p.location.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesCat && matchesSearch;
  });

  const featured = places[0];

  return (
    <div className="flex-1 flex flex-col pb-6 text-slate-100">
      {/* Top Mobile Bar */}
      <div className="px-5 pt-3 pb-2 flex items-center justify-between">
        <div>
          <span className="text-[11px] font-medium tracking-wider text-rose-400 uppercase">
            Curaduría Exclusiva
          </span>
          <h1 className="text-xl font-bold tracking-tight text-white flex items-center gap-1.5 font-display">
            Explorar Espacios
          </h1>
        </div>

        {/* Quick Link Image Action */}
        <button
          onClick={() => onOpenImageLinker('hero', featured?.id)}
          title="Vincular imagen desde HTML o URL"
          className="flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-white/10 hover:bg-white/20 active:scale-95 transition-all text-xs font-medium border border-white/10 text-slate-200"
        >
          <ImagePlus className="w-3.5 h-3.5 text-rose-400" />
          <span className="text-[11px]">Vincular HTML</span>
        </button>
      </div>

      {/* Search Input Bar */}
      <div className="px-5 py-2">
        <div className="relative flex items-center">
          <Search className="w-4 h-4 absolute left-3.5 text-slate-400 pointer-events-none" />
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="Buscar por villa, ciudad o museo..."
            className="w-full bg-slate-900/90 text-sm pl-10 pr-10 py-2.5 rounded-2xl border border-slate-800 focus:outline-none focus:border-rose-500/50 text-slate-100 placeholder:text-slate-500 transition-colors"
          />
          <button
            onClick={() => setSearchQuery('')}
            className="absolute right-3 p-1 text-slate-400 hover:text-slate-200"
            title="Filtros avanzados"
          >
            <SlidersHorizontal className="w-3.5 h-3.5" />
          </button>
        </div>
      </div>

      {/* Stories / Reels Carousel */}
      <div className="pt-2 pb-3">
        <div className="px-5 flex items-center justify-between mb-2">
          <span className="text-xs font-semibold text-slate-300">Historias en Vivo</span>
          <span className="text-[11px] text-slate-500">Actualizado hoy</span>
        </div>

        <div className="flex gap-3 px-5 overflow-x-auto no-scrollbar py-1">
          {stories.map((story) => (
            <div key={story.id} className="flex flex-col items-center gap-1.5 shrink-0 group cursor-pointer">
              <div className="relative p-[2px] rounded-full bg-gradient-to-tr from-rose-500 via-amber-500 to-indigo-500 group-hover:scale-105 transition-transform duration-200">
                <div className="w-14 h-14 rounded-full overflow-hidden border-2 border-slate-950 bg-slate-800">
                  <img
                    src={story.image}
                    alt={story.title}
                    referrerPolicy="no-referrer"
                    className="w-full h-full object-cover"
                  />
                </div>
              </div>
              <span className="text-[11px] text-slate-300 max-w-[62px] truncate text-center font-medium">
                {story.title}
              </span>
            </div>
          ))}
        </div>
      </div>

      {/* Categories Filter Tabs (Segmented control) */}
      <div className="px-5 py-2">
        <div className="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1">
          {categories.map((cat) => (
            <button
              key={cat.id}
              onClick={() => setActiveCategory(cat.id)}
              className={`px-3.5 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition-all active:scale-95 ${
                activeCategory === cat.id
                  ? 'bg-rose-500 text-white shadow-md shadow-rose-500/20'
                  : 'bg-slate-900/80 text-slate-400 hover:text-slate-200 border border-slate-800/80'
              }`}
            >
              {cat.label}
            </button>
          ))}
        </div>
      </div>

      {/* Featured Hero Card */}
      {featured && activeCategory === 'all' && searchQuery.trim() === '' && (
        <div className="px-5 pt-2 pb-4">
          <div className="relative rounded-3xl overflow-hidden group bg-slate-900 border border-slate-800 shadow-xl">
            {/* Main Image with Scrim */}
            <div
              className="relative h-60 w-full cursor-pointer overflow-hidden"
              onClick={() => onSelectPlace(featured)}
            >
              <img
                src={featured.image}
                alt={featured.title}
                referrerPolicy="no-referrer"
                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent" />

              {/* Floating Badge & Bookmark Button */}
              <div className="absolute top-3 inset-x-3 flex items-center justify-between">
                <div className="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md border border-white/10 text-[11px] text-white font-medium">
                  <Sparkles className="w-3 h-3 text-amber-400" />
                  <span>Destacado de la Semana</span>
                </div>

                <div className="flex items-center gap-1.5">
                  <button
                    onClick={(e) => {
                      e.stopPropagation();
                      onOpenImageLinker('hero', featured.id);
                    }}
                    title="Vincular nueva imagen HTML para esta tarjeta"
                    className="w-8 h-8 rounded-full bg-black/60 backdrop-blur-md border border-white/10 flex items-center justify-center text-slate-200 hover:text-white transition-colors"
                  >
                    <ImagePlus className="w-3.5 h-3.5" />
                  </button>
                  <button
                    onClick={(e) => {
                      e.stopPropagation();
                      onToggleBookmark(featured.id);
                    }}
                    className="w-8 h-8 rounded-full bg-black/60 backdrop-blur-md border border-white/10 flex items-center justify-center text-white active:scale-90 transition-transform"
                  >
                    <Heart
                      className={`w-4 h-4 ${
                        featured.isBookmarked ? 'fill-rose-500 text-rose-500' : 'text-white'
                      }`}
                    />
                  </button>
                </div>
              </div>
            </div>

            {/* Content Info */}
            <div
              className="p-4 cursor-pointer hover:bg-slate-900/90 transition-colors"
              onClick={() => onSelectPlace(featured)}
            >
              <div className="flex items-center justify-between gap-2 text-xs text-slate-400 mb-1">
                <span className="flex items-center gap-1 truncate text-slate-300">
                  <MapPin className="w-3 h-3 text-rose-400 shrink-0" />
                  {featured.location}
                </span>
                <span className="flex items-center gap-1 text-amber-400 font-semibold tabular-nums shrink-0">
                  <Star className="w-3 h-3 fill-amber-400" />
                  {featured.rating}
                  <span className="text-slate-500 font-normal">({featured.reviewCount})</span>
                </span>
              </div>

              <h2 className="text-base font-semibold text-white tracking-tight line-clamp-1 mb-1 font-display">
                {featured.title}
              </h2>
              <p className="text-xs text-slate-400 line-clamp-2 leading-relaxed mb-3">
                {featured.subtitle}
              </p>

              {/* Price & Action */}
              <div className="flex items-center justify-between pt-2 border-t border-slate-800">
                <div className="text-xs">
                  <span className="text-base font-bold text-white tabular-nums">
                    ${featured.pricePerNight}
                  </span>
                  <span className="text-slate-400"> / noche</span>
                </div>
                <button className="px-3.5 py-1.5 rounded-xl bg-white text-slate-950 font-semibold text-xs hover:bg-slate-100 active:scale-95 transition-all">
                  Ver Detalles
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Curated Grid of Places */}
      <div className="px-5 pt-1">
        <div className="flex items-center justify-between mb-3">
          <h3 className="text-sm font-semibold text-slate-200">Lugares Seleccionados</h3>
          <span className="text-xs text-slate-500 tabular-nums">{filteredPlaces.length} disponibles</span>
        </div>

        <div className="space-y-4">
          {filteredPlaces.map((item) => (
            <div
              key={item.id}
              onClick={() => onSelectPlace(item)}
              className="flex gap-3.5 p-3 rounded-2xl bg-slate-900/70 border border-slate-800/80 hover:border-slate-700/80 transition-all cursor-pointer group"
            >
              {/* Thumbnail with overlay */}
              <div className="relative w-24 h-24 rounded-xl overflow-hidden bg-slate-800 shrink-0">
                <img
                  src={item.image}
                  alt={item.title}
                  referrerPolicy="no-referrer"
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <button
                  onClick={(e) => {
                    e.stopPropagation();
                    onToggleBookmark(item.id);
                  }}
                  className="absolute top-1.5 right-1.5 p-1 rounded-full bg-black/60 backdrop-blur-xs text-white"
                >
                  <Heart
                    className={`w-3 h-3 ${
                      item.isBookmarked ? 'fill-rose-500 text-rose-500' : 'text-white'
                    }`}
                  />
                </button>
              </div>

              {/* Text Info */}
              <div className="flex-1 min-w-0 flex flex-col justify-between py-0.5">
                <div>
                  <div className="flex items-center justify-between gap-1 text-[11px] text-slate-400 mb-0.5">
                    <span className="truncate">{item.location}</span>
                    <span className="text-amber-400 font-semibold tabular-nums shrink-0 flex items-center gap-0.5">
                      <Star className="w-2.5 h-2.5 fill-amber-400" />
                      {item.rating}
                    </span>
                  </div>
                  <h4 className="text-xs font-semibold text-white tracking-tight line-clamp-1 group-hover:text-rose-400 transition-colors">
                    {item.title}
                  </h4>
                  <p className="text-[11px] text-slate-400 line-clamp-1 mt-0.5">
                    {item.subtitle}
                  </p>
                </div>

                <div className="flex items-center justify-between pt-1">
                  <span className="text-xs font-bold text-white tabular-nums">
                    ${item.pricePerNight} <span className="text-[10px] text-slate-400 font-normal">/noche</span>
                  </span>
                  <button
                    onClick={(e) => {
                      e.stopPropagation();
                      onOpenImageLinker('card', item.id);
                    }}
                    title="Vincular imagen desde HTML"
                    className="p-1 rounded-md text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
                  >
                    <ImagePlus className="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

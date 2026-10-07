import React from 'react';
import { Bookmark, Heart, MapPin, Star, ArrowRight, Trash2 } from 'lucide-react';
import { PlaceItem } from '../../types';

interface SavedScreenProps {
  savedPlaces: PlaceItem[];
  onSelectPlace: (place: PlaceItem) => void;
  onRemoveBookmark: (placeId: string) => void;
  onExploreMore: () => void;
}

export const SavedScreen: React.FC<SavedScreenProps> = ({
  savedPlaces,
  onSelectPlace,
  onRemoveBookmark,
  onExploreMore,
}) => {
  return (
    <div className="flex-1 flex flex-col text-slate-100 pb-12">
      {/* Header */}
      <div className="px-5 pt-3 pb-3">
        <span className="text-[11px] font-medium tracking-wider text-rose-400 uppercase">
          Tus Favoritos
        </span>
        <h1 className="text-xl font-bold tracking-tight text-white font-display">
          Lugares Guardados
        </h1>
        <p className="text-xs text-slate-400 mt-0.5">
          {savedPlaces.length} {savedPlaces.length === 1 ? 'espacio guardado' : 'espacios guardados'} en tu colección
        </p>
      </div>

      {/* List */}
      <div className="px-5 space-y-3.5 flex-1">
        {savedPlaces.length === 0 ? (
          <div className="text-center py-12 px-4 rounded-3xl bg-slate-900/60 border border-slate-800 space-y-3">
            <div className="w-12 h-12 rounded-full bg-slate-800 text-slate-400 flex items-center justify-center mx-auto">
              <Bookmark className="w-6 h-6" />
            </div>
            <h3 className="text-sm font-semibold text-white">No tienes lugares guardados</h3>
            <p className="text-xs text-slate-400 max-w-xs mx-auto">
              Toca el corazón en cualquier villa, galería o cafetería para guardarla aquí.
            </p>
            <button
              onClick={onExploreMore}
              className="px-4 py-2 rounded-xl bg-rose-500 text-white font-medium text-xs hover:bg-rose-600 active:scale-95 transition-all"
            >
              Explorar Colecciones
            </button>
          </div>
        ) : (
          savedPlaces.map((place) => (
            <div
              key={place.id}
              onClick={() => onSelectPlace(place)}
              className="p-3 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-slate-700 transition-all cursor-pointer group flex gap-3.5"
            >
              {/* Image */}
              <div className="relative w-24 h-24 rounded-xl overflow-hidden bg-slate-800 shrink-0">
                <img
                  src={place.image}
                  alt={place.title}
                  referrerPolicy="no-referrer"
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
              </div>

              {/* Info */}
              <div className="flex-1 min-w-0 flex flex-col justify-between py-0.5">
                <div>
                  <div className="flex items-center justify-between text-[11px] text-slate-400">
                    <span className="truncate flex items-center gap-1 text-slate-300">
                      <MapPin className="w-2.5 h-2.5 text-rose-400 shrink-0" />
                      {place.location}
                    </span>
                    <button
                      onClick={(e) => {
                        e.stopPropagation();
                        onRemoveBookmark(place.id);
                      }}
                      title="Eliminar de guardados"
                      className="p-1 text-slate-400 hover:text-rose-400 transition-colors"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>

                  <h3 className="text-xs font-semibold text-white truncate group-hover:text-rose-400 transition-colors mt-0.5">
                    {place.title}
                  </h3>
                  <div className="flex items-center gap-1 text-[11px] text-amber-400 font-semibold tabular-nums mt-0.5">
                    <Star className="w-2.5 h-2.5 fill-amber-400" />
                    <span>{place.rating}</span>
                    <span className="text-slate-500 font-normal">({place.reviewCount})</span>
                  </div>
                </div>

                <div className="flex items-center justify-between pt-1">
                  <span className="text-xs font-bold text-white tabular-nums">
                    ${place.pricePerNight} <span className="text-[10px] text-slate-400 font-normal">/noche</span>
                  </span>
                  <span className="text-[11px] font-medium text-rose-400 flex items-center gap-0.5 group-hover:translate-x-0.5 transition-transform">
                    Ver <ArrowRight className="w-3 h-3" />
                  </span>
                </div>
              </div>
            </div>
          ))
        )}
      </div>
    </div>
  );
};

import React, { useState } from 'react';
import { Camera, Grid, Bookmark, MessageSquare, Check, UserPlus, Heart, ExternalLink, ImagePlus } from 'lucide-react';
import { UserProfile, PlaceItem } from '../../types';

interface ProfileScreenProps {
  profile: UserProfile;
  places: PlaceItem[];
  onSelectPlace: (place: PlaceItem) => void;
  onOpenImageLinker: (target: 'hero' | 'card' | 'avatar' | 'cover') => void;
}

export const ProfileScreen: React.FC<ProfileScreenProps> = ({
  profile,
  places,
  onSelectPlace,
  onOpenImageLinker,
}) => {
  const [isFollowing, setIsFollowing] = useState<boolean>(false);
  const [activeTab, setActiveTab] = useState<'gallery' | 'saved' | 'about'>('gallery');

  return (
    <div className="flex-1 flex flex-col text-slate-100 pb-12">
      {/* Cover Banner */}
      <div className="relative w-full h-36 bg-slate-900 group">
        <img
          src={profile.coverImage}
          alt="Portada"
          referrerPolicy="no-referrer"
          className="w-full h-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-slate-950" />

        {/* Change Cover Trigger */}
        <button
          onClick={() => onOpenImageLinker('cover')}
          title="Vincular imagen de portada desde HTML"
          className="absolute top-3 right-3 p-2 rounded-full bg-black/60 backdrop-blur-md text-white hover:text-rose-400 border border-white/10 transition-colors"
        >
          <Camera className="w-3.5 h-3.5" />
        </button>
      </div>

      {/* Profile Header & Avatar */}
      <div className="px-5 -mt-12 relative z-10 flex flex-col">
        <div className="flex items-end justify-between">
          {/* Avatar with edit trigger */}
          <div className="relative group">
            <div className="w-20 h-20 rounded-full border-4 border-slate-950 overflow-hidden bg-slate-800 shadow-xl">
              <img
                src={profile.avatar}
                alt={profile.name}
                referrerPolicy="no-referrer"
                className="w-full h-full object-cover"
              />
            </div>
            <button
              onClick={() => onOpenImageLinker('avatar')}
              title="Vincular avatar desde HTML o URL"
              className="absolute bottom-0 right-0 p-1.5 rounded-full bg-rose-500 text-white shadow-md hover:bg-rose-600 transition-colors"
            >
              <ImagePlus className="w-3 h-3" />
            </button>
          </div>

          {/* Action Buttons: Follow / Message */}
          <div className="flex items-center gap-2 mb-1">
            <button
              onClick={() => setIsFollowing(!isFollowing)}
              className={`px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all active:scale-95 ${
                isFollowing
                  ? 'bg-slate-800 text-slate-200 border border-slate-700'
                  : 'bg-rose-500 hover:bg-rose-600 text-white shadow-md shadow-rose-500/25'
              }`}
            >
              {isFollowing ? (
                <>
                  <Check className="w-3.5 h-3.5 text-emerald-400" />
                  <span>Siguiendo</span>
                </>
              ) : (
                <>
                  <UserPlus className="w-3.5 h-3.5" />
                  <span>Seguir</span>
                </>
              )}
            </button>

            <button
              onClick={() => alert(`Iniciando chat con ${profile.name}...`)}
              className="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:bg-slate-800 transition-colors"
              title="Enviar mensaje directo"
            >
              <MessageSquare className="w-4 h-4" />
            </button>
          </div>
        </div>

        {/* Bio Information */}
        <div className="mt-3">
          <div className="flex items-center gap-1.5">
            <h2 className="text-base font-bold text-white tracking-tight">{profile.name}</h2>
            {profile.verified && (
              <span className="w-4 h-4 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center text-[10px] font-bold">
                ✓
              </span>
            )}
          </div>
          <span className="text-xs text-rose-400 font-medium">{profile.handle}</span>
          <p className="text-xs text-slate-300/90 mt-2 leading-relaxed">{profile.bio}</p>
        </div>

        {/* Quantitative Metrics (Unboxed text with separators per constitution) */}
        <div className="flex items-center gap-3 text-xs text-slate-400 mt-3 pt-3 border-t border-slate-800/80">
          <div>
            <span className="font-bold text-white tabular-nums">{profile.followers}</span>{' '}
            <span className="text-slate-400 text-[11px]">seguidores</span>
          </div>
          <span aria-hidden="true" className="text-slate-600">·</span>
          <div>
            <span className="font-bold text-white tabular-nums">{profile.savedPlaces}</span>{' '}
            <span className="text-slate-400 text-[11px]">lugares</span>
          </div>
          <span aria-hidden="true" className="text-slate-600">·</span>
          <div>
            <span className="font-bold text-white tabular-nums">{profile.curations}</span>{' '}
            <span className="text-slate-400 text-[11px]">colecciones</span>
          </div>
        </div>
      </div>

      {/* Profile Segmented Tabs */}
      <div className="px-5 mt-4">
        <div className="flex items-center border-b border-slate-800 text-xs">
          <button
            onClick={() => setActiveTab('gallery')}
            className={`flex-1 py-2.5 font-medium flex items-center justify-center gap-1.5 border-b-2 transition-colors ${
              activeTab === 'gallery'
                ? 'border-rose-500 text-rose-400'
                : 'border-transparent text-slate-400 hover:text-slate-200'
            }`}
          >
            <Grid className="w-3.5 h-3.5" />
            <span>Galería</span>
          </button>
          <button
            onClick={() => setActiveTab('saved')}
            className={`flex-1 py-2.5 font-medium flex items-center justify-center gap-1.5 border-b-2 transition-colors ${
              activeTab === 'saved'
                ? 'border-rose-500 text-rose-400'
                : 'border-transparent text-slate-400 hover:text-slate-200'
            }`}
          >
            <Bookmark className="w-3.5 h-3.5" />
            <span>Curadurías</span>
          </button>
          <button
            onClick={() => setActiveTab('about')}
            className={`flex-1 py-2.5 font-medium flex items-center justify-center gap-1.5 border-b-2 transition-colors ${
              activeTab === 'about'
                ? 'border-rose-500 text-rose-400'
                : 'border-transparent text-slate-400 hover:text-slate-200'
            }`}
          >
            <ExternalLink className="w-3.5 h-3.5" />
            <span>Bio & Contacto</span>
          </button>
        </div>
      </div>

      {/* Tab Content */}
      <div className="px-5 pt-3">
        {activeTab === 'gallery' && (
          <div className="grid grid-cols-2 gap-2.5">
            {places.map((place) => (
              <div
                key={place.id}
                onClick={() => onSelectPlace(place)}
                className="group relative rounded-2xl overflow-hidden aspect-4/3 bg-slate-900 cursor-pointer border border-slate-800"
              >
                <img
                  src={place.image}
                  alt={place.title}
                  referrerPolicy="no-referrer"
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-80" />
                <div className="absolute bottom-2 inset-x-2 flex items-center justify-between text-[11px] text-white">
                  <span className="truncate font-medium">{place.title}</span>
                  <span className="flex items-center gap-0.5 text-rose-400 shrink-0">
                    <Heart className="w-2.5 h-2.5 fill-rose-500" />
                  </span>
                </div>
              </div>
            ))}
          </div>
        )}

        {activeTab === 'saved' && (
          <div className="space-y-3">
            <div className="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-between">
              <div>
                <h4 className="text-xs font-semibold text-white">Arquitectura & Luz Zen</h4>
                <span className="text-[11px] text-slate-400">8 ubicaciones seleccionadas</span>
              </div>
              <span className="text-xs text-rose-400 font-medium">Ver ➔</span>
            </div>
            <div className="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-between">
              <div>
                <h4 className="text-xs font-semibold text-white">Cafés de Especialidad 2026</h4>
                <span className="text-[11px] text-slate-400">14 barras de degustación</span>
              </div>
              <span className="text-xs text-rose-400 font-medium">Ver ➔</span>
            </div>
          </div>
        )}

        {activeTab === 'about' && (
          <div className="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 text-xs">
            <div>
              <span className="text-slate-400 block text-[11px]">Localización actual</span>
              <span className="text-white font-medium">Kioto, Japón & Ubud, Bali</span>
            </div>
            <div>
              <span className="text-slate-400 block text-[11px]">Enfoque de diseño</span>
              <span className="text-white font-medium">Espacios biofílicos, concreto escultórico y luz natural.</span>
            </div>
            <div>
              <span className="text-slate-400 block text-[11px]">Contacto profesional</span>
              <span className="text-rose-400 font-medium">studio@elenavaldes.design</span>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

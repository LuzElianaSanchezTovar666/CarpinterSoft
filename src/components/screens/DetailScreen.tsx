import React, { useState } from 'react';
import { ChevronLeft, Share2, Heart, Star, MapPin, CheckCircle2, ShieldCheck, Calendar, Users, ImagePlus, Check } from 'lucide-react';
import { PlaceItem } from '../../types';

interface DetailScreenProps {
  place: PlaceItem;
  onBack: () => void;
  onToggleBookmark: (placeId: string) => void;
  onOpenImageLinker: (target: 'hero' | 'card' | 'avatar' | 'cover', placeId?: string) => void;
}

export const DetailScreen: React.FC<DetailScreenProps> = ({
  place,
  onBack,
  onToggleBookmark,
  onOpenImageLinker,
}) => {
  const [activeImage, setActiveImage] = useState<string>(place.image);
  const [isBookingOpen, setIsBookingOpen] = useState<boolean>(false);
  const [bookingConfirmed, setBookingConfirmed] = useState<boolean>(false);
  const [guestsCount, setGuestsCount] = useState<number>(2);
  const [nightsCount, setNightsCount] = useState<number>(3);
  const [copiedLink, setCopiedLink] = useState<boolean>(false);

  const handleShare = () => {
    setCopiedLink(true);
    setTimeout(() => setCopiedLink(false), 2000);
  };

  const handleConfirmReservation = () => {
    setBookingConfirmed(true);
    setTimeout(() => {
      setBookingConfirmed(false);
      setIsBookingOpen(false);
    }, 2500);
  };

  const totalCalculated = place.pricePerNight * nightsCount;

  return (
    <div className="relative flex-1 flex flex-col text-slate-100 pb-20">
      {/* Top Floating App Bar */}
      <div className="absolute top-3 inset-x-4 z-20 flex items-center justify-between">
        <button
          onClick={onBack}
          className="w-9 h-9 rounded-full bg-black/60 backdrop-blur-md border border-white/10 flex items-center justify-center text-white hover:bg-black/80 active:scale-95 transition-all"
        >
          <ChevronLeft className="w-5 h-5" />
        </button>

        <div className="flex items-center gap-2">
          {/* Vincular HTML Image Button */}
          <button
            onClick={() => onOpenImageLinker('hero', place.id)}
            title="Cambiar imagen con código HTML o URL"
            className="w-9 h-9 rounded-full bg-black/60 backdrop-blur-md border border-white/10 flex items-center justify-center text-rose-300 hover:text-white active:scale-95 transition-all"
          >
            <ImagePlus className="w-4 h-4" />
          </button>

          {/* Share */}
          <button
            onClick={handleShare}
            className="w-9 h-9 rounded-full bg-black/60 backdrop-blur-md border border-white/10 flex items-center justify-center text-white hover:bg-black/80 active:scale-95 transition-all"
          >
            {copiedLink ? <Check className="w-4 h-4 text-emerald-400" /> : <Share2 className="w-4 h-4" />}
          </button>

          {/* Bookmark */}
          <button
            onClick={() => onToggleBookmark(place.id)}
            className="w-9 h-9 rounded-full bg-black/60 backdrop-blur-md border border-white/10 flex items-center justify-center text-white hover:bg-black/80 active:scale-95 transition-all"
          >
            <Heart
              className={`w-4 h-4 ${
                place.isBookmarked ? 'fill-rose-500 text-rose-500' : 'text-white'
              }`}
            />
          </button>
        </div>
      </div>

      {/* Main Full-Bleed Image Header */}
      <div className="relative w-full h-80 bg-slate-900 overflow-hidden">
        <img
          src={activeImage}
          alt={place.title}
          referrerPolicy="no-referrer"
          className="w-full h-full object-cover transition-all duration-300"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent" />

        {/* Thumbnail Selector at bottom of hero */}
        <div className="absolute bottom-4 right-4 flex items-center gap-1.5 p-1 rounded-xl bg-black/50 backdrop-blur-md border border-white/10">
          {[place.image, ...place.secondaryImages.slice(1, 3)].map((imgUrl, idx) => (
            <button
              key={idx}
              onClick={() => setActiveImage(imgUrl)}
              className={`w-9 h-9 rounded-lg overflow-hidden border-2 transition-all ${
                activeImage === imgUrl ? 'border-rose-500 scale-105' : 'border-transparent opacity-70 hover:opacity-100'
              }`}
            >
              <img src={imgUrl} alt="" className="w-full h-full object-cover" />
            </button>
          ))}
        </div>
      </div>

      {/* Place Content Body */}
      <div className="px-5 pt-3 space-y-5">
        {/* Title & Metadata */}
        <div>
          <div className="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <MapPin className="w-3.5 h-3.5 text-rose-400 shrink-0" />
            <span className="text-slate-300 font-medium">{place.location}</span>
            <span aria-hidden="true">·</span>
            <span className="flex items-center gap-1 text-amber-400 font-semibold tabular-nums">
              <Star className="w-3 h-3 fill-amber-400" />
              {place.rating} ({place.reviewCount} reseñas)
            </span>
          </div>

          <h1 className="text-xl font-bold text-white tracking-tight leading-snug font-display">
            {place.title}
          </h1>
          <p className="text-xs text-slate-400 mt-1 leading-relaxed">{place.subtitle}</p>
        </div>

        {/* Host Info Box */}
        <div className="flex items-center justify-between p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800">
          <div className="flex items-center gap-3">
            <div className="relative w-11 h-11 rounded-full overflow-hidden bg-slate-800 border border-slate-700">
              <img src={place.hostAvatar} alt={place.hostName} className="w-full h-full object-cover" />
            </div>
            <div>
              <div className="flex items-center gap-1.5">
                <span className="text-xs font-semibold text-white">{place.hostName}</span>
                <ShieldCheck className="w-3.5 h-3.5 text-emerald-400" />
              </div>
              <span className="text-[11px] text-slate-400">{place.hostRole}</span>
            </div>
          </div>
          <button
            onClick={() => onOpenImageLinker('avatar')}
            title="Cambiar avatar del anfitrión"
            className="text-[11px] font-medium text-rose-400 hover:text-rose-300 transition-colors"
          >
            Editar foto
          </button>
        </div>

        {/* Highlights 3-col Grid */}
        <div className="grid grid-cols-3 gap-2">
          {place.highlights.map((h, i) => (
            <div key={i} className="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800/80 text-center">
              <span className="text-[10px] text-slate-500 block uppercase tracking-wider font-medium">
                {h.label}
              </span>
              <span className="text-xs font-semibold text-slate-200 mt-0.5 block">{h.value}</span>
            </div>
          ))}
        </div>

        {/* Editorial Description */}
        <div>
          <h3 className="text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
            Acerca del Espacio
          </h3>
          <p className="text-xs text-slate-300/90 leading-relaxed">{place.description}</p>
        </div>

        {/* Amenities Checklist */}
        <div>
          <h3 className="text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
            Comodidades Principales
          </h3>
          <div className="grid grid-cols-1 gap-2">
            {place.amenities.map((amenity, idx) => (
              <div key={idx} className="flex items-center gap-2.5 text-xs text-slate-300">
                <CheckCircle2 className="w-3.5 h-3.5 text-rose-400 shrink-0" />
                <span>{amenity}</span>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Fixed Sticky Bottom CTA Bar */}
      <div className="sticky bottom-0 left-0 right-0 p-4 bg-slate-950/90 backdrop-blur-md border-t border-slate-800 z-30 flex items-center justify-between mt-auto">
        <div>
          <div className="flex items-baseline gap-1">
            <span className="text-lg font-bold text-white tabular-nums">${place.pricePerNight}</span>
            <span className="text-xs text-slate-400">/ noche</span>
          </div>
          <span className="text-[10px] text-emerald-400 font-medium">Cancelación flexible</span>
        </div>

        <button
          onClick={() => setIsBookingOpen(true)}
          className="px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 active:scale-95 text-white font-semibold text-xs tracking-tight shadow-lg shadow-rose-500/25 transition-all"
        >
          Reservar Ahora
        </button>
      </div>

      {/* Interactive Booking Bottom Sheet Drawer */}
      {isBookingOpen && (
        <div className="fixed inset-0 z-50 flex items-end justify-center bg-black/70 backdrop-blur-xs">
          <div className="w-full max-w-[380px] bg-slate-900 border-t border-slate-700 rounded-t-3xl p-5 shadow-2xl animate-in slide-in-from-bottom duration-300">
            {/* Grab handle */}
            <div className="w-10 h-1 bg-slate-700 rounded-full mx-auto mb-4" />

            {bookingConfirmed ? (
              <div className="text-center py-6 space-y-3">
                <div className="w-12 h-12 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex items-center justify-center mx-auto">
                  <Check className="w-6 h-6" />
                </div>
                <h3 className="text-base font-bold text-white">¡Reserva Confirmada!</h3>
                <p className="text-xs text-slate-400 max-w-xs mx-auto">
                  Se ha enviado la confirmación para {nightsCount} noches en {place.title}.
                </p>
              </div>
            ) : (
              <div className="space-y-4">
                <div className="flex items-center justify-between pb-2 border-b border-slate-800">
                  <h3 className="text-sm font-bold text-white">Configurar Reserva</h3>
                  <button
                    onClick={() => setIsBookingOpen(false)}
                    className="text-xs text-slate-400 hover:text-white"
                  >
                    Cerrar
                  </button>
                </div>

                {/* Nights Counter */}
                <div className="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800">
                  <div className="flex items-center gap-2 text-xs text-slate-300">
                    <Calendar className="w-4 h-4 text-rose-400" />
                    <span>Noches de estancia</span>
                  </div>
                  <div className="flex items-center gap-3">
                    <button
                      onClick={() => setNightsCount(Math.max(1, nightsCount - 1))}
                      className="w-7 h-7 rounded-lg bg-slate-800 text-white font-bold text-xs"
                    >
                      -
                    </button>
                    <span className="text-xs font-bold text-white tabular-nums">{nightsCount}</span>
                    <button
                      onClick={() => setNightsCount(nightsCount + 1)}
                      className="w-7 h-7 rounded-lg bg-slate-800 text-white font-bold text-xs"
                    >
                      +
                    </button>
                  </div>
                </div>

                {/* Guests Counter */}
                <div className="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800">
                  <div className="flex items-center gap-2 text-xs text-slate-300">
                    <Users className="w-4 h-4 text-rose-400" />
                    <span>Huéspedes</span>
                  </div>
                  <div className="flex items-center gap-3">
                    <button
                      onClick={() => setGuestsCount(Math.max(1, guestsCount - 1))}
                      className="w-7 h-7 rounded-lg bg-slate-800 text-white font-bold text-xs"
                    >
                      -
                    </button>
                    <span className="text-xs font-bold text-white tabular-nums">{guestsCount}</span>
                    <button
                      onClick={() => setGuestsCount(guestsCount + 1)}
                      className="w-7 h-7 rounded-lg bg-slate-800 text-white font-bold text-xs"
                    >
                      +
                    </button>
                  </div>
                </div>

                {/* Price Breakdown */}
                <div className="pt-2 text-xs space-y-1.5 text-slate-400">
                  <div className="flex justify-between">
                    <span>${place.pricePerNight} × {nightsCount} noches</span>
                    <span className="text-white tabular-nums">${totalCalculated}</span>
                  </div>
                  <div className="flex justify-between">
                    <span>Tasa de servicio de diseño</span>
                    <span className="text-white tabular-nums">$35</span>
                  </div>
                  <div className="flex justify-between font-semibold text-white pt-2 border-t border-slate-800">
                    <span>Total estimado</span>
                    <span className="text-rose-400 tabular-nums">${totalCalculated + 35}</span>
                  </div>
                </div>

                {/* Confirm Action Button */}
                <button
                  onClick={handleConfirmReservation}
                  className="w-full py-3 rounded-xl bg-rose-500 hover:bg-rose-600 active:scale-98 text-white font-semibold text-xs tracking-tight transition-all shadow-lg shadow-rose-500/30"
                >
                  Confirmar y Proceder (${totalCalculated + 35})
                </button>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
};

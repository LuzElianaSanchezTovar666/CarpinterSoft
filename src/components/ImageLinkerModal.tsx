import React, { useState } from 'react';
import { X, Code2, Link, Copy, Check, Sparkles, Image, CheckCircle, AlertCircle } from 'lucide-react';
import { PRESET_IMAGE_OPTIONS } from '../data/mockData';

interface ImageLinkerModalProps {
  isOpen: boolean;
  onClose: () => void;
  currentTarget: 'hero' | 'card' | 'avatar' | 'cover';
  targetPlaceId?: string;
  onApplyImage: (imageUrl: string, target: 'hero' | 'card' | 'avatar' | 'cover', targetPlaceId?: string) => void;
}

export const ImageLinkerModal: React.FC<ImageLinkerModalProps> = ({
  isOpen,
  onClose,
  currentTarget,
  targetPlaceId,
  onApplyImage,
}) => {
  const [selectedTarget, setSelectedTarget] = useState<'hero' | 'card' | 'avatar' | 'cover'>(currentTarget);
  const [inputVal, setInputVal] = useState<string>('');
  const [previewUrl, setPreviewUrl] = useState<string>('');
  const [copyCodeSuccess, setCopyCodeSuccess] = useState<boolean>(false);
  const [errorMsg, setErrorMsg] = useState<string>('');

  if (!isOpen) return null;

  // Extract src from HTML tag like `<img src="..." />` or recognize direct URL
  const handleInputChange = (text: string) => {
    setInputVal(text);
    setErrorMsg('');

    // Check if input is an HTML tag <img ... src="..." />
    const imgTagMatch = text.match(/<img[^>]+src=["']([^"']+)["']/i);
    if (imgTagMatch && imgTagMatch[1]) {
      setPreviewUrl(imgTagMatch[1]);
      return;
    }

    // Check if it's a direct URL (http or https or data url or relative /src/)
    if (text.startsWith('http://') || text.startsWith('https://') || text.startsWith('/') || text.startsWith('data:image')) {
      setPreviewUrl(text);
      return;
    }

    if (text.trim() === '') {
      setPreviewUrl('');
    }
  };

  const handleSelectPreset = (url: string) => {
    setInputVal(url);
    setPreviewUrl(url);
    setErrorMsg('');
  };

  const handleApply = () => {
    const finalUrl = previewUrl.trim() || inputVal.trim();
    if (!finalUrl) {
      setErrorMsg('Por favor introduce una URL o etiqueta HTML <img> válida.');
      return;
    }
    onApplyImage(finalUrl, selectedTarget, targetPlaceId);
    onClose();
  };

  // Generate HTML snippet for copying
  const generatedHtmlSnippet = `<img
  src="${previewUrl || 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1200&q=80'}"
  alt="Espacio arquitectónico curado"
  loading="lazy"
  class="rounded-3xl shadow-xl w-full h-auto object-cover transition-transform duration-300 hover:scale-105"
/>`;

  const copyToClipboard = () => {
    navigator.clipboard.writeText(generatedHtmlSnippet);
    setCopyCodeSuccess(true);
    setTimeout(() => setCopyCodeSuccess(false), 2000);
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-in fade-in duration-200">
      <div className="relative w-full max-w-2xl bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl flex flex-col max-h-[90vh] overflow-y-auto no-scrollbar">
        {/* Top Header */}
        <div className="flex items-center justify-between pb-4 border-b border-slate-800">
          <div className="flex items-center gap-2.5">
            <div className="p-2 rounded-xl bg-rose-500/10 text-rose-400 border border-rose-500/20">
              <Code2 className="w-5 h-5" />
            </div>
            <div>
              <h2 className="text-lg font-bold text-white tracking-tight font-display">
                Vincular Imágenes desde HTML & URL
              </h2>
              <p className="text-xs text-slate-400">
                Pega tu etiqueta <code className="text-rose-300 font-mono">&lt;img src="..."&gt;</code> o selecciona una imagen HD.
              </p>
            </div>
          </div>

          <button
            onClick={onClose}
            className="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Target Destination Selector */}
        <div className="pt-4 pb-2">
          <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
            ¿Dónde vincular esta imagen en la app?
          </label>
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-2">
            {[
              { id: 'hero', label: 'Hero Principal' },
              { id: 'card', label: 'Tarjeta de Espacio' },
              { id: 'cover', label: 'Portada de Perfil' },
              { id: 'avatar', label: 'Avatar de Creador' },
            ].map((t) => (
              <button
                key={t.id}
                onClick={() => setSelectedTarget(t.id as any)}
                className={`py-2 px-3 rounded-xl text-xs font-semibold border transition-all text-center ${
                  selectedTarget === t.id
                    ? 'bg-rose-500 text-white border-rose-400 shadow-md shadow-rose-500/20'
                    : 'bg-slate-950 text-slate-400 border-slate-800 hover:text-white hover:border-slate-700'
                }`}
              >
                {t.label}
              </button>
            ))}
          </div>
        </div>

        {/* Input Bar (HTML snippet or URL) */}
        <div className="pt-3 pb-2">
          <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
            <span>Pegar URL o Etiqueta HTML</span>
            <span className="text-[11px] text-slate-500 font-normal lowercase">Soporta &lt;img src="..." /&gt;</span>
          </label>
          <div className="relative">
            <textarea
              rows={2}
              value={inputVal}
              onChange={(e) => handleInputChange(e.target.value)}
              placeholder='<img src="https://images.unsplash.com/photo-..." alt="Mi diseño" /> ó https://...'
              className="w-full bg-slate-950 font-mono text-xs text-rose-300 p-3 rounded-2xl border border-slate-800 focus:outline-none focus:border-rose-500/60 placeholder:text-slate-600 transition-colors"
            />
          </div>
          {errorMsg && (
            <div className="flex items-center gap-1.5 text-xs text-rose-400 mt-1">
              <AlertCircle className="w-3.5 h-3.5" />
              <span>{errorMsg}</span>
            </div>
          )}
        </div>

        {/* Presets Gallery (Locally generated assets) */}
        <div className="pt-2 pb-3">
          <label className="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2 flex items-center gap-1.5">
            <Sparkles className="w-3.5 h-3.5 text-amber-400" />
            <span>O selecciona de la galería editorial HD</span>
          </label>
          <div className="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
            {PRESET_IMAGE_OPTIONS.map((preset) => (
              <div
                key={preset.id}
                onClick={() => handleSelectPreset(preset.url)}
                className={`group relative rounded-2xl overflow-hidden aspect-4/3 cursor-pointer border-2 transition-all ${
                  previewUrl === preset.url
                    ? 'border-rose-500 ring-2 ring-rose-500/40'
                    : 'border-slate-800 hover:border-slate-700'
                }`}
              >
                <img
                  src={preset.url}
                  alt={preset.title}
                  referrerPolicy="no-referrer"
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent p-2.5 flex flex-col justify-end">
                  <span className="text-[10px] text-rose-300 font-mono">{preset.badge}</span>
                  <span className="text-xs font-semibold text-white leading-tight line-clamp-1">
                    {preset.title}
                  </span>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Live Preview of Parsed Image */}
        {previewUrl && (
          <div className="pt-2 pb-3">
            <div className="p-3 rounded-2xl bg-slate-950 border border-slate-800 flex items-center gap-3">
              <div className="w-16 h-16 rounded-xl overflow-hidden bg-slate-900 border border-slate-700 shrink-0">
                <img
                  src={previewUrl}
                  alt="Vista previa"
                  referrerPolicy="no-referrer"
                  onError={() => setErrorMsg('No se pudo cargar la imagen. Verifica la URL.')}
                  className="w-full h-full object-cover"
                />
              </div>
              <div className="min-w-0 flex-1">
                <span className="text-xs font-semibold text-emerald-400 flex items-center gap-1">
                  <CheckCircle className="w-3.5 h-3.5" /> Imagen vinculada correctamente
                </span>
                <p className="text-[11px] text-slate-400 truncate mt-0.5 font-mono">{previewUrl}</p>
                <span className="text-[11px] text-slate-500">Se aplicará a: <b className="text-slate-300 capitalize">{selectedTarget}</b></span>
              </div>
            </div>
          </div>
        )}

        {/* HTML Code Export Box */}
        <div className="mt-2 p-3.5 rounded-2xl bg-slate-950 border border-slate-800/80">
          <div className="flex items-center justify-between mb-2">
            <span className="text-xs font-semibold text-slate-300 flex items-center gap-1.5">
              <Code2 className="w-3.5 h-3.5 text-rose-400" />
              Código HTML para vincular en tu web
            </span>
            <button
              onClick={copyToClipboard}
              className="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium transition-colors"
            >
              {copyCodeSuccess ? (
                <>
                  <Check className="w-3 h-3 text-emerald-400" />
                  <span className="text-emerald-400">¡Copiado!</span>
                </>
              ) : (
                <>
                  <Copy className="w-3 h-3" />
                  <span>Copiar HTML</span>
                </>
              )}
            </button>
          </div>
          <pre className="text-[11px] font-mono text-slate-400 bg-slate-900 p-2.5 rounded-xl overflow-x-auto text-wrap">
            {generatedHtmlSnippet}
          </pre>
        </div>

        {/* Modal Actions */}
        <div className="pt-4 flex items-center justify-end gap-3 border-t border-slate-800 mt-4">
          <button
            onClick={onClose}
            className="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-white transition-colors"
          >
            Cancelar
          </button>
          <button
            onClick={handleApply}
            className="px-6 py-2 rounded-xl bg-rose-500 hover:bg-rose-600 active:scale-95 text-white font-semibold text-xs tracking-tight shadow-md shadow-rose-500/25 transition-all"
          >
            Aplicar a la Pantalla
          </button>
        </div>
      </div>
    </div>
  );
};

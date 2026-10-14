import React, { useState } from 'react';
import { FurnitureCatalogItem, FurnitureCategory } from '../types';
import { formatCOP } from '../utils/calculator';
import { 
  Sparkles, 
  Calendar, 
  Ruler, 
  Layers, 
  CheckCircle2, 
  ArrowRight, 
  Scissors, 
  Search 
} from 'lucide-react';

interface CatalogViewProps {
  catalog: FurnitureCatalogItem[];
  onQuoteCatalogItem: (item: FurnitureCatalogItem) => void;
}

export const CatalogView: React.FC<CatalogViewProps> = ({
  catalog,
  onQuoteCatalogItem,
}) => {
  const [selectedCategory, setSelectedCategory] = useState<string>('todas');
  const [searchTerm, setSearchTerm] = useState('');

  const filteredCatalog = catalog.filter((item) => {
    const matchesCat =
      selectedCategory === 'todas' || item.category === selectedCategory;
    const matchesSearch =
      item.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      item.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
      item.baseMaterial.toLowerCase().includes(searchTerm.toLowerCase());
    return matchesCat && matchesSearch;
  });

  return (
    <div className="space-y-6 animate-fadeIn">
      {/* Banner de Catálogo */}
      <div className="bg-stone-900/80 border border-stone-800 p-6 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 text-xs font-semibold border border-amber-500/20 mb-2">
            <Sparkles className="w-3.5 h-3.5" />
            Showroom & Diseños Fabricados
          </div>
          <h2 className="text-xl sm:text-2xl font-black text-white font-['Outfit']">
            Catálogo de Mobiliario & Ebanistería
          </h2>
          <p className="text-stone-400 text-xs sm:text-sm mt-1">
            Modelos de referencia fabricados en el taller. Haz clic en "Cotizar Modelo" para cargar sus dimensiones automáticamente en la calculadora de despiece.
          </p>
        </div>
      </div>

      {/* Filtros y Buscador */}
      <div className="bg-stone-900/80 border border-stone-800 p-4 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div className="relative flex-1 max-w-md">
          <Search className="w-4 h-4 text-stone-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input
            type="text"
            placeholder="Buscar mueble por nombre, material o estilo..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full pl-10 pr-4 py-2 bg-stone-950/70 border border-stone-800 rounded-xl text-xs sm:text-sm text-stone-200 placeholder-stone-500 focus:outline-none focus:border-amber-500"
          />
        </div>

        <div className="flex flex-wrap gap-1.5 bg-stone-950/60 p-1 rounded-xl border border-stone-800">
          {[
            { key: 'todas', label: 'Todos' },
            { key: 'Cocina', label: 'Cocinas' },
            { key: 'Mesa', label: 'Comedores' },
            { key: 'Closet', label: 'Clósets' },
            { key: 'Puerta', label: 'Puertas' },
            { key: 'Especial', label: 'Centros TV' },
            { key: 'Oficina', label: 'Oficinas' },
          ].map((cat) => (
            <button
              key={cat.key}
              onClick={() => setSelectedCategory(cat.key)}
              className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-all ${
                selectedCategory === cat.key
                  ? 'bg-amber-600/30 text-amber-300 border border-amber-500/30 shadow-sm'
                  : 'text-stone-400 hover:text-stone-200 hover:bg-stone-800/40'
              }`}
            >
              {cat.label}
            </button>
          ))}
        </div>
      </div>

      {/* Grid de Diseños de Mobiliario */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {filteredCatalog.map((item) => (
          <div
            key={item.id}
            className="bg-stone-900/80 border border-stone-800 hover:border-amber-500/40 rounded-2xl overflow-hidden flex flex-col justify-between transition-all hover:shadow-2xl group"
          >
            <div>
              {/* Imagen de Portada con Badge de Días */}
              <div className="relative h-56 overflow-hidden bg-stone-950">
                <img
                  src={item.imageUrl}
                  alt={item.name}
                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                  loading="lazy"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-stone-950 via-stone-950/20 to-transparent" />
                
                <div className="absolute top-3 left-3">
                  <span className="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-stone-950/80 text-amber-400 border border-stone-800 backdrop-blur-md">
                    {item.category}
                  </span>
                </div>

                <div className="absolute top-3 right-3">
                  <span className="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-stone-950/80 text-stone-200 border border-stone-800 backdrop-blur-md flex items-center gap-1">
                    <Calendar className="w-3 h-3 text-amber-400" />
                    {item.estimatedDays} días fab.
                  </span>
                </div>

                <div className="absolute bottom-3 left-3 right-3">
                  <h3 className="text-base font-bold text-white font-['Outfit'] leading-snug drop-shadow-md">
                    {item.name}
                  </h3>
                </div>
              </div>

              {/* Contenido & Ficha Técnica */}
              <div className="p-4 space-y-3">
                <p className="text-xs text-stone-400 line-clamp-2 leading-relaxed">
                  {item.description}
                </p>

                <div className="p-2.5 rounded-xl bg-stone-950/70 border border-stone-800/80 space-y-1.5 text-xs">
                  <div className="flex items-center justify-between">
                    <span className="text-stone-500 flex items-center gap-1">
                      <Ruler className="w-3.5 h-3.5 text-amber-400" />
                      Medidas ref:
                    </span>
                    <span className="font-mono text-stone-200 text-[11px]">{item.dimensions}</span>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-stone-500 flex items-center gap-1">
                      <Layers className="w-3.5 h-3.5 text-amber-400" />
                      Material base:
                    </span>
                    <span className="text-stone-200 text-[11px] truncate max-w-[170px]">{item.baseMaterial}</span>
                  </div>
                </div>

                {/* Features */}
                <div className="space-y-1 pt-1">
                  {item.features.map((feat, idx) => (
                    <div key={idx} className="flex items-center gap-1.5 text-[11px] text-stone-300">
                      <CheckCircle2 className="w-3 h-3 text-emerald-400 flex-shrink-0" />
                      <span>{feat}</span>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            {/* Footer con Precio y Botón de Cotizar */}
            <div className="p-4 pt-3 border-t border-stone-800 flex items-center justify-between bg-stone-950/40">
              <div>
                <span className="text-[10px] text-stone-400 uppercase font-semibold block">Precio Referencial</span>
                <span className="text-base font-bold text-white font-mono">{formatCOP(item.suggestedPrice)}</span>
              </div>

              <button
                onClick={() => onQuoteCatalogItem(item)}
                className="flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-600/20 hover:bg-amber-600 text-amber-300 hover:text-stone-950 border border-amber-500/30 text-xs font-bold transition-all cursor-pointer group-hover:bg-amber-500 group-hover:text-stone-950"
              >
                <Scissors className="w-3.5 h-3.5" />
                <span>Cotizar Este</span>
              </button>
            </div>

          </div>
        ))}
      </div>
    </div>
  );
};

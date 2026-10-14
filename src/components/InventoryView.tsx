import React, { useState } from 'react';
import { MaterialInventoryItem, MaterialCategory } from '../types';
import { formatCOP } from '../utils/calculator';
import { 
  Boxes, 
  Search, 
  Plus, 
  AlertTriangle, 
  PlusCircle, 
  MinusCircle, 
  DollarSign, 
  CheckCircle, 
  MapPin, 
  Truck, 
  Tag, 
  Layers, 
  Filter
} from 'lucide-react';

interface InventoryViewProps {
  inventory: MaterialInventoryItem[];
  onUpdateStock: (itemId: string, delta: number) => void;
  onAddMaterial: (material: MaterialInventoryItem) => void;
}

export const InventoryView: React.FC<InventoryViewProps> = ({
  inventory,
  onUpdateStock,
  onAddMaterial,
}) => {
  const [selectedCategory, setSelectedCategory] = useState<string>('todas');
  const [searchTerm, setSearchTerm] = useState('');
  const [isModalOpen, setIsModalOpen] = useState(false);

  // Nuevo material form state
  const [newMaterial, setNewMaterial] = useState<Partial<MaterialInventoryItem>>({
    name: '',
    category: 'madera',
    unit: 'pie_tablar',
    stock: 50,
    minStock: 20,
    unitCost: 25000,
    supplier: '',
    location: '',
    description: '',
  });

  const filteredItems = inventory.filter((item) => {
    const matchesCategory =
      selectedCategory === 'todas' || item.category === selectedCategory;
    const matchesSearch =
      item.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      item.code.toLowerCase().includes(searchTerm.toLowerCase()) ||
      item.supplier.toLowerCase().includes(searchTerm.toLowerCase()) ||
      item.location.toLowerCase().includes(searchTerm.toLowerCase());
    return matchesCategory && matchesSearch;
  });

  // Estadísticas del inventario
  const totalValue = inventory.reduce((sum, item) => sum + item.stock * item.unitCost, 0);
  const lowStockCount = inventory.filter((item) => item.stock <= item.minStock).length;

  const handleSaveNewMaterial = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newMaterial.name) return;

    const item: MaterialInventoryItem = {
      id: `inv-${Date.now()}`,
      code: `MAT-${Math.floor(1000 + Math.random() * 9000)}`,
      name: newMaterial.name,
      category: (newMaterial.category as MaterialCategory) || 'madera',
      unit: newMaterial.unit as any || 'unidad',
      stock: Number(newMaterial.stock) || 0,
      minStock: Number(newMaterial.minStock) || 10,
      unitCost: Number(newMaterial.unitCost) || 0,
      supplier: newMaterial.supplier || 'Proveedor Local',
      location: newMaterial.location || 'Almacén Central',
      description: newMaterial.description || '',
    };

    onAddMaterial(item);
    setIsModalOpen(false);
    setNewMaterial({
      name: '',
      category: 'madera',
      unit: 'pie_tablar',
      stock: 50,
      minStock: 20,
      unitCost: 25000,
      supplier: '',
      location: '',
      description: '',
    });
  };

  return (
    <div className="space-y-6 animate-fadeIn">
      {/* Banner de Inventario con Valor Total */}
      <div className="bg-stone-900/80 border border-stone-800 p-6 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 text-xs font-semibold border border-amber-500/20 mb-2">
            <Boxes className="w-3.5 h-3.5" />
            Almacén & Materias Primas
          </div>
          <h2 className="text-xl sm:text-2xl font-black text-white font-['Outfit']">
            Control de Inventario de Carpintería
          </h2>
          <p className="text-stone-400 text-xs sm:text-sm mt-1">
            Gestiona el stock de maderas macizas estufadas, tableros de melamina, herrajes de cazoleta, correderas y acabados químicos.
          </p>
        </div>

        <div className="flex flex-wrap items-center gap-4">
          <div className="bg-stone-950 p-3 rounded-xl border border-stone-800">
            <span className="text-[10px] uppercase font-bold text-stone-400 block">Valorización Total Almacén</span>
            <span className="text-lg font-black text-emerald-400 font-mono">{formatCOP(totalValue)}</span>
          </div>

          <button
            onClick={() => setIsModalOpen(true)}
            className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-stone-950 font-bold text-xs sm:text-sm transition-all shadow-md shadow-amber-950/40 cursor-pointer active:scale-95"
          >
            <Plus className="w-4 h-4" />
            <span>Agregar Material</span>
          </button>
        </div>
      </div>

      {/* Controles de Búsqueda y Filtros de Categoría */}
      <div className="bg-stone-900/80 border border-stone-800 p-4 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        {/* Buscador */}
        <div className="relative flex-1 max-w-md">
          <Search className="w-4 h-4 text-stone-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input
            type="text"
            placeholder="Buscar por material, código, proveedor o ubicación..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full pl-10 pr-4 py-2 bg-stone-950/70 border border-stone-800 rounded-xl text-xs sm:text-sm text-stone-200 placeholder-stone-500 focus:outline-none focus:border-amber-500"
          />
        </div>

        {/* Pestañas de Categoría */}
        <div className="flex flex-wrap gap-1.5 bg-stone-950/60 p-1 rounded-xl border border-stone-800">
          {[
            { key: 'todas', label: 'Todos los Materiales' },
            { key: 'madera', label: '🌲 Maderas Macizas' },
            { key: 'tablero', label: '🪵 Tableros & Planchas' },
            { key: 'herraje', label: '🔩 Herrajes' },
            { key: 'quimico', label: '🧪 Químicos & Acabados' },
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

      {/* Grid de Tarjetas de Materiales */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {filteredItems.map((item) => {
          const isLow = item.stock <= item.minStock;
          const stockRatio = Math.min(100, Math.round((item.stock / (item.minStock * 2)) * 100));

          return (
            <div
              key={item.id}
              className={`bg-stone-900/80 border rounded-2xl p-4 flex flex-col justify-between transition-all hover:shadow-xl ${
                isLow ? 'border-rose-500/40 bg-stone-900/90' : 'border-stone-800'
              }`}
            >
              <div className="space-y-3">
                {/* Cabecera Tarjeta */}
                <div className="flex items-center justify-between">
                  <span className="text-[10px] font-mono font-bold text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded border border-amber-400/20">
                    {item.code}
                  </span>
                  {isLow ? (
                    <span className="inline-flex items-center gap-1 text-[10px] font-bold text-rose-400 bg-rose-500/20 px-2 py-0.5 rounded-full border border-rose-500/30">
                      <AlertTriangle className="w-3 h-3" />
                      Stock Crítico
                    </span>
                  ) : (
                    <span className="inline-flex items-center gap-1 text-[10px] font-medium text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">
                      <CheckCircle className="w-3 h-3" />
                      Disponible
                    </span>
                  )}
                </div>

                {/* Nombre y Descripción */}
                <div>
                  <h4 className="text-sm font-bold text-white line-clamp-1">{item.name}</h4>
                  <p className="text-xs text-stone-400 mt-0.5 line-clamp-2">{item.description || 'Sin descripción adicional'}</p>
                </div>

                {/* Ubicación y Proveedor */}
                <div className="p-2 rounded-xl bg-stone-950/70 border border-stone-800/80 space-y-1 text-[11px] text-stone-300">
                  <div className="flex items-center gap-1.5 text-stone-400">
                    <MapPin className="w-3 h-3 text-amber-400" />
                    <span>{item.location}</span>
                  </div>
                  <div className="flex items-center gap-1.5 text-stone-400">
                    <Truck className="w-3 h-3 text-stone-500" />
                    <span>{item.supplier}</span>
                  </div>
                </div>

                {/* Stock actual con Barra */}
                <div>
                  <div className="flex justify-between text-xs mb-1">
                    <span className="text-stone-400">Existencias:</span>
                    <span className="font-bold font-mono text-white">
                      {item.stock} <span className="text-stone-400 font-sans text-[11px]">{item.unit}</span>
                    </span>
                  </div>
                  <div className="w-full bg-stone-950 h-2 rounded-full overflow-hidden border border-stone-800">
                    <div
                      className={`h-full rounded-full transition-all ${
                        isLow ? 'bg-rose-500' : 'bg-amber-500'
                      }`}
                      style={{ width: `${stockRatio}%` }}
                    />
                  </div>
                  <div className="flex justify-between text-[10px] text-stone-500 mt-1">
                    <span>Mínimo requerido: {item.minStock}</span>
                    <span>Costo: {formatCOP(item.unitCost)} / {item.unit}</span>
                  </div>
                </div>
              </div>

              {/* Botones de Ajuste Rápido de Stock (+ / -) */}
              <div className="mt-4 pt-3 border-t border-stone-800/80 flex items-center justify-between">
                <span className="text-[11px] text-stone-400 font-medium">Ajuste de consumo:</span>
                <div className="flex items-center gap-2">
                  <button
                    onClick={() => onUpdateStock(item.id, -1)}
                    disabled={item.stock <= 0}
                    className="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-stone-800 hover:bg-stone-700 disabled:opacity-40 text-stone-300 text-xs font-bold transition-colors cursor-pointer"
                    title="Descontar 1 unidad utilizada en taller"
                  >
                    -1 Uso
                  </button>
                  <button
                    onClick={() => onUpdateStock(item.id, 1)}
                    className="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-600/30 hover:bg-amber-600 text-amber-300 hover:text-stone-950 text-xs font-bold transition-all border border-amber-500/30 cursor-pointer"
                    title="Registrar entrada de 1 unidad"
                  >
                    +1 Ingreso
                  </button>
                </div>
              </div>

            </div>
          );
        })}
      </div>

      {/* Modal para Registrar Nuevo Material */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 bg-stone-950/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-stone-900 border border-stone-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl animate-scaleUp">
            <h3 className="text-lg font-bold text-white font-['Outfit'] mb-4 flex items-center gap-2">
              <PlusCircle className="w-5 h-5 text-amber-400" />
              Registrar Material o Insumo al Taller
            </h3>

            <form onSubmit={handleSaveNewMaterial} className="space-y-4 text-xs">
              <div>
                <label className="block text-stone-300 font-semibold mb-1">Nombre del Material</label>
                <input
                  type="text"
                  required
                  placeholder="Ej: Melamina Rovere 18mm Pelíkano"
                  value={newMaterial.name}
                  onChange={(e) => setNewMaterial({ ...newMaterial, name: e.target.value })}
                  className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white focus:outline-none focus:border-amber-500"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-stone-300 font-semibold mb-1">Categoría</label>
                  <select
                    value={newMaterial.category}
                    onChange={(e) => setNewMaterial({ ...newMaterial, category: e.target.value as MaterialCategory })}
                    className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-stone-200 focus:outline-none focus:border-amber-500"
                  >
                    <option value="madera">Madera Maciza</option>
                    <option value="tablero">Tablero / Melamina</option>
                    <option value="herraje">Herraje</option>
                    <option value="quimico">Químico / Acabado</option>
                  </select>
                </div>

                <div>
                  <label className="block text-stone-300 font-semibold mb-1">Unidad de Medida</label>
                  <select
                    value={newMaterial.unit}
                    onChange={(e) => setNewMaterial({ ...newMaterial, unit: e.target.value as any })}
                    className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-stone-200 focus:outline-none focus:border-amber-500"
                  >
                    <option value="pie_tablar">Pies Tablares (pt)</option>
                    <option value="plancha">Planchas / Láminas</option>
                    <option value="unidad">Unidades</option>
                    <option value="par">Pares (Correderas)</option>
                    <option value="litro">Litros / Galón</option>
                    <option value="metro">Metros lineales (Canto)</option>
                  </select>
                </div>
              </div>

              <div className="grid grid-cols-3 gap-3">
                <div>
                  <label className="block text-stone-300 font-semibold mb-1">Stock Inicial</label>
                  <input
                    type="number"
                    min="0"
                    value={newMaterial.stock}
                    onChange={(e) => setNewMaterial({ ...newMaterial, stock: Number(e.target.value) })}
                    className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white font-mono"
                  />
                </div>
                <div>
                  <label className="block text-stone-300 font-semibold mb-1">Stock Mínimo</label>
                  <input
                    type="number"
                    min="1"
                    value={newMaterial.minStock}
                    onChange={(e) => setNewMaterial({ ...newMaterial, minStock: Number(e.target.value) })}
                    className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white font-mono"
                  />
                </div>
                <div>
                  <label className="block text-stone-300 font-semibold mb-1">Costo Unit. (COP)</label>
                  <input
                    type="number"
                    min="0"
                    value={newMaterial.unitCost}
                    onChange={(e) => setNewMaterial({ ...newMaterial, unitCost: Number(e.target.value) })}
                    className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white font-mono"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-stone-300 font-semibold mb-1">Proveedor</label>
                  <input
                    type="text"
                    placeholder="Ej: Distribuidora Maderas del Norte"
                    value={newMaterial.supplier}
                    onChange={(e) => setNewMaterial({ ...newMaterial, supplier: e.target.value })}
                    className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white"
                  />
                </div>
                <div>
                  <label className="block text-stone-300 font-semibold mb-1">Ubicación en Taller</label>
                  <input
                    type="text"
                    placeholder="Ej: Bodega Vertical 2"
                    value={newMaterial.location}
                    onChange={(e) => setNewMaterial({ ...newMaterial, location: e.target.value })}
                    className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white"
                  />
                </div>
              </div>

              <div className="flex items-center justify-end gap-3 pt-4 border-t border-stone-800">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="px-4 py-2 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-300 font-semibold"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-stone-950 font-bold hover:from-amber-400 hover:to-amber-500 shadow-lg"
                >
                  Guardar Material
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

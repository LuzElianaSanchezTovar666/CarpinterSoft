import React, { useState } from 'react';
import { Order, FurnitureCategory, Priority } from '../types';
import { calculateFurnitureQuote } from '../utils/calculator';
import { 
  X, 
  PlusCircle, 
  Hammer, 
  Calendar, 
  User, 
  Phone, 
  MapPin, 
  Ruler, 
  DollarSign, 
  Tag 
} from 'lucide-react';

interface NewOrderModalProps {
  isOpen: boolean;
  onClose: () => void;
  onAddOrder: (order: Order) => void;
}

export const NewOrderModal: React.FC<NewOrderModalProps> = ({
  isOpen,
  onClose,
  onAddOrder,
}) => {
  if (!isOpen) return null;

  const [projectName, setProjectName] = useState('');
  const [customerName, setCustomerName] = useState('');
  const [customerPhone, setCustomerPhone] = useState('');
  const [customerAddress, setCustomerAddress] = useState('');
  const [category, setCategory] = useState<FurnitureCategory>('Cocina');
  const [woodType, setWoodType] = useState('Melamina Pelíkano Roble Sinatra 18mm');
  const [finishType, setFinishType] = useState('Canto PVC 2mm en frentes');
  const [height, setHeight] = useState(210);
  const [width, setWidth] = useState(180);
  const [depth, setDepth] = useState(60);
  const [priority, setPriority] = useState<Priority>('media');
  const [deliveryDate, setDeliveryDate] = useState(() => {
    const d = new Date();
    d.setDate(d.getDate() + 15);
    return d.toISOString().split('T')[0];
  });
  const [salePrice, setSalePrice] = useState(3800000);
  const [depositPaid, setDepositPaid] = useState(1900000);
  const [assignedCarpenter, setAssignedCarpenter] = useState('Maestro Carlos Echeverry');
  const [notes, setNotes] = useState('');

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!projectName || !customerName) return;

    // Generar despiece paramétrico automático para la orden
    const quote = calculateFurnitureQuote({
      furnitureType: category,
      projectName,
      woodType,
      finishType,
      height,
      width,
      depth,
      doorsCount: category === 'Mesa' ? 0 : 2,
      drawersCount: category === 'Mesa' ? 0 : 2,
      shelvesCount: 2,
      includeHardware: true,
      edgeBandType: 'PVC 2mm en 4 cantos',
      profitMarginPercent: 40,
    });

    const newOrder: Order = {
      id: `ord-${Date.now()}`,
      code: `CP-2026-${Math.floor(100 + Math.random() * 900)}`,
      projectName,
      customerName,
      customerPhone: customerPhone || '+57 300 123 4567',
      customerAddress: customerAddress || 'Taller Central',
      furnitureCategory: category,
      woodType,
      finishType,
      dimensions: { height, width, depth },
      status: 'cotizacion',
      priority,
      createdAt: new Date().toISOString().split('T')[0],
      deliveryDate,
      totalCost: quote.subtotalCost,
      salePrice: Number(salePrice) || quote.suggestedSalePrice,
      depositPaid: Number(depositPaid) || 0,
      assignedCarpenter,
      pieces: quote.pieces,
      materials: [
        {
          id: 'mat-1',
          name: `Tableros ${woodType} (2.44x1.83m)`,
          quantity: quote.sheetsNeeded,
          unit: 'plancha',
          unitPrice: Math.round(quote.materialCost / quote.sheetsNeeded),
          subtotal: quote.materialCost,
        },
        {
          id: 'mat-2',
          name: `Tapacanto PVC 2mm Rígido`,
          quantity: quote.edgeBandMeters,
          unit: 'metro',
          unitPrice: 1800,
          subtotal: Math.round(quote.edgeBandMeters * 1800),
        },
      ],
      notes,
    };

    onAddOrder(newOrder);
    onClose();
  };

  return (
    <div className="fixed inset-0 z-50 bg-stone-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
      <div className="bg-stone-900 border border-stone-800 rounded-3xl max-w-2xl w-full max-h-[92vh] flex flex-col shadow-2xl animate-scaleUp">
        
        {/* Cabecera */}
        <div className="p-5 border-b border-stone-800 flex items-center justify-between bg-stone-950/60 rounded-t-3xl">
          <div className="flex items-center gap-2.5">
            <div className="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
              <PlusCircle className="w-4 h-4" />
            </div>
            <div>
              <h3 className="text-base font-bold text-white font-['Outfit']">
                Registrar Nuevo Proyecto de Taller
              </h3>
              <p className="text-xs text-stone-400">Ingresa las especificaciones del mueble y cliente</p>
            </div>
          </div>

          <button
            onClick={onClose}
            className="p-1.5 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-400 hover:text-white transition-colors"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Formulario */}
        <form onSubmit={handleSubmit} className="p-6 space-y-4 overflow-y-auto flex-1 text-xs">
          
          {/* Nombre Proyecto y Categoría */}
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div className="sm:col-span-2">
              <label className="block text-stone-300 font-semibold mb-1">Nombre del Mueble / Proyecto *</label>
              <input
                type="text"
                required
                placeholder="Ej: Clóset Principal con Zapatero"
                value={projectName}
                onChange={(e) => setProjectName(e.target.value)}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white focus:outline-none focus:border-amber-500"
              />
            </div>
            <div>
              <label className="block text-stone-300 font-semibold mb-1">Tipo de Mueble</label>
              <select
                value={category}
                onChange={(e) => setCategory(e.target.value as FurnitureCategory)}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-stone-200 focus:outline-none focus:border-amber-500 cursor-pointer"
              >
                <option value="Cocina">Cocina Integral</option>
                <option value="Closet">Clóset / Vestidor</option>
                <option value="Mesa">Mesa / Comedor</option>
                <option value="Puerta">Puerta Principal</option>
                <option value="Bano">Mueble de Baño</option>
                <option value="Oficina">Oficina / Escritorio</option>
                <option value="Especial">Mueble Especial</option>
              </select>
            </div>
          </div>

          {/* Datos del Cliente */}
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label className="block text-stone-300 font-semibold mb-1">Cliente *</label>
              <input
                type="text"
                required
                placeholder="Ej: Arq. Carlos López"
                value={customerName}
                onChange={(e) => setCustomerName(e.target.value)}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white focus:outline-none focus:border-amber-500"
              />
            </div>
            <div>
              <label className="block text-stone-300 font-semibold mb-1">Teléfono / WhatsApp</label>
              <input
                type="text"
                placeholder="+57 310 000 0000"
                value={customerPhone}
                onChange={(e) => setCustomerPhone(e.target.value)}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white focus:outline-none focus:border-amber-500"
              />
            </div>
            <div>
              <label className="block text-stone-300 font-semibold mb-1">Dirección Entrega</label>
              <input
                type="text"
                placeholder="Calle 100 #15-20"
                value={customerAddress}
                onChange={(e) => setCustomerAddress(e.target.value)}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white focus:outline-none focus:border-amber-500"
              />
            </div>
          </div>

          {/* Maderas, Acabados y Dimensiones */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label className="block text-stone-300 font-semibold mb-1">Madera / Tablero</label>
              <input
                type="text"
                placeholder="Ej: Melamina Roble Sinatra 18mm"
                value={woodType}
                onChange={(e) => setWoodType(e.target.value)}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white focus:outline-none focus:border-amber-500"
              />
            </div>
            <div>
              <label className="block text-stone-300 font-semibold mb-1">Acabado / Cantos</label>
              <input
                type="text"
                placeholder="Ej: Canto PVC 2mm / Poliuretano mate"
                value={finishType}
                onChange={(e) => setFinishType(e.target.value)}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white focus:outline-none focus:border-amber-500"
              />
            </div>
          </div>

          {/* Dimensiones (cm) */}
          <div className="grid grid-cols-3 gap-3">
            <div>
              <label className="block text-stone-300 font-semibold mb-1 text-center">Alto (cm)</label>
              <input
                type="number"
                min="10"
                value={height}
                onChange={(e) => setHeight(Number(e.target.value))}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white text-center font-mono focus:outline-none focus:border-amber-500"
              />
            </div>
            <div>
              <label className="block text-stone-300 font-semibold mb-1 text-center">Ancho (cm)</label>
              <input
                type="number"
                min="10"
                value={width}
                onChange={(e) => setWidth(Number(e.target.value))}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white text-center font-mono focus:outline-none focus:border-amber-500"
              />
            </div>
            <div>
              <label className="block text-stone-300 font-semibold mb-1 text-center">Fondo (cm)</label>
              <input
                type="number"
                min="10"
                value={depth}
                onChange={(e) => setDepth(Number(e.target.value))}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white text-center font-mono focus:outline-none focus:border-amber-500"
              />
            </div>
          </div>

          {/* Finanzas: Precio y Anticipo */}
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-stone-300 font-semibold mb-1">Precio Venta (COP)</label>
              <input
                type="number"
                min="0"
                value={salePrice}
                onChange={(e) => setSalePrice(Number(e.target.value))}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white font-mono focus:outline-none focus:border-amber-500"
              />
            </div>
            <div>
              <label className="block text-stone-300 font-semibold mb-1">Anticipo Recibido (COP)</label>
              <input
                type="number"
                min="0"
                value={depositPaid}
                onChange={(e) => setDepositPaid(Number(e.target.value))}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white font-mono focus:outline-none focus:border-amber-500"
              />
            </div>
          </div>

          {/* Fecha Entrega, Operario y Prioridad */}
          <div className="grid grid-cols-3 gap-3">
            <div>
              <label className="block text-stone-300 font-semibold mb-1">Fecha Entrega</label>
              <input
                type="date"
                value={deliveryDate}
                onChange={(e) => setDeliveryDate(e.target.value)}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white focus:outline-none focus:border-amber-500"
              />
            </div>
            <div>
              <label className="block text-stone-300 font-semibold mb-1">Carpintero</label>
              <input
                type="text"
                placeholder="Maestro a cargo"
                value={assignedCarpenter}
                onChange={(e) => setAssignedCarpenter(e.target.value)}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white focus:outline-none focus:border-amber-500"
              />
            </div>
            <div>
              <label className="block text-stone-300 font-semibold mb-1">Prioridad</label>
              <select
                value={priority}
                onChange={(e) => setPriority(e.target.value as Priority)}
                className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-stone-200 focus:outline-none focus:border-amber-500 cursor-pointer"
              >
                <option value="baja">Baja</option>
                <option value="media">Media</option>
                <option value="alta">Alta</option>
                <option value="urgente">Urgente</option>
              </select>
            </div>
          </div>

          {/* Notas */}
          <div>
            <label className="block text-stone-300 font-semibold mb-1">Observaciones / Herrajes Especiales</label>
            <textarea
              rows={2}
              placeholder="Ej: Requiere bisagras de 110 grados y correderas pesadas..."
              value={notes}
              onChange={(e) => setNotes(e.target.value)}
              className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white"
            />
          </div>

          <div className="pt-4 border-t border-stone-800 flex items-center justify-end gap-3">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-300 font-semibold"
            >
              Cancelar
            </button>
            <button
              type="submit"
              className="px-6 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-stone-950 font-bold shadow-lg shadow-amber-950/40"
            >
              Crear Orden de Taller
            </button>
          </div>

        </form>

      </div>
    </div>
  );
};

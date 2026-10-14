import React, { useState } from 'react';
import { Customer } from '../types';
import { formatCOP } from '../utils/calculator';
import { 
  Users, 
  Search, 
  Plus, 
  Phone, 
  Mail, 
  MapPin, 
  Calendar, 
  MessageSquare, 
  PlusCircle, 
  ShoppingBag, 
  DollarSign 
} from 'lucide-react';

interface CustomersViewProps {
  customers: Customer[];
  onAddCustomer: (customer: Customer) => void;
}

export const CustomersView: React.FC<CustomersViewProps> = ({
  customers,
  onAddCustomer,
}) => {
  const [searchTerm, setSearchTerm] = useState('');
  const [isModalOpen, setIsModalOpen] = useState(false);

  const [newCustomer, setNewCustomer] = useState<Partial<Customer>>({
    name: '',
    phone: '',
    email: '',
    address: '',
    city: 'Bogotá, D.C.',
    notes: '',
  });

  const filteredCustomers = customers.filter(
    (c) =>
      c.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      c.phone.toLowerCase().includes(searchTerm.toLowerCase()) ||
      c.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
      c.city.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const handleCreateCustomer = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newCustomer.name || !newCustomer.phone) return;

    const customer: Customer = {
      id: `cus-${Date.now()}`,
      name: newCustomer.name,
      phone: newCustomer.phone,
      email: newCustomer.email || '',
      address: newCustomer.address || '',
      city: newCustomer.city || 'Bogotá, D.C.',
      notes: newCustomer.notes || '',
      totalOrders: 0,
      totalSpent: 0,
      registrationDate: new Date().toISOString().split('T')[0],
    };

    onAddCustomer(customer);
    setIsModalOpen(false);
    setNewCustomer({
      name: '',
      phone: '',
      email: '',
      address: '',
      city: 'Bogotá, D.C.',
      notes: '',
    });
  };

  const handleOpenWhatsApp = (phone: string, name: string) => {
    const cleanPhone = phone.replace(/[^0-9]/g, '');
    const message = encodeURIComponent(
      `Hola ${name}, te saludamos de CarpinterSoft Taller de Ebanistería. Queremos consultar los detalles de tu proyecto en madera.`
    );
    window.open(`https://wa.me/${cleanPhone}?text=${message}`, '_blank');
  };

  return (
    <div className="space-y-6 animate-fadeIn">
      {/* Banner */}
      <div className="bg-stone-900/80 border border-stone-800 p-6 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 text-xs font-semibold border border-amber-500/20 mb-2">
            <Users className="w-3.5 h-3.5" />
            Directorio & CRM del Taller
          </div>
          <h2 className="text-xl sm:text-2xl font-black text-white font-['Outfit']">
            Clientes & Propietarios
          </h2>
          <p className="text-stone-400 text-xs sm:text-sm mt-1">
            Historial de compras, direcciones de instalación y comunicación directa por WhatsApp con cada cliente del taller.
          </p>
        </div>

        <button
          onClick={() => setIsModalOpen(true)}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-stone-950 font-bold text-xs sm:text-sm transition-all shadow-md shadow-amber-950/40 cursor-pointer active:scale-95"
        >
          <Plus className="w-4 h-4" />
          <span>Nuevo Cliente</span>
        </button>
      </div>

      {/* Buscador */}
      <div className="bg-stone-900/80 border border-stone-800 p-4 rounded-2xl">
        <div className="relative max-w-md">
          <Search className="w-4 h-4 text-stone-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
          <input
            type="text"
            placeholder="Buscar por nombre, teléfono, correo o ciudad..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full pl-10 pr-4 py-2 bg-stone-950/70 border border-stone-800 rounded-xl text-xs sm:text-sm text-stone-200 placeholder-stone-500 focus:outline-none focus:border-amber-500"
          />
        </div>
      </div>

      {/* Lista de Clientes */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {filteredCustomers.map((cus) => (
          <div
            key={cus.id}
            className="bg-stone-900/80 border border-stone-800 hover:border-amber-500/40 rounded-2xl p-5 flex flex-col justify-between transition-all hover:shadow-xl"
          >
            <div className="space-y-3">
              <div className="flex items-center justify-between">
                <h3 className="text-base font-bold text-white font-['Outfit']">{cus.name}</h3>
                <span className="text-[10px] font-semibold text-stone-400 bg-stone-950 px-2.5 py-1 rounded-full border border-stone-800">
                  {cus.totalOrders} pedidos
                </span>
              </div>

              {/* Contact Data */}
              <div className="space-y-1.5 text-xs text-stone-300">
                <div className="flex items-center gap-2 text-stone-400">
                  <Phone className="w-3.5 h-3.5 text-amber-400 flex-shrink-0" />
                  <span>{cus.phone}</span>
                </div>
                {cus.email && (
                  <div className="flex items-center gap-2 text-stone-400 truncate">
                    <Mail className="w-3.5 h-3.5 text-stone-500 flex-shrink-0" />
                    <span className="truncate">{cus.email}</span>
                  </div>
                )}
                <div className="flex items-center gap-2 text-stone-400">
                  <MapPin className="w-3.5 h-3.5 text-stone-500 flex-shrink-0" />
                  <span>{cus.address}, {cus.city}</span>
                </div>
              </div>

              {cus.notes && (
                <div className="p-2.5 rounded-xl bg-stone-950/70 border border-stone-800/80 text-[11px] text-stone-400 italic">
                  "{cus.notes}"
                </div>
              )}
            </div>

            {/* Total invertido & WhatsApp */}
            <div className="mt-4 pt-3 border-t border-stone-800 flex items-center justify-between">
              <div>
                <span className="text-[10px] text-stone-500 block uppercase font-semibold">Total Facturado</span>
                <span className="text-xs font-bold text-emerald-400 font-mono">{formatCOP(cus.totalSpent)}</span>
              </div>

              <button
                onClick={() => handleOpenWhatsApp(cus.phone, cus.name)}
                className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 text-xs font-bold transition-all cursor-pointer"
                title="Contactar por WhatsApp"
              >
                <MessageSquare className="w-3.5 h-3.5" />
                <span>WhatsApp</span>
              </button>
            </div>

          </div>
        ))}
      </div>

      {/* Modal Nuevo Cliente */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 bg-stone-950/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-stone-900 border border-stone-800 rounded-2xl max-w-md w-full p-6 shadow-2xl animate-scaleUp">
            <h3 className="text-lg font-bold text-white font-['Outfit'] mb-4 flex items-center gap-2">
              <PlusCircle className="w-5 h-5 text-amber-400" />
              Nuevo Cliente
            </h3>

            <form onSubmit={handleCreateCustomer} className="space-y-3.5 text-xs">
              <div>
                <label className="block text-stone-300 font-semibold mb-1">Nombre Completo *</label>
                <input
                  type="text"
                  required
                  placeholder="Ej: Arq. Fernando Torres"
                  value={newCustomer.name}
                  onChange={(e) => setNewCustomer({ ...newCustomer, name: e.target.value })}
                  className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white focus:outline-none focus:border-amber-500"
                />
              </div>

              <div>
                <label className="block text-stone-300 font-semibold mb-1">Teléfono / WhatsApp *</label>
                <input
                  type="text"
                  required
                  placeholder="Ej: +57 312 345 6789"
                  value={newCustomer.phone}
                  onChange={(e) => setNewCustomer({ ...newCustomer, phone: e.target.value })}
                  className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white focus:outline-none focus:border-amber-500"
                />
              </div>

              <div>
                <label className="block text-stone-300 font-semibold mb-1">Correo Electrónico</label>
                <input
                  type="email"
                  placeholder="Ej: cliente@correo.com"
                  value={newCustomer.email}
                  onChange={(e) => setNewCustomer({ ...newCustomer, email: e.target.value })}
                  className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white focus:outline-none focus:border-amber-500"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-stone-300 font-semibold mb-1">Dirección Entrega</label>
                  <input
                    type="text"
                    placeholder="Ej: Cra 7 #45-12"
                    value={newCustomer.address}
                    onChange={(e) => setNewCustomer({ ...newCustomer, address: e.target.value })}
                    className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white"
                  />
                </div>
                <div>
                  <label className="block text-stone-300 font-semibold mb-1">Ciudad / Barrio</label>
                  <input
                    type="text"
                    placeholder="Ej: Bogotá, D.C."
                    value={newCustomer.city}
                    onChange={(e) => setNewCustomer({ ...newCustomer, city: e.target.value })}
                    className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white"
                  />
                </div>
              </div>

              <div>
                <label className="block text-stone-300 font-semibold mb-1">Notas / Preferencias</label>
                <textarea
                  rows={2}
                  placeholder="Ej: Prefiere acabados en roble mate..."
                  value={newCustomer.notes}
                  onChange={(e) => setNewCustomer({ ...newCustomer, notes: e.target.value })}
                  className="w-full px-3 py-2 bg-stone-950 border border-stone-800 rounded-xl text-white"
                />
              </div>

              <div className="flex items-center justify-end gap-3 pt-3 border-t border-stone-800">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="px-4 py-2 rounded-xl bg-stone-800 text-stone-300 font-semibold"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-stone-950 font-bold"
                >
                  Guardar Cliente
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

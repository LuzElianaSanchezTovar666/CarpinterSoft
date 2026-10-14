import { useState, useEffect } from 'react';
import { 
  Order, 
  MaterialInventoryItem, 
  FurnitureCatalogItem, 
  Customer, 
  OrderStatus, 
  QuickQuoteParams 
} from './types';
import { 
  INITIAL_ORDERS, 
  INITIAL_INVENTORY, 
  INITIAL_CATALOG, 
  INITIAL_CUSTOMERS 
} from './data/mockData';
import { Navbar, TabType } from './components/Navbar';
import { DashboardView } from './components/DashboardView';
import { OrdersView } from './components/OrdersView';
import { CutCalculatorView } from './components/CutCalculatorView';
import { InventoryView } from './components/InventoryView';
import { CatalogView } from './components/CatalogView';
import { CustomersView } from './components/CustomersView';
import { OrderDetailModal } from './components/OrderDetailModal';
import { NewOrderModal } from './components/NewOrderModal';
import { AIAssistantModal } from './components/AIAssistantModal';
import { PrintWorkOrderModal } from './components/PrintWorkOrderModal';
import { Check, Info, Hammer, Sparkles } from 'lucide-react';

export default function App() {
  // Estado con persistencia en localStorage para pedidos
  const [orders, setOrders] = useState<Order[]>(() => {
    try {
      const saved = localStorage.getItem('carpintersoft_orders');
      return saved ? JSON.parse(saved) : INITIAL_ORDERS;
    } catch {
      return INITIAL_ORDERS;
    }
  });

  // Estado con persistencia para inventario
  const [inventory, setInventory] = useState<MaterialInventoryItem[]>(() => {
    try {
      const saved = localStorage.getItem('carpintersoft_inventory');
      return saved ? JSON.parse(saved) : INITIAL_INVENTORY;
    } catch {
      return INITIAL_INVENTORY;
    }
  });

  // Estado con persistencia para clientes
  const [customers, setCustomers] = useState<Customer[]>(() => {
    try {
      const saved = localStorage.getItem('carpintersoft_customers');
      return saved ? JSON.parse(saved) : INITIAL_CUSTOMERS;
    } catch {
      return INITIAL_CUSTOMERS;
    }
  });

  const [catalog] = useState<FurnitureCatalogItem[]>(INITIAL_CATALOG);
  const [activeTab, setActiveTab] = useState<TabType>('dashboard');

  // Modales
  const [selectedOrder, setSelectedOrder] = useState<Order | null>(null);
  const [orderToPrint, setOrderToPrint] = useState<Order | null>(null);
  const [isNewOrderModalOpen, setIsNewOrderModalOpen] = useState(false);
  const [isAIModalOpen, setIsAIModalOpen] = useState(false);

  // Precarga para la calculadora desde el catálogo
  const [calculatorPreload, setCalculatorPreload] = useState<Partial<QuickQuoteParams> | undefined>(undefined);

  // Toast feedback
  const [toastMessage, setToastMessage] = useState<{ text: string; type?: 'success' | 'info' } | null>(null);

  const showToast = (text: string, type: 'success' | 'info' = 'success') => {
    setToastMessage({ text, type });
    setTimeout(() => setToastMessage(null), 3500);
  };

  // Guardar en localStorage
  useEffect(() => {
    try {
      localStorage.setItem('carpintersoft_orders', JSON.stringify(orders));
    } catch (e) {
      console.warn('LocalStorage error:', e);
    }
  }, [orders]);

  useEffect(() => {
    try {
      localStorage.setItem('carpintersoft_inventory', JSON.stringify(inventory));
    } catch (e) {
      console.warn('LocalStorage error:', e);
    }
  }, [inventory]);

  useEffect(() => {
    try {
      localStorage.setItem('carpintersoft_customers', JSON.stringify(customers));
    } catch (e) {
      console.warn('LocalStorage error:', e);
    }
  }, [customers]);

  // Manejadores de Pedidos
  const handleAddOrder = (newOrder: Order) => {
    setOrders((prev) => [newOrder, ...prev]);
    showToast(`Proyecto "${newOrder.projectName}" registrado con éxito.`);
  };

  const handleUpdateOrderStatus = (orderId: string, newStatus: OrderStatus) => {
    setOrders((prev) =>
      prev.map((o) => (o.id === orderId ? { ...o, status: newStatus } : o))
    );
    if (selectedOrder && selectedOrder.id === orderId) {
      setSelectedOrder((prev) => (prev ? { ...prev, status: newStatus } : null));
    }
    showToast(`Estado actualizado a: ${newStatus.toUpperCase()}`);
  };

  const handleAddPayment = (orderId: string, paymentAmount: number) => {
    setOrders((prev) =>
      prev.map((o) => {
        if (o.id === orderId) {
          const updatedDeposit = Math.min(o.salePrice, o.depositPaid + paymentAmount);
          return { ...o, depositPaid: updatedDeposit };
        }
        return o;
      })
    );
    if (selectedOrder && selectedOrder.id === orderId) {
      setSelectedOrder((prev) =>
        prev
          ? {
              ...prev,
              depositPaid: Math.min(prev.salePrice, prev.depositPaid + paymentAmount),
            }
          : null
      );
    }
    showToast(`Abono registrado correctamente.`);
  };

  // Convertir cotización desde la calculadora a un pedido formal
  const handleConvertQuoteToOrder = (quoteData: Partial<Order>) => {
    const order: Order = {
      id: `ord-${Date.now()}`,
      code: `CP-2026-${Math.floor(100 + Math.random() * 900)}`,
      projectName: quoteData.projectName || 'Mueble a Medida',
      customerName: quoteData.customerName || 'Cliente Particular',
      customerPhone: quoteData.customerPhone || '+57 300 000 0000',
      customerAddress: quoteData.customerAddress || 'Por coordinar',
      furnitureCategory: quoteData.furnitureCategory || 'Cocina',
      woodType: quoteData.woodType || 'Melamina 18mm',
      finishType: quoteData.finishType || 'Canto PVC',
      dimensions: quoteData.dimensions || { height: 90, width: 120, depth: 60 },
      status: 'cotizacion',
      priority: 'media',
      createdAt: new Date().toISOString().split('T')[0],
      deliveryDate: new Date(Date.now() + 15 * 86400000).toISOString().split('T')[0],
      totalCost: quoteData.totalCost || 1200000,
      salePrice: quoteData.salePrice || 2400000,
      depositPaid: 0,
      pieces: quoteData.pieces || [],
      materials: quoteData.materials || [],
      notes: quoteData.notes || 'Cotización generada con despiece.',
    };

    setOrders((prev) => [order, ...prev]);
    setActiveTab('orders');
    setSelectedOrder(order);
    showToast('Cotización convertida en Pedido de Taller.');
  };

  // Manejadores de Inventario
  const handleUpdateStock = (itemId: string, delta: number) => {
    setInventory((prev) =>
      prev.map((item) => {
        if (item.id === itemId) {
          const nextStock = Math.max(0, item.stock + delta);
          return { ...item, stock: nextStock };
        }
        return item;
      })
    );
    showToast(delta > 0 ? 'Entrada de stock registrada' : 'Consumo de taller descontado', 'info');
  };

  const handleAddMaterial = (material: MaterialInventoryItem) => {
    setInventory((prev) => [material, ...prev]);
    showToast(`Material "${material.name}" agregado al almacén.`);
  };

  // Manejador de Clientes
  const handleAddCustomer = (customer: Customer) => {
    setCustomers((prev) => [customer, ...prev]);
    showToast(`Cliente "${customer.name}" registrado.`);
  };

  // Cotizar ítem del Catálogo
  const handleQuoteCatalogItem = (item: FurnitureCatalogItem) => {
    setCalculatorPreload({
      furnitureType: item.category,
      projectName: item.name,
      woodType: item.baseMaterial,
    });
    setActiveTab('calculator');
    showToast(`Modelo "${item.name}" cargado en la calculadora.`);
  };

  const lowStockCount = inventory.filter((item) => item.stock <= item.minStock).length;
  const activeOrdersCount = orders.filter((o) => o.status !== 'entregado').length;

  return (
    <div className="min-h-screen bg-stone-950 text-stone-100 flex flex-col font-sans selection:bg-amber-600 selection:text-white">
      
      {/* Toast Notification */}
      {toastMessage && (
        <div className="fixed bottom-6 right-6 z-50 animate-slideUp">
          <div className="flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-stone-900 border border-amber-500/40 text-stone-100 text-xs font-semibold shadow-2xl backdrop-blur-md">
            <div className="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center">
              <Check className="w-3.5 h-3.5" />
            </div>
            <span>{toastMessage.text}</span>
          </div>
        </div>
      )}

      {/* Navegación Superior */}
      <Navbar
        activeTab={activeTab}
        setActiveTab={setActiveTab}
        openNewOrderModal={() => setIsNewOrderModalOpen(true)}
        openAIModal={() => setIsAIModalOpen(true)}
        lowStockCount={lowStockCount}
        activeOrdersCount={activeOrdersCount}
      />

      {/* Contenedor Principal de Vistas */}
      <main className="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {activeTab === 'dashboard' && (
          <DashboardView
            orders={orders}
            inventory={inventory}
            onSelectOrder={(order) => setSelectedOrder(order)}
            onNavigateTab={(tab) => setActiveTab(tab)}
            onOpenNewOrder={() => setIsNewOrderModalOpen(true)}
          />
        )}

        {activeTab === 'orders' && (
          <OrdersView
            orders={orders}
            onSelectOrder={(order) => setSelectedOrder(order)}
            onOpenNewOrder={() => setIsNewOrderModalOpen(true)}
            onUpdateOrderStatus={handleUpdateOrderStatus}
          />
        )}

        {activeTab === 'calculator' && (
          <CutCalculatorView
            onConvertQuoteToOrder={handleConvertQuoteToOrder}
            onPrintQuote={(quote) => {
              // Convert to pseudo order to print
              setOrderToPrint(quote);
            }}
            initialParams={calculatorPreload}
          />
        )}

        {activeTab === 'inventory' && (
          <InventoryView
            inventory={inventory}
            onUpdateStock={handleUpdateStock}
            onAddMaterial={handleAddMaterial}
          />
        )}

        {activeTab === 'catalog' && (
          <CatalogView
            catalog={catalog}
            onQuoteCatalogItem={handleQuoteCatalogItem}
          />
        )}

        {activeTab === 'customers' && (
          <CustomersView
            customers={customers}
            onAddCustomer={handleAddCustomer}
          />
        )}
      </main>

      {/* Pie de Página */}
      <footer className="no-print border-t border-stone-800/80 bg-stone-950 py-6 mt-12 text-xs text-stone-500 text-center">
        <div className="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div className="flex items-center gap-2">
            <div className="w-5 h-5 rounded-md bg-amber-600/30 text-amber-400 flex items-center justify-center">
              <Hammer className="w-3 h-3" />
            </div>
            <span className="font-bold text-stone-400 font-['Outfit']">CarpinterSoft 2026</span>
            <span>• Sistema Integral de Gestión para Carpintería y Ebanistería</span>
          </div>
          <div className="text-[11px] text-stone-500">
            Desarrollado para optimización de corte, control de banco de trabajo e inventario
          </div>
        </div>
      </footer>

      {/* Modales */}
      {selectedOrder && (
        <OrderDetailModal
          order={selectedOrder}
          onClose={() => setSelectedOrder(null)}
          onUpdateStatus={handleUpdateOrderStatus}
          onAddPayment={handleAddPayment}
          onPrintOrder={(order) => setOrderToPrint(order)}
        />
      )}

      {isNewOrderModalOpen && (
        <NewOrderModal
          isOpen={isNewOrderModalOpen}
          onClose={() => setIsNewOrderModalOpen(false)}
          onAddOrder={handleAddOrder}
        />
      )}

      {isAIModalOpen && (
        <AIAssistantModal
          isOpen={isAIModalOpen}
          onClose={() => setIsAIModalOpen(false)}
        />
      )}

      {orderToPrint && (
        <PrintWorkOrderModal
          order={orderToPrint}
          onClose={() => setOrderToPrint(null)}
        />
      )}

    </div>
  );
}

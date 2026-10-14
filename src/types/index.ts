export type OrderStatus =
  | 'cotizacion'
  | 'diseno'
  | 'corte'
  | 'ensamble'
  | 'acabado'
  | 'listo'
  | 'entregado';

export type Priority = 'baja' | 'media' | 'alta' | 'urgente';

export type FurnitureCategory =
  | 'Cocina'
  | 'Closet'
  | 'Mesa'
  | 'Puerta'
  | 'Bano'
  | 'Oficina'
  | 'Especial';

export interface CutPiece {
  id: string;
  partName: string;
  quantity: number;
  length: number; // en cm
  width: number;  // en cm
  thickness: number; // en mm
  material: string;
  edgeBanding: string; // e.g. "Canto PVC 2mm en 4 lados"
  grainDirection?: 'largo' | 'ancho' | 'indistinto';
}

export interface OrderMaterial {
  id: string;
  name: string;
  quantity: number;
  unit: string;
  unitPrice: number;
  subtotal: number;
}

export interface Order {
  id: string;
  code: string; // e.g., "CP-2026-042"
  customerName: string;
  customerPhone: string;
  customerAddress: string;
  projectName: string;
  furnitureCategory: FurnitureCategory;
  woodType: string;
  finishType: string;
  dimensions: {
    height: number; // cm
    width: number;  // cm
    depth: number;  // cm
  };
  status: OrderStatus;
  priority: Priority;
  createdAt: string;
  deliveryDate: string;
  totalCost: number;
  salePrice: number;
  depositPaid: number;
  pieces: CutPiece[];
  materials: OrderMaterial[];
  notes?: string;
  assignedCarpenter?: string;
}

export type MaterialCategory = 'madera' | 'tablero' | 'herraje' | 'quimico';

export interface MaterialInventoryItem {
  id: string;
  code: string;
  name: string;
  category: MaterialCategory;
  unit: 'pie_tablar' | 'plancha' | 'unidad' | 'par' | 'litro' | 'metro' | 'caja';
  stock: number;
  minStock: number;
  unitCost: number;
  supplier: string;
  location: string;
  description?: string;
}

export interface FurnitureCatalogItem {
  id: string;
  name: string;
  category: FurnitureCategory;
  description: string;
  dimensions: string;
  suggestedPrice: number;
  baseMaterial: string;
  estimatedDays: number;
  imageUrl: string;
  features: string[];
}

export interface Customer {
  id: string;
  name: string;
  phone: string;
  email: string;
  address: string;
  city: string;
  notes: string;
  totalOrders: number;
  totalSpent: number;
  registrationDate: string;
}

export interface QuickQuoteParams {
  furnitureType: FurnitureCategory;
  projectName: string;
  woodType: string;
  finishType: string;
  height: number;
  width: number;
  depth: number;
  doorsCount: number;
  drawersCount: number;
  shelvesCount: number;
  includeHardware: boolean;
  edgeBandType: string;
  profitMarginPercent: number;
}

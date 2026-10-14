-- Base de Datos para CarpinterSoft: Sistema de Gestión de Carpintería y Ebanistería
-- Compatible con MySQL / MariaDB (XAMPP)

CREATE DATABASE IF NOT EXISTS `carpintersoft` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `carpintersoft`;

-- 1. Tabla de Usuarios / Operarios de Taller
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `rol` ENUM('administrador', 'maestro_carpintero', 'asistente') DEFAULT 'maestro_carpintero',
  `telefono` VARCHAR(20),
  `activo` TINYINT(1) DEFAULT 1,
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabla de Clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(120) NOT NULL,
  `telefono` VARCHAR(30) NOT NULL,
  `email` VARCHAR(120),
  `direccion` VARCHAR(200),
  `ciudad` VARCHAR(80) DEFAULT 'Bogotá, D.C.',
  `notas` TEXT,
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Tabla de Inventario de Materiales e Insumos
CREATE TABLE IF NOT EXISTS `inventario` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(30) NOT NULL UNIQUE,
  `nombre` VARCHAR(150) NOT NULL,
  `categoria` ENUM('madera', 'tablero', 'herraje', 'quimico') NOT NULL,
  `unidad` VARCHAR(30) NOT NULL, -- pie_tablar, plancha, par, unidad, litro, metro
  `stock` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `stock_minimo` DECIMAL(10,2) NOT NULL DEFAULT 10,
  `costo_unitario` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `proveedor` VARCHAR(150),
  `ubicacion` VARCHAR(100),
  `descripcion` TEXT,
  `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. Tabla de Pedidos y Proyectos
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(30) NOT NULL UNIQUE,
  `cliente_id` INT,
  `nombre_proyecto` VARCHAR(150) NOT NULL,
  `categoria_mueble` ENUM('Cocina', 'Closet', 'Mesa', 'Puerta', 'Bano', 'Oficina', 'Especial') NOT NULL,
  `tipo_madera` VARCHAR(150) NOT NULL,
  `tipo_acabado` VARCHAR(150) NOT NULL,
  `alto_cm` DECIMAL(8,2) NOT NULL,
  `ancho_cm` DECIMAL(8,2) NOT NULL,
  `fondo_cm` DECIMAL(8,2) NOT NULL,
  `estado` ENUM('cotizacion', 'diseno', 'corte', 'ensamble', 'acabado', 'listo', 'entregado') DEFAULT 'cotizacion',
  `prioridad` ENUM('baja', 'media', 'alta', 'urgente') DEFAULT 'media',
  `costo_total` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `precio_venta` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `anticipo_pagado` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `fecha_entrega` DATE NOT NULL,
  `carpintero_asignado` VARCHAR(100) DEFAULT 'Maestro Carlos Echeverry',
  `notas` TEXT,
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. Tabla de Despiece de Piezas para Corte
CREATE TABLE IF NOT EXISTS `piezas_despiece` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pedido_id` INT NOT NULL,
  `nombre_pieza` VARCHAR(120) NOT NULL,
  `cantidad` INT NOT NULL DEFAULT 1,
  `largo_cm` DECIMAL(8,2) NOT NULL,
  `ancho_cm` DECIMAL(8,2) NOT NULL,
  `espesor_mm` INT NOT NULL DEFAULT 18,
  `material` VARCHAR(120) NOT NULL,
  `tapacanto` VARCHAR(120) DEFAULT 'Sin canto',
  `veta` ENUM('largo', 'ancho', 'indistinto') DEFAULT 'largo',
  FOREIGN KEY (`pedido_id`) REFERENCES `pedidos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Tabla de Materiales Asignados por Orden
CREATE TABLE IF NOT EXISTS `materiales_orden` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pedido_id` INT NOT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `cantidad` DECIMAL(8,2) NOT NULL,
  `unidad` VARCHAR(30) NOT NULL,
  `precio_unitario` DECIMAL(12,2) NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (`pedido_id`) REFERENCES `pedidos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. Tabla de Abonos y Pagos
CREATE TABLE IF NOT EXISTS `abonos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pedido_id` INT NOT NULL,
  `monto` DECIMAL(12,2) NOT NULL,
  `metodo_pago` ENUM('efectivo', 'transferencia', 'consignacion', 'tarjeta') DEFAULT 'transferencia',
  `comprobante` VARCHAR(100),
  `fecha_pago` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`pedido_id`) REFERENCES `pedidos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Tabla de Catálogo de Muebles de Referencia
CREATE TABLE IF NOT EXISTS `catalogo_muebles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(150) NOT NULL,
  `categoria` ENUM('Cocina', 'Closet', 'Mesa', 'Puerta', 'Bano', 'Oficina', 'Especial') NOT NULL,
  `descripcion` TEXT,
  `dimensiones` VARCHAR(100),
  `precio_sugerido` DECIMAL(12,2) NOT NULL,
  `material_base` VARCHAR(150),
  `dias_fabricacion` INT DEFAULT 15,
  `imagen_url` VARCHAR(255),
  `caracteristicas` TEXT
) ENGINE=InnoDB;

-- DATOS INICIALES (SEMILLA)

-- Clientes
INSERT INTO `clientes` (`id`, `nombre`, `telefono`, `email`, `direccion`, `ciudad`, `notas`) VALUES
(1, 'Arq. Roberto Méndez', '+57 312 458 9210', 'roberto.mendez.arq@gmail.com', 'Calle 127 #18-45, Apto 502', 'Bogotá, D.C.', 'Arquitecto independiente. Diseña remodelaciones de apartamentos de lujo.'),
(2, 'Dra. Carolina Restrepo', '+57 301 789 6543', 'carolina.restrepo.med@udea.edu.co', 'Transversal 39 #74B-12, Laureles', 'Medellín, Antioquia', 'Exigente con los acabados en maderas macizas.'),
(3, 'Ing. Mateo Gómez & Esposa', '+57 315 674 1238', 'mateogomez.civil@constructora.com', 'Carrera 15 #85-30, Chicó Norte', 'Bogotá, D.C.', 'Remodelando casa completa.'),
(4, 'Hacienda San Jerónimo (Don Álvaro)', '+57 310 992 4115', 'administracion@sanjeronimoagro.co', 'Km 7 Vía Sopó, Casa Principal', 'Sopó, Cundinamarca', 'Cliente campestre. Mobiliario en madera maciza.'),
(5, 'Estudio de Diseño Prisma S.A.S.', '+57 318 243 8901', 'proyectos@prisma-interiorismo.co', 'Calle 93B #13-40, Of. 401', 'Bogotá, D.C.', 'Aliados corporativos para oficinas y locales.');

-- Inventario
INSERT INTO `inventario` (`codigo`, `nombre`, `categoria`, `unidad`, `stock`, `stock_minimo`, `costo_unitario`, `proveedor`, `ubicacion`, `descripcion`) VALUES
('MAD-ROB-01', 'Madera Roble Americano Estufado 2"', 'madera', 'pie_tablar', 240, 100, 24000, 'Maderas del Magdalena S.A.S.', 'Patio de Maderas - Rack A-1', 'Roble seleccionado, humedad controlada 10-12%'),
('MAD-CED-02', 'Madera Cedro Caquetá Secado Horno 3"', 'madera', 'pie_tablar', 45, 80, 28000, 'Aserrío El Caquetá', 'Patio de Maderas - Rack A-2', 'Madera aromática resistente a plagas y humedad'),
('MAD-PIN-03', 'Pino Pátula Tratado e Inmunizado 1.5"', 'madera', 'pie_tablar', 380, 150, 11500, 'Forestales Andina', 'Patio de Maderas - Rack B-1', 'Pino claro libre de nudos grandes'),
('MAD-TEC-04', 'Madera Teca de Cultivo 2"', 'madera', 'pie_tablar', 90, 60, 22000, 'Plantaciones del Caribe', 'Patio de Maderas - Rack B-2', 'Alta resistencia al agua y a la intemperie natural'),
('TAB-MEL-ROB', 'Melamina Pelíkano Roble Sinatra 18mm (2.44x1.83m)', 'tablero', 'plancha', 14, 6, 285000, 'Distribuidora Novopan / Pelíkano', 'Almacén Tableros - Bodega Vertical 1', 'Textura sincronizada poro madera, antibacteriano'),
('TAB-MEL-BLA', 'Melamina Blanco Lino 18mm (2.44x1.83m)', 'tablero', 'plancha', 8, 10, 220000, 'Distribuidora Novopan / Pelíkano', 'Almacén Tableros - Bodega Vertical 2', 'Melamina blanca mate para interiores'),
('TAB-MDF-03', 'MDF Crudo 3mm para Fondos (2.44x1.83m)', 'tablero', 'plancha', 22, 12, 55000, 'Tableros de Colombia S.A.', 'Almacén Tableros - Bodega 3', 'Fondos de gavetas y traseros'),
('HER-BIS-35', 'Bisagra Cazoleta 35mm Parche Recto Cierre Lento', 'herraje', 'par', 120, 50, 18500, 'Ferretería Industrial Ducasse', 'Gaveta Herrajes H-01', 'Pistón hidráulico integrado'),
('HER-COR-50', 'Corredera Telescópica Pesada 50cm Cierre Suave (45kg)', 'herraje', 'par', 34, 20, 42000, 'Ferretería Industrial Ducasse', 'Gaveta Herrajes H-04', 'Extracción total con freno'),
('HER-MAN-NEG', 'Manija Tirador Perfil Aluminio Negro Mate 20cm', 'herraje', 'unidad', 65, 30, 14500, 'Importaciones EuroHerrajes', 'Gaveta Herrajes H-08', 'Tirador moderno para puertas y frentes'),
('QUI-POL-GAL', 'Barniz Poliuretano Catalizado Altos Sólidos Semimate', 'quimico', 'litro', 18, 10, 45000, 'Pinturas y Lacas Pintuco Pro', 'Cabina de Pintura - Estante Químicos', 'Alta resistencia para cubiertas'),
('QUI-PEG-D3', 'Pegamento para Madera PVA D3 Titebond II (Galón)', 'quimico', 'litro', 12, 6, 38000, 'Insumos Industriales de Ensamble', 'Mesa de Ensamble Taller', 'Resistencia a humedad D3');

-- Pedidos
INSERT INTO `pedidos` (`id`, `codigo`, `cliente_id`, `nombre_proyecto`, `categoria_mueble`, `tipo_madera`, `tipo_acabado`, `alto_cm`, `ancho_cm`, `fondo_cm`, `estado`, `prioridad`, `costo_total`, `precio_venta`, `anticipo_pagado`, `fecha_entrega`, `carpintero_asignado`, `notas`) VALUES
(1, 'CP-2026-001', 1, 'Cocina Integral Moderna Isla Central', 'Cocina', 'Melamina Roble Sinatra 18mm & Canto PVC 2mm', 'Terminado Mate Texturizado Antihuellas', 215.00, 340.00, 60.00, 'corte', 'alta', 4850000, 8400000, 4500000, '2026-10-18', 'Maestro Carlos Echeverry', 'Cliente solicitó herrajes cierre lento en todas las gavetas y perfil Gola negro mate.'),
(2, 'CP-2026-002', 2, 'Juego de Comedor 8 Puestos en Roble Macizo', 'Mesa', 'Roble Americano Macizo 2 pulgadas', 'Poliuretano Altos Sólidos Semimate 3 Capas', 76.00, 220.00, 105.00, 'ensamble', 'media', 3200000, 5900000, 3000000, '2026-10-14', 'Maestro Fernando Ruiz', 'Sobre de mesa con ensambles de espiga oculta y biselado perimetral de 45°. Base tipo caballete.'),
(3, 'CP-2026-003', 3, 'Clóset Vestidor Piso a Techo con Luz LED', 'Closet', 'Melamina Macadamia & Perfiles Aluminio Negro', 'Textura poro sincrónico & Iluminación cálida 3000K', 260.00, 280.00, 65.00, 'diseno', 'alta', 3900000, 7100000, 3500000, '2026-10-25', 'Técnico Andrés Marín', 'Incluye pantalonero extraíble y joyero tapizado.'),
(4, 'CP-2026-004', 4, 'Puerta Principal Monumental en Cedro Caquetá', 'Puerta', 'Cedro Caquetá Macizo Seleccionado 3 pulgadas', 'Tinte Nogal Clásico + Laca Poliuretano con Filtro UV', 240.00, 140.00, 9.00, 'acabado', 'urgente', 2950000, 5400000, 4000000, '2026-10-10', 'Maestro Carlos Echeverry', 'Puerta pivotante de seguridad para 250kg.'),
(5, 'CP-2026-005', 5, 'Centro de Entretenimiento TV con Panel Alistonado', 'Especial', 'MDF Enchapado Roble Natural & Flotante Grafito', 'Laca Poliuretano Extramate', 220.00, 260.00, 38.00, 'listo', 'media', 2150000, 4200000, 4200000, '2026-10-09', 'Técnico Andrés Marín', 'Completamente embalado con plástico burbuja, listo para instalación.');

-- Piezas Despiece
INSERT INTO `piezas_despiece` (`pedido_id`, `nombre_pieza`, `cantidad`, `largo_cm`, `ancho_cm`, `espesor_mm`, `material`, `tapacanto`, `veta`) VALUES
(1, 'Lateral Bajo Isla Izquierdo', 2, 85.00, 58.00, 18, 'Melamina Roble 18mm', 'PVC 2mm en 4 cantos', 'largo'),
(1, 'Lateral Bajo Isla Derecho', 2, 85.00, 58.00, 18, 'Melamina Roble 18mm', 'PVC 2mm en 4 cantos', 'largo'),
(1, 'Base Inferior Módulo Gavetero', 2, 120.00, 58.00, 18, 'Melamina Roble 18mm', 'PVC 2mm frontal', 'ancho'),
(1, 'Frente Gaveta Cacerolera', 4, 89.50, 32.00, 18, 'Melamina Roble 18mm', 'PVC 2mm en 4 cantos', 'ancho'),
(1, 'Puertas Mueble Superior Batiente', 6, 70.00, 39.50, 18, 'Melamina Blanco Mate 18mm', 'PVC 1mm en 4 cantos', 'largo'),
(1, 'Fondos Mueble Trasero', 3, 85.00, 118.00, 3, 'MDF Blanco 3mm', 'Sin canto', 'indistinto');

-- Catálogo
INSERT INTO `catalogo_muebles` (`nombre`, `categoria`, `descripcion`, `dimensiones`, `precio_sugerido`, `material_base`, `dias_fabricacion`, `imagen_url`, `caracteristicas`) VALUES
('Cocina Integral Escandinava con Isla', 'Cocina', 'Diseño minimalista con cajones de extracción total, torre de hornos y barra desayunadora.', '3.40m x 2.15m x 0.60m', 8900000, 'Melamina Roble Sinatra 18mm & Frentes Blanco Mate', 18, 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=1200&q=80', 'Herrajes cierre lento|Perfil Gola negro integrado|Iluminación LED bajo mueble'),
('Mesa de Comedor Roble Nórdica 8 Puestos', 'Mesa', 'Cubierta maciza de Roble seleccionado de 2 pulgadas de espesor con base en cruz.', '2.20m x 1.05m x 0.76m', 5900000, 'Roble Americano Macizo Estufado', 14, 'https://images.unsplash.com/photo-1615066390971-03e4e1c36ddf?auto=format&fit=crop&w=1200&q=80', 'Madera estufada garantizada|Uniones con espiga y caja|Acabado poliuretano resistente'),
('Clóset Vestidor Walk-in Closet Piso a Techo', 'Closet', 'Sistema modular abierto con división para él y ella, pantaloneros telescópicos y joyero.', '2.80m x 2.60m x 0.65m', 7500000, 'Melamina Pelíkano Macadamia & Perfilería Negro', 15, 'https://images.unsplash.com/photo-1558997519-83ea9252edf8?auto=format&fit=crop&w=1200&q=80', 'Luces LED automáticas con sensor|Correderas ocultas|Tapacantos 2mm anti-golpes'),
('Puerta Principal Pivotante en Cedro Real', 'Puerta', 'Puerta monumental con estructura entablerada en Cedro Caquetá macizo y eje pivote 300kg.', '2.40m x 1.40m x 0.08m', 5600000, 'Cedro Caquetá Macizo 3 pulgadas', 20, 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1200&q=80', 'Eje pivote inox 304|Laca marina filtro UV|Manillón tubular 1.2m inox');

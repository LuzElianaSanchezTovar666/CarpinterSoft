<?php
/**
 * CarpinterSoft - Conexión a Base de Datos y Capa de Persistencia MySQL (XAMPP)
 * Soporta conexión directa a MySQL (Puertos 3307 / 3306) con respaldo JSON automático.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Database {
    private static $pdo = null;
    private static $useFallback = false;
    private static $activePort = null;
    private static $storageFile = __DIR__ . '/../data/storage.json';

    public static function getConnection() {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $host = '127.0.0.1';
        $ports = [3607, 3307, 3306]; // Soporta 3607, 3307 (XAMPP activo) y 3306
        $db   = 'carpintersoft';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        foreach ($ports as $port) {
            try {
                $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_SILENT,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_TIMEOUT            => 2,
                ];
                self::$pdo = new PDO($dsn, $user, $pass, $options);
                self::$activePort = $port;
                self::$useFallback = false;
                return self::$pdo;
            } catch (\PDOException $e) {
                continue;
            }
        }

        // Si no se pudo conectar en ningún puerto, activar fallback JSON
        self::$useFallback = true;
        return null;
    }

    public static function isUsingFallback() {
        if (self::$pdo === null) {
            self::getConnection();
        }
        return self::$useFallback;
    }

    public static function getActivePort() {
        if (self::$pdo === null) {
            self::getConnection();
        }
        return self::$activePort;
    }

    // Inicializar datos semilla en JSON si no existe
    public static function initStorage() {
        $dir = dirname(self::$storageFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (!file_exists(self::$storageFile)) {
            $initialData = [
                'pedidos' => [
                    [
                        'id' => 1,
                        'codigo' => 'CP-2026-001',
                        'cliente' => 'Arq. Roberto Méndez',
                        'telefono' => '+57 312 458 9210',
                        'direccion' => 'Calle 127 #18-45, Apto 502, Bogotá',
                        'proyecto' => 'Cocina Integral Moderna Isla Central',
                        'categoria' => 'Cocina',
                        'madera' => 'Melamina Roble Sinatra 18mm & Canto PVC 2mm',
                        'acabado' => 'Terminado Mate Texturizado Antihuellas',
                        'alto' => 215,
                        'ancho' => 340,
                        'fondo' => 60,
                        'estado' => 'corte',
                        'prioridad' => 'alta',
                        'costo' => 4850000,
                        'precio' => 8400000,
                        'anticipo' => 4500000,
                        'fecha_entrega' => '2026-10-18',
                        'carpintero' => 'Maestro Carlos Echeverry',
                        'notas' => 'Cliente solicitó herrajes cierre lento y perfil Gola negro mate.',
                        'piezas' => []
                    ]
                ],
                'inventario' => [],
                'clientes' => [],
                'catalogo' => []
            ];
            file_put_contents(self::$storageFile, json_encode($initialData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    public static function getStorageData() {
        self::initStorage();
        $content = file_get_contents(self::$storageFile);
        return json_decode($content, true) ?: [];
    }

    public static function saveStorageData($data) {
        self::initStorage();
        file_put_contents(self::$storageFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

// Inicializar almacenamiento local
Database::initStorage();

// ========================================================
// FUNCIONES AUXILIARES GLOBALES (CONEXIÓN DIRECTA A MYSQL)
// ========================================================

function formatCOP($monto) {
    return '$ ' . number_format($monto, 0, ',', '.');
}

// 1. PEDIDOS
function getPedidos() {
    $pdo = Database::getConnection();
    if ($pdo) {
        try {
            $sql = "SELECT 
                        p.id, 
                        p.codigo, 
                        COALESCE(c.nombre, 'Cliente General') AS cliente,
                        c.telefono,
                        c.direccion,
                        p.nombre_proyecto AS proyecto,
                        p.categoria_mueble AS categoria,
                        p.tipo_madera AS madera,
                        p.tipo_acabado AS acabado,
                        p.alto_cm AS alto,
                        p.ancho_cm AS ancho,
                        p.fondo_cm AS fondo,
                        p.estado,
                        p.prioridad,
                        p.costo_total AS costo,
                        p.precio_venta AS precio,
                        p.anticipo_pagado AS anticipo,
                        p.fecha_entrega,
                        p.carpintero_asignado AS carpintero,
                        p.notas
                    FROM pedidos p
                    LEFT JOIN clientes c ON p.cliente_id = c.id
                    ORDER BY p.id ASC";
            $stmt = $pdo->query($sql);
            $pedidos = $stmt->fetchAll();

            // Adjuntar piezas de despiece
            foreach ($pedidos as &$ped) {
                $stmtPiezas = $pdo->prepare("SELECT nombre_pieza AS nombre, cantidad, largo_cm AS largo, ancho_cm AS ancho, espesor_mm AS espesor, material, tapacanto AS canto, veta FROM piezas_despiece WHERE pedido_id = ?");
                $stmtPiezas->execute([$ped['id']]);
                $ped['piezas'] = $stmtPiezas->fetchAll();
            }
            return $pedidos;
        } catch (\Exception $e) {
            // Error en query, continuar con fallback
        }
    }

    $data = Database::getStorageData();
    return $data['pedidos'] ?? [];
}

function getPedidoById($id) {
    $pedidos = getPedidos();
    foreach ($pedidos as $p) {
        if ($p['id'] == $id) return $p;
    }
    return null;
}

function actualizarEstadoPedido($id, $nuevoEstado) {
    $pdo = Database::getConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
            $stmt->execute([$nuevoEstado, $id]);
        } catch (\Exception $e) {}
    }

    $data = Database::getStorageData();
    foreach ($data['pedidos'] as &$p) {
        if ($p['id'] == $id) {
            $p['estado'] = $nuevoEstado;
            break;
        }
    }
    Database::saveStorageData($data);
    return true;
}

function registrarAbonoPedido($id, $monto) {
    $pdo = Database::getConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE pedidos SET anticipo_pagado = LEAST(precio_venta, anticipo_pagado + ?) WHERE id = ?");
            $stmt->execute([$monto, $id]);
            
            $stmtAbono = $pdo->prepare("INSERT INTO abonos (pedido_id, monto, metodo_pago) VALUES (?, ?, 'transferencia')");
            $stmtAbono->execute([$id, $monto]);
        } catch (\Exception $e) {}
    }

    $data = Database::getStorageData();
    foreach ($data['pedidos'] as &$p) {
        if ($p['id'] == $id) {
            $p['anticipo'] = min($p['precio'], $p['anticipo'] + $monto);
            break;
        }
    }
    Database::saveStorageData($data);
    return true;
}

function guardarNuevoPedido($nuevo) {
    $pdo = Database::getConnection();
    $nuevo['codigo'] = 'CP-2026-' . rand(100, 999);

    if ($pdo) {
        try {
            // Verificar o crear cliente
            $clienteNombre = trim($nuevo['cliente'] ?? 'Cliente General');
            $stmtCliente = $pdo->prepare("SELECT id FROM clientes WHERE nombre = ? LIMIT 1");
            $stmtCliente->execute([$clienteNombre]);
            $clienteId = $stmtCliente->fetchColumn();

            if (!$clienteId) {
                $stmtInsCliente = $pdo->prepare("INSERT INTO clientes (nombre, telefono, direccion) VALUES (?, ?, ?)");
                $stmtInsCliente->execute([$clienteNombre, $nuevo['telefono'] ?? '', $nuevo['direccion'] ?? '']);
                $clienteId = $pdo->lastInsertId();
            }

            $sql = "INSERT INTO pedidos (codigo, cliente_id, nombre_proyecto, categoria_mueble, tipo_madera, tipo_acabado, alto_cm, ancho_cm, fondo_cm, estado, prioridad, costo_total, precio_venta, anticipo_pagado, fecha_entrega, carpintero_asignado, notas)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'cotizacion', ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nuevo['codigo'],
                $clienteId,
                $nuevo['proyecto'] ?? 'Proyecto Taller',
                $nuevo['categoria'] ?? 'Especial',
                $nuevo['madera'] ?? 'Melamina',
                $nuevo['acabado'] ?? 'Estándar',
                $nuevo['alto'] ?? 200,
                $nuevo['ancho'] ?? 100,
                $nuevo['fondo'] ?? 50,
                $nuevo['prioridad'] ?? 'media',
                ($nuevo['precio'] ?? 0) * 0.55,
                $nuevo['precio'] ?? 0,
                $nuevo['anticipo'] ?? 0,
                $nuevo['fecha_entrega'] ?? date('Y-m-d', strtotime('+15 days')),
                $nuevo['carpintero'] ?? 'Maestro Carlos Echeverry',
                $nuevo['notas'] ?? ''
            ]);
            $nuevo['id'] = $pdo->lastInsertId();
            return $nuevo;
        } catch (\Exception $e) {}
    }

    $data = Database::getStorageData();
    $nuevo['id'] = time();
    array_unshift($data['pedidos'], $nuevo);
    Database::saveStorageData($data);
    return $nuevo;
}

// 2. INVENTARIO
function getInventario() {
    $pdo = Database::getConnection();
    if ($pdo) {
        try {
            $sql = "SELECT 
                        id, 
                        codigo, 
                        nombre, 
                        categoria, 
                        unidad, 
                        stock, 
                        stock_minimo AS min_stock, 
                        costo_unitario AS costo, 
                        proveedor, 
                        ubicacion, 
                        descripcion 
                    FROM inventario 
                    ORDER BY id ASC";
            return $pdo->query($sql)->fetchAll();
        } catch (\Exception $e) {}
    }

    $data = Database::getStorageData();
    return $data['inventario'] ?? [];
}

function actualizarStockInventario($id, $delta) {
    $pdo = Database::getConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE inventario SET stock = GREATEST(0, stock + ?) WHERE id = ?");
            $stmt->execute([$delta, $id]);
        } catch (\Exception $e) {}
    }

    $data = Database::getStorageData();
    foreach ($data['inventario'] as &$item) {
        if ($item['id'] == $id) {
            $item['stock'] = max(0, $item['stock'] + $delta);
            break;
        }
    }
    Database::saveStorageData($data);
    return true;
}

function guardarMaterialInventario($nuevo) {
    $nuevo['codigo'] = 'MAT-' . rand(1000, 9999);
    $pdo = Database::getConnection();
    if ($pdo) {
        try {
            $sql = "INSERT INTO inventario (codigo, nombre, categoria, unidad, stock, stock_minimo, costo_unitario, proveedor, ubicacion, descripcion)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nuevo['codigo'],
                $nuevo['nombre'],
                $nuevo['categoria'] ?? 'madera',
                $nuevo['unidad'] ?? 'unidad',
                $nuevo['stock'] ?? 0,
                $nuevo['min_stock'] ?? 10,
                $nuevo['costo'] ?? 0,
                $nuevo['proveedor'] ?? 'Proveedor General',
                $nuevo['ubicacion'] ?? 'Taller',
                $nuevo['descripcion'] ?? ''
            ]);
            $nuevo['id'] = $pdo->lastInsertId();
            return $nuevo;
        } catch (\Exception $e) {}
    }

    $data = Database::getStorageData();
    $nuevo['id'] = time();
    $data['inventario'][] = $nuevo;
    Database::saveStorageData($data);
    return $nuevo;
}

// 3. CLIENTES
function getClientes() {
    $pdo = Database::getConnection();
    if ($pdo) {
        try {
            $sql = "SELECT 
                        c.id, 
                        c.nombre, 
                        c.telefono, 
                        c.email, 
                        c.direccion, 
                        c.ciudad, 
                        c.notas,
                        COUNT(p.id) AS pedidos_count,
                        COALESCE(SUM(p.precio_venta), 0) AS total_gastado
                    FROM clientes c
                    LEFT JOIN pedidos p ON c.id = p.cliente_id
                    GROUP BY c.id
                    ORDER BY total_gastado DESC";
            return $pdo->query($sql)->fetchAll();
        } catch (\Exception $e) {}
    }

    $data = Database::getStorageData();
    return $data['clientes'] ?? [];
}

function guardarNuevoCliente($cliente) {
    $pdo = Database::getConnection();
    if ($pdo) {
        try {
            $sql = "INSERT INTO clientes (nombre, telefono, email, direccion, ciudad, notas) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $cliente['nombre'],
                $cliente['telefono'] ?? '',
                $cliente['email'] ?? '',
                $cliente['direccion'] ?? '',
                $cliente['ciudad'] ?? 'Bogotá, D.C.',
                $cliente['notas'] ?? ''
            ]);
            $cliente['id'] = $pdo->lastInsertId();
            $cliente['pedidos_count'] = 0;
            $cliente['total_gastado'] = 0;
            return $cliente;
        } catch (\Exception $e) {}
    }

    $data = Database::getStorageData();
    $cliente['id'] = time();
    $cliente['pedidos_count'] = 0;
    $cliente['total_gastado'] = 0;
    $data['clientes'][] = $cliente;
    Database::saveStorageData($data);
    return $cliente;
}

// 4. CATÁLOGO
function getCatalogo() {
    $pdo = Database::getConnection();
    if ($pdo) {
        try {
            $sql = "SELECT 
                        id, 
                        nombre, 
                        categoria, 
                        descripcion, 
                        dimensiones, 
                        precio_sugerido AS precio, 
                        material_base AS material, 
                        dias_fabricacion AS dias, 
                        imagen_url AS imagen, 
                        caracteristicas 
                    FROM catalogo_muebles 
                    ORDER BY id ASC";
            return $pdo->query($sql)->fetchAll();
        } catch (\Exception $e) {}
    }

    $data = Database::getStorageData();
    return $data['catalogo'] ?? [];
}

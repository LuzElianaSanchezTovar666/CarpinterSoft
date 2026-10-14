<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alto = floatval($_POST['alto'] ?? 210);
    $ancho = floatval($_POST['ancho'] ?? 180);
    $fondo = floatval($_POST['fondo'] ?? 60);
    $madera = $_POST['madera'] ?? 'Melamina Roble Sinatra 18mm';
    $precio = floatval($_POST['precio'] ?? 3850000);

    $nuevo = [
        'cliente' => 'Cliente Diseño 3D WebGL',
        'telefono' => '+57 312 000 3D00',
        'direccion' => 'Cotización desde Estudio 3D',
        'proyecto' => $_POST['proyecto'] ?? 'Mueble Paramétrico 3D',
        'categoria' => $_POST['categoria'] ?? 'Closet',
        'madera' => $madera,
        'acabado' => 'Canto PVC 2mm & Textura 3D Seleccionada',
        'alto' => $alto,
        'ancho' => $ancho,
        'fondo' => $fondo,
        'estado' => 'diseno',
        'prioridad' => 'alta',
        'precio' => $precio,
        'costo' => round($precio * 0.55),
        'anticipo' => round($precio * 0.50),
        'fecha_entrega' => date('Y-m-d', strtotime('+12 days')),
        'carpintero' => 'Maestro Carlos Echeverry',
        'notas' => "Diseño generado con el Estudio 3D WebGL (Three.js). Medidas: {$alto}cm alto x {$ancho}cm ancho x {$fondo}cm fondo.",
        'piezas' => [
            ['nombre' => 'Costados Laterales Estructurales', 'cantidad' => 2, 'largo' => $alto, 'ancho' => $fondo, 'espesor' => 18, 'material' => $madera, 'canto' => 'PVC 2mm en 4 cantos'],
            ['nombre' => 'Techo y Base Modular', 'cantidad' => 2, 'largo' => max(10, $ancho - 3.6), 'ancho' => $fondo, 'espesor' => 18, 'material' => $madera, 'canto' => 'PVC 2mm frontal'],
            ['nombre' => 'Fondo Trasero', 'cantidad' => 1, 'largo' => $alto - 2, 'ancho' => $ancho - 2, 'espesor' => 3, 'material' => 'MDF 3mm', 'canto' => 'Sin canto'],
            ['nombre' => 'Repisas Intermedias', 'cantidad' => 3, 'largo' => max(10, $ancho - 3.6), 'ancho' => max(10, $fondo - 2), 'espesor' => 18, 'material' => $madera, 'canto' => 'PVC 1mm frontal'],
            ['nombre' => 'Puertas Batientes Frontales', 'cantidad' => 2, 'largo' => $alto - 2, 'ancho' => ($ancho - 1) / 2, 'espesor' => 18, 'material' => $madera, 'canto' => 'PVC 2mm en 4 cantos']
        ]
    ];

    $guardado = guardarNuevoPedido($nuevo);

    // Si está conectado a MySQL, insertar las piezas despiece en piezas_despiece
    $pdo = Database::getConnection();
    if ($pdo && !empty($guardado['id'])) {
        try {
            $stmtPieza = $pdo->prepare("INSERT INTO piezas_despiece (pedido_id, nombre_pieza, cantidad, largo_cm, ancho_cm, espesor_mm, material, tapacanto, veta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($nuevo['piezas'] as $pz) {
                $stmtPieza->execute([
                    $guardado['id'],
                    $pz['nombre'],
                    $pz['cantidad'],
                    $pz['largo'],
                    $pz['ancho'],
                    $pz['espesor'],
                    $pz['material'],
                    $pz['canto'],
                    'largo'
                ]);
            }
        } catch (\Exception $e) {}
    }

    header('Location: ../pedidos.php?origen=3d_creado');
    exit;
}

header('Location: ../pedidos.php');
exit;

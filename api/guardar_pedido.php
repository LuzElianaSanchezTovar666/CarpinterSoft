<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo = [
        'cliente' => $_POST['cliente'] ?? 'Cliente General',
        'telefono' => $_POST['telefono'] ?? '+57 300 000 0000',
        'direccion' => $_POST['direccion'] ?? 'Taller Central',
        'proyecto' => $_POST['proyecto'] ?? 'Proyecto a Medida',
        'categoria' => $_POST['categoria'] ?? 'Cocina',
        'madera' => $_POST['madera'] ?? 'Melamina 18mm',
        'acabado' => $_POST['acabado'] ?? 'Canto PVC 2mm',
        'alto' => floatval($_POST['alto'] ?? 90),
        'ancho' => floatval($_POST['ancho'] ?? 120),
        'fondo' => floatval($_POST['fondo'] ?? 60),
        'estado' => 'cotizacion',
        'prioridad' => $_POST['prioridad'] ?? 'media',
        'precio' => floatval($_POST['precio'] ?? 2500000),
        'costo' => round(floatval($_POST['precio'] ?? 2500000) * 0.55),
        'anticipo' => floatval($_POST['anticipo'] ?? 0),
        'fecha_entrega' => $_POST['fecha_entrega'] ?? date('Y-m-d', strtotime('+15 days')),
        'carpintero' => $_POST['carpintero'] ?? 'Maestro Carlos Echeverry',
        'notas' => $_POST['notas'] ?? '',
        'piezas' => [
            ['nombre' => 'Laterales Estructurales (Costados)', 'cantidad' => 2, 'largo' => floatval($_POST['alto'] ?? 90), 'ancho' => floatval($_POST['fondo'] ?? 60), 'espesor' => 18, 'material' => $_POST['madera'] ?? 'Melamina 18mm', 'canto' => 'PVC 2mm en 4 cantos'],
            ['nombre' => 'Bases Superior e Inferior', 'cantidad' => 2, 'largo' => max(10, floatval($_POST['ancho'] ?? 120) - 3.6), 'ancho' => floatval($_POST['fondo'] ?? 60), 'espesor' => 18, 'material' => $_POST['madera'] ?? 'Melamina 18mm', 'canto' => 'PVC 2mm frontal']
        ]
    ];

    guardarNuevoPedido($nuevo);
    header('Location: ../pedidos.php');
    exit;
}

header('Location: ../pedidos.php');
exit;

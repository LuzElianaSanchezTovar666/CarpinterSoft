<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo = [
        'nombre' => $_POST['nombre'] ?? 'Nuevo Material',
        'categoria' => $_POST['categoria'] ?? 'madera',
        'unidad' => $_POST['unidad'] ?? 'unidad',
        'stock' => floatval($_POST['stock'] ?? 0),
        'min_stock' => floatval($_POST['min_stock'] ?? 10),
        'costo' => floatval($_POST['costo'] ?? 0),
        'proveedor' => $_POST['proveedor'] ?? 'Proveedor Local',
        'ubicacion' => $_POST['ubicacion'] ?? 'Almacén Central',
        'descripcion' => $_POST['descripcion'] ?? ''
    ];

    guardarMaterialInventario($nuevo);
}

header('Location: ../inventario.php');
exit;

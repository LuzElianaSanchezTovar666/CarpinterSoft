<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo = [
        'nombre' => $_POST['nombre'] ?? 'Nuevo Cliente',
        'telefono' => $_POST['telefono'] ?? '',
        'email' => $_POST['email'] ?? '',
        'direccion' => $_POST['direccion'] ?? '',
        'ciudad' => $_POST['ciudad'] ?? 'Bogotá, D.C.',
        'notas' => $_POST['notas'] ?? ''
    ];

    guardarNuevoCliente($nuevo);
}

header('Location: ../clientes.php');
exit;

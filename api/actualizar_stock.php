<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;
$delta = intval($_GET['delta'] ?? 0);

if ($id && $delta !== 0) {
    actualizarStockInventario($id, $delta);
}

$retorno = $_SERVER['HTTP_REFERER'] ?? '../inventario.php';
header("Location: $retorno");
exit;

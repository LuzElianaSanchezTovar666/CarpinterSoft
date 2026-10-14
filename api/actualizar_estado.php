<?php
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? null;
$estado = $_GET['estado'] ?? null;

if ($id && $estado) {
    actualizarEstadoPedido($id, $estado);
}

$retorno = $_SERVER['HTTP_REFERER'] ?? '../pedidos.php';
header("Location: $retorno");
exit;

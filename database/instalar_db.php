<?php
/**
 * CarpinterSoft - Instalador automático en MySQL XAMPP (Puerto 3307 y 3306)
 */

$host = '127.0.0.1';
$ports = [3307, 3306];
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

header('Content-Type: text/plain; charset=utf-8');

echo "========================================================\n";
echo "   CarpinterSoft - Instalando Base de Datos en XAMPP   \n";
echo "========================================================\n\n";

$pdo = null;
$puertoConectado = null;

foreach ($ports as $port) {
    try {
        echo "Probando conexión en 127.0.0.1:{$port} (root)...\n";
        $pdo = new PDO("mysql:host={$host};port={$port};charset={$charset}", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $puertoConectado = $port;
        echo "   [OK] Conectado exitosamente al servidor MySQL de XAMPP en puerto {$port}!\n\n";
        break;
    } catch (PDOException $e) {
        echo "   [!] No se pudo conectar en puerto {$port}: " . $e->getMessage() . "\n";
    }
}

if (!$pdo) {
    die("\n[ERROR] No se pudo conectar a ningún puerto MySQL.\n");
}

try {
    echo "1. Creando base de datos 'carpintersoft'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `carpintersoft` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `carpintersoft`;");
    echo "   [OK] Base de datos 'carpintersoft' creada y seleccionada.\n\n";

    echo "2. Leyendo script 'database/carpintersoft.sql'...\n";
    $sqlFile = __DIR__ . '/carpintersoft.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Archivo SQL no encontrado en: " . $sqlFile);
    }
    $sql = file_get_contents($sqlFile);

    echo "3. Importando tablas y datos semilla...\n";
    $pdo->exec($sql);
    echo "   [OK] Tablas y datos importados con éxito.\n\n";

    echo "4. Resumen de tablas creadas:\n";
    $tablas = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tablas as $t) {
        $c = $pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
        echo "   - {$t}: {$c} registros\n";
    }

    echo "\n========================================================\n";
    echo "   ¡BASE DE DATOS INSTALADA Y LISTA CON ÉXITO!          \n";
    echo "========================================================\n";
    echo "Host: 127.0.0.1\n";
    echo "Puerto: {$puertoConectado}\n";
    echo "Base de datos: carpintersoft\n";
    echo "Total tablas: " . count($tablas) . "\n";

} catch (Exception $e) {
    echo "\n[ERROR]: " . $e->getMessage() . "\n";
}

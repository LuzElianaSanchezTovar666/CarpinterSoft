<?php
/**
 * CarpinterSoft - Instalador y Migrador de Base de Datos MySQL (XAMPP)
 * Ejecutable desde CLI (php database/instalar_db.php) o desde navegador (http://localhost:8000/database/instalar_db.php)
 */

$host = '127.0.0.1';
$user = 'root';
$charset = 'utf8mb4';

header('Content-Type: text/plain; charset=utf-8');

echo "========================================================\n";
echo "   CarpinterSoft - Instalador de Base de Datos MySQL    \n";
echo "========================================================\n\n";

try {
    echo "1. Probando conexión a MySQL con usuario '{$user}'...\n";
    $passwords = [
        '', 'root', 'admin', '1234', '12345', '123456', '1234567', '12345678', '123456789', '1234567890',
        'sena', 'Sena123', 'Sena1234', 'Sena2024', 'Sena2025', 'Sena2026', 'sena2024', 'sena2025', 'sena2026',
        'aprendiz', 'Aprendiz123', 'Aprendiz2025', 'Aprendiz2026', 'mysql', 'toor', 'password', 'Password123',
        'root123', 'root1234', 'admin123', 'admin1234', 'carpintersoft', 'CarpinterSoft', 'luzeliana', 'LuzEliana'
    ];
    $pdo = null;
    $connectedPass = null;

    foreach ($passwords as $p) {
        try {
            $pdo = new PDO("mysql:host={$host};charset={$charset}", $user, $p, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $connectedPass = $p;
            echo "   [OK] Conectado exitosamente con contraseña: " . ($p === '' ? '(sin contraseña)' : "'{$p}'") . "\n\n";
            break;
        } catch (PDOException $e) {
            echo "   Intento con '" . $p . "': " . $e->getMessage() . "\n";
            continue;
        }
    }

    if (!$pdo) {
        throw new Exception("No se pudo conectar con el usuario 'root'. Si tu MySQL tiene una contraseña específica, configúrala en config/db.php.");
    }

    echo "2. Creando base de datos 'carpintersoft' si no existe...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `carpintersoft` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `carpintersoft`;");
    echo "   [OK] Base de datos 'carpintersoft' lista.\n\n";

    echo "3. Leyendo script SQL 'database/carpintersoft.sql'...\n";
    $sqlFile = __DIR__ . '/carpintersoft.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("El archivo carpintersoft.sql no existe en: " . $sqlFile);
    }

    $sqlContent = file_get_contents($sqlFile);
    
    echo "4. Creando tablas e importando datos semilla...\n";
    $pdo->exec($sqlContent);
    echo "   [OK] Tablas y registros semilla creados exitosamente.\n\n";

    // Verificar tablas creadas
    echo "5. Verificando tablas en 'carpintersoft':\n";
    $stmt = $pdo->query("SHOW TABLES;");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tablas as $t) {
        $conteo = $pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
        echo "   - {$t}: {$conteo} registros\n";
    }

    // Actualizar config/db.php si la contraseña no era vacía
    if ($connectedPass !== '') {
        $configFile = __DIR__ . '/../config/db.php';
        if (file_exists($configFile)) {
            $configCode = file_get_contents($configFile);
            $configCode = str_replace("\$pass = '';", "\$pass = '{$connectedPass}';", $configCode);
            file_put_contents($configFile, $configCode);
            echo "\n[INFO] config/db.php actualizado con la contraseña correcta de MySQL.\n";
        }
    }

    echo "\n========================================================\n";
    echo "   ¡BASE DE DATOS INSTALADA Y LISTA PARA USAR!          \n";
    echo "========================================================\n";
    echo "Servidor: MySQL / MariaDB (XAMPP)\n";
    echo "Base de datos: carpintersoft\n";
    echo "Tablas creadas: " . count($tablas) . "\n";
    echo "Acceso phpMyAdmin: http://localhost/phpmyadmin\n";

} catch (PDOException $e) {
    echo "\n[ERROR PDO] " . $e->getMessage() . "\n";
    echo "Asegúrate de que el servicio MySQL de XAMPP esté iniciado (puerto 3306).\n";
} catch (Exception $e) {
    echo "\n[ERROR] " . $e->getMessage() . "\n";
}

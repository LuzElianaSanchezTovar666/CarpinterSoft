<?php
require_once __DIR__ . '/../config/db.php';
$paginaActual = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CarpinterSoft - Sistema de Gestión de Carpintería y Ebanistería (PHP)</title>
  <meta name="description" content="Software profesional PHP para talleres de carpintería y ebanistería: control de producción, despiece de tableros, inventario y cotizaciones.">
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
            display: ['Outfit', 'sans-serif'],
            mono: ['"JetBrains Mono"', 'monospace'],
          },
          colors: {
            wood: {
              50: '#fbf7f4',
              100: '#f4ede6',
              200: '#ead9cc',
              300: '#dbbfa9',
              400: '#c89e81',
              500: '#b88160',
              600: '#a76d4f',
              700: '#8b5640',
              800: '#714636',
              900: '#5c3b2f',
              950: '#321d17',
            }
          }
        }
      }
    }
  </script>

  <!-- Fuentes de Google -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <style>
    /* Sleek wood scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: rgba(28, 25, 23, 0.6); }
    ::-webkit-scrollbar-thumb { background: rgba(180, 83, 9, 0.35); border-radius: 9999px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(217, 119, 6, 0.65); }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    @media print {
      body { background: white !important; color: black !important; }
      .no-print { display: none !important; }
      .print-only { display: block !important; }
    }
  </style>
</head>
<body class="bg-stone-950 text-stone-100 min-h-screen flex flex-col font-sans selection:bg-amber-600 selection:text-white">

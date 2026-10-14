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
            // Paleta Oficial CarpinterSoft
            petroleo: {
              DEFAULT: '#0D4F60', // Azul petróleo
              oscuro: '#103E4A',  // Azul petróleo oscuro
              profundo: '#09252D',
              claro: '#1A677B',
              borde: '#175667',
            },
            naranja: {
              DEFAULT: '#ED991F', // Naranja
              oscuro: '#E58D17',  // Naranja oscuro
              claro: '#F3B14F',
            },
            crema: {
              DEFAULT: '#F7F5EF', // Crema
              suave: '#FAF9F5',
            },
            grisclaro: '#F0EEE8', // Gris claro
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
    :root {
      --azul-petroleo-oscuro: #103E4A;
      --azul-petroleo: #0D4F60;
      --naranja: #ED991F;
      --naranja-oscuro: #E58D17;
      --crema: #F7F5EF;
      --blanco: #FFFFFF;
      --gris-claro: #F0EEE8;
    }

    /* Scrollbar con paleta personalizada */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #103E4A; }
    ::-webkit-scrollbar-thumb { background: #ED991F; border-radius: 9999px; }
    ::-webkit-scrollbar-thumb:hover { background: #E58D17; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    @media print {
      body { background: white !important; color: black !important; }
      .no-print { display: none !important; }
      .print-only { display: block !important; }
    }
  </style>
</head>
<body class="bg-[#09252D] text-crema min-h-screen flex flex-col font-sans selection:bg-naranja selection:text-petroleo-profundo">

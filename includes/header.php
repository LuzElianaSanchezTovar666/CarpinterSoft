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
    
    /* Estilo Retro-Futurista: Fondo Blueprint sutil y Bento Grid */
    body {
      background-color: #09252D;
      background-image: radial-gradient(rgba(13, 79, 96, 0.45) 1px, transparent 1px);
      background-size: 28px 28px;
    }

    /* Tarjetas Bento Grid Retro-Futuristas */
    .bento-card {
      background: linear-gradient(145deg, rgba(16, 62, 74, 0.88), rgba(9, 37, 45, 0.95));
      border: 1px solid rgba(13, 79, 96, 0.85);
      border-radius: 1.25rem;
      backdrop-filter: blur(12px);
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
    }

    .bento-card:hover {
      border-color: rgba(237, 153, 31, 0.65);
      box-shadow: 0 10px 30px -10px rgba(237, 153, 31, 0.12), inset 0 1px 0 rgba(237, 153, 31, 0.2);
      transform: translateY(-2px);
    }

    /* Marcas técnicas vintage en esquinas */
    .corner-mark::before {
      content: '+';
      position: absolute;
      top: 6px;
      right: 10px;
      font-family: 'JetBrains Mono', monospace;
      font-size: 11px;
      color: rgba(237, 153, 31, 0.4);
      pointer-events: none;
    }

    /* Medidores tipo gauge retro */
    .retro-gauge {
      background: repeating-linear-gradient(90deg, #0D4F60, #0D4F60 4px, transparent 4px, transparent 8px);
    }
  </style>
</head>
<body class="bg-[#09252D] text-crema min-h-screen flex flex-col font-sans selection:bg-naranja selection:text-petroleo-profundo antialiased">

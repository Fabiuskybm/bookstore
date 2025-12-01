<?php
declare(strict_types=1);

// ==============================
// |  Front controller (index)  |
// ==============================



// ------------------
//   CARGAR MÓDULOS  
// ------------------

// Helpers de presentación (función e())
require_once __DIR__ . '/../src/Shared/html.php';



// -----------------------
//   Procesar vistas GET  
// -----------------------

// 1. Determinar vista solicitada (home por defecto)
$view = $_GET['view'] ?? 'home';

// 2. Resolver archivo de vista
switch ($view) {

    case 'home':
    default:
        $viewFile = __DIR__ . '/../src/Home/views/home.php';
        $pageTitle = 'Bookstore | Home';
        break;

}

// 3. Cargar layout común
require __DIR__ . '/../src/Shared/templates/layout.php';

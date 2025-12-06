<?php
declare(strict_types=1);

// ==============================
// |  Front controller (index)  |
// ==============================


// ------------------
//   CARGAR MÓDULOS  
// ------------------

require_once __DIR__ . '/../src/Shared/SessionService.php';
require_once __DIR__ . '/../src/Shared/config.php';
require_once __DIR__ . '/../src/Shared/validation.php';

require_once __DIR__ . '/../src/Auth/services/AuthService.php';
require_once __DIR__ . '/../src/Auth/controllers/AuthController.php';

require_once __DIR__ . '/../src/Home/controllers/HomeController.php';

require_once __DIR__ . '/../src/Wishlist/services/WishlistService.php';
require_once __DIR__ . '/../src/Wishlist/controllers/WishlistController.php';

require_once __DIR__ . '/../src/Preference/controllers/PreferenceController.php';


// Helpers de presentación
require_once __DIR__ . '/../src/Shared/html.php';
require_once __DIR__ . '/../src/Shared/i18n.php';


session_start_safe();


$auth = new AuthController();
$home = new HomeController();
$wishlist = new WishlistController();
$preference = new PreferenceController();

$data = [];



// -----------------------------
//   Procesar formularios POST  
// -----------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';
    $result = null;

    switch ($action) {

        case 'login':
            $result = $auth->processLogin();
            break;

        case 'logout':
            $result = $auth->logout();
            break;
        
        case 'wishlist_add':
            $result = $wishlist->add();
            break;

        case 'wishlist_remove':
            $result = $wishlist->remove();
            break;

        case 'wishlist_bulk_remove':
            $result = $wishlist->bulkRemove();
            break;

        case 'wishlist_clear':
            $result = $wishlist->clear();
            break;

        case 'set_language':
            $result = $preference->setLanguage();
            break;

        case 'preferences_update':
            $result = $preference->update();
            break;

    }

    // Redirecciones
    if ($result !== null && isset($result['redirect'])) {
        header('Location: index.php?view=' . urlencode($result['redirect']));
        exit;
    }

    // Preparar datos para mostrar vista
    if ($result !== null && isset($result['view'])) {
        $view  = $result['view'];
        $data  = $result['data'] ?? [];
    }
}




// -----------------------
//   Procesar vistas GET  
// -----------------------

// 1. Determinar vista solicitada (home por defecto)
$view = $view ?? ($_GET['view'] ?? 'home');

// Si es petición GET y la vista es login, preparar datos vacíos
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    switch ($view) {

        case 'home':
            $result = $home->show();
            $data = $result['data'] ?? [];
            break;
        
        case 'login':
            $result = $auth->showLogin();
            $data = $result['data'] ?? [];
            break;

        case 'wishlist':
            $result = $wishlist->show();
            $data = $result['data'] ?? [];
            break;
    }
}


// 2. Resolver archivo de vista
switch ($view) {

    case 'home':
        $viewFile = __DIR__ . '/../src/Home/views/home.php';
        $pageTitle = t('layout.page_title_home');
        break;

    case 'login':
        $viewFile = __DIR__ . '/../src/Auth/views/login.php';
        $pageTitle = t('layout.page_title_login');
        break;

    case 'cart':
        $viewFile = __DIR__ . '/../src/Cart/views/cart.php';
        $pageTitle = t('layout.page_title_cart');
        break;

    case 'wishlist':
        $viewFile = __DIR__ . '/../src/Wishlist/views/wishlist.php';
        $pageTitle = t('layout.page_title_wishlist');
        break;

    case 'preferences':
        $viewFile = __DIR__ . '/../src/Preference/views/preferences.php';
        $pageTitle = t('layout.page_title_preferences');
        break;

    default:
        $view = 'home';
        $viewFile = __DIR__ . '/../src/Home/views/home.php';
        $pageTitle = t('layout.page_title_home');
        break;

}


// 3. Cargar layout común
require __DIR__ . '/../src/Shared/templates/layout.php';

<?php 
declare(strict_types=1);

$currentUser = auth_user();
$currentTheme = pref_theme();
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Bookstore') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Geolocalización (Plugin/Librería externa: Leaflet) -->
      <link rel="stylesheet" 
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""
        />

</head>

<body 
    class="page page--theme-<?= e($currentTheme) ?>"
    data-user="<?= $currentUser ? e($currentUser-> getUsername()) : 'guest' ?>"
>

    <?php require __DIR__ . '/partials/header.php' ?>
    <?php require __DIR__ . '/partials/nav.php' ?>
    
    <main class="page__main">
        <?php 
            if (isset($viewFile) && file_exists($viewFile)) {
                require $viewFile;
            } else {
                echo '<p class="page__error">Vista no encontrada.</p>';
            }
        ?>
    </main>

    <?php require __DIR__ . '/partials/footer.php' ?>

    <button 
        class="scroll-top"
        type="button"
        aria-label="<?= e(t('layout.scroll_top_label')) ?>"
        data-scroll-top
    >
        <svg 
            class="scroll-top__icon"
            width="24"
            height="24"
            viewBox="0 0 1024 1024"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M509.928 387.16c7.24-7.991 17.58-7.898 24.782 0.333l270.568 309.222c7.759 8.867 21.237 9.765 30.103 2.007 8.867-7.759 9.766-21.237 2.007-30.103L566.82 359.397c-24-27.429-64.127-27.792-88.507-0.89L197.526 668.342c-7.912 8.73-7.249 22.221 1.482 30.133 8.73 7.912 22.221 7.249 30.133-1.482L509.928 387.16z"
                fill="currentColor"
            />
        </svg>
    </button>

    <!-- Geolocalización (Plugin/Librería externa: Leaflet) -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin="">
    </script>

    <!-- main.js de mi aplicación -->
    <script src="assets/js/main.js"></script>
</body>

</html>
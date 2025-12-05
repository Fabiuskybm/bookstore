<?php 
declare(strict_types=1);

$currentUser = auth_user();
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Bookstore') ?></title>
    <!-- <link rel="stylesheet" href="assets/css/main.css"> -->
</head>

<body 
    class="page"
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

    <footer class="page__footer footer"></footer>
    

    <script src="assets/js/main.js"></script>
</body>

</html>
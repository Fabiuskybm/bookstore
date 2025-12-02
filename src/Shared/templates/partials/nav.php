<?php
declare(strict_types=1);

require_once __DIR__ . '/../../navigation.php';


// Vista actual (se define en index.php)
$currentView = $view ?? 'home';


// Elementos del menú
$items = get_navigation_items();

?>


<nav class="nav">
    <ul class="nav__list">
        <?php foreach ($items as $item): ?>

            <?php
                $isActive = ($item['view'] === $currentView);
                $classes = 'nav__link' . ($isActive ? ' nav__link--active' : ''); 
            ?>

            <li class="nav__item">
                <a 
                    href="index.php?view=<?= e($item['view']) ?>"
                    class="<?= e($classes) ?>">
                    <?= e($item['label']) ?>
                </a>
            </li>

        <?php endforeach; ?>

    </ul>
</nav>
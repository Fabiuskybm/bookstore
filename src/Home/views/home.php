<?php
declare(strict_types=1);

$books = $data['books'] ?? [];
$featuredBooks = $data['featuredBooks'] ?? [];
?>

<section class="home">

    <h1 class="home__title"><?= e(t('home.title')) ?></h1>
    <div class="home__divider"></div>

    <?php
        $carouselTitleKey = 'home.featured_title';
        $carouselBooks = $featuredBooks;
        $carouselId = 'book-carousel-featured';
        require __DIR__ . '/../../Shared/templates/partials/book-carousel.php';
    ?>

    <h2 class="home__subtitle home__subtitle--catalog">
        <?= e(t('home.catalog_title')) ?>
    </h2>
    
    <div class="home__divider"></div>
    
    <?php if (!empty($data['books'])): ?>

        <div class="book-grid book-grid--home">
            <?php 
                $cardContext = 'home';
                foreach($data['books'] as $book): ?>

                <?php require __DIR__ . '/../../Book/views/partials/book-card.php'; ?>
                
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <p><?= e(t('home.empty')) ?></p>
    <?php endif; ?>

</section>

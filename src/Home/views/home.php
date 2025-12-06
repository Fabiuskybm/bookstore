<?php
declare(strict_types=1);
?>

<section class="home">
    <h1 class="home__title"><?= e(t('home.title')) ?></h1>
    <p class="home__subtitle"><?= e(t('home.subtitle')) ?></p>

    <?php if (!empty($data['books'])): ?>

        <div class="book-grid book-grid--home">
            <?php foreach($data['books'] as $book): ?>

                <?php require __DIR__ . '/../../Book/views/partials/book-card.php'; ?>
                
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <p><?= e(t('home.empty')) ?></p>
    <?php endif; ?>

</section>

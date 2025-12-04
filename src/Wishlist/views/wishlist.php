<?php
declare(strict_types=1);

$wishlistBooks = wishlist_get_books();
?>


<section class="wishlist">
    <h1 class="wishlist__title">Wishlist</h1>

    <?php if (empty($wishlistBooks)): ?>
        <p class="wishlist__empty">Tu lista de deseos está vacía.</p>
    <?php else: ?>

        <div class="wishlist__grid">
            <?php foreach($wishlistBooks as $book): ?>
                <?php require __DIR__ . '/../../Book/views/partials/book-card.php'; ?>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>
</section>

<?php
declare(strict_types=1);
?>


<article
    class="book-card"
    data-book-id="<?= e($book->getId()) ?>"
    data-book-title="<?= e($book->getTitle()) ?>"
    data-book-author="<?= e($book->getAuthor()) ?>"
    data-book-price="<?= e($book->getPrice()) ?>">

    <div class="book-card__image-wrapper">
        <img 
            src="<?= e($book->getCoverImage()) ?>" 
            alt="<?= e('Portada de ' . $book->getTitle()) ?>"
            class="book-card__image"
        >
    </div>

    <div class="book-card__body">

        <h2 class="book-card__title">
            <?= e($book->getTitle()) ?>
        </h2>
        
        <p class="book-card__author">
            <?= e($book->getAuthor()) ?>
        </p>

        <p class="book-card__price">
            <?= number_format($book->getPrice(), 2, ',', '.') ?> €
        </p>

        <div class="book-card__actions">

            <button 
                type="button"
                class="book-card__btn book-card__btn--wishlist">

                <img 
                    src="assets/images/wishlist.png" 
                    alt="Wishlist icon"
                    class="book-card__icon book-card__icon--wishlist"
                >
            </button>

            <button 
                type="button"
                class="book-card__btn book-card__btn--cart">

                <div class="book-card__btn-content">
                    <img 
                        src="assets/images/shopping-bag.png" 
                        alt="Añadir al carrito"
                        class="book-card__icon book-card__icon--cart"
                    >
                    <span class="book-card__btn-label">Añadir al carrito</span>
                </div>
            </button>

        </div>

    </div>

</article>
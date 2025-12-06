<?php
declare(strict_types=1);


$cardContext = $cardContext ?? 'home';
?>


<article
    class="book-card book-card--<?= e($cardContext) ?>"
    data-book-id="<?= e($book->getId()) ?>"
    data-book-title="<?= e($book->getTitle()) ?>"
    data-book-author="<?= e($book->getAuthor()) ?>"
    data-book-price="<?= e($book->getPrice()) ?>">

    <div class="book-card__image-wrapper">
        <img 
            src="<?= e($book->getCoverImage()) ?>" 
            alt="<?= e(t('book.cover_alt_prefix') . ' ' . $book->getTitle()) ?>"
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

            <?php
                $actionTemplate = __DIR__ . '/book-card-actions-' . $cardContext . '.php';
                if (file_exists($actionTemplate)) require $actionTemplate;
            ?>

        </div>

    </div>

</article>
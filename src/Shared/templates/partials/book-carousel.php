<?php
declare(strict_types=1);

$carouselBooks = $carouselBooks ?? [];
$carouselTitleKey = $carouselTitleKey ?? 'home.featured_title';
$carouselId = $carouselId ?? 'book-carousel-featured';
?>

<?php if (!empty($carouselBooks)): ?>

<section
    class="book-carousel"
    id="<?= e($carouselId) ?>"
    aria-label="<?= e(t($carouselTitleKey)) ?>"
    data-book-carousel
>
    <header class="book-carousel__header">
        <h2 class="book-carousel__title">
            <?= e(t($carouselTitleKey)) ?>
        </h2>
    </header>

    <div class="book-carousel__viewport">
        <div
            class="book-carousel__track"
            data-book-carousel-track
        >
            <?php foreach ($carouselBooks as $book): ?>
                <?php
                    $cardContext = 'featured';
                    require __DIR__ . '/../../../Book/views/partials/book-card.php';
                ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="book-carousel__controls">
        <button
            type="button"
            class="book-carousel__control book-carousel__control--prev"
            data-book-carousel-prev
            aria-label="<?= e(t('home.featured_prev_label') ?? 'Anterior') ?>"
        >
            <svg
                class="book-carousel__icon"
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
            >
                <path
                    d="M15.41 7.41L14 6L8 12L14 18L15.41 16.59L10.83 12L15.41 7.41Z"
                    fill="currentColor"
                />
            </svg>
        </button>

        <div
            class="book-carousel__dots"
            data-book-carousel-dots
        ></div>

        <button
            type="button"
            class="book-carousel__control book-carousel__control--next"
            data-book-carousel-next
            aria-label="<?= e(t('home.featured_next_label') ?? 'Siguiente') ?>"
        >
            <svg
                class="book-carousel__icon"
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
            >
                <path
                    d="M15.41 7.41L14 6L8 12L14 18L15.41 16.59L10.83 12L15.41 7.41Z"
                    fill="currentColor"
                />
            </svg>
        </button>
    </div>

</section>

<?php endif; ?>

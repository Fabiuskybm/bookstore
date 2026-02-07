<?php
declare(strict_types=1);

$book = $data['book'] ?? null;

if (!$book instanceof Book) {
    return;
}

$synopsis = trim((string) ($data['synopsis'] ?? ''));
$publishedYear = $data['publishedYear'] ?? null;
$pages = $data['pages'] ?? null;
$categories = $book->getCategories();
$isInWishlist = wishlist_has((string) $book->getId());
$stock = $book->getStock();
?>

<section class="product-detail" aria-labelledby="product-detail-title">

    <article class="product-detail__layout">

        <div class="product-detail__main">

            <div class="product-detail__media">
                <img
                    src="<?= e($book->getImagePath()) ?>"
                    alt="<?= e(t('book.cover_alt_prefix') . ' ' . $book->getName()) ?>"
                    class="product-detail__image"
                >
            </div>

            <div class="product-detail__content">
                <h1 id="product-detail-title" class="product-detail__title"><?= e($book->getName()) ?></h1>
                <p class="product-detail__author"><?= e($book->getAuthor()) ?></p>

                <section class="product-detail__metadata" aria-label="<?= e(t('product.metadata_aria_label')) ?>">
                    <ul class="product-detail__metadata-list">
                        <li class="product-detail__metadata-item">
                            <span class="product-detail__metadata-label"><?= e(t('product.format_label')) ?>:</span>
                            <span class="product-detail__metadata-value"><?= e(t('book.format_' . $book->getFormat(), $book->getFormat())) ?></span>
                        </li>

                        <?php if ($publishedYear !== null): ?>
                            <li class="product-detail__metadata-item">
                                <span class="product-detail__metadata-label"><?= e(t('product.published_year_label')) ?>:</span>
                                <span class="product-detail__metadata-value"><?= e((string) $publishedYear) ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if ($pages !== null): ?>
                            <li class="product-detail__metadata-item">
                                <span class="product-detail__metadata-label"><?= e(t('product.pages_label')) ?>:</span>
                                <span class="product-detail__metadata-value"><?= e((string) $pages) ?></span>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($categories)): ?>
                            <li class="product-detail__metadata-item">
                                <span class="product-detail__metadata-label"><?= e(t('product.categories_label')) ?>:</span>
                                <span class="product-detail__metadata-value">
                                    <?php
                                    $translatedCategories = array_map(
                                        static fn(string $slug): string => t('categories.' . $slug, $slug),
                                        $categories
                                    );
                                    echo e(implode(', ', $translatedCategories));
                                    ?>
                                </span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </section>

                <section class="product-detail__synopsis" aria-labelledby="product-detail-synopsis-title">
                    <h2 id="product-detail-synopsis-title" class="product-detail__synopsis-title">
                        <?= e(t('product.synopsis_title')) ?>
                    </h2>

                    <p class="product-detail__synopsis-text">
                        <?= e($synopsis !== '' ? $synopsis : t('product.synopsis_empty')) ?>
                    </p>
                </section>
            </div>
        </div>

        <aside class="product-detail__purchase" aria-label="<?= e(t('product.purchase_panel_aria')) ?>">
            <p class="product-detail__price"><?= number_format($book->getPrice(), 2, ',', '.') ?> €</p>

            <?php if ($stock > 0): ?>
                <p class="product-detail__stock">
                    <?= e(t('product.stock_prefix')) ?>
                </p>
            <?php else: ?>
                <p class="product-detail__stock product-detail__stock--empty"><?= e(t('product.out_of_stock')) ?></p>
            <?php endif; ?>

            <button type="button" class="product-detail__cart-btn">
                <?= e(t('book.add_to_cart_label')) ?>
            </button>

            <form method="post" action="index.php" class="product-detail__wishlist-form book-card__wishlist-form">
                <input type="hidden" name="action" value="wishlist_toggle">
                <input type="hidden" name="product_id" value="<?= e((string) $book->getId()) ?>">
                <input type="hidden" name="_return" value="product&id=<?= e((string) $book->getId()) ?>">

                <!-- Reutilizamos la clase base de wishlist para aprovechar la asincronía existente -->
                <button
                    type="submit"
                    class="product-detail__wishlist-btn book-card__btn--wishlist<?= $isInWishlist ? ' book-card__btn--wishlist-active' : '' ?>"
                    aria-pressed="<?= $isInWishlist ? 'true' : 'false' ?>"
                >
                    <svg
                        class="product-detail__wishlist-icon"
                        width="20"
                        height="20"
                        viewBox="0 0 16 16"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                    >
                        <path
                            d="M1.24264 8.24264L8 15L14.7574 8.24264C15.553 7.44699 16 6.36786 16 5.24264V5.05234C16 2.8143 14.1857 1 11.9477 1C10.7166 1 9.55233 1.55959 8.78331 2.52086L8 3.5L7.21669 2.52086C6.44767 1.55959 5.28338 1 4.05234 1C1.8143 1 0 2.8143 0 5.05234V5.24264C0 6.36786 0.44699 7.44699 1.24264 8.24264Z"
                            fill="currentColor"
                        />
                    </svg>
                    <span><?= e(t('product.add_to_wishlist')) ?></span>
                </button>
            </form>

            <section
                class="product-detail__rating-shell"
                id="rating-root"
                data-product-id="<?= e((string) $book->getId()) ?>"

                data-i18n-title="<?= e(t('rating.title')) ?>"
                data-i18n-vote-title="<?= e(t('rating.vote_title')) ?>"
                data-i18n-loading="<?= e(t('rating.loading')) ?>"
                data-i18n-error-load="<?= e(t('rating.error_load')) ?>"
                data-i18n-error-html="<?= e(t('rating.error_html')) ?>"
                data-i18n-error-json="<?= e(t('rating.error_json')) ?>"
                data-i18n-error-network-load="<?= e(t('rating.error_network_load')) ?>"
                data-i18n-error-auth-required="<?= e(t('rating.error_auth_required')) ?>"
                data-i18n-error-vote="<?= e(t('rating.error_vote')) ?>"
                data-i18n-error-network-vote="<?= e(t('rating.error_network_vote')) ?>"
                data-i18n-stars-label-avg="<?= e(t('rating.stars_label_avg')) ?>"
                data-i18n-stars-label-vote="<?= e(t('rating.stars_label_vote')) ?>"
                data-i18n-dist-aria="<?= e(t('rating.dist_aria')) ?>"
                data-i18n-dist-stars="<?= e(t('rating.dist_stars')) ?>"
                data-i18n-vote-aria="<?= e(t('rating.vote_aria')) ?>"
            >
                <p class="product-detail__rating-placeholder"><?= e(t('product.rating_placeholder')) ?></p>
            </section>
        </aside>
    </article>
</section>

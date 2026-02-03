<?php
declare(strict_types=1);
?>


<div class="book-card__wishlist-actions">
    <label class="book-card__select">
        <input
            type="checkbox"
            name="selected_books[]"
            value="<?= e((string) $book->getId()) ?>"
            class="book-card__select-input"
            aria-label="<?= e(t('wishlist.select_item')) ?>"
        >
    </label>
</div>
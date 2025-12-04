<?php
declare(strict_types=1);

$wishlistBooks = wishlist_get_books();
?>


<section class="wishlist">
    <h1 class="wishlist__title">Wishlist</h1>

    <?php if (empty($wishlistBooks)): ?>
        <p class="wishlist__empty">Tu lista de deseos está vacía.</p>
    <?php else: ?>

        <form 
            method="post"
            class="wishlist__form">

            <input type="hidden" name="_return" value="wishlist">

            <div class="wishlist__select-all">
                <label class="wishlist__select-all-label">
                    <input 
                        type="checkbox" 
                        class="wishlist__select-all-input">
                        Seleccionar todo
                </label>

            </div>

            <div class="book-grid book-grid--wishlist">
                <?php foreach($wishlistBooks as $book):
                    $cardContext = 'wishlist';
                    require __DIR__ . '/../../Book/views/partials/book-card.php';
                endforeach; ?>
            </div>

            <div class="wishlist__actions">
                
                <button 
                    type="button"
                    class="wishlist__btn wishlist__btn--cart">
                    Añadir al carrito
                </button>

                <button 
                    type="submit"
                    name="action"
                    value="wishlist_bulk_remove"
                    class="wishlist__btn wishlist__btn--remove">
                    Eliminar
                </button>

            </div>

            <p
                class="wishlist__error"
                data-wishlist-error
            ></p>

        </form>


    <?php endif; ?>
</section>

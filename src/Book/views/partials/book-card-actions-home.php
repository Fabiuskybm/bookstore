<?php
declare(strict_types=1);

/**
 * Estado del usuario y wishlist
 * 
 *  - Determina si hay usuario autenticad.
 *  - Comprueba si el libro actual está en la wishlist.
 */
$user = auth_user();
$isInWishlist = false;

if ($user !== null) {
    $isInWishlist = wishlist_has($book->getId());
}


/**
 * Configuración de clases y acciones del botón wishlist
 * 
 *  - Define las clases base del botón.
 *  - Añade modificadores según estado (activo/inactivo).
 *  - Determina la acción a realizar (añadir o eliminar).
 */
$wishlistBtnClass = 'book-card__btn book-card__btn--wishlist';
                
if ($isInWishlist) {
    $wishlistBtnClass .= ' book-card__btn--wishlist-active';
}

$wishlistAction = $isInWishlist ? 'wishlist_remove' : 'wishlist_add';
$currentView = $view ?? 'home';
?>



<form 
    method="post"
    class="book-card__wishlist-form">

    <input type="hidden" name="action" value="<?= e($wishlistAction) ?>">
    <input type="hidden" name="book_id" value="<?= e($book->getId()) ?>">
    <input type="hidden" name="_return" value="<?= e($currentView) ?>">

    <button 
        type="submit"
        class="<?= e($wishlistBtnClass) ?>">

        <img 
            src="assets/images/icons/wishlist.svg" 
            alt="<?= e(t('book.wishlist_icon_alt')) ?>"
            class="book-card__icon book-card__icon--wishlist"
        >
    </button>
</form>


<button 
    type="button"
    class="book-card__btn book-card__btn--cart">

    <div class="book-card__btn-content">
        <img 
            src="assets/images/icons/shopping-bag.svg" 
            alt="<?= e(t('book.cart_icon_alt')) ?>"
            class="book-card__icon book-card__icon--cart"
        >
        <span class="book-card__btn-label"><?= e(t('book.add_to_cart_label')) ?></span>
    </div>
</button>
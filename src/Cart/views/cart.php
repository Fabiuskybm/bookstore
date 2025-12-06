<?php
declare(strict_types=1); 
?>


<section 
    class="cart"
    data-cart-empty-text="<?= e(t('cart.empty')) ?>"
    data-cart-ticket-title="<?= e(t('cart.ticket_title')) ?>"
    data-cart-alert-empty="<?= e(t('cart.alert_empty')) ?>"
    data-cart-cover-alt="<?= e(t('cart.cover_alt')) ?>"
    data-cart-quantity-label="<?= e(t('cart.quantity_label')) ?>"
    data-cart-remove-alt="<?= e(t('cart.remove_alt')) ?>">

    <h1 class="cart__title"><?= e(t('cart.title')) ?></h1>

    <div class="cart__layout">

        <section class="cart__items-area">

            <header class="cart__header">
                <span class="cart__header-cell cart__header-cell--title"><?= e(t('cart.header_title')) ?></span>
                <span class="cart__header-cell cart__header-cell--price"><?= e(t('cart.header_price')) ?></span>
                <span class="cart__header-cell cart__header-cell--quantity"><?= e(t('cart.header_quantity')) ?></span>
                <span class="cart__header-cell cart__header-cell--total"><?= e(t('cart.header_total')) ?></span>
            </header>

            <div
                class="cart__items"
                data-cart-items>
                <!-- Aquí se pinta las card con JS -->
            </div>

            <div class="cart__footer">
                <a 
                    href="index.php?view=home"
                    class="cart__continue-link">
                    <?= e(t('cart.continue_shopping')) ?>
                </a>
            </div>

        </section>

        <aside class="cart__summary">
            <h2 class="cart__summary-title"><?= e(t('cart.summary_title')) ?></h2>

            <dl class="cart__summary-list">

                <div class="cart__summary-row">
                    <dt class="cart__summary-label"><?= e(t('cart.summary_items_label')) ?></dt>
                    <dd
                        class="cart__summary-value"
                        data-cart-total-quantity>
                        0
                    </dd>
                </div>

                <div class="cart__summary-row cart__summary-row--total">
                    <dt class="cart__summary-label"><?= e(t('cart.summary_total_label')) ?></dt>
                    <dd
                        class="cart__summary-value cart__summary-value--total"
                        data-cart-total-price>
                        0,00 €
                    </dd>
                </div>
            </dl>

            <button 
                type="button"
                class="cart__checkout-btn"
                data-cart-checkout>
                <?= e(t('cart.checkout')) ?>
            </button>

            <div
                class="cart__ticket"
                data-cart-ticket
                hidden>
            </div>

        </aside>

    </div>


</section>

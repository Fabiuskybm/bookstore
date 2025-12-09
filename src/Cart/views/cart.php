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
    data-cart-remove-alt="<?= e(t('cart.remove_alt')) ?>"
    data-cart-ticket-subtotal-label="<?= e(t('cart.ticket_subtotal_label')) ?>"
    data-cart-ticket-discount-label="<?= e(t('cart.ticket_discount_label')) ?>"
    data-cart-ticket-total-label="<?= e(t('cart.ticket_total_label')) ?>"
    data-cart-promo-invalid="<?= e(t('cart.promo_invalid')) ?>"
    data-cart-promo-applied="<?= e(t('cart.promo_applied')) ?>">

    <h1 class="cart__title"><?= e(t('cart.title')) ?></h1>
    <div class="cart__divider"></div>

    <div class="cart__layout">

        <section class="cart__items-area">

            <header class="cart__header">
                <span class="cart__header-cell cart__header-cell--image"></span>
                <span class="cart__header-cell cart__header-cell--title"><?= e(t('cart.header_title')) ?></span>
                <span class="cart__header-cell cart__header-cell--price"><?= e(t('cart.header_price')) ?></span>
                <span class="cart__header-cell cart__header-cell--quantity"><?= e(t('cart.header_quantity')) ?></span>
                <span class="cart__header-cell cart__header-cell--total"><?= e(t('cart.header_total')) ?></span>
                <span class="cart__header-cell cart__header-cell--actions"></span>
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

                    <svg
                        class="cart__continue-icon"
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

                    <span><?= e(t('cart.continue_shopping')) ?></span>
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

            <div class="cart__promo">
                <label 
                    for="cart-promo-code"
                    class="cart__promo-label">
                    <?= e(t('cart.promo_label')) ?>
                </label>

                <div class="cart__promo-row">
                    <input
                        type="text"
                        id="cart-promo-code"
                        name="promo_code"
                        class="cart__promo-input"
                        placeholder="<?= e(t('cart.promo_placeholder')) ?>"
                        data-cart-promo-input
                    >

                    <button
                        type="button"
                        class="cart__promo-button"
                        data-cart-promo-apply>
                        <?= e(t('cart.promo_apply')) ?>
                    </button>
                </div>

                <p 
                    class="cart__promo-message"
                    data-cart-promo-message
                    hidden>
                </p>
            </div>

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

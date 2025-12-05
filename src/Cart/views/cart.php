<?php
declare(strict_types=1); 
?>


<section class="cart">
    <h1 class="cart__title">Carrito</h1>

    <div class="cart__layout">

        <section class="cart__items-area">

            <header class="cart__header">
                <span class="cart__header-cell cart__header-cell--title">Título</span>
                <span class="cart__header-cell cart__header-cell--price">Precio</span>
                <span class="cart__header-cell cart__header-cell--quantity">Cantidad</span>
                <span class="cart__header-cell cart__header-cell--total">Total</span>
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
                    Continuar comprando
                </a>
            </div>

        </section>

        <aside class="cart__summary">
            <h2 class="cart__summary-title">Resumen del pedido</h2>

            <dl class="cart__summary-list">

                <div class="cart__summary-row">
                    <dt class="cart__summary-label">Artículos</dt>
                    <dd
                        class="cart__summary-value"
                        data-cart-total-quantity>
                        0
                    </dd>
                </div>

                <div class="cart__summary-row cart__summary-row--total">
                    <dt class="cart__summary-label">Total</dt>
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
                Tramitar pedido
            </button>

            <div
                class="cart__ticket"
                data-cart-ticket
                hidden>
            </div>

        </aside>

    </div>


</section>

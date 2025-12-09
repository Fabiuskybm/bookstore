
// ==================================================
//  CART PROMO
//  - Códigos promocionales
//  - Cálculo de totales con descuento
//  - Inicialización de validación y apply
// ==================================================

import { getCartTexts } from './cart-dom.js';
import {
    initLiveValidation,
    createCustomRule
} from '../Shared/form-validation.js';
import { DiscountedProduct } from './Product.js';



// ==================================================
//  CONFIGURACIÓN Y ESTADO
// ==================================================

/**
 * Mapa de códigos promocionales válidos.
 * La clave es el código, el valor es el porcentaje de descuento.
 */
const PROMO_CODES = {
    BLACK10: 10,
    BLACK20: 20
};

/**
 * Porcentaje de descuento actualmente aplicado.
 * Se actualiza cada vez que el usuario introduce un código válido.
 */
let currentDiscountPercent = 0;



// ==================================================
//  ACCESORES DE ESTADO
// ==================================================

/**
 * Devuelve el porcentaje de descuento actual.
 */
export function getCurrentDiscountPercent() {
    return currentDiscountPercent;
}



// ==================================================
//  CÁLCULO DE TOTALES CON DESCUENTO
// ==================================================

/**
 * Calcula subtotal, total de descuento y total final
 * a partir de una lista de productos y un porcentaje.
 *
 * - subtotal: suma de lineTotal sin descuento
 * - discountTotal: suma de descuentos aplicados
 * - grandTotal: total final tras aplicar el descuento
 */
export function calculateTotalsWithDiscount(products, discountPercent) {
    const percent = Number(discountPercent) || 0;
    const hasDiscount = percent > 0;

    let subtotal = 0;
    let discountTotal = 0;
    let grandTotal = 0;

    products.forEach((product) => {
        const baseLine = product.lineTotal;
        subtotal += baseLine;

        if (hasDiscount) {
            const discounted = new DiscountedProduct(
                {
                    id: product.id,
                    title: product.title,
                    price: product.price,
                    quantity: product.quantity
                },
                percent
            );

            grandTotal += discounted.finalLineTotal;
            discountTotal += discounted.discountAmount;

        } else {
            grandTotal += baseLine;
        }
    });

    return {
        subtotal,
        discountTotal,
        grandTotal,
        hasDiscount
    };
}



// ==================================================
//  INICIALIZACIÓN DEL BLOQUE PROMO
// ==================================================

/**
 * Inicializa la lógica del código promocional:
 * - Valida el input en vivo.
 * - Aplica el código al hacer clic en el botón.
 * - Llama a updateTotals() para refrescar el resumen.
 */
export function initCartPromo(dom, updateTotals) {
    const { promoInput, promoApplyBtn, promoMessage } = dom;
    if (!promoInput || !promoApplyBtn) return;

    let validator = null;
    const texts = getCartTexts();

    // Validación en vivo del input de código
    if (promoMessage) {
        validator = initLiveValidation(promoInput, promoMessage, {
            rules: [
                createCustomRule((value) => {
                    const trimmed = value.trim();

                    // Campo vacío: se considera válido, sin mensaje
                    if (trimmed === '') {
                        return { valid: true };
                    }

                    const normalized = trimmed.toUpperCase();

                    if (PROMO_CODES[normalized]) {
                        return { valid: true };
                    }

                    return {
                        valid: false,
                        message: texts.promoInvalid
                    };
                })
            ],
            events: ['input'],
            initialSilent: true
        });
    }

    // Botón "Aplicar" del código promocional
    promoApplyBtn.addEventListener('click', () => {
        // Si hay validador, se comprueba antes de aplicar
        if (validator && !validator.validate()) {
            console.log('Código inválido. No se aplica.');
            return;
        }

        const code = promoInput.value.trim();
        handlePromoCode(dom, code, texts, updateTotals);
    });
}



// ==================================================
//  LÓGICA INTERNA DE APLICAR CÓDIGO
// ==================================================

/**
 * Aplica el código promocional:
 * - Actualiza el porcentaje global de descuento.
 * - Llama a updateTotals() para recalcular los totales.
 * - Muestra u oculta el mensaje de éxito en la interfaz.
 */
function handlePromoCode(dom, code, texts, updateTotals) {
    const normalized = (code || '').trim().toUpperCase();
    const percent = PROMO_CODES[normalized] || 0;

    // Actualizamos el estado global de descuento
    currentDiscountPercent = percent;

    // Actualizar resumen con el nuevo descuento
    if (typeof updateTotals === 'function') {
        updateTotals();
    }

    const { promoMessage } = dom;
    if (!promoMessage) return;

    // Mensaje de feedback en la interfaz
    if (percent > 0) {
        const template = texts.promoApplied;
        const message = template.replace('{percent}', String(percent));

        promoMessage.textContent = message;
        promoMessage.hidden = false;
    } else {
        promoMessage.textContent = '';
        promoMessage.hidden = true;
    }
}

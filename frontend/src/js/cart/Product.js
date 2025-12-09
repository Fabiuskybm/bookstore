
// ==================================================
//  PRODUCT MODELS
//  - Product: modelo base del carrito
//  - DiscountedProduct: producto con descuento aplicado
// ==================================================


// ==================================================
//  PRODUCTO BASE
// ==================================================

/**
 * Representa un producto estándar dentro del carrito.
 *
 * Propiedades:
 * - id: string
 * - title: string
 * - price: número unitario
 * - quantity: cantidad, mínimo 1
 *
 * Getter:
 * - lineTotal → price * quantity
 */
class Product {

    constructor({ id, title, price, quantity = 1 }) {
        this.id = String(id);
        this.title = String(title ?? '');
        this.price = Number(price) || 0;

        // La cantidad siempre es un entero ≥ 1
        this.quantity =
            Number.isInteger(quantity) && quantity > 0
                ? quantity
                : 1;
    }

    /**
     * Total de la línea sin descuento.
     */
    get lineTotal() {
        return this.price * this.quantity;
    }
}



// ==================================================
//  PRODUCTO DESCONTADO
// ==================================================

/**
 * Extiende Product añadiendo un porcentaje de descuento.
 *
 * Propiedades adicionales:
 * - discountPercent (0–100)
 *
 * Getters:
 * - discountAmount → cantidad descontada
 * - finalLineTotal → total tras aplicar el descuento
 */
class DiscountedProduct extends Product {
    
    constructor(data, discountPercent = 0) {
        super(data);

        const raw = Number(discountPercent);
        const safe = Number.isFinite(raw) ? raw : 0;

        // Clamp: 0–100
        this.discountPercent = Math.min(100, Math.max(0, safe));
    }

    /**
     * Cantidad descontada sobre el total de la línea.
     */
    get discountAmount() {
        return (this.lineTotal * this.discountPercent) / 100;
    }

    /**
     * Total final tras aplicar el descuento.
     */
    get finalLineTotal() {
        return this.lineTotal - this.discountAmount;
    }
}


export { Product, DiscountedProduct };

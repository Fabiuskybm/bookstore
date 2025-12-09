

// Representa un producto genérico del carrito
class Product {

    constructor({ id, title, price, quantity = 1 }) {
        this.id = String(id);
        this.title = String(title ?? '');
        this.price = Number(price) || 0;
        this.quantity = Number.isInteger(quantity) && quantity > 0
            ? quantity
            : 1;
    }

    get lineTotal() {
        return this.price * this.quantity;
    }

}


// Representa un producto con descuento aplicado (porcentaje)
class DiscountedProduct extends Product {
    
    constructor(data, discountPercent = 0) {
        super(data);

        const raw = Number(discountPercent);
        const safe = Number.isFinite(raw) ? raw : 0;

        this.discountPercent = Math.min(100, Math.max(0, safe));
    }

    get discountAmount() {
        return (this.lineTotal * this.discountPercent) / 100;
    }

    get finalLineTotal() {
        return this.lineTotal - this.discountAmount;
    }
}


export { Product, DiscountedProduct };

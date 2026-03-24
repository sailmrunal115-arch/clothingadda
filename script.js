/* ================= CART ================= */
let cart = [];

/* ================= DOM ELEMENTS ================= */
const cartIcon = document.getElementById("cart-icon");
const cartCount = document.getElementById("cart-count");
const cartModal = document.getElementById("cart-modal");
const closeCart = document.querySelector(".close-cart");
const cartItems = document.getElementById("cart-items");
const totalPrice = document.getElementById("total-price");
const checkoutBtn = document.querySelector(".checkout-btn");

/* ================= INIT ================= */
document.addEventListener("DOMContentLoaded", () => {
    updateCartCount();

    document.querySelectorAll(".add-to-cart").forEach(btn => {
        btn.addEventListener("click", addToCart);
    });

    cartIcon.addEventListener("click", openCart);
    closeCart.addEventListener("click", closeCartModal);
    checkoutBtn.addEventListener("click", checkout);

    window.addEventListener("click", (e) => {
        if (e.target === cartModal) closeCartModal();
    });
});

/* ================= ADD TO CART ================= */
function addToCart(e) {
    const btn = e.target;

    const product = {
        id: btn.dataset.id,
        name: btn.dataset.name,
        price: parseInt(btn.dataset.price),
        quantity: 1
    };

    const existing = cart.find(item => item.id === product.id);

    if (existing) {
        existing.quantity++;
    } else {
        cart.push(product);
    }

    updateCartCount();
    showNotification(`${product.name} added to cart`);
}

/* ================= CART COUNT ================= */
function updateCartCount() {
    cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
}

/* ================= CART MODAL ================= */
function openCart() {
    cartModal.style.display = "block";
    updateCartDisplay();
}

function closeCartModal() {
    cartModal.style.display = "none";
}

/* ================= CART DISPLAY ================= */
function updateCartDisplay() {
    cartItems.innerHTML = "";

    if (cart.length === 0) {
        cartItems.innerHTML = "<p style='text-align:center'>Your cart is empty</p>";
        totalPrice.textContent = "0";
        return;
    }

    let total = 0;

    cart.forEach(item => {
        total += item.price * item.quantity;

        const div = document.createElement("div");
        div.className = "cart-item";

        div.innerHTML = `
            <div class="item-info">
                <h4>${item.name}</h4>
                <p class="item-price">₹${item.price} × ${item.quantity}</p>
            </div>
            <div class="item-quantity">
                <button class="quantity-btn minus" data-id="${item.id}">-</button>
                <span>${item.quantity}</span>
                <button class="quantity-btn plus" data-id="${item.id}">+</button>
            </div>
            <button class="remove-item" data-id="${item.id}">Remove</button>
        `;

        cartItems.appendChild(div);
    });

    totalPrice.textContent = total;

    document.querySelectorAll(".plus, .minus").forEach(btn => {
        btn.addEventListener("click", updateQuantity);
    });

    document.querySelectorAll(".remove-item").forEach(btn => {
        btn.addEventListener("click", removeItem);
    });
}

/* ================= UPDATE QUANTITY ================= */
function updateQuantity(e) {
    const id = e.target.dataset.id;
    const item = cart.find(i => i.id === id);

    if (e.target.classList.contains("plus")) item.quantity++;
    if (e.target.classList.contains("minus") && item.quantity > 1) item.quantity--;

    updateCartCount();
    updateCartDisplay();
}

/* ================= REMOVE ITEM ================= */
function removeItem(e) {
    const id = e.target.dataset.id;
    cart = cart.filter(item => item.id !== id);
    updateCartCount();
    updateCartDisplay();
}

/* ================= CHECKOUT ================= */
function checkout() {
    if (cart.length === 0) {
        alert("Your cart is empty!");
        return;
    }

    const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

    alert(`Order placed successfully!\nTotal Amount: ₹${total}`);
    cart = [];
    updateCartCount();
    updateCartDisplay();
    closeCartModal();
}

/* ================= NOTIFICATION ================= */
function showNotification(msg) {
    const note = document.createElement("div");
    note.textContent = msg;
    note.style.cssText = `
        position:fixed;
        top:100px;
        right:20px;
        background:#27ae60;
        color:white;
        padding:12px 20px;
        border-radius:5px;
        z-index:3000;
    `;
    document.body.appendChild(note);

    setTimeout(() => document.body.removeChild(note), 2500);
}

/* =========================================================================
    tcg.name — Shopping Cart -> Checkout logic
    -------------------------------------------------------------------------
    - Renders cart items from CART_ITEMS
    - Handles quantity +/- and remove
    - Recalculates subtotal / total live
    - Switches from #cartView to #checkoutView ("Proceed to checkout")
    - Validates billing form and POSTs the order to your backend/database
   ========================================================================= */

// ---- 1. CART DATA ---------------------------------------------------------
// Replace this with data pulled from your real cart (session, localStorage,
// or an API call to your backend) when wiring this into the live site.
let CART_ITEMS = [
  {
    id: "sv-280-217-a",
    name: "Lillie's Clefairy ex - 280/217",
    price: 14.00,
    quantity: 5,
    image: "https://placehold.co/60x84/2a2a2a/ffffff?text=Card"
  },
  {
    id: "sv-280-217-b",
    name: "Lillie's Clefairy ex - 280/217",
    price: 14.00,
    quantity: 1,
    image: "https://placehold.co/60x84/2a2a2a/ffffff?text=Card"
  }
];

const currency = (n) => `$${n.toFixed(2)}`;

// ---- 2. RENDER CART TABLE ---------------------------------------------
function renderCart() {
  const body = document.getElementById("cartItemsBody");
  body.innerHTML = "";

  CART_ITEMS.forEach((item, index) => {
    const subtotal = item.price * item.quantity;
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>
        <div class="d-flex align-items-center gap-3">
          <img src="${item.image}" class="cart-item-thumb" alt="${item.name}">
          <span class="cart-item-name text-white">${item.name}</span>
        </div>
      </td>
      <td class="text-white">${currency(item.price)}</td>
      <td class="text-center">
        <div class="qty-control">
          <button type="button" class="qty-btn" data-action="decrease" data-index="${index}">−</button>
          <span class="qty-value" id="qty-${index}">${item.quantity}</span>
          <button type="button" class="qty-btn" data-action="increase" data-index="${index}">+</button>
        </div>
      </td>
      <td class="text-end text-white" id="rowSubtotal-${index}">${currency(subtotal)}</td>
      <td class="text-end">
        <button type="button" class="remove-item-btn" data-action="remove" data-index="${index}">✕</button>
      </td>
    `;
    body.appendChild(row);
  });

  updateTotals();
}

// ---- 3. TOTALS --------------------------------------------------------
function calculateSubtotal() {
  return CART_ITEMS.reduce((sum, item) => sum + item.price * item.quantity, 0);
}

function updateTotals() {
  const subtotal = calculateSubtotal();
  const total = subtotal; // shipping is Free, adjust here if that changes

  const sumSubtotalEl = document.getElementById("sumSubtotal");
  const sumTotalEl = document.getElementById("sumTotal");
  if (sumSubtotalEl) sumSubtotalEl.textContent = currency(subtotal);
  if (sumTotalEl) sumTotalEl.textContent = currency(total);
}

// ---- 4. QUANTITY / REMOVE HANDLERS ------------------------------------
document.getElementById("cartItemsBody").addEventListener("click", (e) => {
  const btn = e.target.closest("button[data-action]");
  if (!btn) return;

  const index = Number(btn.dataset.index);
  const action = btn.dataset.action;

  if (action === "increase") {
    CART_ITEMS[index].quantity += 1;
  } else if (action === "decrease") {
    CART_ITEMS[index].quantity = Math.max(1, CART_ITEMS[index].quantity - 1);
  } else if (action === "remove") {
    CART_ITEMS.splice(index, 1);
  }

  renderCart();
});

document.getElementById("updateCartBtn").addEventListener("click", () => {
  // Placeholder: call your backend here to persist the updated quantities
  // e.g. fetch('/api/cart/update', { method: 'POST', body: JSON.stringify(CART_ITEMS) })
  renderCart();
});

document.getElementById("returnToShopBtn").addEventListener("click", () => {
  window.location.href = "/tcgzone/customer/Shop Page/shop-page.html"; // point this to your actual shop page
});

// ---- 5. VIEW SWITCHING (Cart <-> Checkout) -----------------------------
function showCheckout() {
  if (CART_ITEMS.length === 0) return; // don't let an empty cart proceed

  document.getElementById("cartView").classList.add("d-none");
  document.getElementById("checkoutView").classList.remove("d-none");
  renderCheckoutSummary();
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function showCart() {
  document.getElementById("checkoutView").classList.add("d-none");
  document.getElementById("cartView").classList.remove("d-none");
  window.scrollTo({ top: 0, behavior: "smooth" });
}

document.getElementById("proceedToCheckoutBtn").addEventListener("click", showCheckout);
document.getElementById("backToCartBtn").addEventListener("click", showCart);

// ---- 6. CHECKOUT ORDER SUMMARY ------------------------------------------
function renderCheckoutSummary() {
  const list = document.getElementById("checkoutItemsList");
  list.innerHTML = "";

  CART_ITEMS.forEach((item) => {
    const line = document.createElement("div");
    line.className = "summary-line";
    line.innerHTML = `
      <img src="${item.image}" alt="${item.name}">
      <div class="name">${item.name} x${item.quantity}</div>
      <div class="price">${currency(item.price * item.quantity)}</div>
    `;
    list.appendChild(line);
  });

  const subtotal = calculateSubtotal();
  document.getElementById("checkoutSubtotal").textContent = currency(subtotal);
  document.getElementById("checkoutTotal").textContent = currency(subtotal);
}

// ---- 7. BILLING FORM VALIDATION + SUBMIT TO DATABASE --------------------
const billingForm = document.getElementById("billingForm");
const placeOrderBtn = document.getElementById("placeOrderBtn");
const placeOrderStatus = document.getElementById("placeOrderStatus");
const billingFormError = document.getElementById("billingFormError");

function getBillingData() {
  const formData = new FormData(billingForm);
  return {
    firstName: formData.get("firstName")?.trim(),
    lastName: formData.get("lastName")?.trim(),
    streetAddress: formData.get("streetAddress")?.trim(),
    zipCode: formData.get("zipCode")?.trim(),
    province: formData.get("province")?.trim(),
    city: formData.get("city")?.trim(),
    houseNumber: formData.get("houseNumber")?.trim(),
    email: formData.get("email")?.trim(),
    phone: formData.get("phone")?.trim(),
    orderNotes: formData.get("orderNotes")?.trim() || "",
    paymentMethod: formData.get("paymentMethod") || "cod"
  };
}

function validateBilling(data) {
  const required = ["firstName", "lastName", "streetAddress", "zipCode", "province", "city", "houseNumber", "email", "phone"];
  const missing = required.filter((field) => !data[field]);

  const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email || "");

  return missing.length === 0 && emailOk;
}

placeOrderBtn.addEventListener("click", async () => {
  const billingData = getBillingData();

  if (!validateBilling(billingData)) {
    billingFormError.classList.remove("d-none");
    billingForm.reportValidity();
    return;
  }
  billingFormError.classList.add("d-none");

  const order = {
    customer: billingData,
    items: CART_ITEMS.map((item) => ({
      id: item.id,
      name: item.name,
      price: item.price,
      quantity: item.quantity,
      subtotal: item.price * item.quantity
    })),
    subtotal: calculateSubtotal(),
    shipping: 0,
    total: calculateSubtotal(),
    createdAt: new Date().toISOString()
  };

  placeOrderBtn.disabled = true;
  placeOrderStatus.textContent = "Placing your order...";
  placeOrderStatus.classList.remove("text-danger", "text-success");

  try {
    await sendOrderToDatabase(order);
    placeOrderStatus.textContent = "Order placed! We'll email you a confirmation.";
    placeOrderStatus.classList.add("text-success");
    billingForm.reset();
  } catch (err) {
    console.error(err);
    placeOrderStatus.textContent = "Something went wrong placing your order. Please try again.";
    placeOrderStatus.classList.add("text-danger");
  } finally {
    placeOrderBtn.disabled = false;
  }
});

// ---- 8. DATABASE CALL ----------------------------------------------------
// This is the single place that talks to your backend. Point it at your
// real API route (e.g. an Express/PHP/Node endpoint) that inserts the
// customer + order info into your database (MySQL, Postgres, MongoDB, etc).
async function sendOrderToDatabase(order) {
  const response = await fetch("/api/orders", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(order)
  });

  if (!response.ok) {
    throw new Error(`Server responded with ${response.status}`);
  }

  return response.json();
}

// ---- 9. INIT --------------------------------------------------------------
renderCart();

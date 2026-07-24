/* =========================================================================
    tcg.name — Shopping Cart -> Checkout logic
    -------------------------------------------------------------------------
    - Renders cart items from CART_ITEMS
    - Handles quantity +/- and remove
    - Recalculates subtotal / total live
    - Switches from #cartView to #checkoutView ("Proceed to checkout")
    - Validates billing form and POSTs the order to your backend/database
   ========================================================================= */

// ---- 1. CART DATA ----------------------------------------------------
let CART_ITEMS = [];

async function loadCart() {
  const response = await fetch("get_cart.php");
  const result = await response.json();

  if (!result.success) {
    console.error("Could not load cart:", result.message);
    CART_ITEMS = [];
  } else {
    CART_ITEMS = result.items;
  }

  renderCart();
}

const currency = (n) => `₱${n.toFixed(2)}`;

function showToast(message, type = "success") {
    let container = document.getElementById("toast-container");

    if (!container) {
        container = document.createElement("div");
        container.id = "toast-container";
        container.className = "app-toast-container";
        document.body.appendChild(container);
    }

    container.innerHTML = "";

    const toast = document.createElement("div");
    toast.className = "app-toast " + type;

    toast.innerHTML = `
        <span class="toast-icon">${type === "success" ? "✓" : "!"}</span>
        <span>${message}</span>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add("toast-out");
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

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

const SHIPPING_FEE = 150;
let paymentConfirmed = false;

function updateTotals() {
  const subtotal = calculateSubtotal();
  const total = subtotal + SHIPPING_FEE;

  const sumSubtotalEl = document.getElementById("sumSubtotal");
  const sumTotalEl = document.getElementById("sumTotal");
  const sumShippingEl = document.getElementById("sumShipping");
  if (sumSubtotalEl) sumSubtotalEl.textContent = currency(subtotal);
  if (sumShippingEl) sumShippingEl.textContent = currency(SHIPPING_FEE);
  if (sumTotalEl) sumTotalEl.textContent = currency(total);
}

// ---- 4. QUANTITY / REMOVE HANDLERS (persist to DB on every click) ------
document.getElementById("cartItemsBody").addEventListener("click", async (e) => {
  const btn = e.target.closest("button[data-action]");
  if (!btn) return;

  const index = Number(btn.dataset.index);
  const action = btn.dataset.action;
  const item = CART_ITEMS[index];
  if (!item) return;

  btn.disabled = true;

  try {
    if (action === "increase") {
      const success = await updateCartItemQuantity(item.cartItemId, item.quantity + 1);
      if (!success) {
        btn.disabled = false;   // ← add this
        return;
      }
    }
    else if (action === "decrease") {
      const newQty = Math.max(1, item.quantity - 1);
      const success = await updateCartItemQuantity(item.cartItemId, newQty);
      if (!success) {
        btn.disabled = false;   // ← add this
        return;
      }
    }
    else if (action === "remove") {
      await removeCartItem(item.cartItemId);
    }
  } catch (err) {
    console.error(err);
    showToast(err.message || "Something went wrong updating your cart.", "error");
    btn.disabled = false;   // ← add this too, same reasoning for network errors
    return;
  }

  await loadCart();
});

async function updateCartItemQuantity(cartItemId, quantity) {
  const response = await fetch("update_cart_item.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ cartItemId, quantity })
  });

  const result = await response.json();

  if (!result.success) {
    showToast(result.message || "Update failed", "error");
    return false;
  }

  return true;
}

async function removeCartItem(cartItemId) {
  const response = await fetch("remove_cart_item.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ cartItemId })
  });
  const result = await response.json();
  if (!result.success) throw new Error(result.message || "Remove failed");
}
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
  const total = subtotal + SHIPPING_FEE;
  document.getElementById("checkoutSubtotal").textContent = currency(subtotal);
  document.getElementById("checkoutShipping").textContent = currency(SHIPPING_FEE);
  document.getElementById("checkoutTotal").textContent = currency(total);
}

// ---- 7. BILLING FORM VALIDATION + SUBMIT TO DATABASE --------------------
const billingForm = document.getElementById("billingForm");
const placeOrderBtn = document.getElementById("placeOrderBtn");
const placeOrderStatus = document.getElementById("placeOrderStatus");
const billingFormError = document.getElementById("billingFormError");
const gcashPaymentInfo = document.getElementById("gcashPaymentInfo");
const gcashConfirmBtn = document.getElementById("gcashConfirmBtn");
const paymentMethodInputs = document.querySelectorAll('input[name="paymentMethod"]');

function syncPaymentMethodUI() {
  const selectedPaymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value || "cod";

  if (selectedPaymentMethod === "gcash") {
    gcashPaymentInfo.classList.remove("d-none");
    placeOrderBtn.disabled = true;
    paymentConfirmed = false;
    placeOrderStatus.textContent = "Scan the QR, then confirm your GCash payment to place the order.";
    placeOrderStatus.classList.remove("text-danger", "text-success");
    placeOrderStatus.classList.add("text-secondary");
  } else {
    gcashPaymentInfo.classList.add("d-none");
    paymentConfirmed = true;
    placeOrderBtn.disabled = false;
    placeOrderStatus.textContent = "";
    placeOrderStatus.classList.remove("text-danger", "text-success", "text-secondary");
  }
}

paymentMethodInputs.forEach((input) => {
  input.addEventListener("change", syncPaymentMethodUI);
});

gcashConfirmBtn.addEventListener("click", () => {
  paymentConfirmed = true;
  placeOrderBtn.disabled = false;
  placeOrderStatus.textContent = "GCash payment confirmed. You can now place your order.";
  placeOrderStatus.classList.remove("text-danger", "text-secondary");
  placeOrderStatus.classList.add("text-success");
});

function getBillingData() {
  const formData = new FormData(billingForm);
  const selectedPaymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value || "cod";

  return {
    firstName: formData.get("firstName")?.trim(),
    lastName: formData.get("lastName")?.trim(),
    zipCode: formData.get("zipCode")?.trim(),
    province: formData.get("province")?.trim(),
    city: formData.get("city")?.trim(),
    houseNumber: formData.get("houseNumber")?.trim(),
    email: formData.get("email")?.trim(),
    phone: formData.get("phone")?.trim(),
    orderNotes: formData.get("orderNotes")?.trim() || "",
    paymentMethod: selectedPaymentMethod
  };
}

function validateBilling(data) {
  const required = ["firstName", "lastName", "zipCode", "province", "city", "houseNumber", "email", "phone"];
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

  if (billingData.paymentMethod === "gcash" && !paymentConfirmed) {
    placeOrderStatus.textContent = "Please scan the QR and confirm your GCash payment before placing the order.";
    placeOrderStatus.classList.add("text-danger");
    return;
  }

  billingFormError.classList.add("d-none");

  placeOrderBtn.disabled = true;
  placeOrderStatus.textContent = "Placing your order...";
  placeOrderStatus.classList.remove("text-danger", "text-success");

  try {
    const result = await sendOrderToDatabase(billingData);
    billingForm.reset();
    paymentConfirmed = false;
    syncPaymentMethodUI();
    showToast("Order has been placed", "success");
    setTimeout(() => {
      window.location.href = "/tcgzone/customer/My Order Page/my-orders.php";
    }, 1500);
  } catch (err) {
    console.error(err);
    placeOrderStatus.textContent = err.message || "Something went wrong placing your order. Please try again.";
    placeOrderStatus.classList.add("text-danger");
  } finally {
    placeOrderBtn.disabled = false;
  }
});

// ---- 8. DATABASE CALL ----------------------------------------------------
async function sendOrderToDatabase(billingData) {
  const response = await fetch("place_order.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(billingData)
  });

  const result = await response.json();

  if (!result.success) {
    throw new Error(result.message || `Server responded with ${response.status}`);
  }

  return result;
}

// ---- 9. INIT --------------------------------------------------------------
loadCart();
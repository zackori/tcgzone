/* =========================================================================
   My Orders — fetches this user's orders, splits into Ongoing/Completed
   (same status grouping the admin dashboard uses), renders each order as
   a card. Item rows are built with the exact same row pattern as
   shopping-cart.js's renderCart() — image + name, quantity, subtotal —
   just without the +/-/remove controls, since these are past orders.
   ========================================================================= */

let ALL_ORDERS = [];
let activeTab = "ongoing";
let pendingCancelOrderId = null;

const currency = (n) => `₱${Number(n).toFixed(2)}`;

async function loadOrders() {
  try {
    const response = await fetch("get_my_orders.php");
    const result = await response.json();

    if (!result.success) {
      console.error("Could not load orders:", result.message);
      ALL_ORDERS = [];
    } else {
      ALL_ORDERS = result.orders;
    }
  } catch (err) {
    console.error(err);
    ALL_ORDERS = [];
  }

  renderOrders();
}

function isOngoing(order) {
  return ["Pending", "Processing", "In Transit"].includes(order.status);
}
function isCompleted(order) {
  return ["Delivered", "Cancelled"].includes(order.status);
}

function statusClass(status) {
  return "status-" + status.toLowerCase().replace(/\s+/g, "-");
}

function renderCancelAction(order) {
  if (order.status !== "Pending") {
    return "";
  }

  const cancelRequest = order.cancelRequest;

  if (cancelRequest?.status === "Pending") {
    return `<div class="cancel-request-note">Cancellation request pending review</div>`;
  }

  if (cancelRequest?.status === "Rejected") {
    return `
      <div class="cancel-request-note rejected">Cancellation request rejected</div>
      <button type="button" class="btn-cancel-order" data-order-id="${order.orderId}">Request Cancel</button>
    `;
  }

  return `<button type="button" class="btn-cancel-order" data-order-id="${order.orderId}">Request Cancel</button>`;
}

function renderOrderItemRows(items) {
  return items.map((item) => `
    <tr>
      <td>
        <div class="d-flex align-items-center gap-3">
          <img src="${item.image}" class="order-item-thumb" alt="${item.name}">
          <span class="order-item-name">${item.name}</span>
        </div>
      </td>
      <td>${item.quantity}x</td>
      <td>${currency(item.subtotal)}</td>
    </tr>
  `).join("");
}

function renderOrderCard(order) {
  return `
    <div class="order-card">
      <table class="order-items-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          ${renderOrderItemRows(order.items)}
        </tbody>
      </table>

      <div class="order-footer">
        <div>
          <div class="order-id">Order ID #${order.orderId}</div>
          <div class="order-status ${statusClass(order.status)}">${order.status}</div>
          ${renderCancelAction(order)}
        </div>
        <div class="order-summary">
          <div class="summary-row"><span>Subtotal</span><strong>${currency(order.subtotal)}</strong></div>
          <div class="summary-row"><span>Shipping Fee</span><strong>${order.shippingFee > 0 ? currency(order.shippingFee) : "Free"}</strong></div>
          <div class="summary-row"><span>Total</span><strong class="grand-total">${currency(order.total)}</strong></div>
        </div>
      </div>
    </div>
  `;
}

function renderOrders() {
  const list = document.getElementById("ordersList");
  const noOrdersMsg = document.getElementById("noOrdersMsg");

  const filtered = ALL_ORDERS.filter(activeTab === "ongoing" ? isOngoing : isCompleted);

  if (filtered.length === 0) {
    list.innerHTML = "";
    noOrdersMsg.classList.remove("d-none");
    return;
  }

  noOrdersMsg.classList.add("d-none");
  list.innerHTML = filtered.map(renderOrderCard).join("");

  list.querySelectorAll(".btn-cancel-order").forEach((btn) => {
    btn.addEventListener("click", () => openCancelModal(Number(btn.dataset.orderId)));
  });
}

function openCancelModal(orderId) {
  pendingCancelOrderId = orderId;
  document.getElementById("cancelModalOrderId").textContent = `#${orderId}`;
  document.getElementById("cancelModalMsg").classList.add("d-none");
  document.getElementById("cancelModalMsg").textContent = "";
  document.getElementById("cancelReasons").classList.remove("d-none");
  document.getElementById("cancelModal").classList.remove("d-none");
}

function closeCancelModal() {
  pendingCancelOrderId = null;
  document.getElementById("cancelModal").classList.add("d-none");
}

async function submitCancelRequest(reason) {
  if (!pendingCancelOrderId) {
    return;
  }

  const msgEl = document.getElementById("cancelModalMsg");
  msgEl.classList.remove("d-none", "success", "error");
  msgEl.textContent = "Submitting request...";

  try {
    const response = await fetch("create_cancel_request.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        order_id: pendingCancelOrderId,
        reason
      })
    });

    const result = await response.json();

    if (result.success) {
      msgEl.classList.add("success");
      msgEl.textContent = result.message;
      document.getElementById("cancelReasons").classList.add("d-none");

      setTimeout(async () => {
        closeCancelModal();
        await loadOrders();
      }, 1200);
    } else {
      msgEl.classList.add("error");
      msgEl.textContent = result.message || "Could not submit cancellation request.";
    }
  } catch (err) {
    console.error(err);
    msgEl.classList.add("error");
    msgEl.textContent = "Something went wrong. Please try again.";
  }
}

document.getElementById("ongoingTab").addEventListener("click", () => {
  activeTab = "ongoing";
  document.getElementById("ongoingTab").classList.add("active");
  document.getElementById("completedTab").classList.remove("active");
  renderOrders();
});

document.getElementById("completedTab").addEventListener("click", () => {
  activeTab = "completed";
  document.getElementById("completedTab").classList.add("active");
  document.getElementById("ongoingTab").classList.remove("active");
  renderOrders();
});

document.getElementById("cancelModalClose").addEventListener("click", closeCancelModal);

document.getElementById("cancelModal").addEventListener("click", (event) => {
  if (event.target.id === "cancelModal") {
    closeCancelModal();
  }
});

document.querySelectorAll(".cancel-reason-btn").forEach((btn) => {
  btn.addEventListener("click", () => submitCancelRequest(btn.dataset.reason));
});

loadOrders();

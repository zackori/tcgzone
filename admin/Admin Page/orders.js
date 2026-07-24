// =========================================
// orders.js
// =========================================
function setupOverviewCardRedirects() {
    const totalOrdersCard = document.getElementById("totalOrdersCard");
    const completedOrdersCard = document.getElementById("completedOrdersCard");
    const pendingOrdersCard = document.getElementById("pendingOrdersCard");
    const statusFilter = document.getElementById("statusFilter");

    if (totalOrdersCard) {
        const filterAll = () => {
            statusFilter.value = "all";
            filterOrders();
        };
        totalOrdersCard.addEventListener("click", filterAll);
        totalOrdersCard.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") { e.preventDefault(); filterAll (); }
        });
    }

    if (completedOrdersCard) {
        const filterCompleted = () => {
            statusFilter.value = "Delivered";
            filterOrders();
        };
        completedOrdersCard.addEventListener("click", filterCompleted);
        completedOrdersCard.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") { e.preventDefault(); filterCompleted(); }
        });
    }

    if (pendingOrdersCard) {
        const filterOngoing = () => {
            statusFilter.value = "ongoing";
            filterOrders();
        };
        pendingOrdersCard.addEventListener("click", filterOngoing);
        pendingOrdersCard.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") { e.preventDefault(); filterOngoing(); }
        });
    }
}

// Call this function inside loadOrders() or at startup:
setupOverviewCardRedirects();


let allOrders = [];
let allCancelRequests = [];
let activeCancelRequest = null;
let activeOrderDetailsId = null;

const formatCurrency = (amount) => `₱${Number(amount).toLocaleString(undefined, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
})}`;

function getStatusClass(status) {
    switch (status) {
        case "Pending":
            return "pending";
        case "Processing":
            return "processing";
        case "In Transit":
            return "transit";
        case "Delivered":
            return "delivered";
        case "Cancelled":
            return "cancelled";
        default:
            return "";
    }
}

function escapeHtml(value = "") {
    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

async function loadOrders() {
    try {
        const [ordersResponse, cancelResponse] = await Promise.all([
            fetch("api/get-order.php"),
            fetch("api/get_cancel_requests.php")
        ]);

        allOrders = await ordersResponse.json();

        const cancelResult = await cancelResponse.json();
        allCancelRequests = cancelResult.success ? cancelResult.requests : [];

        updateCards(allOrders, allCancelRequests);
        renderCancelRequestsList(allCancelRequests);
        displayOrders(allOrders);
        applyUrlStatusFilter();

    } catch (error) {
        console.error("Failed to load orders:", error);
    }
}

function updateCards(orders, cancelRequests) {
    document.getElementById("totalOrders").textContent = orders.length;

    const ongoing = orders.filter(order =>
        ["Pending", "Processing", "In Transit"].includes(order.status)
    ).length;

    const completed = orders.filter(order =>
        ["Delivered"].includes(order.status)
    ).length;

    const pendingCancelRequests = cancelRequests.filter(request =>
        request.status === "Pending"
    ).length;

    document.getElementById("ongoingOrders").textContent = ongoing;
    document.getElementById("completedOrders").textContent = completed;
    document.getElementById("cancelRequestCount").textContent = pendingCancelRequests;
}

function cancelRequestBadgeClass(status) {
    switch (status) {
        case "Pending":
            return "pending";
        case "Approved":
            return "approved";
        case "Rejected":
            return "rejected";
        default:
            return "";
    }
}

function renderCancelRequestsList(requests) {
    const grid = document.getElementById("cancelRequestsGrid");
    const emptyMsg = document.getElementById("cancelRequestsEmptyMsg");

    if (requests.length === 0) {
        grid.innerHTML = "";
        emptyMsg.classList.remove("d-none");
        return;
    }

    emptyMsg.classList.add("d-none");

    grid.innerHTML = requests.map((request) => `
        <button
            type="button"
            class="cancel-request-card"
            data-request-id="${request.cancel_request_id}"
        >
            <div class="cancel-request-card-top">
                <span class="cancel-request-label">Cancel Request</span>
                <span class="cancel-request-badge ${cancelRequestBadgeClass(request.status)}">${request.status}</span>
            </div>
            <h4>Order #${request.order_id}</h4>
            <p>${request.customer_name || "Unknown customer"}</p>
            <p class="cancel-request-reason">${request.reason}</p>
            <span class="cancel-request-date">${request.requested_at}</span>
        </button>
    `).join("");

    grid.querySelectorAll(".cancel-request-card").forEach((card) => {
        card.addEventListener("click", () => {
            const requestId = Number(card.dataset.requestId);
            const request = allCancelRequests.find(
                (item) => Number(item.cancel_request_id) === requestId
            );
            if (request) {
                closeCancelRequestsListModal();
                openCancelRequestModal(request);
            }
        });
    });
}

function openCancelRequestsListModal() {
    renderCancelRequestsList(allCancelRequests);
    document.getElementById("cancelRequestsListModal").classList.remove("d-none");
}

function closeCancelRequestsListModal() {
    document.getElementById("cancelRequestsListModal").classList.add("d-none");
}

function openCancelRequestModal(request) {
    activeCancelRequest = request;

    document.getElementById("modalOrderId").textContent = `#${request.order_id}`;
    document.getElementById("modalReason").textContent = request.reason;
    document.getElementById("modalRequestedAt").textContent = request.requested_at;
    document.getElementById("modalStatus").textContent = request.status;

    const reviewActions = document.getElementById("modalReviewActions");
    const msgEl = document.getElementById("cancelRequestModalMsg");

    msgEl.classList.add("d-none");
    msgEl.textContent = "";

    if (request.status === "Pending") {
        reviewActions.classList.remove("d-none");
    } else {
        reviewActions.classList.add("d-none");
    }

    document.getElementById("cancelRequestModal").classList.remove("d-none");
}

function closeCancelRequestModal() {
    activeCancelRequest = null;
    document.getElementById("cancelRequestModal").classList.add("d-none");
}

async function reviewCancelRequest(action) {
    if (!activeCancelRequest) {
        return;
    }

    const msgEl = document.getElementById("cancelRequestModalMsg");
    msgEl.classList.remove("d-none", "success", "error");
    msgEl.textContent = "Updating request...";

    try {
        const response = await fetch("api/update_cancel_request.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                cancel_request_id: activeCancelRequest.cancel_request_id,
                action
            })
        });

        const result = await response.json();

        if (result.success) {
            msgEl.classList.add("success");
            msgEl.textContent = action === "approve"
                ? "Cancel request accepted. Order has been cancelled."
                : "Cancel request rejected.";

            document.getElementById("modalReviewActions").classList.add("d-none");
            document.getElementById("modalStatus").textContent = result.status;

            await loadOrders();
        } else {
            msgEl.classList.add("error");
            msgEl.textContent = result.message || "Could not update cancel request.";
        }

    } catch (error) {
        console.error(error);
        msgEl.classList.add("error");
        msgEl.textContent = "Something went wrong. Please try again.";
    }
}

function displayOrders(orders) {
    const tbody = document.getElementById("ordersTable");
    tbody.innerHTML = "";

    if (orders.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center">
                    No orders found.
                </td>
            </tr>
        `;
        return;
    }

    orders.forEach(order => {
        const badgeClass = getStatusClass(order.status);

        tbody.innerHTML += `
        <tr class="order-row-clickable" data-order-id="${order.order_id}">
            <td>${order.order_id}</td>
            <td>${escapeHtml(order.customer_name || "Unknown customer")}</td>
            <td>${escapeHtml([order.address1, order.address2].filter(Boolean).join(", "))}</td>
            <td>${formatCurrency(order.total_amount)}</td>
            <td>${escapeHtml((order.payment_method || "cod").toUpperCase())}</td>
            <td><span class="modal-status-badge ${badgeClass}">${escapeHtml(order.status)}</span></td>
            <td>${escapeHtml(order.order_date || "")}</td>
        </tr>
        `;
    });
}

document.getElementById("searchOrder").addEventListener("keyup", filterOrders);
document.getElementById("statusFilter").addEventListener("change", filterOrders);

function filterOrders() {
    const keyword = document.getElementById("searchOrder").value.toLowerCase();
    const status = document.getElementById("statusFilter").value;

    const filtered = allOrders.filter(order => {
        const matchesSearch =
            (order.customer_name && order.customer_name.toLowerCase().includes(keyword)) ||
            (order.address1 && order.address1.toLowerCase().includes(keyword)) ||
            (order.address2 && order.address2.toLowerCase().includes(keyword)) ||
            order.order_id.toString().includes(keyword);

        let matchesStatus = false;

        if (status === "all") {
            matchesStatus = true;
        } else if (status === "ongoing") {
            matchesStatus = ["Pending", "Processing", "In Transit"].includes(order.status);
        } else {
            matchesStatus = order.status === status;
        }

        return matchesSearch && matchesStatus;
    });

    displayOrders(filtered);
}

function applyUrlStatusFilter() {
    const urlStatus = new URLSearchParams(window.location.search).get("status");
    const statusFilter = document.getElementById("statusFilter");

    if (!statusFilter || !urlStatus) {
        return;
    }

    const validStatuses = ["all", "Pending", "Processing", "In Transit", "Delivered", "Cancelled"];

    if (validStatuses.includes(urlStatus)) {
        statusFilter.value = urlStatus;
        filterOrders();
    }
}

document.getElementById("cancelRequestCard").addEventListener("click", openCancelRequestsListModal);
document.getElementById("cancelRequestCard").addEventListener("keydown", (event) => {
    if (event.key === "Enter" || event.key === " ") {
        event.preventDefault();
        openCancelRequestsListModal();
    }
});

document.getElementById("closeCancelRequestsListModal").addEventListener("click", closeCancelRequestsListModal);
document.getElementById("cancelRequestsListModal").addEventListener("click", (event) => {
    if (event.target.id === "cancelRequestsListModal") {
        closeCancelRequestsListModal();
    }
});

document.getElementById("closeCancelRequestModal").addEventListener("click", closeCancelRequestModal);
document.getElementById("cancelRequestModal").addEventListener("click", (event) => {
    if (event.target.id === "cancelRequestModal") {
        closeCancelRequestModal();
    }
});
document.getElementById("acceptCancelRequest").addEventListener("click", () => reviewCancelRequest("approve"));
document.getElementById("rejectCancelRequest").addEventListener("click", () => reviewCancelRequest("reject"));

document.getElementById("closeOrderDetailsModal").addEventListener("click", closeOrderDetailsModal);
document.getElementById("orderDetailsModal").addEventListener("click", (event) => {
    if (event.target.id === "orderDetailsModal") {
        closeOrderDetailsModal();
    }
});

document.getElementById("updateOrderDetailsStatus").addEventListener("click", async () => {
    if (!activeOrderDetailsId) return;

    const message = document.getElementById("orderDetailsMsg");
    const button = document.getElementById("updateOrderDetailsStatus");
    const status = document.getElementById("orderDetailsStatusSelect").value;
    button.disabled = true;
    message.className = "modal-msg";
    message.textContent = "Updating status...";

    const success = await updateStatus(activeOrderDetailsId, status);
    button.disabled = false;

    if (success) {
        await openOrderDetailsModal(activeOrderDetailsId);
        message.className = "modal-msg success";
        message.textContent = "Order status updated.";
    }
});

document.getElementById("ordersTable").addEventListener("click", (event) => {
    const row = event.target.closest("tr.order-row-clickable");

    if (!row) {
        return;
    }

    const orderId = Number(row.dataset.orderId);

    if (!Number.isNaN(orderId)) {
        openOrderDetailsModal(orderId);
    }
});

loadOrders();

async function openOrderDetailsModal(orderId) {
    activeOrderDetailsId = Number(orderId);
    const modal = document.getElementById("orderDetailsModal");
    const titleEl = document.getElementById("orderDetailsTitle");
    const customerEl = document.getElementById("orderDetailsCustomer");
    const statusEl = document.getElementById("orderDetailsStatus");
    const paymentEl = document.getElementById("orderDetailsPayment");
    const dateEl = document.getElementById("orderDetailsDate");
    const subtotalEl = document.getElementById("orderDetailsSubtotal");
    const shippingEl = document.getElementById("orderDetailsShipping");
    const totalEl = document.getElementById("orderDetailsTotal");
    const itemsEl = document.getElementById("orderDetailsItems");
    const msgEl = document.getElementById("orderDetailsMsg");
    const statusSelect = document.getElementById("orderDetailsStatusSelect");

    titleEl.textContent = "Loading order details...";
    customerEl.textContent = "—";
    statusEl.textContent = "—";
    paymentEl.textContent = "—";
    dateEl.textContent = "—";
    subtotalEl.textContent = "₱0";
    shippingEl.textContent = "₱0";
    totalEl.textContent = "₱0";
    itemsEl.innerHTML = "";
    statusSelect.disabled = true;
    document.getElementById("updateOrderDetailsStatus").disabled = true;
    msgEl.classList.add("d-none");
    msgEl.textContent = "";
    modal.classList.remove("d-none");

    try {
        const response = await fetch(`api/get_order_items.php?order_id=${encodeURIComponent(orderId)}`);
        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || "Could not load order details.");
        }

        const order = result.order || {};
        titleEl.textContent = `Order Details #${order.order_id || orderId}`;
        customerEl.textContent = order.customer_name || "Unknown customer";
        statusEl.className = "modal-status-badge";

        if (order.status) {
            statusEl.classList.add(getStatusClass(order.status));
            statusEl.textContent = order.status;
            statusSelect.value = order.status;
            const isFinalStatus = ["Delivered", "Cancelled"].includes(order.status);
            statusSelect.disabled = isFinalStatus;
            document.getElementById("updateOrderDetailsStatus").disabled = isFinalStatus;
        } else {
            statusEl.textContent = "—";
        }

        paymentEl.textContent = (order.payment_method || "cod").toUpperCase();
        dateEl.textContent = order.order_date || "—";
        subtotalEl.textContent = formatCurrency(order.subtotal_amount || 0);
        shippingEl.textContent = formatCurrency(order.shipping_fee || 0);
        totalEl.textContent = formatCurrency(order.total_amount || 0);

        if (!order.items || order.items.length === 0) {
            itemsEl.innerHTML = `
                <tr>
                    <td colspan="3" class="modal-empty-msg">No items found for this order.</td>
                </tr>
            `;
            return;
        }

        itemsEl.innerHTML = order.items.map((item) => `
            <tr>
                <td>
                    <div class="order-item-cell">
                        <img src="${escapeHtml(item.image || "")}" class="order-item-thumb" alt="${escapeHtml(item.name || "Product")}">
                        <span class="order-item-name">${escapeHtml(item.name || "Unknown product")}</span>
                    </div>
                </td>
                <td class="order-item-qty">${Number(item.quantity || 0)}x</td>
                <td class="order-item-subtotal">${formatCurrency(item.subtotal || 0)}</td>
            </tr>
        `).join("");
    } catch (error) {
        console.error(error);
        msgEl.classList.remove("d-none");
        msgEl.classList.add("error");
        msgEl.textContent = error.message || "Could not load order details.";
    }
}

function closeOrderDetailsModal() {
    activeOrderDetailsId = null;
    document.getElementById("orderDetailsModal").classList.add("d-none");
}

async function updateStatus(orderId, status) {
    try {
        const response = await fetch("api/update_order_status.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                order_id: orderId,
                status: status
            })
        });

        const result = await response.json();

        if (result.success) {
            const order = allOrders.find(o => o.order_id == orderId);

            if (order) {
                order.status = status;
            }

            updateCards(allOrders, allCancelRequests);
            filterOrders();
            return true;
        } else {
            const message = document.getElementById("orderDetailsMsg");
            message.className = "modal-msg error";
            message.textContent = result.message || "Could not update order status.";
            return false;
        }

    } catch (error) {
        console.error(error);
        const message = document.getElementById("orderDetailsMsg");
        message.className = "modal-msg error";
        message.textContent = "Could not update order status.";
        return false;
    }
}

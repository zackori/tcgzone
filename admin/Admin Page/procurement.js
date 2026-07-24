let procurementOrders = [];
let lowStockProducts = [];
let activeProcurementOrder = null;

const currency = (value) => `₱${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
const escapeHtml = (value = "") => String(value).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#39;");
const statusClass = (status) => ({ "Pending": "pending", "Processing": "processing", "In Transit": "transit", "Delivered": "delivered", "Cancelled": "cancelled" }[status] || "");

function renderOrders(orders) {
    const table = document.getElementById("procurementTable");
    if (!orders.length) { table.innerHTML = '<tr><td colspan="5" class="text-center">No procurement receipts found.</td></tr>'; return; }
    table.innerHTML = orders.map((order) => `
        <tr class="order-row-clickable" data-order-id="${Number(order.procurement_order_id)}">
            <td>${Number(order.procurement_order_id)}</td><td>${escapeHtml(order.supplier_name)}</td><td>${currency(order.total_amount)}</td>
            <td><span class="modal-status-badge ${statusClass(order.order_status)}">${escapeHtml(order.order_status)}</span></td><td>${escapeHtml(order.order_date)}</td>
        </tr>`).join("");
}

function filterOrders() {
    const keyword = document.getElementById("searchProcurement").value.trim().toLowerCase();
    const status = document.getElementById("procurementStatusFilter").value;
    renderOrders(procurementOrders.filter((order) =>
        (status === "all" || order.order_status === status) &&
        [order.procurement_order_id, order.supplier_name, order.order_status].some((value) => String(value || "").toLowerCase().includes(keyword))
    ));
}

async function loadProcurement() {
    try {
        const response = await fetch("api/get-procurement.php");
        if (!response.ok) throw new Error("Could not load procurement receipts.");
        procurementOrders = await response.json();
        document.getElementById("buyRequestCount").textContent = procurementOrders.filter((order) => order.order_status === "Pending").length;
        filterOrders();
    } catch (error) {
        document.getElementById("procurementTable").innerHTML = '<tr><td colspan="5" class="text-center">Could not load procurement receipts.</td></tr>';
    }
}

function renderLowStock(products) {
    const table = document.getElementById("lowStockTable");
    if (!products.length) { table.innerHTML = '<tr><td colspan="10" class="text-center">No cards are low in stock.</td></tr>'; return; }
    table.innerHTML = products.map((product) => {
        return `<tr data-product-id="${Number(product.product_id)}">
            <td><input class="resupply-checkbox" type="checkbox" aria-label="Resupply ${escapeHtml(product.card_name)}"></td>
            <td>${escapeHtml(product.card_name)}</td><td>${escapeHtml(product.set_name)}</td><td>${escapeHtml(product.category)}</td>
            <td>${escapeHtml(product.product_type)}</td><td>${escapeHtml(product.rarity || "—")}</td><td>${escapeHtml(product.condition || "—")}</td>
            <td>${Number(product.stock_quantity)}</td><td>${currency(product.market_price)}</td>
            <td><input class="resupply-quantity" type="number" min="1" placeholder="Qty." disabled aria-label="Quantity for ${escapeHtml(product.card_name)}"></td>
        </tr>`;
    }).join("");
    table.querySelectorAll(".resupply-checkbox").forEach((checkbox) => checkbox.addEventListener("change", () => {
        checkbox.closest("tr").querySelector(".resupply-quantity").disabled = !checkbox.checked;
    }));
}

async function loadLowStock(showModal = true) {
    const message = document.getElementById("lowStockMessage");
    message.className = "modal-msg d-none";
    try {
        const response = await fetch("api/get-low-stock.php");
        const result = await response.json();
        if (!response.ok || !result.success) throw new Error(result.message || "Could not load low-stock cards.");
        lowStockProducts = result.products;
        document.getElementById("stockCount").textContent = lowStockProducts.length;
        document.getElementById("supplierSelect").innerHTML = result.suppliers.map((supplier) => `<option value="${Number(supplier.supplier_id)}">${escapeHtml(supplier.supplier_name)}</option>`).join("");
        renderLowStock(lowStockProducts);
        if (showModal) document.getElementById("lowStockModal").classList.remove("d-none");
    } catch (error) {
        message.className = "modal-msg error";
        message.textContent = error.message || "Could not load low-stock cards.";
    }
}

function openLowStockModal() { return loadLowStock(true); }

async function placeProcurementOrder() {
    const rows = [...document.querySelectorAll("#lowStockTable tr[data-product-id]")];
    const items = rows.filter((row) => row.querySelector(".resupply-checkbox").checked).map((row) => ({
        product_id: Number(row.dataset.productId), quantity: Number(row.querySelector(".resupply-quantity").value)
    }));
    const message = document.getElementById("lowStockMessage");
    message.className = "modal-msg"; message.textContent = "Placing supplier order...";
    try {
        const response = await fetch("api/place-procurement-order.php", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ supplier_id: Number(document.getElementById("supplierSelect").value), items }) });
        const result = await response.json();
        if (!response.ok || !result.success) throw new Error(result.message || "Could not place procurement order.");
        document.getElementById("lowStockModal").classList.add("d-none");
        await loadProcurement();
    } catch (error) { message.className = "modal-msg error"; message.textContent = error.message || "Could not place procurement order."; }
}

async function openProcurementDetails(orderId) {
    const modal = document.getElementById("procurementDetailsModal");
    modal.classList.remove("d-none");
    document.getElementById("procurementDetailsTitle").textContent = "Loading receipt...";
    try {
        const response = await fetch(`api/get-procurement-items.php?procurement_order_id=${encodeURIComponent(orderId)}`);
        const result = await response.json();
        if (!response.ok || !result.success) throw new Error(result.message || "Could not load receipt.");
        activeProcurementOrder = result.order;
        document.getElementById("procurementDetailsTitle").textContent = `Procurement Receipt #${result.order.procurement_order_id}`;
        document.getElementById("procurementSupplier").textContent = result.order.supplier_name;
        const status = document.getElementById("procurementStatus"); status.className = `modal-status-badge ${statusClass(result.order.order_status)}`; status.textContent = result.order.order_status;
        document.getElementById("procurementDate").textContent = result.order.order_date;
        document.getElementById("procurementTotal").textContent = currency(result.order.total_amount);
        document.getElementById("procurementStatusSelect").value = result.order.order_status;
        const isFinalStatus = ["Delivered", "Cancelled"].includes(result.order.order_status);
        document.getElementById("procurementStatusSelect").disabled = isFinalStatus;
        document.getElementById("updateProcurementStatus").disabled = isFinalStatus;
        document.getElementById("procurementDetailsItems").innerHTML = result.items.map((item) => `<tr><td>${escapeHtml(item.card_name)}<br><small>${escapeHtml(item.set_name)}</small></td><td>${Number(item.quantity)}x</td><td>${currency(item.unit_cost)}</td><td>${currency(item.subtotal)}</td></tr>`).join("") || '<tr><td colspan="4" class="modal-empty-msg">No cards found for this receipt.</td></tr>';
    } catch (error) { document.getElementById("procurementDetailsMessage").className = "modal-msg error"; document.getElementById("procurementDetailsMessage").textContent = error.message || "Could not load receipt."; }
}

async function updateProcurementStatus() {
    if (!activeProcurementOrder) return;
    const status = document.getElementById("procurementStatusSelect").value;
    const message = document.getElementById("procurementDetailsMessage");
    message.className = "modal-msg"; message.textContent = "Updating status...";
    try {
        const response = await fetch("api/update-procurement-status.php", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ procurement_order_id: activeProcurementOrder.procurement_order_id, status }) });
        const result = await response.json();
        if (!response.ok || !result.success) throw new Error(result.message || "Could not update status.");
        await loadProcurement();
        await openProcurementDetails(activeProcurementOrder.procurement_order_id);
        if (status === "Delivered") {
            message.className = "modal-msg success";
            message.textContent = "Delivered. The ordered quantities were added to product stock.";
        }
    } catch (error) { message.className = "modal-msg error"; message.textContent = error.message || "Could not update status."; }
}

document.getElementById("searchProcurement").addEventListener("input", filterOrders);
document.getElementById("procurementStatusFilter").addEventListener("change", filterOrders);
document.getElementById("lowStockCard").addEventListener("click", openLowStockModal);
document.getElementById("buyRequestCard").addEventListener("click", () => { document.getElementById("procurementStatusFilter").value = "Pending"; filterOrders(); });
[["lowStockCard", openLowStockModal], ["buyRequestCard", () => { document.getElementById("procurementStatusFilter").value = "Pending"; filterOrders(); }]].forEach(([id, action]) => document.getElementById(id).addEventListener("keydown", (event) => { if (event.key === "Enter" || event.key === " ") { event.preventDefault(); action(); } }));
document.getElementById("closeLowStockModal").addEventListener("click", () => document.getElementById("lowStockModal").classList.add("d-none"));
document.getElementById("lowStockModal").addEventListener("click", (event) => { if (event.target.id === "lowStockModal") event.currentTarget.classList.add("d-none"); });
document.getElementById("placeProcurementOrder").addEventListener("click", placeProcurementOrder);
document.getElementById("procurementTable").addEventListener("click", (event) => { const row = event.target.closest("tr[data-order-id]"); if (row) openProcurementDetails(row.dataset.orderId); });
document.getElementById("closeProcurementDetailsModal").addEventListener("click", () => document.getElementById("procurementDetailsModal").classList.add("d-none"));
document.getElementById("procurementDetailsModal").addEventListener("click", (event) => { if (event.target.id === "procurementDetailsModal") event.currentTarget.classList.add("d-none"); });
document.getElementById("updateProcurementStatus").addEventListener("click", updateProcurementStatus);

loadProcurement();
loadLowStock(false);

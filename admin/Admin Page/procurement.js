let procurementOrders = [];
let lowStockProducts = [];
let activeProcurementOrder = null;
let purchasedCardCount = 0;

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

function applyUrlStatusFilter() {
    const status = new URLSearchParams(window.location.search).get("status");
    const validStatuses = ["Pending", "Processing", "In Transit", "Delivered", "Cancelled"];
    if (validStatuses.includes(status)) {
        document.getElementById("procurementStatusFilter").value = status;
    }
}

async function loadProcurement() {
    try {
        const response = await fetch("api/get-procurement.php");
        if (!response.ok) throw new Error("Could not load procurement receipts.");
        procurementOrders = await response.json();
        document.getElementById("buyRequestCount").textContent = procurementOrders.filter((order) => order.order_status === "Pending").length;
        document.getElementById("successBuyCount").textContent = procurementOrders.filter((order) => order.order_status === "Delivered").length;
        applyUrlStatusFilter();
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
            <td><span class="procurement-card-name" title="${escapeHtml(product.card_name)}">${escapeHtml(product.card_name)}</span></td><td>${escapeHtml(product.set_name)}</td><td>${escapeHtml(product.category)}</td>
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

function purchasedCardTemplate(index) {
    return `
        <section class="purchased-card-form" data-card-index="${index}">
            <div class="purchased-card-heading"><h4>Card ${index + 1}</h4>${index > 0 ? '<button type="button" class="admin-modal-close remove-purchased-card" aria-label="Remove card">&times;</button>' : ''}</div>
            <div class="purchased-image-area">
                <img class="purchased-image-preview d-none" alt="Card preview">
                <label class="purchased-image-picker"><i class="fa-solid fa-image"></i> Choose Card Image<input class="purchased-image-input" type="file" accept="image/jpeg,image/png,image/gif,image/webp" required></label>
            </div>
            <div class="purchased-card-fields">
                <label>Card Name<input name="card_name" type="text" required></label>
                <label>Set Name<input name="set_name" type="text" required></label>
                <label>Category<select name="category" required><option value="">Select category</option><option value="Pokémon">Pokémon</option><option value="Magic: The Gathering">Magic: The Gathering</option><option value="One Piece">One Piece</option></select></label>
                <label>Product Type<select name="product_type" required><option value="">Select type</option><option value="Cards">Cards</option><option value="Sealed">Sealed</option><option value="Collections">Collections</option><option value="Character">Character</option><option value="Leader">Leader</option><option value="Artifact">Artifact</option><option value="Legendary Creature">Legendary Creature</option><option value="Legendary Artifact">Legendary Artifact</option><option value="Enchantment">Enchantment</option><option value="Instant">Instant</option><option value="Creature">Creature</option></select></label>
                <label>Rarity<select name="rarity" required><option value="">Select rarity</option><option>Common</option><option>Uncommon</option><option>Rare</option><option>Ultra Rare</option><option>Secret Rare</option><option>C</option><option>UC</option><option>R</option><option>SR</option><option>SEC</option><option>L</option><option>P</option><option>SP</option><option>AA</option><option>TR</option><option>MR</option><option>M</option><option>S</option><option>U</option></select></label>
                <label>Condition<select name="condition" required><option value="">Select condition</option><option>Mint</option><option>Near Mint</option><option>Lightly Played</option><option>Damaged</option></select></label>
                <label>Selling Price<input name="selling_price" type="number" min="0" step="0.01" required></label>
                <label>Product Cost<input name="product_cost" type="number" min="0" step="0.01" required></label>
                <label>Market Price<input name="market_price" type="number" min="0" step="0.01" required></label>
                <label>Stock Quantity<input name="stock_quantity" type="number" min="0" step="1" required></label>
            </div>
        </section>`;
}

function addPurchasedCard() {
    const list = document.getElementById("purchasedCardsList");
    list.insertAdjacentHTML("beforeend", purchasedCardTemplate(purchasedCardCount));
    purchasedCardCount += 1;
}

function openPurchasedCardsModal() {
    purchasedCardCount = 0;
    document.getElementById("purchasedCardsList").innerHTML = "";
    document.getElementById("purchasedCardsMessage").className = "modal-msg d-none";
    addPurchasedCard();
    document.getElementById("purchasedCardsModal").classList.remove("d-none");
}

function closePurchasedCardsModal() { document.getElementById("purchasedCardsModal").classList.add("d-none"); }

async function insertPurchasedCards(event) {
    event.preventDefault();
    const sections = [...document.querySelectorAll(".purchased-card-form")];
    const message = document.getElementById("purchasedCardsMessage");
    const submit = document.getElementById("insertPurchasedCards");
    const cards = [];
    const formData = new FormData();
    try {
        sections.forEach((section, index) => {
            const values = Object.fromEntries([...section.querySelectorAll("input[name], select[name]")].map((field) => [field.name, field.value]));
            const image = section.querySelector(".purchased-image-input").files[0];
            if (!image) throw new Error(`Add an image for Card ${index + 1}.`);
            cards.push(values);
            formData.append(`image_${index}`, image);
        });
    } catch (error) { message.className = "modal-msg error"; message.textContent = error.message; return; }

    formData.append("cards", JSON.stringify(cards));
    submit.disabled = true;
    message.className = "modal-msg"; message.textContent = "Adding purchased card(s)...";
    try {
        const response = await fetch("api/add-purchased-cards.php", { method: "POST", body: formData });
        const result = await response.json();
        if (!response.ok || !result.success) throw new Error(result.message || "Could not add purchased cards.");
        closePurchasedCardsModal();
        await loadProcurement();
        await loadLowStock(false);
    } catch (error) { message.className = "modal-msg error"; message.textContent = error.message || "Could not add purchased cards."; }
    finally { submit.disabled = false; }
}

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
            await loadLowStock(false); // stock just changed — refresh the count/list without popping the modal open
            message.className = "modal-msg success";
            message.textContent = "Delivered. The ordered quantities were added to product stock.";
        }
    } catch (error) { message.className = "modal-msg error"; message.textContent = error.message || "Could not update status."; }
}

document.getElementById("searchProcurement").addEventListener("input", filterOrders);
document.getElementById("procurementStatusFilter").addEventListener("change", filterOrders);
document.getElementById("lowStockCard").addEventListener("click", openLowStockModal);
document.getElementById("buyRequestCard").addEventListener("click", () => { document.getElementById("procurementStatusFilter").value = "Pending"; filterOrders(); });
document.getElementById("successBuyCard").addEventListener("click", () => { document.getElementById("procurementStatusFilter").value = "Delivered"; filterOrders(); });
[["lowStockCard", openLowStockModal], ["buyRequestCard", () => { document.getElementById("procurementStatusFilter").value = "Pending"; filterOrders(); }], ["successBuyCard", () => { document.getElementById("procurementStatusFilter").value = "Delivered"; filterOrders(); }]].forEach(([id, action]) => document.getElementById(id).addEventListener("keydown", (event) => { if (event.key === "Enter" || event.key === " ") { event.preventDefault(); action(); } }));
document.getElementById("closeLowStockModal").addEventListener("click", () => document.getElementById("lowStockModal").classList.add("d-none"));
document.getElementById("lowStockModal").addEventListener("click", (event) => { if (event.target.id === "lowStockModal") event.currentTarget.classList.add("d-none"); });
document.getElementById("placeProcurementOrder").addEventListener("click", placeProcurementOrder);
document.getElementById("addPurchasedCards").addEventListener("click", openPurchasedCardsModal);
document.getElementById("closePurchasedCardsModal").addEventListener("click", closePurchasedCardsModal);
document.getElementById("purchasedCardsModal").addEventListener("click", (event) => { if (event.target.id === "purchasedCardsModal") closePurchasedCardsModal(); });
document.getElementById("addAnotherPurchasedCard").addEventListener("click", addPurchasedCard);
document.getElementById("purchasedCardsList").addEventListener("change", (event) => {
    if (!event.target.classList.contains("purchased-image-input")) return;
    const file = event.target.files[0];
    if (!file) return;
    const preview = event.target.closest(".purchased-card-form").querySelector(".purchased-image-preview");
    preview.src = URL.createObjectURL(file);
    preview.classList.remove("d-none");
});
document.getElementById("purchasedCardsList").addEventListener("click", (event) => {
    const remove = event.target.closest(".remove-purchased-card");
    if (remove) remove.closest(".purchased-card-form").remove();
});
document.getElementById("purchasedCardsForm").addEventListener("submit", insertPurchasedCards);
document.getElementById("procurementTable").addEventListener("click", (event) => { const row = event.target.closest("tr[data-order-id]"); if (row) openProcurementDetails(row.dataset.orderId); });
document.getElementById("closeProcurementDetailsModal").addEventListener("click", () => document.getElementById("procurementDetailsModal").classList.add("d-none"));
document.getElementById("procurementDetailsModal").addEventListener("click", (event) => { if (event.target.id === "procurementDetailsModal") event.currentTarget.classList.add("d-none"); });
document.getElementById("updateProcurementStatus").addEventListener("click", updateProcurementStatus);

loadProcurement();
loadLowStock(new URLSearchParams(window.location.search).get("low_stock") === "1");

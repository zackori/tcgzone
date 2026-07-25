let allSellRequests = [];
let activeSellRequest = null;
let sellRequestPage = 1;
let sellRequestsPollTimer = null;
let isLoadingSellRequests = false;
const SELL_REQUESTS_PER_PAGE = 10;
const SELL_REQUESTS_POLL_INTERVAL_MS = 3000;

function escapeHtml(value = "") {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function formatCurrency(amount) {
  return `₱${Number(amount || 0).toLocaleString(undefined, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })}`;
}

function getStatusClass(status) {
  return String(status || "").toLowerCase();
}

function displaySellRequests(requests) {
  const tbody = document.getElementById("sellRequestsTable");

  const totalPages = Math.max(
    1,
    Math.ceil(requests.length / SELL_REQUESTS_PER_PAGE),
  );
  sellRequestPage = Math.min(sellRequestPage, totalPages);
  const start = (sellRequestPage - 1) * SELL_REQUESTS_PER_PAGE;
  const pageRequests = requests.slice(start, start + SELL_REQUESTS_PER_PAGE);
  updateSellRequestPagination(totalPages);

  if (requests.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="7" class="text-center">No sell requests found.</td></tr>';
    return;
  }

  tbody.innerHTML = pageRequests
    .map(
      (request) => `
        <tr class="order-row-clickable" data-request-id="${Number(request.request_id)}">
            <td>${Number(request.request_id)}</td>
            <td>${escapeHtml(request.username || "Unknown user")}</td>
            <td>${escapeHtml(request.card_name)}</td>
            <td>${Number(request.quantity || 0)}</td>
            <td>${formatCurrency(request.approved_product_cost)}</td>
            <td>${formatCurrency(Number(request.approved_product_cost || 0) * Number(request.quantity || 0))}</td>
            <td><span class="modal-status-badge ${getStatusClass(request.status)}">${escapeHtml(request.status)}</span></td>
        </tr>
    `,
    )
    .join("");
}

function updateSellRequestPagination(totalPages) {
  document.getElementById("sellRequestPage").textContent = sellRequestPage;
  document.getElementById("previousSellRequestPage").disabled =
    sellRequestPage === 1;
  document.getElementById("nextSellRequestPage").disabled =
    sellRequestPage === totalPages;
}

function filterSellRequests(resetPage = false) {
  const keyword = document
    .getElementById("searchSellRequest")
    .value.trim()
    .toLowerCase();
  const status = document.getElementById("sellRequestStatusFilter").value;

  const filtered = allSellRequests.filter((request) => {
    const searchable = [
      request.request_id,
      request.username,
      request.card_name,
      request.set_name,
      request.category,
      request.product_type,
    ];
    const matchesSearch = searchable.some((value) =>
      String(value || "")
        .toLowerCase()
        .includes(keyword),
    );
    return matchesSearch && (status === "all" || request.status === status);
  });

  if (resetPage) {
    sellRequestPage = 1;
  }

  renderFilteredSellRequests(filtered);
}

function renderFilteredSellRequests(requests) {
  displaySellRequests(requests);
}

function setStatusFilter(status) {
  document.getElementById("sellRequestStatusFilter").value = status;
  document
    .getElementById("approvedRequestsCard")
    .setAttribute("aria-pressed", String(status === "Approved"));
  document
    .getElementById("pendingRequestsCard")
    .setAttribute("aria-pressed", String(status === "Pending"));
  filterSellRequests(true);
}

async function loadSellRequests() {
  if (isLoadingSellRequests) {
    return;
  }

  isLoadingSellRequests = true;

  try {
    const response = await fetch("api/get-sell-requests.php");
    if (!response.ok) throw new Error("Could not load sell requests.");

    allSellRequests = await response.json();
    document.getElementById("approvedRequests").textContent =
      allSellRequests.filter((item) => item.status === "Approved").length;
    document.getElementById("pendingRequests").textContent =
      allSellRequests.filter((item) => item.status === "Pending").length;
    filterSellRequests(false);
  } catch (error) {
    console.error(error);
    document.getElementById("sellRequestsTable").innerHTML =
      '<tr><td colspan="7" class="text-center">Could not load sell requests. Please try again.</td></tr>';
  } finally {
    isLoadingSellRequests = false;
  }
}

function startSellRequestsPolling() {
  if (sellRequestsPollTimer) {
    return;
  }

  sellRequestsPollTimer = window.setInterval(() => {
    loadSellRequests();
  }, SELL_REQUESTS_POLL_INTERVAL_MS);
}

function stopSellRequestsPolling() {
  if (sellRequestsPollTimer) {
    window.clearInterval(sellRequestsPollTimer);
    sellRequestsPollTimer = null;
  }
}

function openSellRequestDetails(requestId) {
  const request = allSellRequests.find(
    (item) => Number(item.request_id) === Number(requestId),
  );
  if (!request) return;
  activeSellRequest = request;

  document.getElementById("sellRequestDetailsTitle").textContent =
    `Sell Request Details #${request.request_id}`;
  document.getElementById("sellRequestUsername").textContent =
    request.username || "Unknown user";
  document.getElementById("sellRequestProductId").textContent =
    request.product_id || "—";
  document.getElementById("sellRequestCardName").textContent =
    request.card_name || "—";
  document.getElementById("sellRequestSetName").textContent =
    request.set_name || "—";
  document.getElementById("sellRequestCategory").textContent =
    request.category || "—";
  document.getElementById("sellRequestProductType").textContent =
    request.product_type || "—";
  document.getElementById("sellRequestRarity").textContent =
    request.rarity || "—";
  document.getElementById("sellRequestCondition").textContent =
    request.condition || "—";
  document.getElementById("sellRequestQuantity").textContent = Number(
    request.quantity || 0,
  );
  document.getElementById("sellRequestPrice").textContent = formatCurrency(
    request.selling_price,
  );
  document.getElementById("sellRequestNotes").textContent =
    request.notes || "No notes provided.";

  const statusEl = document.getElementById("sellRequestStatus");
  statusEl.className = `modal-status-badge ${getStatusClass(request.status)}`;
  statusEl.textContent = request.status || "—";

  const image = document.getElementById("sellRequestImage");
  image.src =
    request.image || "/tcgzone/assets/logos/logo/transparent-image.png";
  image.alt = request.card_name
    ? `${request.card_name} card image`
    : "Requested card image";
  image.onerror = () => {
    image.src = "/tcgzone/assets/logos/logo/transparent-image.png";
  };

  const actions = document.getElementById("sellRequestReviewActions");
  actions.classList.toggle("d-none", request.status !== "Pending");
  const message = document.getElementById("sellRequestDetailsMsg");
  message.className = "modal-msg d-none";
  message.textContent = "";

  document.getElementById("sellRequestDetailsModal").classList.remove("d-none");
}

function closeSellRequestDetails() {
  activeSellRequest = null;
  document.getElementById("sellRequestDetailsModal").classList.add("d-none");
}

function showDetailsMessage(message, type = "error") {
  const messageEl = document.getElementById("sellRequestDetailsMsg");
  messageEl.className = `modal-msg ${type}`;
  messageEl.textContent = message;
}

async function rejectSellRequest() {
  if (!activeSellRequest) return;

  const rejectButton = document.getElementById("rejectSellRequest");
  rejectButton.disabled = true;
  showDetailsMessage("Rejecting sell request...", "");

  try {
    const response = await fetch("api/review-sell-request.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        request_id: activeSellRequest.request_id,
        action: "reject",
      }),
    });
    const result = await response.json();
    if (!response.ok || !result.success)
      throw new Error(result.message || "Could not reject sell request.");

    await loadSellRequests();
    closeSellRequestDetails();
  } catch (error) {
    showDetailsMessage(error.message || "Could not reject sell request.");
  } finally {
    rejectButton.disabled = false;
  }
}

function openApproveSellRequestModal() {
  if (!activeSellRequest) return;

  document.getElementById("approvalProductSummary").textContent =
    `${activeSellRequest.card_name} — ${activeSellRequest.set_name} (${Number(activeSellRequest.quantity)} item${Number(activeSellRequest.quantity) === 1 ? "" : "s"})`;
  document.getElementById("productCost").value = Number(
    activeSellRequest.selling_price || 0,
  ).toFixed(2);
  document.getElementById("approvedSellingPrice").value = Number(
    activeSellRequest.selling_price || 0,
  ).toFixed(2);
  document.getElementById("marketPrice").value = Number(
    activeSellRequest.selling_price || 0,
  ).toFixed(2);
  document.getElementById("approveSellRequestMsg").className =
    "modal-msg d-none";
  document.getElementById("approveSellRequestMsg").textContent = "";
  document.getElementById("approveSellRequestModal").classList.remove("d-none");
}

function closeApproveSellRequestModal() {
  document.getElementById("approveSellRequestModal").classList.add("d-none");
}

async function submitApprovedSellRequest(event) {
  event.preventDefault();
  if (!activeSellRequest) return;

  const submitButton = document.getElementById("submitApprovedSellRequest");
  const messageEl = document.getElementById("approveSellRequestMsg");
  submitButton.disabled = true;
  messageEl.className = "modal-msg";
  messageEl.textContent = "Adding product...";

  try {
    const response = await fetch("api/review-sell-request.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        request_id: activeSellRequest.request_id,
        action: "approve",
        product_cost: document.getElementById("productCost").value,
        selling_price: document.getElementById("approvedSellingPrice").value,
        market_price: document.getElementById("marketPrice").value,
      }),
    });
    const result = await response.json();
    if (!response.ok || !result.success)
      throw new Error(result.message || "Could not approve sell request.");

    await loadSellRequests();
    closeApproveSellRequestModal();
    closeSellRequestDetails();
  } catch (error) {
    messageEl.className = "modal-msg error";
    messageEl.textContent = error.message || "Could not approve sell request.";
  } finally {
    submitButton.disabled = false;
  }
}

document
  .getElementById("searchSellRequest")
  .addEventListener("input", () => filterSellRequests(true));
document
  .getElementById("sellRequestStatusFilter")
  .addEventListener("change", () =>
    setStatusFilter(document.getElementById("sellRequestStatusFilter").value),
  );
document
  .getElementById("approvedRequestsCard")
  .addEventListener("click", () => setStatusFilter("Approved"));
document
  .getElementById("pendingRequestsCard")
  .addEventListener("click", () => setStatusFilter("Pending"));

[
  ["approvedRequestsCard", "Approved"],
  ["pendingRequestsCard", "Pending"],
].forEach(([id, status]) => {
  document.getElementById(id).addEventListener("keydown", (event) => {
    if (event.key === "Enter" || event.key === " ") {
      event.preventDefault();
      setStatusFilter(status);
    }
  });
});

document
  .getElementById("sellRequestsTable")
  .addEventListener("click", (event) => {
    const row = event.target.closest("tr.order-row-clickable");
    if (row) openSellRequestDetails(row.dataset.requestId);
  });
document
  .getElementById("closeSellRequestDetailsModal")
  .addEventListener("click", closeSellRequestDetails);
document
  .getElementById("sellRequestDetailsModal")
  .addEventListener("click", (event) => {
    if (event.target.id === "sellRequestDetailsModal")
      closeSellRequestDetails();
  });
document
  .getElementById("rejectSellRequest")
  .addEventListener("click", rejectSellRequest);
document
  .getElementById("approveSellRequest")
  .addEventListener("click", openApproveSellRequestModal);
document
  .getElementById("closeApproveSellRequestModal")
  .addEventListener("click", closeApproveSellRequestModal);
document
  .getElementById("cancelApproveSellRequest")
  .addEventListener("click", closeApproveSellRequestModal);
document
  .getElementById("approveSellRequestModal")
  .addEventListener("click", (event) => {
    if (event.target.id === "approveSellRequestModal")
      closeApproveSellRequestModal();
  });
document
  .getElementById("approveSellRequestForm")
  .addEventListener("submit", submitApprovedSellRequest);
document
  .getElementById("previousSellRequestPage")
  .addEventListener("click", () => {
    if (sellRequestPage > 1) {
      sellRequestPage -= 1;
      renderCurrentSellRequestPage();
    }
  });
document.getElementById("nextSellRequestPage").addEventListener("click", () => {
  sellRequestPage += 1;
  renderCurrentSellRequestPage();
});

function renderCurrentSellRequestPage() {
  const keyword = document
    .getElementById("searchSellRequest")
    .value.trim()
    .toLowerCase();
  const status = document.getElementById("sellRequestStatusFilter").value;
  renderFilteredSellRequests(
    allSellRequests.filter((request) => {
      const searchable = [
        request.request_id,
        request.username,
        request.card_name,
        request.set_name,
        request.category,
        request.product_type,
      ];
      return (
        searchable.some((value) =>
          String(value || "")
            .toLowerCase()
            .includes(keyword),
        ) &&
        (status === "all" || request.status === status)
      );
    }),
  );
}

document.addEventListener("visibilitychange", () => {
  if (document.visibilityState === "visible") {
    loadSellRequests();
  }
});

window.addEventListener("focus", () => {
  loadSellRequests();
});

loadSellRequests();
startSellRequestsPolling();

const sellForm = document.getElementById("sellRequestForm");
const historyBtn = document.getElementById("openSellHistoryBtn");
const historyModalEl = document.getElementById("sellHistoryModal");
const historyLoadingEl = document.getElementById("sellHistoryLoading");
const historyEmptyEl = document.getElementById("sellHistoryEmpty");
const historyTableWrapEl = document.getElementById("sellHistoryTableWrap");
const historyListEl = document.getElementById("sellHistoryList");
let sellHistoryModal = null;

function showNotification(message, isError = false) {
  let notification = document.querySelector(".notification");

  if (!notification) {
    notification = document.createElement("div");
    notification.className = "notification";
    document.body.appendChild(notification);
  }

  notification.textContent = message;
  notification.classList.toggle("error", isError);
  notification.classList.add("show");

  clearTimeout(notification.hideTimer);
  notification.hideTimer = setTimeout(() => {
    notification.classList.remove("show");
  }, 2800);
}

function formatCurrency(amount) {
  return `₱${Number(amount || 0).toLocaleString(undefined, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })}`;
}

function getStatusBadgeClass(status) {
  const normalized = String(status || "").toLowerCase();
  if (normalized === "approved") return "sell-history-badge approved";
  if (normalized === "rejected") return "sell-history-badge rejected";
  return "sell-history-badge pending";
}

async function loadSellHistory() {
  historyLoadingEl.classList.remove("d-none");
  historyEmptyEl.classList.add("d-none");
  historyTableWrapEl.classList.add("d-none");
  historyListEl.innerHTML = "";

  try {
    const response = await fetch("get_sell_request_history.php", {
      credentials: "include",
    });
    const result = await response.json();

    if (!response.ok || !result.success) {
      throw new Error(result.message || "Unable to load sell request history.");
    }

    if (!result.requests || result.requests.length === 0) {
      historyEmptyEl.classList.remove("d-none");
      return;
    }

    historyListEl.innerHTML = result.requests
      .map(
        (request) => `
          <article class="sell-history-card">
            <div class="sell-history-card-top">
              <div>
                <div class="sell-history-id">Request #${request.request_id}</div>
                <div class="sell-history-card-name">${request.card_name || "—"}</div>
              </div>
              <span class="${getStatusBadgeClass(request.status)}">${request.status || "Pending"}</span>
            </div>

            <div class="sell-history-meta">
              <div>
                <span class="sell-history-label">Set</span>
                <div>${request.set_name || "—"}</div>
              </div>
              <div>
                <span class="sell-history-label">Quantity</span>
                <div>${request.quantity || 0}</div>
              </div>
              <div>
                <span class="sell-history-label">Price</span>
                <div>${formatCurrency(request.selling_price)}</div>
              </div>
              <div>
                <span class="sell-history-label">Submitted</span>
                <div>${request.created_at ? new Date(request.created_at).toLocaleString() : "—"}</div>
              </div>
            </div>
          </article>
        `,
      )
      .join("");

    historyTableWrapEl.classList.remove("d-none");
  } catch (error) {
    console.error(error);
    historyEmptyEl.textContent = "Unable to load history right now.";
    historyEmptyEl.classList.remove("d-none");
  } finally {
    historyLoadingEl.classList.add("d-none");
  }
}

if (historyBtn) {
  historyBtn.addEventListener("click", async () => {
    if (!sellHistoryModal) {
      sellHistoryModal = new bootstrap.Modal(historyModalEl);
    }
    sellHistoryModal.show();
    await loadSellHistory();
  });
}

sellForm?.addEventListener("submit", async function (event) {
  event.preventDefault();

  if (!isLoggedIn) {
    window.location.href = "/tcgzone/customer/Login Page/login.html";
    return;
  }

  const formData = new FormData(sellForm);
  formData.append("action", "submit_sell_request");

  try {
    const response = await fetch("submit_sell_request.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (!result || result.status !== "ok") {
      showNotification(
        result?.message || "Unable to submit your request now.",
        true,
      );
      return;
    }

    showNotification(
      "Sell request submitted successfully. Admins will review it soon.",
    );
    sellForm.reset();
  } catch (error) {
    console.error(error);
    showNotification("Something went wrong. Please try again.", true);
  }
});

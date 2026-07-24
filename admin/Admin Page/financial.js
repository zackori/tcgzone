let revenueDetails, profitDetails, averageOrderDetails, chartPayload;
const months = [
  "Jan",
  "Feb",
  "Mar",
  "Apr",
  "May",
  "Jun",
  "Jul",
  "Aug",
  "Sep",
  "Oct",
  "Nov",
  "Dec",
];
const fullMonths = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December",
];
const currency = (value) =>
  `₱${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

async function loadFinancialDashboard() {
  try {
    const response = await fetch("api/financial.php");
    if (!response.ok) throw new Error("Unable to load financial data.");
    const data = await response.json();
    chartPayload = data;
    revenueDetails = data.details.revenue;
        profitDetails = data.details.profit;
        averageOrderDetails = data.details.average;
    document.getElementById("totalRevenue").textContent = currency(
      data.cards.revenue,
    );
    document.getElementById("netProfit").textContent = currency(
      data.cards.gross_profit,
    );
    document.getElementById("completedOrders").textContent = Number(
      data.cards.completed,
    ).toLocaleString();
    document.getElementById("averageOrder").textContent = currency(
      data.cards.average,
    );
    createChart(
      "revenueChart",
      "Revenue",
      data.revenue,
      "#29B6F6",
      "rgba(41,182,246,.2)",
      "line",
      "revenueChart",
    );
    createChart(
      "profitChart",
      "Profit",
      data.profit,
      "#00E676",
      "rgba(0,230,118,.2)",
      "line",
      "profitChart",
    );
    createChart(
      "ordersChart",
      "Orders",
      data.orders,
      "#2979FF",
      "#2979FF",
      "bar",
      "ordersChart",
    );
    if (new URLSearchParams(window.location.search).get("modal") === "revenue") {
      openRevenueModal();
    }
  } catch (error) {
    console.error(error);
  }
}

function createChart(id, label, values, color, fillColor, type) {
  const canvas = document.getElementById(id);
  const rect = canvas.getBoundingClientRect();
  const width = Math.max(320, rect.width);
  const height = Math.max(220, rect.height);
  const ratio = window.devicePixelRatio || 1;
  canvas.width = width * ratio;
  canvas.height = height * ratio;

  const ctx = canvas.getContext("2d");
  ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
  ctx.clearRect(0, 0, width, height);

  const padding = { top: 32, right: 20, bottom: 42, left: 62 };
  const graphWidth = width - padding.left - padding.right;
  const graphHeight = height - padding.top - padding.bottom;
  const maxValue = Math.max(...values, 1);
  const isOrderChart = type === "bar";
  const niceMax = isOrderChart ? Math.max(1, Math.ceil(maxValue)) : Math.ceil(maxValue / 5) * 5 || 1;
  const gridSteps = isOrderChart ? niceMax : 4;
  const labelValue = (value) =>
    type === "bar"
      ? Math.round(value).toString()
      : `₱${Math.round(value).toLocaleString()}`;

  ctx.font = "13px Arial";
  ctx.fillStyle = "#fff";
  ctx.fillText(label, padding.left, 18);
  ctx.font = "11px Arial";

  for (let step = 0; step <= gridSteps; step++) {
    const value = (niceMax * step) / gridSteps;
    const y = padding.top + graphHeight - (graphHeight * step) / gridSteps;
    ctx.strokeStyle = "rgba(255,255,255,.12)";
    ctx.beginPath();
    ctx.moveTo(padding.left, y);
    ctx.lineTo(width - padding.right, y);
    ctx.stroke();
    ctx.fillStyle = "#d8d8d8";
    ctx.textAlign = "right";
    ctx.fillText(labelValue(value), padding.left - 8, y + 4);
  }

  ctx.textAlign = "center";
  months.forEach((month, index) => {
    const x = padding.left + (graphWidth * index) / (months.length - 1);
    ctx.fillStyle = "#d8d8d8";
    ctx.fillText(month, x, height - 16);
  });

  if (type === "bar") {
    const spacing = graphWidth / (months.length - 1);
    const barWidth = spacing * 0.58;
    values.forEach((value, index) => {
      const x = padding.left + spacing * index - barWidth / 2;
      const barHeight = (graphHeight * Number(value)) / niceMax;
      ctx.fillStyle = color;
      ctx.fillRect(
        x,
        padding.top + graphHeight - barHeight,
        barWidth,
        barHeight,
      );
    });
    return;
  }

  const points = values.map((value, index) => ({
    x: padding.left + (graphWidth * index) / (values.length - 1),
    y: padding.top + graphHeight - (graphHeight * Number(value)) / niceMax,
  }));
  const drawSmoothPath = () => {
    ctx.moveTo(points[0].x, points[0].y);
    for (let index = 0; index < points.length - 1; index++) {
      const previous = points[index - 1] || points[index];
      const current = points[index];
      const next = points[index + 1];
      const afterNext = points[index + 2] || next;

      // Keep periods with no sales perfectly flat instead of allowing
      // the smooth curve to dip below the zero baseline.
      if (Number(values[index]) === 0 && Number(values[index + 1]) === 0) {
        ctx.lineTo(next.x, next.y);
        continue;
      }

      const controlOne = {
        x: current.x + (next.x - previous.x) / 6,
        y: current.y + (next.y - previous.y) / 6,
      };
      const controlTwo = {
        x: next.x - (afterNext.x - current.x) / 6,
        y: next.y - (afterNext.y - current.y) / 6,
      };
      ctx.bezierCurveTo(
        controlOne.x,
        controlOne.y,
        controlTwo.x,
        controlTwo.y,
        next.x,
        next.y,
      );
    }
  };

  ctx.beginPath();
  drawSmoothPath();
  ctx.lineTo(padding.left + graphWidth, padding.top + graphHeight);
  ctx.lineTo(padding.left, padding.top + graphHeight);
  ctx.closePath();
  ctx.fillStyle = fillColor;
  ctx.fill();

  ctx.beginPath();
  drawSmoothPath();
  ctx.strokeStyle = color;
  ctx.lineWidth = 2;
  ctx.stroke();
}

function renderMonthlyRevenue(year) {
  const rows = revenueDetails.monthly[year] || [];
  document.getElementById("monthlyRevenueRows").innerHTML = fullMonths
    .map(
      (month, index) =>
        `<tr><td>${month}</td><td>${Number(rows[index]?.orders || 0)}</td><td>${currency(rows[index]?.revenue || 0)}</td></tr>`,
    )
    .join("");
}

function openRevenueModal() {
  if (!revenueDetails) return;
  const highest = revenueDetails.highest_month;
  document.getElementById("revenueDetailsSummary").innerHTML = [
    ["Total Revenue", currency(revenueDetails.total)],
    [
      "Completed Orders",
      Number(revenueDetails.completed_orders).toLocaleString(),
    ],
    ["Average Order", currency(revenueDetails.average_order)],
    ["Highest Month", `${highest.label} (${currency(highest.revenue)})`],
  ]
    .map(
      ([label, value]) =>
        `<div class="finance-detail-row"><span>${label}</span><strong>${value}</strong></div>`,
    )
    .join("");
  const year = document.getElementById("revenueYearSelect");
  year.innerHTML = revenueDetails.years
    .map((value) => `<option value="${value}">${value}</option>`)
    .join("");
  renderMonthlyRevenue(year.value);
  document.getElementById("monthlyRevenueContent").classList.add("d-none");
  document
    .getElementById("monthlyRevenueToggle")
    .setAttribute("aria-expanded", "false");
  document.getElementById("revenueDetailsModal").classList.remove("d-none");
}

function openGrossProfitModal() {
  if (!profitDetails) return;
  document.getElementById("grossProfitSummary").innerHTML = [
    ["Revenue", currency(profitDetails.revenue)],
    ["Product Cost", currency(profitDetails.product_cost)],
    ["Gross Profit", currency(profitDetails.gross_profit)],
    ["Profit Margin", `${Number(profitDetails.margin).toFixed(2)}%`],
  ]
    .map(
      ([label, value]) =>
        `<div class="finance-detail-row"><span>${label}</span><strong>${value}</strong></div>`,
    )
    .join("");
  document.getElementById("grossProfitFormula").innerHTML =
    `<strong>Formula</strong><p>Gross Profit = Revenue − Product Cost</p><p> ${currency(profitDetails.gross_profit)} = ${currency(profitDetails.revenue)} − ${currency(profitDetails.product_cost)} </p><p>Profit Margin = (Gross Profit ÷ Revenue) × 100 = ${Number(profitDetails.margin).toFixed(2)}%</p>`;
  document.getElementById("grossProfitDetailsModal").classList.remove("d-none");
}

function openAverageOrderModal() {
  if (!averageOrderDetails) return;
  document.getElementById("averageOrderSummary").innerHTML = [
    ["Average Order", currency(averageOrderDetails.average_order)],
    ["Total Revenue", currency(averageOrderDetails.revenue)],
    ["Completed Orders", Number(averageOrderDetails.completed_orders).toLocaleString()],
    ["Largest Order", currency(averageOrderDetails.largest_order)],
    ["Smallest Order", currency(averageOrderDetails.smallest_order)],
  ].map(([label, value]) => `<div class="finance-detail-row"><span>${label}</span><strong>${value}</strong></div>`).join("");
  document.getElementById("averageOrderFormula").innerHTML = `<strong>Formula</strong><p>Average Order = Total Revenue ÷ Completed Orders</p><p>${currency(averageOrderDetails.average_order)} = ${currency(averageOrderDetails.revenue)} ÷ ${Number(averageOrderDetails.completed_orders).toLocaleString()}</p>`;
  document.getElementById("averageOrderDetailsModal").classList.remove("d-none");
}

const revenueCard = document.getElementById("revenueDetailsCard");
revenueCard.addEventListener("click", openRevenueModal);
revenueCard.addEventListener("keydown", (event) => {
  if (event.key === "Enter" || event.key === " ") {
    event.preventDefault();
    openRevenueModal();
  }
});
document
  .getElementById("closeRevenueDetailsModal")
  .addEventListener("click", () =>
    document.getElementById("revenueDetailsModal").classList.add("d-none"),
  );
document
  .getElementById("revenueDetailsModal")
  .addEventListener("click", (event) => {
    if (event.target.id === "revenueDetailsModal")
      event.currentTarget.classList.add("d-none");
  });
document
  .getElementById("monthlyRevenueToggle")
  .addEventListener("click", (event) => {
    const content = document.getElementById("monthlyRevenueContent");
    content.classList.toggle("d-none");
    event.currentTarget.setAttribute(
      "aria-expanded",
      String(!content.classList.contains("d-none")),
    );
  });
document
  .getElementById("revenueYearSelect")
  .addEventListener("change", (event) =>
    renderMonthlyRevenue(event.target.value),
  );

const grossProfitCard = document.getElementById("grossProfitDetailsCard");
grossProfitCard.addEventListener("click", openGrossProfitModal);
grossProfitCard.addEventListener("keydown", (event) => {
  if (event.key === "Enter" || event.key === " ") {
    event.preventDefault();
    openGrossProfitModal();
  }
});
document
  .getElementById("closeGrossProfitDetailsModal")
  .addEventListener("click", () =>
    document.getElementById("grossProfitDetailsModal").classList.add("d-none"),
  );
document
  .getElementById("grossProfitDetailsModal")
  .addEventListener("click", (event) => {
    if (event.target.id === "grossProfitDetailsModal")
      event.currentTarget.classList.add("d-none");
  });

const averageOrderCard = document.getElementById("averageOrderDetailsCard");
averageOrderCard.addEventListener("click", openAverageOrderModal);
averageOrderCard.addEventListener("keydown", (event) => {
  if (event.key === "Enter" || event.key === " ") {
    event.preventDefault();
    openAverageOrderModal();
  }
});
document.getElementById("closeAverageOrderDetailsModal").addEventListener("click", () => document.getElementById("averageOrderDetailsModal").classList.add("d-none"));
document.getElementById("averageOrderDetailsModal").addEventListener("click", (event) => {
  if (event.target.id === "averageOrderDetailsModal") event.currentTarget.classList.add("d-none");
});

const financialCompletedOrdersCard = document.getElementById("financialCompletedOrdersCard");
financialCompletedOrdersCard.addEventListener("click", () => { window.location.href = "orders.php?status=Delivered"; });
financialCompletedOrdersCard.addEventListener("keydown", (event) => {
  if (event.key === "Enter" || event.key === " ") {
    event.preventDefault();
    window.location.href = "orders.php?status=Delivered";
  }
});
window.addEventListener("resize", () => {
  if (!chartPayload) return;
  createChart(
    "revenueChart",
    "Revenue",
    chartPayload.revenue,
    "#29B6F6",
    "rgba(41,182,246,.2)",
    "line",
  );
  createChart(
    "profitChart",
    "Profit",
    chartPayload.profit,
    "#00E676",
    "rgba(0,230,118,.2)",
    "line",
  );
  createChart(
    "ordersChart",
    "Orders",
    chartPayload.orders,
    "#2979FF",
    "#2979FF",
    "bar",
  );
});
loadFinancialDashboard();

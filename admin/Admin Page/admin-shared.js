// =========================================
// Dashboard.js
// Requires:
// dashboard.php
// chart_data.php
// Chart.js
// =========================================

Chart.defaults.color = "#ffffff";
Chart.defaults.font.family = "Poppins";

const months = [
    "Jan","Feb","Mar","Apr","May","Jun",
    "Jul","Aug","Sep","Oct","Nov","Dec"
];

let ordersChart;
let salesChart;
let stockChart;

// =========================================
// Load Dashboard Cards
// =========================================

async function loadDashboardCards(){

    try{

        const response = await fetch("api/dashboard.php");

        const data = await response.json();

        document.getElementById("ordersCount").innerHTML =
            Number(data.orders).toLocaleString();

        document.getElementById("salesCount").innerHTML =
            "₱" + Number(data.sales).toLocaleString();

        document.getElementById("stockCount").innerHTML =
            Number(data.stock_quantity).toLocaleString();

        const buyRequestCount = document.getElementById("buyRequestCount");
        if (buyRequestCount) {
            buyRequestCount.innerHTML = Number(data.pending_buy_requests || 0).toLocaleString();
        }

    }

    catch(error){

        console.error("Dashboard Error:",error);

    }

}

function setupOverviewCardRedirects() {
    const revenueCard = document.getElementById("revenueCard");
    const completedOrdersCard = document.getElementById("completedOrdersCard");
    const buyRequestCard = document.getElementById("buyRequestCard");
    const lowStockCard = document.getElementById("lowStockCard");

    if (revenueCard) {
        const redirectRevenue = () => {
            window.location.href = "financial.php";
        };
        revenueCard.addEventListener("click", redirectRevenue);
        revenueCard.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                redirectRevenue();
            }
        });
    }

    if (completedOrdersCard) {
        const redirectCompleted = () => {
            window.location.href = "orders.php?status=Delivered";
        };
        completedOrdersCard.addEventListener("click", redirectCompleted);
        completedOrdersCard.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                redirectCompleted();
            }
        });
    }

    if (buyRequestCard) {
        const redirectPendingBuyRequests = () => {
            window.location.href = "procurement.php?status=Pending";
        };
        buyRequestCard.addEventListener("click", redirectPendingBuyRequests);
        buyRequestCard.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                redirectPendingBuyRequests();
            }
        });
    }

    if (lowStockCard) {
        const openLowStockResupply = () => {
            window.location.href = "procurement.php?low_stock=1";
        };
        lowStockCard.addEventListener("click", openLowStockResupply);
        lowStockCard.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                openLowStockResupply();
            }
        });
    }
}

// =========================================
// Load Charts
// =========================================

async function loadCharts(){

    try{

        const response = await fetch("api/chart_data.php");

        const data = await response.json();

        createOrdersChart(data.orders);

        createSalesChart(data.sales);

        createStockChart(data.stockLabels,data.stockValues);

    }

    catch(error){

        console.error(error);

    }

}

// =========================================
// Orders Chart
// =========================================

function createOrdersChart(chartData){

    if(ordersChart){

        ordersChart.destroy();

    }

    ordersChart = new Chart(document.getElementById("ordersChart"),{

        type:"line",

        data:{

            labels:months,

            datasets:[{

                label:"Orders",

                data:chartData,

                borderColor:"rgb(25, 135, 84)",

                backgroundColor:"rgba(25, 135, 84,.15)",

                fill:true,

                borderWidth:3,

                tension:.4,

                pointRadius:5,

                pointBackgroundColor:"#198754"

            }]

        },

        options:{

            responsive:true,

            maintainAspectRatio:false,

            plugins:{

                legend:{display:false}

            },

            scales:{

                x:{

                    grid:{color:"#2d2d2d"}

                },

                y:{

                    beginAtZero:true,

                    grid:{color:"#2d2d2d"}

                }

            }

        }

    });

}

// =========================================
// Sales Chart
// =========================================

function createSalesChart(chartData){

    if(salesChart){

        salesChart.destroy();

    }

    salesChart = new Chart(document.getElementById("salesChart"),{

        type:"line",

        data:{

            labels:months,

            datasets:[{

                label:"Sales",

                data:chartData,

                borderColor:"rgb(63, 139, 253)",

                backgroundColor:"rgba(63, 139, 253, 0.15)",

                fill:true,

                borderWidth:3,

                tension:.4,

                pointRadius:5,

                pointBackgroundColor:"rgb(63, 139, 253)"

            }]

        },

        options:{

            responsive:true,

            maintainAspectRatio:false,

            plugins:{

                legend:{display:false}

            },

            scales:{

                x:{

                    grid:{color:"#2d2d2d"}

                },

                y:{

                    beginAtZero:true,

                    grid:{color:"#2d2d2d"}

                }

            }

        }

    });

}

// =========================================
// Stock Chart
// =========================================

function createStockChart(labels,data){

    if(stockChart){

        stockChart.destroy();

    }

    const MAX_LABEL_LENGTH = 12;
    const barColors = data.map(value => value <= 2 ? "#cf1c40" : "#198754");

    stockChart = new Chart(document.getElementById("stockChart"),{

        type:"bar",

        data:{

            labels:labels,

            datasets:[{

                label:"Stock",

                data:data,

                backgroundColor:barColors,

                borderRadius:8

            }]

        },

        options:{

            responsive:true,

            maintainAspectRatio:false,

            plugins:{

                legend:{display:false}

            },

            scales:{

                x:{

                    grid:{display:false},

                    ticks:{

                        color:"#ffffff",

                        maxRotation:90,

                        minRotation:90,

                        autoSkip:false,

                        align:"end",

                        callback:function(value,index){

                            const label = this.getLabelForValue(value);

                            return label.length > MAX_LABEL_LENGTH ? label.slice(0,MAX_LABEL_LENGTH) + "…" : label;

                        }

                    }

                },

                y:{

                    beginAtZero:true,

                    grid:{color:"#2d2d2d"},

                    ticks:{

                        color:"#ffffff"

                    }

                }

            }

        }

    });

}

// =========================================
// Initial Load
// =========================================

loadDashboardCards();

loadCharts();

setupOverviewCardRedirects();

// =========================================
// Auto Refresh Every 5 Seconds
// =========================================

setInterval(()=>{

    loadDashboardCards();

    loadCharts();

},10000);

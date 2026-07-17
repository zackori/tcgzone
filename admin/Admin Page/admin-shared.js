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
            Number(data.stock).toLocaleString();

    }

    catch(error){

        console.error("Dashboard Error:",error);

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

                borderColor:"#4dabff",

                backgroundColor:"rgba(77,171,255,.15)",

                fill:true,

                borderWidth:3,

                tension:.4,

                pointRadius:5,

                pointBackgroundColor:"#4dabff"

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

                borderColor:"#00d084",

                backgroundColor:"rgba(0,208,132,.15)",

                fill:true,

                borderWidth:3,

                tension:.4,

                pointRadius:5,

                pointBackgroundColor:"#00d084"

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

    stockChart = new Chart(document.getElementById("stockChart"),{

        type:"bar",

        data:{

            labels:labels,

            datasets:[{

                label:"Stock",

                data:data,

                backgroundColor:"#ff4d6d",

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

                    grid:{display:false}

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
// Initial Load
// =========================================

loadDashboardCards();

loadCharts();

// =========================================
// Auto Refresh Every 5 Seconds
// =========================================

setInterval(()=>{

    loadDashboardCards();

    loadCharts();

},5000);
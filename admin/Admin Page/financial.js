// =========================================
// Financial Dashboard
// =========================================

let revenueChart;
let profitChart;
let ordersChart;

// Load Dashboard
async function loadFinancialDashboard() {

    try {

        const response = await fetch("api/financial.php");
        const data = await response.json();

        updateCards(data.cards);

        createRevenueChart(data.revenue);

        createProfitChart(data.profit);

        createOrdersChart(data.orders);

    } catch (error) {

        console.error(error);

    }

}

// =========================================
// Cards
// =========================================

function updateCards(cards){

    document.getElementById("totalRevenue").textContent =
        "₱" + Number(cards.revenue).toLocaleString();

    document.getElementById("netProfit").textContent =
        "₱" + (Number(cards.revenue)*0.35).toLocaleString();

    document.getElementById("completedOrders").textContent =
        cards.completed;

    document.getElementById("averageOrder").textContent =
        "₱" + Number(cards.average).toLocaleString();

}

// =========================================

const months=[

"Jan","Feb","Mar","Apr","May","Jun",

"Jul","Aug","Sep","Oct","Nov","Dec"

];

// =========================================
// Revenue Chart
// =========================================

function createRevenueChart(values){

if(revenueChart){

revenueChart.destroy();

}

revenueChart=new Chart(

document.getElementById("revenueChart"),

{

type:"line",

data:{

labels:months,

datasets:[{

label:"Revenue",

data:values,

borderColor:"#29B6F6",

backgroundColor:"rgba(41,182,246,.2)",

fill:true,

tension:.4

}]

},

options:{

    responsive:true,

    maintainAspectRatio:false,

    plugins:{

        legend:{
            labels:{
                color:"#ffffff",
                font:{
                    size:14
                }
            }
        }

    },

    scales:{

        x:{

            ticks:{
                color:"#ffffff"
            },

            grid:{
                color:"rgba(255,255,255,.08)"
            }

        },

        y:{

            ticks:{
                color:"#ffffff"
            },

            grid:{
                color:"rgba(255,255,255,.08)"
            }

        }

    }

}

});

}

// =========================================
// Profit Chart
// =========================================

function createProfitChart(values){

if(profitChart){

profitChart.destroy();

}

profitChart=new Chart(

document.getElementById("profitChart"),

{

type:"line",

data:{

labels:months,

datasets:[{

label:"Profit",

data:values,

borderColor:"#00E676",

backgroundColor:"rgba(0,230,118,.2)",

fill:true,

tension:.4

}]

},

options:{

    responsive:true,

    maintainAspectRatio:false,

    plugins:{

        legend:{
            labels:{
                color:"#ffffff",
                font:{
                    size:14
                }
            }
        }

    },

    scales:{

        x:{

            ticks:{
                color:"#ffffff"
            },

            grid:{
                color:"rgba(255,255,255,.08)"
            }

        },

        y:{

            ticks:{
                color:"#ffffff"
            },

            grid:{
                color:"rgba(255,255,255,.08)"
            }

        }

    }

}

});

}

// =========================================
// Orders Chart
// =========================================

function createOrdersChart(values){

if(ordersChart){

ordersChart.destroy();

}

ordersChart=new Chart(

document.getElementById("ordersChart"),

{

type:"bar",

data:{

labels:months,

datasets:[{

label:"Orders",

data:values,

backgroundColor:"#2979FF",

borderRadius:8

}]

},

options:{

    responsive:true,

    maintainAspectRatio:false,

    plugins:{

        legend:{
            labels:{
                color:"#ffffff",
                font:{
                    size:14
                }
            }
        }

    },

    scales:{

        x:{

            ticks:{
                color:"#ffffff"
            },

            grid:{
                color:"rgba(255,255,255,.08)"
            }

        },

        y:{

            ticks:{
                color:"#ffffff"
            },

            grid:{
                color:"rgba(255,255,255,.08)"
            }

        }

    }

}
});

}

// =========================================

loadFinancialDashboard();
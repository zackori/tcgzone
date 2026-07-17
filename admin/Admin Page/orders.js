// =========================================
// orders.js
// =========================================

let allOrders = [];

// ----------------------------
// Load Orders
// ----------------------------
async function loadOrders() {

    try {

        const response = await fetch("api/order.php");
        allOrders = await response.json();

        updateCards(allOrders);
        displayOrders(allOrders);

    } catch (error) {

        console.error("Failed to load orders:", error);

    }

}

// ----------------------------
// Update Cards
// ----------------------------
function updateCards(orders) {

    document.getElementById("totalOrders").textContent = orders.length;

    const ongoing = orders.filter(order =>
        ["Pending", "Processing", "In Transit"].includes(order.status)
    ).length;

    const completed = orders.filter(order =>
        ["Delivered", "Cancelled"].includes(order.status)
    ).length;

    document.getElementById("ongoingOrders").textContent = ongoing;
    document.getElementById("completedOrders").textContent = completed;

}

// ----------------------------
// Display Orders
// ----------------------------
function displayOrders(orders) {

    const tbody = document.getElementById("ordersTable");

    tbody.innerHTML = "";

    if (orders.length === 0) {

        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    No orders found.
                </td>
            </tr>
        `;

        return;

    }

    orders.forEach(order => {

        let badgeClass = "";

        switch (order.status) {

            case "Pending":
                badgeClass = "pending";
                break;

            case "Processing":
                badgeClass = "processing";
                break;

            case "In Transit":
                badgeClass = "transit";
                break;

            case "Delivered":
                badgeClass = "delivered";
                break;

            case "Cancelled":
                badgeClass = "cancelled";
                break;

        }

        tbody.innerHTML += `

        <tr>

            <td>${order.order_id}</td>

            <td>${order.customer_name}</td>

            <td>${order.pokemon_name}</td>

            <td>${order.quantity}</td>

            <td>₱${Number(order.total_amount).toLocaleString()}</td>

            <td>

        <select
            class="status-select ${badgeClass}"
            onchange="
                this.className='status-select';

                if(this.value==='Pending'){
                    this.classList.add('pending');
                }
                else if(this.value==='Processing'){
                    this.classList.add('processing');
                }
                else if(this.value==='In Transit'){
                    this.classList.add('transit');
                }
                else if(this.value==='Delivered'){
                    this.classList.add('delivered');
                }
                else{
                    this.classList.add('cancelled');
                }

                updateStatus(${order.order_id}, this.value);
            ">

            <option value="Pending" ${order.status=="Pending"?"selected":""}>Pending</option>

            <option value="Processing" ${order.status=="Processing"?"selected":""}>Processing</option>

            <option value="In Transit" ${order.status=="In Transit"?"selected":""}>In Transit</option>

            <option value="Delivered" ${order.status=="Delivered"?"selected":""}>Delivered</option>

            <option value="Cancelled" ${order.status=="Cancelled"?"selected":""}>Cancelled</option>

        </select>

</td>

            <td>${order.order_date}</td>

        </tr>

        `;

    });

}

// ----------------------------
// Search
// ----------------------------
document.getElementById("searchOrder").addEventListener("keyup", filterOrders);
document.getElementById("statusFilter").addEventListener("change", filterOrders);

function filterOrders() {

    const keyword = document.getElementById("searchOrder").value.toLowerCase();

    const status = document.getElementById("statusFilter").value;

    const filtered = allOrders.filter(order => {

        const matchesSearch =

            order.customer_name.toLowerCase().includes(keyword) ||

            order.pokemon_name.toLowerCase().includes(keyword) ||

            order.order_id.toString().includes(keyword);

        const matchesStatus =

            status === "all" || order.status === status;

        return matchesSearch && matchesStatus;

    });

    displayOrders(filtered);

}


// initial load
loadOrders();

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

            // Update the order inside the array
            const order = allOrders.find(o => o.order_id == orderId);

            if (order) {
                order.status = status;
            }

            // Refresh dashboard cards
            updateCards(allOrders);

            // Keep current search/filter
            filterOrders();

        } else {

            alert(result.message);

        }

    } catch (error) {

        console.error(error);

    }

}
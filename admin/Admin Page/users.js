let allUsers = [];
let showingPendingOrderUsers = false;
let userToDelete = null;

function escapeHtml(value = "") {
    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/\"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

function displayUsers(users) {
    const tbody = document.getElementById("usersTable");

    if (users.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">No users found.</td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = users.map((user) => `
        <tr>
            <td>${escapeHtml(user.username)}</td>
            <td>${escapeHtml(user.name)}</td>
            <td>${escapeHtml(user.email)}</td>
            <td>******</td>
            <td>${escapeHtml(user.phone)}</td>
            <td>${escapeHtml(user.address)}</td>
            <td>
                <button type="button" class="modal-btn reject delete-user-btn" data-user-id="${Number(user.id)}">
                    Delete
                </button>
            </td>
        </tr>
    `).join("");
}

function openDeleteUserModal(userId) {
    const user = allUsers.find((item) => Number(item.id) === Number(userId));
    if (!user) return;

    userToDelete = user;
    document.getElementById("deleteUserMessage").textContent =
        `Delete ${user.username}? This will permanently remove their account, orders, and cart.`;
    document.getElementById("deleteUserModalMessage").classList.add("d-none");
    document.getElementById("deleteUserModal").classList.remove("d-none");
}

function closeDeleteUserModal() {
    userToDelete = null;
    document.getElementById("deleteUserModal").classList.add("d-none");
}

async function deleteUser() {
    if (!userToDelete) return;

    const confirmButton = document.getElementById("confirmDeleteUser");
    const message = document.getElementById("deleteUserModalMessage");
    confirmButton.disabled = true;
    message.className = "modal-msg";
    message.textContent = "Deleting user...";

    try {
        const response = await fetch("api/delete-user.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ user_id: userToDelete.id })
        });
        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Could not delete user.");
        }

        allUsers = allUsers.filter((user) => Number(user.id) !== Number(userToDelete.id));
        document.getElementById("totalUsers").textContent = allUsers.length;
        document.getElementById("pendingUsers").textContent = allUsers.filter(
            (user) => Number(user.has_pending_order) === 1
        ).length;
        closeDeleteUserModal();
        filterUsers();
    } catch (error) {
        message.className = "modal-msg error";
        message.textContent = error.message || "Could not delete user.";
    } finally {
        confirmButton.disabled = false;
    }
}

function filterUsers() {
    const keyword = document.getElementById("searchUser").value.trim().toLowerCase();

    const filteredUsers = allUsers.filter((user) => {
        const matchesPendingFilter = !showingPendingOrderUsers || Number(user.has_pending_order) === 1;
        const searchableValues = [user.username, user.name, user.email, user.phone, user.address];
        const matchesSearch = searchableValues.some((value) =>
            String(value || "").toLowerCase().includes(keyword)
        );

        return matchesPendingFilter && matchesSearch;
    });

    displayUsers(filteredUsers);
}

function setPendingOrderFilter(enabled) {
    showingPendingOrderUsers = enabled;
    document.getElementById("pendingUsersCard").setAttribute("aria-pressed", String(enabled));
    filterUsers();
}

function setupOverviewCards() {
    const totalUsersCard = document.getElementById("totalUsersCard");
    const pendingUsersCard = document.getElementById("pendingUsersCard");

    const showAllUsers = () => setPendingOrderFilter(false);
    const showPendingOrderUsers = () => setPendingOrderFilter(true);

    totalUsersCard.addEventListener("click", showAllUsers);
    pendingUsersCard.addEventListener("click", showPendingOrderUsers);

    [[totalUsersCard, showAllUsers], [pendingUsersCard, showPendingOrderUsers]].forEach(([card, action]) => {
        card.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                action();
            }
        });
    });
}

async function loadUsers() {
    try {
        const response = await fetch("api/get-users.php");

        if (!response.ok) {
            throw new Error("Could not load users.");
        }

        allUsers = await response.json();

        document.getElementById("totalUsers").textContent = allUsers.length;
        document.getElementById("pendingUsers").textContent = allUsers.filter(
            (user) => Number(user.has_pending_order) === 1
        ).length;

        filterUsers();
    } catch (error) {
        console.error("Failed to load users:", error);
        document.getElementById("usersTable").innerHTML = `
            <tr>
                <td colspan="7" class="text-center">Could not load users. Please try again.</td>
            </tr>
        `;
    }
}

document.getElementById("searchUser").addEventListener("input", filterUsers);
document.getElementById("usersTable").addEventListener("click", (event) => {
    const button = event.target.closest(".delete-user-btn");
    if (button) openDeleteUserModal(button.dataset.userId);
});
document.getElementById("closeDeleteUserModal").addEventListener("click", closeDeleteUserModal);
document.getElementById("cancelDeleteUser").addEventListener("click", closeDeleteUserModal);
document.getElementById("confirmDeleteUser").addEventListener("click", deleteUser);
document.getElementById("deleteUserModal").addEventListener("click", (event) => {
    if (event.target.id === "deleteUserModal") closeDeleteUserModal();
});
setupOverviewCards();
loadUsers();

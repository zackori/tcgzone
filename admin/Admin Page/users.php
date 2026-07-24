<?php
$pageTitle = "Users";
$currentPage = "users";

session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | <?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="admin-shared.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="icon" type="image/svg" href="/tcgzone/assets/logos/logo/transparent-image.png">
</head>

<body>
    <div class="container">

        <?php include 'includes/sidebar.php'; ?>

        <main class="main">

            <?php include 'includes/header.php'; ?>

            <!-- Users content goes here -->

            <section class="users-cards">

                <div class="card-neutral card-clickable" id="totalUsersCard" role="button" tabindex="0">
                    <div class="card-info">
                        <p>Total Users</p>
                        <h2 id="totalUsers">0</h2>
                    </div>

                    <div class="card-icon-neutral">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>

                <div class="card-pending card-clickable" id="pendingUsersCard" role="button" tabindex="0">
                    <div class="card-info">
                        <p>Users With Pending Orders</p>
                        <h2 id="pendingUsers">0</h2>
                    </div>

                    <div class="card-icon-pending">
                        <i class="fa-solid fa-user-clock"></i>
                    </div>

                </div>

            </section>

            <div class="users-card">

                <div class="users-header">

                    <h3>Users</h3>

                    <div class="users-actions">

                        <input type="text" id="searchUser" placeholder="Search users">


                    </div>

                </div>

                <table class="users-table">

                    <thead>

                        <tr>

                            <th>Username</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th></th>

                        </tr>

                    </thead>

                    <tbody id="usersTable">

                    </tbody>

                </table>

            </div>

            <div class="admin-modal-overlay d-none" id="deleteUserModal" role="dialog" aria-modal="true" aria-labelledby="deleteUserTitle">
                <div class="admin-modal">
                    <div class="admin-modal-header">
                        <h3 id="deleteUserTitle">Delete User</h3>
                        <button type="button" class="admin-modal-close" id="closeDeleteUserModal" aria-label="Close">&times;</button>
                    </div>
                    <div class="admin-modal-body">
                        <p id="deleteUserMessage">Are you sure you want to delete this user?</p>
                        <p class="modal-msg d-none" id="deleteUserModalMessage"></p>
                        <div class="modal-actions">
                            <button type="button" class="modal-btn reject" id="cancelDeleteUser">No</button>
                            <button type="button" class="modal-btn accept" id="confirmDeleteUser">Yes, Delete</button>
                        </div>
                    </div>
                </div>
            </div>


        </main>

    </div>
    <script src="admin-shared.js"></script>
    <script src="users.js"></script>
</body>

</html>

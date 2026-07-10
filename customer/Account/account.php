<?php
/* ============================================================
ACCOUNT PAGE — server-rendered from the database
Fields shown: Username (locked), Email, Phone, Shipping Address,
Date of Birth, Gender — matching what was specified.
Requires login — redirects to login.html if no session exists.
============================================================ */

require '../../config/db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /tcgzone/customer/Login Page/login.html");
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result  = $stmt->get_result();
$profile = $result->fetch_assoc();

function esc($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES);
}

$dobDisplay = '';
if (!empty($profile['dob'])) {
    $dobDisplay = date("d-m-Y", strtotime($profile['dob']));
}

$addressParts = array_filter([
    $profile['address_details'],
    $profile['address_city'],
    $profile['address_province'],
    $profile['address_zip']
]);
$addressDisplay = $addressParts ? implode(", ", $addressParts) : "No address on file";

$nameParts = array_filter([$profile['first_name'], $profile['last_name']]);
$nameDisplay = $nameParts ? implode(" ", $nameParts) : "No name on file";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/tcgzone/bootstrap/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/tcgzone/assets/css/shared.css">
    <link rel="stylesheet" href="account.css">
    <title>My Profile — TCGZONE</title>
</head>
<body style="background-color:#0a0a0a;">

<div class="profile-overlay" style="display:flex; position:static; min-height:100vh; backdrop-filter:none; -webkit-backdrop-filter:none; background-color:transparent;">
    <div class="profile-panel">

        <div class="profile-header">
            <h2>My Profile</h2>
            <p>Manage your profile</p>
        </div>

        <div class="profile-card">
            <form class="profile-form" id="profileForm" action="save_profile.php" method="POST" novalidate>

                <div class="form-row">
                    <label>Username</label>
                    <span class="static-value"><?= esc($profile['username']) ?></span>
                </div>

                <div class="form-row">
                    <label>Name</label>
                    <div class="editable-field" data-field="name">
                        <span class="static-value" data-static="name"><?= esc($nameDisplay) ?></span>
                        <div class="address-fields">
                            <input type="text" name="first_name" class="text-input address-input"
                                data-input="firstName" placeholder="First Name"
                                value="<?= esc($profile['first_name']) ?>">
                            <input type="text" name="last_name" class="text-input address-input"
                                data-input="lastName" placeholder="Last Name"
                                value="<?= esc($profile['last_name']) ?>">
                        </div>
                        <a href="#" class="change-link" data-target="name">Change</a>
                        <a href="#" class="cancel-link" data-target="name">Cancel</a>
                    </div>
                </div>

                <div class="form-row">
                    <label>Email</label>
                    <div class="editable-field" data-field="email">
                        <span class="static-value" data-static="email"><?= esc($profile['email']) ?></span>
                        <input type="email" name="email" class="text-input hidden-input"
                            data-input="email" value="<?= esc($profile['email']) ?>">
                        <a href="#" class="change-link" data-target="email">Change</a>
                        <a href="#" class="cancel-link" data-target="email">Cancel</a>
                    </div>
                </div>

                <div class="form-row">
                    <label>Phone Number</label>
                    <div class="editable-field" data-field="phone">
                        <span class="static-value" data-static="phone"><?= esc($profile['phone']) ?></span>
                        <input type="text" name="phone" class="text-input hidden-input"
                            data-input="phone" value="<?= esc($profile['phone']) ?>">
                        <a href="#" class="change-link" data-target="phone">Change</a>
                        <a href="#" class="cancel-link" data-target="phone">Cancel</a>
                    </div>
                </div>

                <div class="form-row">
                    <label>Shipping Address</label>
                    <div class="editable-field" data-field="address">
                        <span class="static-value" data-static="address"><?= esc($addressDisplay) ?></span>
                        <div class="address-fields">
                            <input type="text" name="address_details" class="text-input address-input"
                                data-input="addressDetails" placeholder="House/Unit No., Street"
                                value="<?= esc($profile['address_details']) ?>">
                            <input type="text" name="address_city" class="text-input address-input"
                                data-input="addressCity" placeholder="City"
                                value="<?= esc($profile['address_city']) ?>">
                            <input type="text" name="address_province" class="text-input address-input"
                                data-input="addressProvince" placeholder="Province"
                                value="<?= esc($profile['address_province']) ?>">
                            <input type="text" name="address_zip" class="text-input address-input"
                                data-input="addressZip" placeholder="ZIP Code"
                                value="<?= esc($profile['address_zip']) ?>">
                        </div>
                        <a href="#" class="change-link" data-target="address">Change</a>
                        <a href="#" class="cancel-link" data-target="address">Cancel</a>
                    </div>
                </div>

                <div class="form-row">
                    <label>Date of Birth</label>
                    <div class="editable-field" data-field="dob">
                        <span class="static-value" data-static="dob"><?= esc($dobDisplay ?: 'Not set') ?></span>
                        <input type="date" name="dob" class="text-input hidden-input"
                            data-input="dob" value="<?= esc($profile['dob']) ?>">
                        <a href="#" class="change-link" data-target="dob">Change</a>
                        <a href="#" class="cancel-link" data-target="dob">Cancel</a>
                    </div>
                </div>

                <div class="form-row gender-row">
                    <label>Gender</label>
                    <div class="gender-options">
                        <label class="radio-label">
                            <input type="radio" name="gender" value="Male" <?= $profile['gender'] === 'Male' ? 'checked' : '' ?>> Male
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="gender" value="Female" <?= $profile['gender'] === 'Female' ? 'checked' : '' ?>> Female
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="gender" value="Other" <?= $profile['gender'] === 'Other' ? 'checked' : '' ?>> Other
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">Save</button>
                    <button type="submit" class="btn-logout"
                            formaction="/tcgzone/customer/Account/logout.php"
                            formmethod="POST"
                            formnovalidate>Log Out</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll(".change-link").forEach((link) => {
    link.addEventListener("click", (e) => {
        e.preventDefault();
        const wrapper = link.closest(".editable-field");
        const inputs = wrapper.querySelectorAll("[data-input]");
        const revertData = {};
        inputs.forEach((input) => revertData[input.dataset.input] = input.value);
        wrapper.dataset.revert = JSON.stringify(revertData);
        wrapper.classList.add("editing");
        if (inputs[0]) inputs[0].focus();
    });
});

document.querySelectorAll(".cancel-link").forEach((link) => {
    link.addEventListener("click", (e) => {
        e.preventDefault();
        const wrapper = link.closest(".editable-field");
        if (wrapper.dataset.revert) {
            const revertData = JSON.parse(wrapper.dataset.revert);
            Object.keys(revertData).forEach((key) => {
                const input = wrapper.querySelector(`[data-input="${key}"]`);
                if (input) input.value = revertData[key];
            });
        }
        wrapper.classList.remove("editing");
    });
});
</script>

</body>
</html>

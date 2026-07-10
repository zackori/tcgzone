// ======================================
// PASSWORD TOGGLE
// ======================================

const password = document.getElementById("password");
const togglePassword = document.getElementById("togglePassword");

togglePassword.addEventListener("click", function () {

    if (password.type === "password") {

        password.type = "text";
        togglePassword.classList.remove("fa-eye");
        togglePassword.classList.add("fa-eye-slash");

    } else {

        password.type = "password";
        togglePassword.classList.remove("fa-eye-slash");
        togglePassword.classList.add("fa-eye");

    }

});

// ======================================
// ONLY ACCEPTS NUMBERS FOR PHONE NUMBER
// ======================================
const phone = document.getElementById("phone");

phone.addEventListener("input", function () {
    this.value = this.value.replace(/\D/g, ""); // Remove everything except digits
});

// ======================================
// CONFIRM PASSWORD TOGGLE
// ======================================

const confirmPassword = document.getElementById("confirmPassword");
const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");

toggleConfirmPassword.addEventListener("click", function () {

    if (confirmPassword.type === "password") {

        confirmPassword.type = "text";
        toggleConfirmPassword.classList.remove("fa-eye");
        toggleConfirmPassword.classList.add("fa-eye-slash");

    } else {

        confirmPassword.type = "password";
        toggleConfirmPassword.classList.remove("fa-eye-slash");
        toggleConfirmPassword.classList.add("fa-eye");

    }

});

// ======================================
// FORM SUBMISSION
// ======================================
// No JS interception — <form method="POST" action="signup.php">
// submits for real. Required fields are enforced by the "required"
// attribute; password match + duplicate-account checks happen
// server-side in signup.php, since those need the database.

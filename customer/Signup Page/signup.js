

const params = new URLSearchParams(window.location.search);

function showError(id){

    const error = document.getElementById(id);

    error.classList.add("show");

    setTimeout(() => {

        error.classList.add("hide");

        setTimeout(() => {

            error.classList.remove("show", "hide");

        }, 400); // matches the CSS transition time

    }, 5000);

}

if (params.has("match")) {
    showError("match-error");
    document.getElementById("password").closest(".input-box").classList.add("input-error");
    document.getElementById("confirmPassword").closest(".input-box").classList.add("input-error");
}

if (params.has("password")) {
    showError("password-error");
    document.getElementById("password").closest(".input-box").classList.add("input-error");
}

if (params.has("email")) {
    showError("email-error");
    document.querySelector('input[name="email"]').closest(".input-box").classList.add("input-error");
}

if (params.has("username")) {
    showError("username-error");
    document.querySelector('input[name="username"]').closest(".input-box").classList.add("input-error");
}

if (params.has("old_email")) {
    document.querySelector('input[name="email"]').value = params.get("old_email");
}

if (params.has("old_username")) {
    document.querySelector('input[name="username"]').value = params.get("old_username");
}

if (params.has("old_phone")) {
    document.getElementById("phone").value = params.get("old_phone");
}


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
// CLEAR RED ERROR MARK ON INPUT
// ======================================
document.querySelectorAll(".input-box input").forEach(function (input) {
    input.addEventListener("input", function () {
        this.closest(".input-box").classList.remove("input-error");
    });
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
// TERMS MODAL
// ======================================

const termsLink = document.getElementById("openTermsModal");
const termsModal = document.getElementById("termsModalOverlay");
const closeTermsModal = document.getElementById("closeTermsModal");

function openTermsModal() {
    if (!termsModal) return;
    termsModal.classList.add("active");
    termsModal.setAttribute("aria-hidden", "false");
    document.body.classList.add("modal-open");
}

function closeTermsModalHandler() {
    if (!termsModal) return;
    termsModal.classList.remove("active");
    termsModal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-open");
}

if (termsLink) {
    termsLink.addEventListener("click", function (event) {
        event.preventDefault();
        openTermsModal();
    });
}

if (closeTermsModal) {
    closeTermsModal.addEventListener("click", closeTermsModalHandler);
}

if (termsModal) {
    termsModal.addEventListener("click", function (event) {
        if (event.target === termsModal) {
            closeTermsModalHandler();
        }
    });
}

document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && termsModal && termsModal.classList.contains("active")) {
        closeTermsModalHandler();
    }
});

// ======================================
// FORM SUBMISSION
// ======================================
// No JS interception — <form method="POST" action="signup.php">
// submits for real. Required fields are enforced by the "required"
// attribute; password match + duplicate-account checks happen
// server-side in signup.php, since those need the database.

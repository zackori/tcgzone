// =====================================
// LOGIN PAGE JAVASCRIPT
// =====================================
const params = new URLSearchParams(window.location.search);



if(params.get("error") === "invalid"){

    document.getElementById("login-error").style.display = "block";

};


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

// =====================================
// FORM SUBMISSION
// =====================================
// No JS interception — <form action="login.php" method="POST">
// submits for real. Required fields are enforced by the "required"
// attribute; the actual email/password check happens server-side
// in login.php, since that needs the database.

// Session Validation - Check every 30 seconds if the user's session is still valid
// This detects if an admin archived the user's account

(function initSessionValidation() {
  // Only run on customer pages (not admin)
  if (window.location.pathname.includes("/admin/")) {
    return;
  }

  const SESSION_CHECK_INTERVAL = 30000; // Check every 30 seconds

  async function validateSession() {
    try {
      const response = await fetch(
        "/tcgzone/customer/api/validate-session.php",
      );
      const data = await response.json();

      if (!data.valid) {
        // Session is no longer valid - user was archived
        console.log("Session invalidated: " + data.message);
        alert("Your account has been archived. You will be logged out.");
        window.location.href = "/tcgzone/customer/Login Page/login.html";
      }
    } catch (error) {
      // Network error - don't log out, just skip this check
      console.error("Session validation error:", error);
    }
  }

  // Run validation periodically
  setInterval(validateSession, SESSION_CHECK_INTERVAL);

  // Also validate on page visibility change (when user returns to tab)
  document.addEventListener("visibilitychange", () => {
    if (!document.hidden) {
      validateSession();
    }
  });
})();

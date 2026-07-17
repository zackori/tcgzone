    <?php
    /* ============================================================
    LOGIN — process sign in
    - Looks up the user by email, verifies the hashed password
    - Starts the session, redirects to landing page
    ============================================================ */

    require '../../config/db_connect.php';
    session_start();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: /tcgzone/customer/Login Page/login.html");
        exit;
    }

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';



    // DEFAULT ADMIN ACCOUNT

    $adminEmail = "admin@gmail.com";
    $adminPassword = "admin123";

    if ($email === $adminEmail && $password === $adminPassword) {

        $_SESSION['admin'] = true;
        $_SESSION['username'] = "Administrator";

        header("Location: /tcgzone/admin/Admin Page/overview.php");
        exit;
    }


    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        header("Location: login.html?error=invalid");
        exit;
    }

    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];

    header("Location: /tcgzone/customer/Landing Page/index.php");
    exit;

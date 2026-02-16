<?php
session_start();
require_once '../includes/db.php';

// If already logged in, redirect
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: /staymate/pages/my_bookings.php");
    exit();
}

// Handle POST (form submission)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: login.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $_SESSION['error'] = "Email not registered.";
        $stmt->close();
        header("Location: login.php");
        exit();
    }

    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['password'])) {
        $_SESSION['error'] = "Incorrect password.";
        $stmt->close();
        header("Location: login.php");
        exit();
    }

    // Set session
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['name']     = $user['name'];
    $_SESSION['role']     = $user['role'];
    $_SESSION['logged_in'] = true;

    $stmt->close();

    // Redirect based on role
    if ($user['role'] === 'admin') {
        header("Location: /staymate/admin/admin_dashboard.php");
    } else {
        header("Location: /staymate/pages/my_bookings.php");
    }
    exit();
}
// If GET — show the login form below
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StayMate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { font-family: "Poppins", sans-serif; }
        body { background: #f8f9fa; }
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .brand {
            font-size: 28px;
            font-weight: 700;
            font-family: "Merienda", cursive;
            text-align: center;
            margin-bottom: 8px;
        }
        .login-title {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 28px;
        }
        .btn-login {
            width: 100%;
            padding: 11px;
            background: #1aab8a;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-login:hover { background: #158a6e; }
        .register-link {
            text-align: center;
            margin-top: 16px;
            font-size: 14px;
            color: #666;
        }
        .register-link a { color: #1aab8a; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">

        <div class="brand">StayMate</div>
        <p class="login-title">Welcome back! Please login to continue.</p>

        <!-- Error message -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger py-2" style="font-size:14px;">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="/staymate/pages/login.php" method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Email Address</label>
                <input type="email" class="form-control shadow-none"
                       name="email" placeholder="Enter your email" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Password</label>
                <input type="password" class="form-control shadow-none"
                       name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>

        <div class="register-link">
            Don't have an account?
            <a href="/staymate/pages/register.php">Register here</a>
        </div>

        <div class="register-link mt-2">
            <a href="/staymate/index.php" style="color:#999;">← Back to Home</a>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
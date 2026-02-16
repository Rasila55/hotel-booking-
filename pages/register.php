<?php
require_once('../includes/db.php');
session_start();

// If already logged in redirect
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: /staymate/pages/my_bookings.php");
    exit();
}

// Handle POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name             = trim($_POST['name']);
    $email            = trim($_POST['email']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone            = trim($_POST['phone']);
    $address          = trim($_POST['address']);
    $pincode          = trim($_POST['pincode']);
    $dob              = $_POST['dob'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: register.php"); exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: register.php"); exit();
    }
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: register.php"); exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        $_SESSION['error'] = "Email is already registered.";
        $check_stmt->close();
        header("Location: register.php"); exit();
    }
    $check_stmt->close();

    $role = "user";
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, address, pincode, dob, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $email, $phone, $address, $pincode, $dob, $hashed_password, $role);

    if ($stmt->execute()) {
        $_SESSION['user_id']  = $conn->insert_id;
        $_SESSION['name']     = $name;
        $_SESSION['role']     = "user";
        $_SESSION['logged_in'] = true;
        $_SESSION['success']  = "Registration successful! Welcome to StayMate.";
        $stmt->close();
        header("Location: /staymate/pages/my_bookings.php"); exit();
    } else {
        $_SESSION['error'] = "Registration failed. Please try again.";
        $stmt->close();
        header("Location: register.php"); exit();
    }
}
// GET — show the form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - StayMate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { font-family: "Poppins", sans-serif; }
        body { background: #f8f9fa; }
        .register-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .register-card {
            background: #fff;
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 560px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .brand {
            font-size: 28px;
            font-weight: 700;
            font-family: "Merienda", cursive;
            text-align: center;
            margin-bottom: 6px;
        }
        .register-title {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 28px;
        }
        .btn-register {
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
        .btn-register:hover { background: #158a6e; }
        .login-link {
            text-align: center;
            margin-top: 16px;
            font-size: 14px;
            color: #666;
        }
        .login-link a { color: #1aab8a; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
<div class="register-wrapper">
    <div class="register-card">

        <div class="brand">StayMate</div>
        <p class="register-title">Create your account to start booking</p>

        <!-- Error / Success messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger py-2" style="font-size:14px;">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success py-2" style="font-size:14px;">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form action="/staymate/pages/register.php" method="POST">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Full Name</label>
                    <input type="text" class="form-control shadow-none"
                           name="name" placeholder="Your full name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" class="form-control shadow-none"
                           name="email" placeholder="Your email" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <input type="text" class="form-control shadow-none"
                           name="phone" placeholder="Your phone number">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Date of Birth</label>
                    <input type="date" class="form-control shadow-none" name="dob">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Pincode</label>
                    <input type="text" class="form-control shadow-none"
                           name="pincode" placeholder="Pincode">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Address</label>
                    <input type="text" class="form-control shadow-none"
                           name="address" placeholder="Your address">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" class="form-control shadow-none"
                           name="password" placeholder="Create password" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold">Confirm Password</label>
                    <input type="password" class="form-control shadow-none"
                           name="confirm_password" placeholder="Confirm password" required>
                </div>
            </div>

            <button type="submit" class="btn-register">Create Account</button>
        </form>

        <div class="login-link">
            Already have an account?
            <a href="/staymate/pages/login.php">Login here</a>
        </div>
        <div class="login-link mt-2">
            <a href="/staymate/index.php" style="color:#999;">← Back to Home</a>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
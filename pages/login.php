<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<form action="login.php" method="POST">
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Login</button>
</form>

<p>Don't have an account? <a href="register.php">Register here</a></p>

<?php
require_once '../includes/db.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(empty($email) || empty($password)){
        echo "All fields are required.";
        exit();
    }

    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0){
        echo "Email not registered.";
        exit();
    }

    $user = $result->fetch_assoc();

    if(!password_verify($password, $user['password'])){
        echo "Incorrect password.";
        exit();
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    if($user['role'] === 'admin'){
        header("Location: ../admin/admin_dashboard.php");
        exit();
    } else {
        header("Location: ../pages/user_dashboard.php");
        exit();
    }
}
?>
</body>
</html>

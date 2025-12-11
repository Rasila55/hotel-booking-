<?php
require_once('../includes/db.php');

session_start(); // Start session to store user info if needed

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if(empty($name) || empty($email) || empty($password) || empty($confirm_password)){
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../index.php"); // redirect back to registration page
        exit();
    }

    // Validate email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION['error'] = "Invalid email format.";
        header("Location: ../index.php");
        exit();
    }

    // Check password match
    if($password !== $confirm_password){
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: ../index.php");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        $_SESSION['error'] = "Email is already registered.";
        $stmt->close();
         header("Location: ../pages/user_dashboard.php");
        exit();
    }


    // Insert new user
    $role = "user"; // default role
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

   if($stmt->execute()){
    $_SESSION['success'] = "Registration successful.";

    // Set login session automatically after registration
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['name'] = $name;
    $_SESSION['role'] = $role;

    // Redirect
    if($role === 'admin'){
        header("Location: ../admin/admin_dashboard.php");
    } else {
        header("Location: ../pages/user_dashboard.php");
    }
    exit();
}


    $stmt->close();
    $conn->close();
}
?>

<?php
require_once('../includes/db.php');
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $pincode = trim($_POST['pincode']);
    $dob = $_POST['dob'];
    
    // Validate inputs
    if(empty($name) || empty($email) || empty($password) || empty($confirm_password)){
        $_SESSION['error'] = "All fields are required.";
        header("Location: register.php");
        exit();
    }
    
    // Validate email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION['error'] = "Invalid email format.";
        header("Location: register.php");
        exit();
    }
    
    // Check password match
    if($password !== $confirm_password){
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: register.php");
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if email exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if($check_stmt->num_rows > 0){
        $_SESSION['error'] = "Email is already registered.";
        $check_stmt->close();
        header("Location: register.php");
        exit();
    }
    $check_stmt->close();
    
    // Insert new user
    $role = "user";
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, address, pincode, dob, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $email, $phone, $address, $pincode, $dob, $hashed_password, $role);
    
    if($stmt->execute()){
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['name'] = $name;
        $_SESSION['role'] = "user";
        $_SESSION['success'] = "Registration successful!";
        
        $stmt->close();
        $conn->close();
        
        // Redirect to index.php in staymate folder
        header("Location: my_bookings.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed: " . $stmt->error;
        $stmt->close();
        $conn->close();
        header("Location: register.php");
        exit();
    }
}
?>
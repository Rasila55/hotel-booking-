<?php 
session_start();
require_once '../includes/db.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if(empty($email) || empty($password)){
        $_SESSION['error'] = "All fields are required.";
        header("Location: login.php");
        exit();
    }
    
    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 0){
        $_SESSION['error'] = "Email not registered.";
        $stmt->close();
        header("Location: login.php");
        exit();
    }
    
    $user = $result->fetch_assoc();
    
    if(!password_verify($password, $user['password'])){
        $_SESSION['error'] = "Incorrect password.";
        $stmt->close();
        header("Location: login.php");
        exit();
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['logged_in'] = true;
    
    $stmt->close();
    $conn->close();
    
    // Redirect based on role
    if($user['role'] === 'admin'){
        header("Location: ../admin/admin_dashboard.php");
        exit();
    } else {
        header("Location: my_bookings.php"); 
        exit();
    }
}
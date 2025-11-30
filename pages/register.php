<?php 
require_once('../includes/db.php');

//checking if the form is submitted
if($_SERVER["REQUEST_METHOD"]=="POST"){

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    //validating the form inputs
    if(empty($name) || empty($email) || empty($password) || empty($confirm_password)){
        echo "All fields are required.";
        exit();
    }

    //validating email format
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "Invalid email format.";
        exit();
    }

    //checking if password and confirm password match
    if($password !== $confirm_password){
        echo "Passwords do not match.";
        exit();
    }

    //hashing the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    //checking if the email is already registered
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        echo "Email is already registered.";
        exit();
    }

    //Inserting the new user into the database
    $role = "user"; // default role

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

    if($stmt->execute()){
        echo "Registration successful.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

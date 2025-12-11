<?php 
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../index.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Welcome, <?php echo $_SESSION['name']; ?> 👋</h2>
    <p>Email: <?php echo $_SESSION['email'] ?? 'Not stored'; ?></p>
    <p>Role: <?php echo $_SESSION['role']; ?></p>

    <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
</div>



</body>
</html>























<?php
session_start();
require_once '../includes/db.php';

// Must be logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /staymate/pages/login.php');
    exit();
}

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id    = $_SESSION['user_id'];

if ($booking_id > 0) {
    // Make sure this booking belongs to this user
    $sql    = "SELECT id FROM bookings WHERE id = $booking_id AND user_id = $user_id AND (status = 'pending' OR status = 'confirmed')";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Cancel it
        $update = "UPDATE bookings SET status = 'cancelled' WHERE id = $booking_id";
        mysqli_query($conn, $update);
        $_SESSION['success'] = "Booking #$booking_id has been cancelled.";
    } else {
        $_SESSION['error'] = "Booking not found or cannot be cancelled.";
    }
}

header('Location: /staymate/pages/my_bookings.php');
exit();
?>
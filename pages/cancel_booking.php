<?php
session_start();
require_once('../includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

// Check if booking ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid booking ID";
    header('Location: my_bookings.php');
    exit();
}

$booking_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Verify this booking belongs to the logged-in user
$verify_query = "SELECT id, status, check_in FROM bookings WHERE id = $booking_id AND user_id = $user_id";
$verify_result = mysqli_query($conn, $verify_query);

if (mysqli_num_rows($verify_result) == 0) {
    $_SESSION['error'] = "Booking not found or you don't have permission to cancel it";
    mysqli_close($conn);
    header('Location: my_bookings.php');
    exit();
}

$booking = mysqli_fetch_assoc($verify_result);

// Check if booking can be cancelled (only pending or confirmed)
if ($booking['status'] == 'cancelled') {
    $_SESSION['error'] = "This booking is already cancelled";
    mysqli_close($conn);
    header('Location: my_bookings.php');
    exit();
}

// Check if check-in date has passed
if (strtotime($booking['check_in']) < strtotime(date('Y-m-d'))) {
    $_SESSION['error'] = "Cannot cancel past bookings";
    mysqli_close($conn);
    header('Location: my_bookings.php');
    exit();
}

// Cancel the booking
$cancel_query = "UPDATE bookings SET status = 'cancelled' WHERE id = $booking_id";

if (mysqli_query($conn, $cancel_query)) {
    // Also update room status back to available
    $update_room_query = "UPDATE rooms r 
                         INNER JOIN bookings b ON r.id = b.room_id 
                         SET r.status = 'available' 
                         WHERE b.id = $booking_id";
    mysqli_query($conn, $update_room_query);
    
    $_SESSION['success'] = "Booking cancelled successfully!";
} else {
    $_SESSION['error'] = "Error cancelling booking: " . mysqli_error($conn);
}

mysqli_close($conn);
header('Location: my_bookings.php');
exit();
?>
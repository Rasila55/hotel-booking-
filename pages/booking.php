<?php
session_start();

require_once('db.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // $conn = getConnection();
    
    // Get form data
    $check_in = mysqli_real_escape_string($conn, trim($_POST['checkin']));
    $check_out = mysqli_real_escape_string($conn, trim($_POST['checkout']));
    $adults = intval($_POST['adults']);
    $children = intval($_POST['children']);
    
    // Initialize errors array
    $errors = array();
    
    // Validation
    if (empty($check_in) || empty($check_out)) {
        $errors[] = "Check-in and check-out dates are required";
    }
    
    if ($adults < 1) {
        $errors[] = "At least one adult is required";
    }
    
    // Validate dates
    $checkin_timestamp = strtotime($check_in);
    $checkout_timestamp = strtotime($check_out);
    $today_timestamp = strtotime(date('Y-m-d'));
    
    if ($checkin_timestamp < $today_timestamp) {
        $errors[] = "Check-in date cannot be in the past";
    }
    
    if ($checkout_timestamp <= $checkin_timestamp) {
        $errors[] = "Check-out date must be after check-in date";
    }
    
    // If there are validation errors
    if (count($errors) > 0) {
        $_SESSION['booking_message'] = implode(', ', $errors);
        $_SESSION['booking_message_type'] = 'danger';
        header('Location: index.php');
        exit();
    }
    
    // Calculate number of nights
    $nights = floor(($checkout_timestamp - $checkin_timestamp) / (60 * 60 * 24));
    
    // Get total guests
    $total_guests = $adults + $children;
    
    // Find available room that matches capacity
    $available_room_query = "
        SELECT r.id, r.room_number, r.room_type, r.price, r.capacity 
        FROM rooms r
        WHERE r.status = 'available' 
        AND r.capacity >= $total_guests
        AND r.id NOT IN (
            SELECT room_id 
            FROM bookings 
            WHERE status != 'cancelled' 
            AND (
                (check_in <= '$check_out' AND check_out >= '$check_in')
            )
        )
        ORDER BY r.price ASC
        LIMIT 1
    ";
    
    $result = mysqli_query($conn, $available_room_query);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        $_SESSION['booking_message'] = "Sorry, no rooms available for selected dates and number of guests";
        $_SESSION['booking_message_type'] = 'warning';
        mysqli_close($conn);
        header('Location: index.php');
        exit();
    }
    
    // Get available room details
    $room = mysqli_fetch_assoc($result);
    $room_id = $room['id'];
    $room_price = $room['price'];
    
    // Calculate total price
    $total_price = $nights * $room_price;
    
    // Get user_id from session (if logged in)
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 'NULL';
    
    // Insert booking
    $insert_query = "
        INSERT INTO bookings 
        (user_id, room_id, check_in, check_out, adults, children, total_price, status, created_at) 
        VALUES 
        ($user_id, $room_id, '$check_in', '$check_out', $adults, $children, $total_price, 'pending', NOW())
    ";
    
    if (mysqli_query($conn, $insert_query)) {
        $booking_id = mysqli_insert_id($conn);
        
        // Store booking details in session
        $_SESSION['booking_message'] = "Booking successful! Room {$room['room_number']} ({$room['room_type']}) reserved for $nights night(s). Total: $" . number_format($total_price, 2);
        $_SESSION['booking_message_type'] = 'success';
        
        $_SESSION['last_bookings'] = array(
            'booking_id' => $booking_id,
            'room_id' => $room_id,
            'room_number' => $room['room_number'],
            'room_type' => $room['room_type'],
            'check_in' => $check_in,
            'check_out' => $check_out,
            'adults' => $adults,
            'children' => $children,
            'nights' => $nights,
            'price_per_night' => $room_price,
            'total_price' => $total_price
        );
    } else {
        $_SESSION['booking_message'] = "Error creating booking: " . mysqli_error($conn);
        $_SESSION['booking_message_type'] = 'danger';
    }
    
    mysqli_close($conn);
    header('Location: index.php');
    exit();
    
} else {
    header('Location: index.php');
    exit();
}
?>
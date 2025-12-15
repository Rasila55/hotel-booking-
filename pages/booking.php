<?php
// START SESSION FIRST - This must be the very first line!
session_start();

// Include database connection
require_once '../includes/db.php';


// CHECK IF USER IS LOGGED IN
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['booking_message'] = "Please login or register to make a booking";
    $_SESSION['booking_message_type'] = 'warning';
    header('Location: ../index.php');
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
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
        header('Location: ../index.php');
        exit();
    }
    
    // Calculate number of nights
    $nights = floor(($checkout_timestamp - $checkin_timestamp) / (60 * 60 * 24));
    
    // Get total guests
    $total_guests = $adults + $children;



// ===== ADD THIS DEBUG CODE =====
echo "<h3>Debug Info:</h3>";
echo "Check-in: $check_in<br>";
echo "Check-out: $check_out<br>";
echo "Adults: $adults<br>";
echo "Children: $children<br>";
echo "Total guests: " . ($adults + $children) . "<br><br>";


// Find available room that matches capacity
        $available_room_query = "
        SELECT r.id, r.room_number, r.room_type, r.price, r.capacity 
        FROM rooms r
        WHERE r.status = 'available' 
        AND r.capacity >= $total_guests
        AND r.id NOT IN (
            SELECT room_id 
            FROM bookings 
            WHERE (status = 'pending' OR status = 'confirmed')
            AND NOT (
                check_out <= '$check_in' OR check_in >= '$check_out'
            )
        )
        ORDER BY r.price ASC
        LIMIT 1
        "; 
    $result = mysqli_query($conn, $available_room_query);
    
    // Check if rooms available
    if (!$result || mysqli_num_rows($result) == 0) {
        $_SESSION['booking_message'] = "Sorry, no rooms available for selected dates and number of guests";
        $_SESSION['booking_message_type'] = 'warning';
        mysqli_close($conn);
        header('Location: ../index.php'); // Fixed: redirect to homepage, not confirmation
        exit();
    }
    
    // Get available room details
    $room = mysqli_fetch_assoc($result);
    $room_id = $room['id'];
    $room_price = $room['price'];
    
    // Calculate total price
    $total_price = $nights * $room_price;
    
    // Get user_id from session (we know user is logged in now)
    $user_id = $_SESSION['user_id'];
    
    // Insert booking
    $insert_query = "
        INSERT INTO bookings 
        (user_id, room_id, check_in, check_out, adults, children, total_price, status, created_at) 
        VALUES 
        ($user_id, $room_id, '$check_in', '$check_out', $adults, $children, $total_price, 'pending', NOW())
    ";
    
    if (mysqli_query($conn, $insert_query)) {
        $booking_id = mysqli_insert_id($conn);
        
        // Store booking details in session for confirmation page
        $_SESSION['last_booking'] = array(
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
        
        mysqli_close($conn);
        // Redirect to confirmation page
        header('Location: booking_confirmation.php');
        exit();
        
    } else {
        // Booking insert failed
        $_SESSION['booking_message'] = "Error creating booking: " . mysqli_error($conn);
        $_SESSION['booking_message_type'] = 'danger';
        mysqli_close($conn);
        header('Location: ../index.php');
        exit();
    }
    
} else {
    // Not a POST request
    header('Location: ../index.php');
    exit();
}
?>
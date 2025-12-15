<?php
session_start();
require_once('../includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

// Check if booking details exist in session
if (!isset($_SESSION['last_booking'])) {
    header('Location: ../index.php');
    exit();
}

$booking = $_SESSION['last_booking'];
$user_name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - StayMate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        * {
            font-family: "Poppins", sans-serif;
        }
        .h-font {
            font-family: "Merienda", cursive;
        }
        .confirmation-card {
            max-width: 800px;
            margin: 50px auto;
        }
        .success-icon {
            font-size: 80px;
            color: #198754;
        }
        .booking-detail-row {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        .booking-detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .detail-value {
            color: #212529;
        }
        .status-badge {
            font-size: 14px;
            padding: 6px 15px;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 shadow-sm sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="../index.php">StayMate</a>
            <div class="d-flex align-items-center">
                <span class="me-3">Welcome, <?php echo htmlspecialchars($user_name); ?></span>
                <a href="../index.php" class="btn btn-outline-dark shadow-none">Back to Home</a>
            </div>
        </div>
    </nav>

    <!-- Confirmation Content -->
    <div class="container">
        <div class="confirmation-card">
            <div class="card shadow border-0">
                <div class="card-body p-5 text-center">
                    <!-- Success Icon -->
                    <i class="bi bi-check-circle-fill success-icon"></i>
                    
                    <h2 class="mt-4 mb-2 h-font">Booking Confirmed!</h2>
                    <p class="text-muted mb-4">Your reservation has been successfully created</p>
                    
                    <!-- Booking Details -->
                    <div class="text-start mt-5">
                        <h5 class="mb-4">Booking Details</h5>
                        
                        <div class="booking-detail-row row">
                            <div class="col-md-4 detail-label">Booking ID:</div>
                            <div class="col-md-8 detail-value">#<?php echo $booking['booking_id']; ?></div>
                        </div>
                        
                        <div class="booking-detail-row row">
                            <div class="col-md-4 detail-label">Room Number:</div>
                            <div class="col-md-8 detail-value"><?php echo $booking['room_number']; ?></div>
                        </div>
                        
                        <div class="booking-detail-row row">
                            <div class="col-md-4 detail-label">Room Type:</div>
                            <div class="col-md-8 detail-value text-capitalize"><?php echo $booking['room_type']; ?></div>
                        </div>
                        
                        <div class="booking-detail-row row">
                            <div class="col-md-4 detail-label">Check-in Date:</div>
                            <div class="col-md-8 detail-value"><?php echo date('d M Y', strtotime($booking['check_in'])); ?></div>
                        </div>
                        
                        <div class="booking-detail-row row">
                            <div class="col-md-4 detail-label">Check-out Date:</div>
                            <div class="col-md-8 detail-value"><?php echo date('d M Y', strtotime($booking['check_out'])); ?></div>
                        </div>
                        
                        <div class="booking-detail-row row">
                            <div class="col-md-4 detail-label">Number of Nights:</div>
                            <div class="col-md-8 detail-value"><?php echo $booking['nights']; ?> night(s)</div>
                        </div>
                        
                        <div class="booking-detail-row row">
                            <div class="col-md-4 detail-label">Guests:</div>
                            <div class="col-md-8 detail-value"><?php echo $booking['adults']; ?> Adult(s), <?php echo $booking['children']; ?> Child(ren)</div>
                        </div>
                        
                        <div class="booking-detail-row row">
                            <div class="col-md-4 detail-label">Price per Night:</div>
                            <div class="col-md-8 detail-value">Rs. <?php echo number_format($booking['price_per_night'], 2); ?></div>
                        </div>
                        
                        <div class="booking-detail-row row">
                            <div class="col-md-4 detail-label">Status:</div>
                            <div class="col-md-8 detail-value">
                                <span class="badge bg-warning text-dark status-badge">Pending Confirmation</span>
                            </div>
                        </div>
                        
                        <div class="booking-detail-row row bg-light">
                            <div class="col-md-4 detail-label fs-5">Total Amount:</div>
                            <div class="col-md-8 detail-value fs-5 fw-bold text-success">
                                Rs. <?php echo number_format($booking['total_price'], 2); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-5 d-flex gap-3 justify-content-center">
                        <a href="my_bookings.php" class="btn btn-primary btn-lg shadow-none">
                            <i class="bi bi-calendar-check me-2"></i>View My Bookings
                        </a>
                        <a href="../index.php" class="btn btn-outline-secondary btn-lg shadow-none">
                            <i class="bi bi-house-door me-2"></i>Back to Home
                        </a>
                    </div>
                    
                    <!-- Info Message -->
                    <div class="alert alert-info mt-5 text-start" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Next Steps:</strong> Your booking is currently pending. Admin will confirm your reservation shortly. You will be notified once confirmed.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
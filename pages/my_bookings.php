<?php
session_start();
require_once('../includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['error'] = "Please login to view your bookings";
    header('Location: ../index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];

// Get all bookings for this user with room details
$bookings_query = "
    SELECT 
        b.id as booking_id,
        b.check_in,
        b.check_out,
        b.adults,
        b.children,
        b.total_price,
        b.status,
        b.created_at,
        r.room_number,
        r.room_type,
        r.price as price_per_night,
        DATEDIFF(b.check_out, b.check_in) as nights
    FROM bookings b
    INNER JOIN rooms r ON b.room_id = r.id
    WHERE b.user_id = $user_id
    ORDER BY b.created_at DESC
";

$result = mysqli_query($conn, $bookings_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - StayMate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        * {
            font-family: "Poppins", sans-serif;
        }
        .h-font {
            font-family: "Merienda", cursive;
        }
        .booking-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
        }
        .status-badge {
            font-size: 13px;
            padding: 5px 12px;
        }
        .no-bookings {
            padding: 60px 20px;
            text-align: center;
        }
        .no-bookings i {
            font-size: 80px;
            color: #6c757d;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 shadow-sm sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="../index.php">StayMate</a>
            <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="my_bookings.php">My Bookings</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($user_name); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="user_dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="edit_profile.php"><i class="bi bi-person-gear me-2"></i>Edit Profile</a></li>
                            <li><a class="dropdown-item" href="change_password.php"><i class="bi bi-key me-2"></i>Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container my-5">
        <!-- Success/Error Messages -->
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?php echo $_SESSION['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i><?php echo $_SESSION['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4 h-font"><i class="bi bi-calendar-check me-2"></i>My Bookings</h2>
                
                <?php if (mysqli_num_rows($result) == 0): ?>
                    <!-- No Bookings -->
                    <div class="card shadow-sm border-0">
                        <div class="card-body no-bookings">
                            <i class="bi bi-calendar-x"></i>
                            <h4>No Bookings Yet</h4>
                            <p class="text-muted">You haven't made any bookings yet. Start exploring our rooms!</p>
                            <a href="../index.php" class="btn btn-primary mt-3">
                                <i class="bi bi-house-door me-2"></i>Browse Rooms
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Bookings List -->
                    <div class="row">
                        <?php while($booking = mysqli_fetch_assoc($result)): 
                            // Determine status badge color
                            $badge_class = 'bg-secondary';
                            if($booking['status'] == 'pending') $badge_class = 'bg-warning text-dark';
                            if($booking['status'] == 'confirmed') $badge_class = 'bg-success';
                            if($booking['status'] == 'cancelled') $badge_class = 'bg-danger';
                            
                            // Check if booking is upcoming or past
                            $is_upcoming = strtotime($booking['check_in']) >= strtotime(date('Y-m-d'));
                        ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card booking-card shadow-sm border-0 h-100">
                                    <div class="card-body">
                                        <!-- Booking ID and Status -->
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0 text-muted">Booking #<?php echo $booking['booking_id']; ?></h6>
                                            <span class="badge status-badge <?php echo $badge_class; ?>">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </div>
                                        
                                        <!-- Room Details -->
                                        <h5 class="card-title mb-3">
                                            <i class="bi bi-door-open me-2"></i>Room <?php echo $booking['room_number']; ?>
                                        </h5>
                                        <p class="text-muted mb-3 text-capitalize">
                                            <small><?php echo $booking['room_type']; ?> Room</small>
                                        </p>
                                        
                                        <!-- Dates -->
                                        <div class="mb-2">
                                            <i class="bi bi-calendar-event me-2 text-primary"></i>
                                            <small>
                                                <strong>Check-in:</strong> <?php echo date('d M Y', strtotime($booking['check_in'])); ?>
                                            </small>
                                        </div>
                                        <div class="mb-2">
                                            <i class="bi bi-calendar-event me-2 text-danger"></i>
                                            <small>
                                                <strong>Check-out:</strong> <?php echo date('d M Y', strtotime($booking['check_out'])); ?>
                                            </small>
                                        </div>
                                        
                                        <!-- Nights and Guests -->
                                        <div class="mb-2">
                                            <i class="bi bi-moon-stars me-2 text-info"></i>
                                            <small><?php echo $booking['nights']; ?> night(s)</small>
                                        </div>
                                        <div class="mb-3">
                                            <i class="bi bi-people me-2 text-success"></i>
                                            <small><?php echo $booking['adults']; ?> Adult(s), <?php echo $booking['children']; ?> Child(ren)</small>
                                        </div>
                                        
                                        <!-- Price -->
                                        <div class="border-top pt-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted"><small>Total Amount</small></span>
                                                <h5 class="mb-0 text-success">Rs. <?php echo number_format($booking['total_price'], 2); ?></h5>
                                            </div>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="mt-3">
                                            <?php if($booking['status'] == 'pending' || $booking['status'] == 'confirmed'): ?>
                                                <?php if($is_upcoming): ?>
                                                    <button class="btn btn-sm btn-outline-danger w-100" 
                                                            onclick="cancelBooking(<?php echo $booking['booking_id']; ?>)">
                                                        <i class="bi bi-x-circle me-1"></i>Cancel Booking
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary w-100 p-2">Completed</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>Booked on: <?php echo date('d M Y', strtotime($booking['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>

    <!-- Cancel Booking Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this booking?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                    <button type="button" class="btn btn-danger" id="confirmCancelBtn">Yes, Cancel Booking</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let bookingIdToCancel = null;
        
        function cancelBooking(bookingId) {
            bookingIdToCancel = bookingId;
            const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
            modal.show();
        }
        
        document.getElementById('confirmCancelBtn').addEventListener('click', function() {
            if(bookingIdToCancel) {
                // Send cancel request
                window.location.href = 'cancel_booking.php?id=' + bookingIdToCancel;
            }
        });
    </script>
</body>
</html>

<?php mysqli_close($conn); ?>
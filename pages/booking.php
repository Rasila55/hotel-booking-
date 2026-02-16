<?php
session_start();
require_once '../includes/db.php';

// Must be logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /staymate/pages/login.php');
    exit();
}

// Get room_id from URL
$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;
if ($room_id === 0) {
    header('Location: /staymate/pages/rooms.php');
    exit();
}

// Get room from DB
$sql    = "SELECT r.*, h.name AS hotel_name FROM rooms r LEFT JOIN hotels h ON r.hotel_id = h.id WHERE r.id = $room_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$room   = mysqli_fetch_assoc($result);

if (!$room) {
    header('Location: /staymate/pages/rooms.php');
    exit();
}

// Pre-fill from URL if coming from rooms page with filters
$checkin  = isset($_GET['checkin'])  ? $_GET['checkin']  : '';
$checkout = isset($_GET['checkout']) ? $_GET['checkout'] : '';

include '../includes/header.php';
?>

<style>
    .booking-wrapper { max-width:680px; margin:50px auto; padding:0 20px 60px; }
    .room-summary { display:flex; gap:16px; align-items:center; background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:16px; margin-bottom:24px; }
    .room-summary img { width:100px; height:80px; object-fit:cover; border-radius:6px; }
    .room-summary h5 { margin:0 0 4px; font-size:16px; font-weight:600; }
    .room-summary .price { color:#1aab8a; font-weight:700; font-size:15px; }
    .form-card { background:#fff; border:1px solid #e0e0e0; border-radius:8px; padding:28px; }
    .form-card h5 { font-weight:700; margin-bottom:20px; font-size:16px; }
    .price-box { background:#f8f9fa; border-radius:6px; padding:16px; margin:20px 0; }
    .price-box .label { font-size:13px; color:#666; }
    .price-box .amount { font-size:22px; font-weight:700; color:#1aab8a; }
    .btn-confirm { width:100%; padding:12px; background:#1aab8a; color:#fff; border:none; border-radius:6px; font-size:15px; font-weight:600; cursor:pointer; }
    .btn-confirm:hover { background:#158a6e; }
    .btn-back { display:block; text-align:center; margin-top:12px; color:#666; text-decoration:none; font-size:14px; }
    .btn-back:hover { color:#333; }
</style>

<div class="booking-wrapper">
    <h3 class="fw-bold mb-4">Complete Your Booking</h3>

    <!-- Room Summary -->
    <div class="room-summary">
        <img src="/staymate/images/rooms/room1.png" alt="Room">
        <div>
            <h5><?php echo htmlspecialchars($room['room_type']); ?> Room</h5>
            <p style="margin:0;color:#999;font-size:13px;"><?php echo htmlspecialchars($room['hotel_name']); ?></p>
            <p class="price">Rs. <?php echo number_format($room['price']); ?> / night</p>
        </div>
    </div>

    <!-- Booking Form — posts to your existing booking.php -->
    <div class="form-card">
        <h5>Enter Your Details</h5>
        <form action="/staymate/pages/booking.php" method="POST">
            <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Check-in Date</label>
                    <input type="date" class="form-control shadow-none" name="checkin"
                           value="<?php echo $checkin; ?>"
                           min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Check-out Date</label>
                    <input type="date" class="form-control shadow-none" name="checkout"
                           value="<?php echo $checkout; ?>"
                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Adults</label>
                    <select class="form-select shadow-none" name="adults" required>
                        <?php for($i = 1; $i <= ($room['max_adults'] ?: 5); $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Children</label>
                    <select class="form-select shadow-none" name="children">
                        <?php for($i = 0; $i <= ($room['max_children'] ?: 3); $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <!-- Price info -->
            <div class="price-box">
                <div class="label">Price per night</div>
                <div class="amount">Rs. <?php echo number_format($room['price']); ?></div>
                <div style="font-size:12px;color:#999;margin-top:4px;">
                    Total = price × number of nights
                </div>
            </div>

            <button type="submit" class="btn-confirm">✓ Confirm Booking</button>
        </form>
        <a href="/staymate/pages/rooms.php" class="btn-back">← Back to Rooms</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
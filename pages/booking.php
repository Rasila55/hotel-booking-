<?php
session_start();
require_once '../includes/db.php';

// Must be logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /staymate/pages/login.php');
    exit();
}

// ═══════════════════════════════════════════════════
// HANDLE POST — this was completely missing before!
// ═══════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $room_id  = isset($_POST['room_id'])  ? (int)$_POST['room_id']  : 0;
    $checkin  = isset($_POST['checkin'])  ? trim($_POST['checkin'])  : '';
    $checkout = isset($_POST['checkout']) ? trim($_POST['checkout']) : '';
    $adults   = isset($_POST['adults'])   ? (int)$_POST['adults']   : 1;
    $children = isset($_POST['children']) ? (int)$_POST['children'] : 0;
    $user_id  = $_SESSION['user_id'];

    // ── Validate ──
    if ($room_id === 0 || empty($checkin) || empty($checkout)) {
        $_SESSION['error'] = "Please fill in all required fields.";
        header('Location: /staymate/pages/booking.php?room_id=' . $room_id);
        exit();
    }

    $checkin_date  = new DateTime($checkin);
    $checkout_date = new DateTime($checkout);

    if ($checkout_date <= $checkin_date) {
        $_SESSION['error'] = "Check-out date must be after check-in date.";
        header('Location: /staymate/pages/booking.php?room_id=' . $room_id);
        exit();
    }

    $nights = $checkout_date->diff($checkin_date)->days;

    if ($nights < 1) {
        $_SESSION['error'] = "Minimum booking is 1 night.";
        header('Location: /staymate/pages/booking.php?room_id=' . $room_id);
        exit();
    }

    // ── Get room price from DB ──
    $room_sql    = "SELECT r.*, h.name AS hotel_name FROM rooms r LEFT JOIN hotels h ON r.hotel_id = h.id WHERE r.id = $room_id LIMIT 1";
    $room_result = mysqli_query($conn, $room_sql);
    $room        = mysqli_fetch_assoc($room_result);

    if (!$room) {
        $_SESSION['error'] = "Room not found.";
        header('Location: /staymate/pages/rooms.php');
        exit();
    }

    $total_price = $nights * $room['price'];

    // ── Insert booking into DB ──
    $stmt = $conn->prepare("
        INSERT INTO bookings (user_id, room_id, check_in, check_out, adults, children, nights, total_price, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    $stmt->bind_param(
        "iissiiid",
        $user_id,
        $room_id,
        $checkin,
        $checkout,
        $adults,
        $children,
        $nights,
        $total_price
    );

    if ($stmt->execute()) {
        $booking_id = $conn->insert_id;

        // ── Save booking details in session for confirmation page ──
        $_SESSION['last_booking'] = [
            'booking_id'     => $booking_id,
            'room_number'    => $room['room_number'],
            'room_type'      => $room['room_type'],
            'hotel_name'     => $room['hotel_name'],
            'check_in'       => $checkin,
            'check_out'      => $checkout,
            'adults'         => $adults,
            'children'       => $children,
            'nights'         => $nights,
            'price_per_night'=> $room['price'],
            'total_price'    => $total_price,
        ];

        $_SESSION['success'] = "Booking confirmed! Your booking #$booking_id has been placed successfully.";

        // ── Redirect to confirmation page ──
        header('Location: /staymate/pages/booking_confirmation.php');
        exit();

    } else {
        $_SESSION['error'] = "Booking failed. Please try again. Error: " . $conn->error;
        header('Location: /staymate/pages/booking.php?room_id=' . $room_id);
        exit();
    }
}

// ═══════════════════════════════════════════════════
// SHOW FORM (GET request)
// ═══════════════════════════════════════════════════

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

// Pre-fill dates from URL if coming from rooms page
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
    .price-preview { background:#e6f7f2; border:1px solid #b2dfdb; border-radius:6px; padding:14px 16px; margin:16px 0; display:none; }
    .price-preview .total-label { font-size:13px; color:#555; }
    .price-preview .total-amount { font-size:20px; font-weight:700; color:#1aab8a; }
    .btn-confirm { width:100%; padding:12px; background:#1aab8a; color:#fff; border:none; border-radius:6px; font-size:15px; font-weight:600; cursor:pointer; }
    .btn-confirm:hover { background:#158a6e; }
    .btn-back { display:block; text-align:center; margin-top:12px; color:#666; text-decoration:none; font-size:14px; }
    .btn-back:hover { color:#333; }
    .alert { padding:12px 16px; border-radius:6px; margin-bottom:16px; font-size:14px; }
    .alert-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
    .alert-danger  { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
</style>

<div class="booking-wrapper">
    <h3 class="fw-bold mb-4">Complete Your Booking</h3>

    <!-- ── Session messages ── -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Room Summary -->
    <div class="room-summary">
        <?php if (!empty($room['image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/staymate/admin/uploads/rooms/' . $room['image'])): ?>
            <img src="/staymate/admin/uploads/rooms/<?php echo htmlspecialchars($room['image']); ?>" alt="Room">
        <?php else: ?>
            <img src="/staymate/images/rooms/room1.png" alt="Room">
        <?php endif; ?>
        <div>
            <h5><?php echo htmlspecialchars($room['room_type']); ?> Room — <?php echo htmlspecialchars($room['room_number']); ?></h5>
            <p style="margin:0;color:#999;font-size:13px;"><?php echo htmlspecialchars($room['hotel_name']); ?></p>
            <p class="price">Rs. <?php echo number_format($room['price']); ?> / night</p>
        </div>
    </div>

    <!-- Booking Form -->
    <div class="form-card">
        <h5>Enter Your Details</h5>

        <!-- form posts to itself (POST handler is at top of this file) -->
        <form action="/staymate/pages/booking.php" method="POST" id="bookingForm">
            <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Check-in Date</label>
                    <input type="date" class="form-control shadow-none" name="checkin" id="checkin"
                           value="<?php echo htmlspecialchars($checkin); ?>"
                           min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Check-out Date</label>
                    <input type="date" class="form-control shadow-none" name="checkout" id="checkout"
                           value="<?php echo htmlspecialchars($checkout); ?>"
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

            <!-- Price per night -->
            <div class="price-box">
                <div class="label">Price per night</div>
                <div class="amount">Rs. <?php echo number_format($room['price']); ?></div>
            </div>

            <!-- Live price preview (shown when dates are picked) -->
            <div class="price-preview" id="pricePreview">
                <div class="total-label" id="nightsLabel"></div>
                <div class="total-amount" id="totalAmount"></div>
            </div>

            <button type="submit" class="btn-confirm">✓ Confirm Booking</button>
        </form>
        <a href="/staymate/pages/rooms.php" class="btn-back">← Back to Rooms</a>
    </div>
</div>

<script>
const pricePerNight = <?php echo (float)$room['price']; ?>;

function updatePrice() {
    const checkin  = document.getElementById('checkin').value;
    const checkout = document.getElementById('checkout').value;
    const preview  = document.getElementById('pricePreview');

    if (!checkin || !checkout) { preview.style.display = 'none'; return; }

    const d1 = new Date(checkin);
    const d2 = new Date(checkout);
    const nights = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));

    if (nights < 1) { preview.style.display = 'none'; return; }

    const total = nights * pricePerNight;
    document.getElementById('nightsLabel').textContent  = nights + ' night(s) × Rs. ' + pricePerNight.toLocaleString();
    document.getElementById('totalAmount').textContent  = 'Total: Rs. ' + total.toLocaleString();
    preview.style.display = 'block';
}

document.getElementById('checkin').addEventListener('change', function() {
    // Set checkout min to day after checkin
    const next = new Date(this.value);
    next.setDate(next.getDate() + 1);
    document.getElementById('checkout').min = next.toISOString().split('T')[0];
    updatePrice();
});
document.getElementById('checkout').addEventListener('change', updatePrice);

// Run on load if dates pre-filled
updatePrice();
</script>

<?php include '../includes/footer.php'; ?>
<?php
$page_title = "Bookings Management";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (deleteById('bookings', $id)) {
        $_SESSION['success'] = "Booking deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete booking!";
    }
    header('Location: ' . BASE_PATH . '/bookings');
    exit();
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $user_id  = (int)$_POST['user_id'];
    $room_id  = (int)$_POST['room_id'];
    $check_in  = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $adults   = (int)$_POST['adults'];
    $children = (int)$_POST['children'];
    $status   = $_POST['status'];

    // Calculate nights & total price
    $nights = max(1, (int)((strtotime($check_out) - strtotime($check_in)) / 86400));

    // Get room price
    $room_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price FROM rooms WHERE id = $room_id"));
    $total_price = $nights * ($room_row ? $room_row['price'] : 0);

    $data = [
        'user_id'     => $user_id,
        'room_id'     => $room_id,
        'check_in'    => $check_in,
        'check_out'   => $check_out,
        'adults'      => $adults,
        'children'    => $children,
        'nights'      => $nights,
        'total_price' => $total_price,
        'status'      => $status,
    ];

    if ($id > 0) {
        if (updateById('bookings', $id, $data)) {
            $_SESSION['success'] = "Booking updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update booking!";
        }
    } else {
        if (create('bookings', $data)) {
            $_SESSION['success'] = "Booking added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add booking!";
        }
    }

    header('Location: ' . BASE_PATH . '/bookings');
    exit();
}

// Get booking for editing
$editBooking = null;
if (isset($_GET['edit'])) {
    $editBooking = readOne('bookings', (int)$_GET['edit']);
}

// Get all bookings with user & room info
$bookings = query(
    "SELECT b.*,
            u.name AS user_name, u.email AS user_email,
            r.room_number, r.room_type, r.price AS room_price,
            h.name AS hotel_name
     FROM bookings b
     LEFT JOIN users u  ON b.user_id = u.id
     LEFT JOIN rooms r  ON b.room_id = r.id
     LEFT JOIN hotels h ON r.hotel_id = h.id
     ORDER BY b.id DESC",
    [], ''
);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
<style>
    /* ── Base ── */
    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        transition: all 0.3s;
    }
    .btn-primary   { background: #667eea; color: white; }
    .btn-primary:hover { background: #5568d3; }
    .btn-warning   { background: #ffc107; color: #333; }
    .btn-danger    { background: #dc3545; color: white; }
    .btn-danger:hover { background: #c82333; }

    /* ── Alerts ── */
    .alert { padding: 12px 20px; border-radius: 5px; margin-bottom: 20px; }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    /* ── Form ── */
    .form-group { margin-bottom: 20px; }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #333;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        box-sizing: border-box;
    }
    .form-group textarea { min-height: 80px; resize: vertical; }

    /* ── Stats grid ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 15px;
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2,1fr); } }
    .stat-item small { color: #666; display: block; font-size: 12px; }
    .stat-item .stat-value { font-size: 24px; font-weight: bold; margin-top: 4px; }

    /* ── Table ── */
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    table th {
        background: #f8f9fa;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        font-size: 13px;
    }
    table td { padding: 12px; border-bottom: 1px solid #dee2e6; font-size: 13px; vertical-align: middle; }
    table tr:hover { background: #fafafa; }

    /* ── Badges ── */
    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .status-pending    { background: #fff3cd; color: #856404; }
    .status-confirmed  { background: #d4edda; color: #155724; }
    .status-cancelled  { background: #f8d7da; color: #721c24; }
    .status-completed  { background: #cce5ff; color: #004085; }

    .room-type-badge {
        padding: 3px 9px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
    }
    .type-single  { background: #e3f2fd; color: #1976d2; }
    .type-double  { background: #f3e5f5; color: #7b1fa2; }
    .type-suite   { background: #fff3e0; color: #e65100; }
    .type-deluxe  { background: #fce4ec; color: #c2185b; }

    .price-tag { font-weight: bold; color: #667eea; }

    .action-buttons { display: flex; gap: 5px; }

    /* ── Price auto-calc banner ── */
    .calc-info {
        background: #e8f4fd;
        border: 1px solid #bee5eb;
        border-radius: 6px;
        padding: 10px 14px;
        font-size: 13px;
        color: #0c5460;
        margin-bottom: 16px;
        display: none;
    }
</style>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- ══════════════════════════════════════
     ADD / EDIT FORM
══════════════════════════════════════ -->
<div class="card">
    <h2><?php echo $editBooking ? 'Edit Booking' : 'Add New Booking'; ?></h2>

    <form method="POST" action="" id="bookingForm">
        <?php if ($editBooking): ?>
            <input type="hidden" name="id" value="<?php echo $editBooking['id']; ?>">
        <?php endif; ?>

        <!-- Row 1: User + Room -->
        <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:20px;">
            <div class="form-group">
                <label for="user_id">Guest (User) *</label>
                <select id="user_id" name="user_id" required>
                    <option value="">Select Guest</option>
                    <?php
                    $users = mysqli_query($conn, "SELECT id, name, email FROM users ORDER BY name ASC");
                    while ($u = mysqli_fetch_assoc($users)):
                        $sel = ($editBooking && $editBooking['user_id'] == $u['id']) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $u['id']; ?>" <?php echo $sel; ?>>
                            <?php echo htmlspecialchars($u['name']); ?> — <?php echo htmlspecialchars($u['email']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="room_id">Room *</label>
                <select id="room_id" name="room_id" required>
                    <option value="">Select Room</option>
                    <?php
                    $rooms_dd = mysqli_query($conn,
                        "SELECT r.id, r.room_number, r.room_type, r.price, h.name AS hotel_name
                         FROM rooms r
                         LEFT JOIN hotels h ON r.hotel_id = h.id
                         ORDER BY h.name, r.room_number");
                    while ($rd = mysqli_fetch_assoc($rooms_dd)):
                        $sel = ($editBooking && $editBooking['room_id'] == $rd['id']) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $rd['id']; ?>"
                                data-price="<?php echo $rd['price']; ?>"
                                <?php echo $sel; ?>>
                            <?php echo htmlspecialchars($rd['hotel_name']); ?> — 
                            Room <?php echo htmlspecialchars($rd['room_number']); ?> 
                            (<?php echo $rd['room_type']; ?>) — Rs. <?php echo number_format($rd['price']); ?>/night
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <!-- Row 2: Dates -->
        <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:20px;">
            <div class="form-group">
                <label for="check_in">Check-in Date *</label>
                <input type="date" id="check_in" name="check_in" required
                       value="<?php echo $editBooking ? $editBooking['check_in'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="check_out">Check-out Date *</label>
                <input type="date" id="check_out" name="check_out" required
                       value="<?php echo $editBooking ? $editBooking['check_out'] : ''; ?>">
            </div>
        </div>

        <!-- Price preview -->
        <div class="calc-info" id="calcInfo">
            🧮 <strong id="calcNights">0</strong> night(s) ×
               <strong id="calcRate">Rs. 0</strong>/night =
               <strong id="calcTotal">Rs. 0</strong> total
        </div>

        <!-- Row 3: Adults / Children / Status -->
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:20px;">
            <div class="form-group">
                <label for="adults">Adults *</label>
                <select id="adults" name="adults" required>
                    <?php for($i=1;$i<=5;$i++): ?>
                        <option value="<?php echo $i; ?>"
                            <?php echo ($editBooking && $editBooking['adults'] == $i) ? 'selected' : ''; ?>>
                            <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="children">Children</label>
                <select id="children" name="children">
                    <?php for($i=0;$i<=5;$i++): ?>
                        <option value="<?php echo $i; ?>"
                            <?php echo ($editBooking && $editBooking['children'] == $i) ? 'selected' : ''; ?>>
                            <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status *</label>
                <select id="status" name="status" required>
                    <?php
                    $statuses = ['pending','confirmed','cancelled','completed'];
                    foreach ($statuses as $s):
                        $sel = ($editBooking && $editBooking['status'] === $s) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $s; ?>" <?php echo $sel; ?>>
                            <?php echo ucfirst($s); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary">
                <?php echo $editBooking ? '💾 Update Booking' : '➕ Add Booking'; ?>
            </button>
            <?php if ($editBooking): ?>
                <a href="<?php echo BASE_PATH; ?>/bookings" class="btn btn-warning">✕ Cancel Edit</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- ══════════════════════════════════════
     STATS + TABLE
══════════════════════════════════════ -->
<div class="card">
    <h2>All Bookings (<?php echo countRecords('bookings'); ?>)</h2>

    <div class="stats-grid">
        <div class="stat-item">
            <small>Total Bookings</small>
            <div class="stat-value" style="color:#333;"><?php echo countRecords('bookings'); ?></div>
        </div>
        <div class="stat-item">
            <small>Pending</small>
            <div class="stat-value" style="color:#856404;"><?php echo countRecords('bookings', ['status'=>'pending']); ?></div>
        </div>
        <div class="stat-item">
            <small>Confirmed</small>
            <div class="stat-value" style="color:#155724;"><?php echo countRecords('bookings', ['status'=>'confirmed']); ?></div>
        </div>
        <div class="stat-item">
            <small>Cancelled</small>
            <div class="stat-value" style="color:#721c24;"><?php echo countRecords('bookings', ['status'=>'cancelled']); ?></div>
        </div>
        <div class="stat-item">
            <small>Completed</small>
            <div class="stat-value" style="color:#004085;"><?php echo countRecords('bookings', ['status'=>'completed']); ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Guest</th>
                <th>Hotel / Room</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Guests</th>
                <th>Nights</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Booked On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bookings)): ?>
                <tr>
                    <td colspan="11" style="text-align:center; padding:40px; color:#999;">
                        No bookings found. Add the first one above!
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><strong>#<?php echo $b['id']; ?></strong></td>
                    <td>
                        <strong><?php echo htmlspecialchars($b['user_name']); ?></strong><br>
                        <small style="color:#999;"><?php echo htmlspecialchars($b['user_email']); ?></small>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($b['hotel_name']); ?></strong><br>
                        Room <?php echo htmlspecialchars($b['room_number']); ?> —
                        <span class="room-type-badge type-<?php echo strtolower($b['room_type']); ?>">
                            <?php echo $b['room_type']; ?>
                        </span>
                    </td>
                    <td><?php echo date('d M Y', strtotime($b['check_in'])); ?></td>
                    <td><?php echo date('d M Y', strtotime($b['check_out'])); ?></td>
                    <td>
                        👤 <?php echo $b['adults']; ?> adult<?php echo $b['adults']>1?'s':''; ?>
                        <?php if ($b['children'] > 0): ?>
                            <br>🧒 <?php echo $b['children']; ?> child<?php echo $b['children']>1?'ren':''; ?>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;"><?php echo $b['nights']; ?></td>
                    <td class="price-tag">Rs. <?php echo number_format($b['total_price'], 2); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $b['status']; ?>">
                            <?php echo ucfirst($b['status']); ?>
                        </span>
                    </td>
                    <td>
                        <small style="color:#999;">
                            <?php echo date('d M Y', strtotime($b['created_at'])); ?><br>
                            <?php echo date('h:i A', strtotime($b['created_at'])); ?>
                        </small>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?php echo BASE_PATH; ?>/bookings?edit=<?php echo $b['id']; ?>"
                               class="btn btn-warning" title="Edit">Edit</a>
                            <a href="<?php echo BASE_PATH; ?>/bookings?delete=<?php echo $b['id']; ?>"
                               class="btn btn-danger" title="Delete"
                               onclick="return confirm('Delete Booking #<?php echo $b['id']; ?>?')">
                               Delete
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Auto-calculate nights + total price preview
const roomSel   = document.getElementById('room_id');
const checkIn   = document.getElementById('check_in');
const checkOut  = document.getElementById('check_out');
const calcInfo  = document.getElementById('calcInfo');
const calcNights = document.getElementById('calcNights');
const calcRate   = document.getElementById('calcRate');
const calcTotal  = document.getElementById('calcTotal');

function updateCalc() {
    const ci = checkIn.value, co = checkOut.value;
    const opt = roomSel.options[roomSel.selectedIndex];
    const price = opt ? parseFloat(opt.dataset.price) || 0 : 0;

    if (ci && co && price > 0) {
        const nights = Math.max(0, (new Date(co) - new Date(ci)) / 86400000);
        if (nights > 0) {
            calcNights.textContent = nights;
            calcRate.textContent   = 'Rs. ' + price.toLocaleString();
            calcTotal.textContent  = 'Rs. ' + (nights * price).toLocaleString();
            calcInfo.style.display = 'block';
            return;
        }
    }
    calcInfo.style.display = 'none';
}

roomSel.addEventListener('change', updateCalc);
checkIn.addEventListener('change', function() {
    checkOut.min = this.value;
    updateCalc();
});
checkOut.addEventListener('change', updateCalc);

// Run on page load (edit mode)
updateCalc();

// Prevent check-out <= check-in
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    const ci = new Date(checkIn.value);
    const co = new Date(checkOut.value);
    if (co <= ci) {
        alert('Check-out date must be after check-in date.');
        e.preventDefault();
    }
});
</script>

</div>

<?php include 'includes/footer.php'; ?>
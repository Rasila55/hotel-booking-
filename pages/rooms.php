<?php
session_start();
require_once '../includes/db.php';

$page_title = "Rooms";

// Filters
$checkin    = isset($_GET['checkin'])    ? $_GET['checkin']              : '';
$checkout   = isset($_GET['checkout'])   ? $_GET['checkout']             : '';
$adults     = isset($_GET['adults'])     ? (int)$_GET['adults']          : 0;
$children   = isset($_GET['children'])   ? (int)$_GET['children']        : 0;
$hotel_id   = isset($_GET['hotel_id'])   ? (int)$_GET['hotel_id']        : 0;
$facilities = isset($_GET['facilities']) && is_array($_GET['facilities']) ? $_GET['facilities'] : [];

$where = "WHERE r.status = 'available'";
if ($hotel_id  > 0) $where .= " AND r.hotel_id = " . $hotel_id;
if ($adults    > 0) $where .= " AND r.max_adults >= "   . (int)$adults;
if ($children  > 0) $where .= " AND r.max_children >= " . (int)$children;
foreach ($facilities as $fac) {
    $fac    = mysqli_real_escape_string($conn, $fac);
    $where .= " AND FIND_IN_SET('$fac', REPLACE(r.facilities, ', ', ','))";
}

$sql    = "SELECT r.*, h.name AS hotel_name, h.location AS hotel_location
           FROM rooms r
           LEFT JOIN hotels h ON r.hotel_id = h.id
           $where
           ORDER BY r.price ASC";
$result = mysqli_query($conn, $sql);
$rooms  = [];
while ($row = mysqli_fetch_assoc($result)) $rooms[] = $row;

// All hotels for dropdown
$hotels_result = mysqli_query($conn, "SELECT id, name FROM hotels ORDER BY name ASC");
$all_hotels = [];
while ($h = mysqli_fetch_assoc($hotels_result)) $all_hotels[] = $h;

// If filtering by hotel, get hotel name for heading
$filtered_hotel_name = '';
if ($hotel_id > 0) {
    foreach ($all_hotels as $h) {
        if ($h['id'] === $hotel_id) { $filtered_hotel_name = $h['name']; break; }
    }
}

$allFacilities = ['Wifi', 'Air conditioner', 'Television', 'Spa', 'Room Heater', 'Geyser'];
include '../includes/header.php';
?>

<style>
    .page-title-section { text-align:center; padding:40px 0 30px; }
    .page-title-section h1 { font-size:32px; font-weight:700; text-transform:uppercase; letter-spacing:2px; font-family:'Merienda', cursive; display:inline-block; }
    .page-title-section h1::after { content:''; display:block; width:60%; height:2px; background:#333; margin:8px auto 0; }
    .page-title-section p { color:#777; font-size:14px; margin-top:10px; }

    .rooms-layout { display:flex; gap:24px; padding:0 40px 60px; align-items:flex-start; }

    /* ── Sidebar ── */
    .filter-sidebar { width:240px; min-width:240px; border:1px solid #ddd; border-radius:6px; padding:20px; background:#fff; position:sticky; top:20px; }
    .filter-sidebar h6 { font-size:13px; font-weight:700; letter-spacing:1px; text-transform:uppercase; margin-bottom:14px; color:#222; }
    .filter-section { margin-bottom:20px; border-bottom:1px solid #eee; padding-bottom:18px; }
    .filter-section:last-child { border-bottom:none; margin-bottom:0; }
    .filter-section label { font-size:13px; color:#444; display:block; margin-bottom:4px; }
    .filter-section input[type="date"],
    .filter-section input[type="number"],
    .filter-section select { width:100%; padding:7px 10px; border:1px solid #ccc; border-radius:4px; font-size:13px; margin-bottom:10px; box-sizing:border-box; }
    .guests-row { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
    .btn-filter { width:100%; padding:9px; background:#1aab8a; color:#fff; border:none; border-radius:4px; font-size:14px; cursor:pointer; margin-top:16px; }
    .btn-filter:hover { background:#158a6e; }
    .btn-reset { width:100%; padding:9px; background:transparent; color:#555; border:1px solid #ccc; border-radius:4px; font-size:14px; margin-top:8px; text-align:center; text-decoration:none; display:block; }
    .btn-reset:hover { background:#f5f5f5; color:#333; }

    /* ── Active filter banner ── */
    .active-filter-bar {
        background:#e6f7f2; border:1px solid #b2dfdb; border-radius:6px;
        padding:10px 16px; margin-bottom:16px; font-size:13px; color:#1aab8a;
        display:flex; align-items:center; justify-content:space-between; gap:10px;
    }
    .active-filter-bar a { color:#c62828; text-decoration:none; font-size:12px; white-space:nowrap; }
    .active-filter-bar a:hover { text-decoration:underline; }

    /* ── Results bar ── */
    .results-bar { font-size:13px; color:#777; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid #eee; }

    /* ── Room cards ── */
    .rooms-list { flex:1; }
    .room-card { display:flex; border:1px solid #e0e0e0; border-radius:8px; overflow:hidden; margin-bottom:20px; background:#fff; transition:box-shadow 0.2s; }
    .room-card:hover { box-shadow:0 4px 16px rgba(0,0,0,0.1); }
    .room-card-img { width:280px; min-width:280px; height:220px; object-fit:cover; }
    .room-card-img-placeholder { width:280px; min-width:280px; height:220px; background:#f0f0f0; display:flex; align-items:center; justify-content:center; font-size:48px; color:#aaa; }
    .room-card-body { padding:20px; flex:1; }
    .room-card-body h5 { font-size:18px; font-weight:600; margin-bottom:12px; color:#222; }
    .tag-label { font-size:12px; font-weight:600; color:#444; margin:10px 0 5px; }
    .tag { display:inline-block; background:#f0f0f0; color:#444; font-size:12px; padding:3px 10px; border-radius:3px; margin:2px 3px 2px 0; }
    .room-card-actions { display:flex; flex-direction:column; justify-content:center; align-items:center; padding:20px; min-width:160px; border-left:1px solid #f0f0f0; gap:8px; }
    .room-price { font-size:15px; font-weight:600; color:#222; text-align:center; margin-bottom:6px; }
    .btn-book { display:block; width:130px; text-align:center; padding:9px 0; background:#1aab8a; color:#fff !important; border-radius:4px; font-size:14px; text-decoration:none; }
    .btn-book:hover { background:#158a6e; }
    .btn-details { display:block; width:130px; text-align:center; padding:8px 0; background:transparent; color:#333 !important; border:1.5px solid #ccc; border-radius:4px; font-size:14px; text-decoration:none; }
    .btn-details:hover { background:#f5f5f5; }
    .no-results { text-align:center; padding:60px; color:#999; }

    @media (max-width: 768px) {
        .rooms-layout { flex-direction:column; padding:0 16px 40px; }
        .filter-sidebar { width:100%; min-width:unset; position:static; }
        .room-card { flex-direction:column; }
        .room-card-img, .room-card-img-placeholder { width:100%; min-width:unset; height:200px; }
        .room-card-actions { border-left:none; border-top:1px solid #f0f0f0; flex-direction:row; justify-content:space-between; }
    }
</style>

<div class="page-title-section">
    <h1><?php echo $filtered_hotel_name ? htmlspecialchars($filtered_hotel_name) : 'Our Rooms'; ?></h1>
    <?php if ($filtered_hotel_name): ?>
        <p>Browsing rooms for <strong><?php echo htmlspecialchars($filtered_hotel_name); ?></strong></p>
    <?php endif; ?>
</div>

<div class="rooms-layout">

    <!-- ── Sidebar ── -->
    <div class="filter-sidebar">
        <h6>Filters</h6>
        <form method="GET" action="">

            <div class="filter-section">
                <h6>Hotel</h6>
                <label>Select Hotel</label>
                <select name="hotel_id">
                    <option value="0">All Hotels</option>
                    <?php foreach ($all_hotels as $h): ?>
                        <option value="<?php echo $h['id']; ?>"
                            <?php echo ($hotel_id === $h['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($h['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-section">
                <h6>Check Availability</h6>
                <label>Check-in</label>
                <input type="date" name="checkin" value="<?php echo htmlspecialchars($checkin); ?>">
                <label>Check-out</label>
                <input type="date" name="checkout" value="<?php echo htmlspecialchars($checkout); ?>">
            </div>

            <div class="filter-section">
                <h6>Facilities</h6>
                <?php foreach ($allFacilities as $facility): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="facilities[]"
                               value="<?php echo $facility; ?>"
                               id="fac_<?php echo str_replace(' ', '_', $facility); ?>"
                               <?php echo in_array($facility, $facilities) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="fac_<?php echo str_replace(' ', '_', $facility); ?>">
                            <?php echo $facility; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="filter-section">
                <h6>Guests</h6>
                <div class="guests-row">
                    <div>
                        <label>Adults</label>
                        <input type="number" name="adults" min="0" value="<?php echo $adults ?: ''; ?>" placeholder="0">
                    </div>
                    <div>
                        <label>Children</label>
                        <input type="number" name="children" min="0" value="<?php echo $children ?: ''; ?>" placeholder="0">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-filter">Apply Filters</button>
            <a href="/staymate/pages/rooms.php" class="btn-reset">Reset All</a>
        </form>
    </div>

    <!-- ── Rooms List ── -->
    <div class="rooms-list">

        <!-- Active hotel filter banner -->
        <?php if ($filtered_hotel_name): ?>
        <div class="active-filter-bar">
            <span>🏨 Showing rooms for: <strong><?php echo htmlspecialchars($filtered_hotel_name); ?></strong></span>
            <a href="/staymate/pages/rooms.php">✕ Clear hotel filter</a>
        </div>
        <?php endif; ?>

        <div class="results-bar">
            Showing <strong><?php echo count($rooms); ?></strong> room<?php echo count($rooms) != 1 ? 's' : ''; ?>
            <?php if ($adults || $children || !empty($facilities)): ?>
                — filtered
            <?php endif; ?>
        </div>

        <?php if (empty($rooms)): ?>
            <div class="no-results">
                <p style="font-size:48px;">🔍</p>
                <p>No rooms found. Try different filters.</p>
                <a href="/staymate/pages/rooms.php" style="color:#1aab8a;">Clear all filters</a>
            </div>
        <?php else: ?>
            <?php foreach ($rooms as $room):
                $features = !empty($room['features'])   ? array_map('trim', explode(',', $room['features']))   : [];
                $facList  = !empty($room['facilities'])  ? array_map('trim', explode(',', $room['facilities'])) : [];
            ?>
            <div class="room-card">

                <?php if (!empty($room['image'])): ?>
                    <img src="/staymate/admin/uploads/rooms/<?php echo htmlspecialchars($room['image']); ?>"
                         alt="Room" class="room-card-img">
                <?php else: ?>
                    <img src="/staymate/images/rooms/room1.png" alt="Room" class="room-card-img">
                <?php endif; ?>

                <div class="room-card-body">
                    <h5>
                        <?php echo htmlspecialchars($room['room_type']); ?> Room
                        <small style="font-size:12px; color:#999; font-weight:400;">
                            — <?php echo htmlspecialchars($room['hotel_name']); ?>
                            <?php if (!empty($room['hotel_location'])): ?>
                                · <?php echo htmlspecialchars($room['hotel_location']); ?>
                            <?php endif; ?>
                        </small>
                    </h5>

                    <?php if (!empty($features)): ?>
                        <div class="tag-label">Features</div>
                        <?php foreach ($features as $f): ?>
                            <span class="tag"><?php echo htmlspecialchars($f); ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($facList)): ?>
                        <div class="tag-label">Facilities</div>
                        <?php foreach ($facList as $f): ?>
                            <span class="tag"><?php echo htmlspecialchars($f); ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="tag-label">Guests</div>
                    <span class="tag">👤 <?php echo $room['max_adults']; ?> Adults</span>
                    <span class="tag">🧒 <?php echo $room['max_children']; ?> Children</span>
                </div>

                <div class="room-card-actions">
                    <div class="room-price">Rs. <?php echo number_format($room['price']); ?> <span style="font-size:12px; font-weight:400; color:#777;">per night</span></div>

                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                        <a href="/staymate/pages/booking.php?room_id=<?php echo $room['id']; ?><?php echo $checkin  ? '&checkin='  . urlencode($checkin)  : ''; ?><?php echo $checkout ? '&checkout=' . urlencode($checkout) : ''; ?>"
                           class="btn-book">Book Now</a>
                    <?php else: ?>
                        <a href="/staymate/pages/login.php" class="btn-book">Book Now</a>
                    <?php endif; ?>
                </div>

            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
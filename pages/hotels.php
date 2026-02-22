<?php
session_start();
require_once '../includes/db.php';

$page_title = "Hotels";

// Filters
$search   = isset($_GET['search'])   ? trim($_GET['search'])       : '';
$location = isset($_GET['location']) ? trim($_GET['location'])     : '';

$where = "WHERE 1=1";
if ($search)   $where .= " AND h.name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
if ($location) $where .= " AND h.location LIKE '%" . mysqli_real_escape_string($conn, $location) . "%'";

$sql = "SELECT h.*, 
               COUNT(r.id) AS total_rooms,
               SUM(r.status = 'available') AS available_rooms,
               MIN(r.price) AS min_price
        FROM hotels h
        LEFT JOIN rooms r ON r.hotel_id = h.id
        $where
        GROUP BY h.id
        ORDER BY h.id ASC";

$result = mysqli_query($conn, $sql);
$hotels = [];
while ($row = mysqli_fetch_assoc($result)) $hotels[] = $row;

// All unique locations for filter dropdown
$loc_result = mysqli_query($conn, "SELECT DISTINCT location FROM hotels WHERE location != '' ORDER BY location ASC");
$locations  = [];
while ($l = mysqli_fetch_assoc($loc_result)) $locations[] = $l['location'];

include '../includes/header.php';
?>

<style>
    /* ── Page title ── */
    .page-title-section { text-align:center; padding:40px 0 30px; }
    .page-title-section h1 {
        font-size:32px; font-weight:700; text-transform:uppercase;
        letter-spacing:2px; font-family:'Merienda', cursive; display:inline-block;
    }
    .page-title-section h1::after {
        content:''; display:block; width:60%; height:2px;
        background:#333; margin:8px auto 0;
    }

    /* ── Layout ── */
    .hotels-layout { display:flex; gap:24px; padding:0 40px 60px; align-items:flex-start; }

    /* ── Sidebar ── */
    .filter-sidebar {
        width:240px; min-width:240px; border:1px solid #ddd;
        border-radius:6px; padding:20px; background:#fff;
        position:sticky; top:20px;
    }
    .filter-sidebar h6 {
        font-size:13px; font-weight:700; letter-spacing:1px;
        text-transform:uppercase; margin-bottom:14px; color:#222;
    }
    .filter-section { margin-bottom:20px; border-bottom:1px solid #eee; padding-bottom:18px; }
    .filter-section:last-child { border-bottom:none; margin-bottom:0; }
    .filter-section label { font-size:13px; color:#444; display:block; margin-bottom:4px; }
    .filter-section input[type="text"],
    .filter-section select {
        width:100%; padding:7px 10px; border:1px solid #ccc;
        border-radius:4px; font-size:13px; margin-bottom:10px;
        box-sizing:border-box;
    }
    .btn-filter {
        width:100%; padding:9px; background:#1aab8a; color:#fff;
        border:none; border-radius:4px; font-size:14px; cursor:pointer; margin-top:4px;
    }
    .btn-filter:hover { background:#158a6e; }
    .btn-reset {
        width:100%; padding:9px; background:transparent; color:#555;
        border:1px solid #ccc; border-radius:4px; font-size:14px;
        margin-top:8px; text-align:center; text-decoration:none; display:block;
    }
    .btn-reset:hover { background:#f5f5f5; color:#333; }

    /* ── Hotel list ── */
    .hotels-list { flex:1; }

    .hotel-card {
        display:flex; border:1px solid #e0e0e0; border-radius:8px;
        overflow:hidden; margin-bottom:20px; background:#fff;
        transition:box-shadow 0.2s;
    }
    .hotel-card:hover { box-shadow:0 4px 16px rgba(0,0,0,0.1); }

    .hotel-card-img { width:280px; min-width:280px; height:220px; object-fit:cover; }
    .hotel-card-img-placeholder {
        width:280px; min-width:280px; height:220px;
        background:#f0f0f0; display:flex; align-items:center;
        justify-content:center; font-size:48px; color:#aaa;
    }

    .hotel-card-body { padding:20px; flex:1; }
    .hotel-card-body h5 { font-size:18px; font-weight:600; margin-bottom:6px; color:#222; }

    .hotel-location {
        font-size:13px; color:#777; margin-bottom:12px;
        display:flex; align-items:center; gap:5px;
    }

    .hotel-desc {
        font-size:13px; color:#555; line-height:1.6;
        margin-bottom:12px;
        display:-webkit-box; -webkit-line-clamp:3;
        -webkit-box-orient:vertical; overflow:hidden;
    }

    .tag-label { font-size:12px; font-weight:600; color:#444; margin:10px 0 5px; }
    .tag {
        display:inline-block; background:#f0f0f0; color:#444;
        font-size:12px; padding:3px 10px; border-radius:3px; margin:2px 3px 2px 0;
    }
    .tag-green { background:#e6f7f2; color:#1aab8a; }

    .hotel-card-actions {
        display:flex; flex-direction:column; justify-content:center;
        align-items:center; padding:20px; min-width:160px;
        border-left:1px solid #f0f0f0; gap:8px;
    }
    .hotel-price { font-size:14px; color:#777; text-align:center; margin-bottom:4px; }
    .hotel-price strong { font-size:16px; font-weight:700; color:#222; display:block; }

    .btn-view-rooms {
        display:block; width:130px; text-align:center; padding:9px 0;
        background:#1aab8a; color:#fff !important; border-radius:4px;
        font-size:14px; text-decoration:none;
    }
    .btn-view-rooms:hover { background:#158a6e; }

    .btn-details {
        display:block; width:130px; text-align:center; padding:8px 0;
        background:transparent; color:#333 !important; border:1.5px solid #ccc;
        border-radius:4px; font-size:14px; text-decoration:none;
    }
    .btn-details:hover { background:#f5f5f5; }

    /* ── No results ── */
    .no-results { text-align:center; padding:60px; color:#999; }

    /* ── Results count ── */
    .results-bar {
        font-size:13px; color:#777; margin-bottom:16px;
        padding-bottom:12px; border-bottom:1px solid #eee;
    }

    @media (max-width: 768px) {
        .hotels-layout { flex-direction:column; padding:0 16px 40px; }
        .filter-sidebar { width:100%; min-width:unset; position:static; }
        .hotel-card { flex-direction:column; }
        .hotel-card-img, .hotel-card-img-placeholder { width:100%; min-width:unset; height:200px; }
        .hotel-card-actions { border-left:none; border-top:1px solid #f0f0f0; flex-direction:row; justify-content:space-between; }
    }
</style>

<div class="page-title-section"><h1>Our Hotels</h1></div>

<div class="hotels-layout">

    <!-- ── Sidebar Filter ── -->
    <div class="filter-sidebar">
        <h6>Filters</h6>
        <form method="GET" action="">
            <div class="filter-section">
                <h6>Search</h6>
                <label>Hotel Name</label>
                <input type="text" name="search" placeholder="e.g. Alpine..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <div class="filter-section">
                <h6>Location</h6>
                <label>City / Area</label>
                <select name="location">
                    <option value="">All Locations</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?php echo htmlspecialchars($loc); ?>"
                            <?php echo ($location === $loc) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($loc); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-filter">Apply Filters</button>
            <a href="/staymate/pages/hotels.php" class="btn-reset">Reset</a>
        </form>
    </div>

    <!-- ── Hotel Cards ── -->
    <div class="hotels-list">
        <div class="results-bar">
            Showing <strong><?php echo count($hotels); ?></strong> hotel<?php echo count($hotels) != 1 ? 's' : ''; ?>
            <?php if ($search || $location): ?>
                — filtered by
                <?php if ($search)   echo ' name: <strong>' . htmlspecialchars($search)   . '</strong>'; ?>
                <?php if ($location) echo ' location: <strong>' . htmlspecialchars($location) . '</strong>'; ?>
            <?php endif; ?>
        </div>

        <?php if (empty($hotels)): ?>
            <div class="no-results">
                <p style="font-size:48px;">🏨</p>
                <p>No hotels found. Try different filters.</p>
                <a href="/staymate/pages/hotels.php" style="color:#1aab8a;">Clear filters</a>
            </div>
        <?php else: ?>
            <?php foreach ($hotels as $hotel): ?>
            <div class="hotel-card">

                <!-- Image -->
                <?php if (!empty($hotel['image'])): ?>
                    <img src="/staymate/admin/uploads/hotels/<?php echo htmlspecialchars($hotel['image']); ?>"
                         alt="<?php echo htmlspecialchars($hotel['name']); ?>"
                         class="hotel-card-img">
                <?php else: ?>
                    <div class="hotel-card-img-placeholder">🏨</div>
                <?php endif; ?>

                <!-- Body -->
                <div class="hotel-card-body">
                    <h5><?php echo htmlspecialchars($hotel['name']); ?></h5>

                    <div class="hotel-location">
                        📍 <?php echo htmlspecialchars($hotel['location']); ?>
                    </div>

                    <?php if (!empty($hotel['description'])): ?>
                        <p class="hotel-desc"><?php echo htmlspecialchars($hotel['description']); ?></p>
                    <?php endif; ?>

                    <div class="tag-label">Rooms</div>
                    <span class="tag"><?php echo $hotel['total_rooms']; ?> Total</span>
                    <?php if ($hotel['available_rooms'] > 0): ?>
                        <span class="tag tag-green"><?php echo $hotel['available_rooms']; ?> Available</span>
                    <?php else: ?>
                        <span class="tag" style="background:#fdecea; color:#c62828;">No Availability</span>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div class="hotel-card-actions">
                    <?php if ($hotel['min_price']): ?>
                        <div class="hotel-price">
                            From<strong>Rs. <?php echo number_format($hotel['min_price']); ?></strong>per night
                        </div>
                    <?php endif; ?>

                    <a href="/staymate/pages/rooms.php?hotel_id=<?php echo $hotel['id']; ?>"
                       class="btn-view-rooms">View Rooms</a>
                </div>

            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<?php include '../includes/footer.php'; ?>
<?php
$page_title = "Rooms Management";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Delete image file from disk too
    $roomToDelete = readOne('rooms', $id);
    if ($roomToDelete && !empty($roomToDelete['image'])) {
        $imgPath ='/uploads/rooms/' . $roomToDelete['image'];
        
        if (file_exists($imgPath)) unlink($imgPath);
    }

    if (deleteById('rooms', $id)) {
        $_SESSION['success'] = "Room deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete room!";
    }
    header('Location: ' . BASE_PATH . '/rooms');
    exit();
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $hotel_id    = (int)$_POST['hotel_id'];
    $room_number = trim($_POST['room_number']);
    $room_type   = $_POST['room_type'];
    $price       = (float)$_POST['price'];
    $capacity    = (int)$_POST['capacity'];
    $status      = $_POST['status'];
    $description = trim($_POST['description']);

   // Image Upload
$image = '';
$existingImage = '';

if ($id > 0) {
    $existing = readOne('rooms', $id);
    $existingImage = $existing['image'] ?? '';
    $image = $existingImage;
}

if (!empty($_FILES['image']['name'])) {

    $uploadDir = 'uploads/rooms/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $fileName;

    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg','jpeg','png','webp'];

    if (in_array($imageFileType, $allowedTypes)) {

        if ($_FILES['image']['size'] <= 2 * 1024 * 1024) {

            // delete old image when editing
            if (!empty($existingImage)) {
                $oldPath = $uploadDir . $existingImage;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $image = $fileName;
            }

        } else {
            $_SESSION['error'] = "Image must be under 2MB.";
            header('Location: ' . BASE_PATH . '/rooms');
            exit();
        }

    } else {
        $_SESSION['error'] = "Only JPG, JPEG, PNG, WEBP allowed.";
        header('Location: ' . BASE_PATH . '/rooms');
        exit();
    }
}

    $data = [
        'hotel_id'    => $hotel_id,
        'room_number' => $room_number,
        'room_type'   => $room_type,
        'price'       => $price,
        'capacity'    => $capacity,
        'status'      => $status,
        'description' => $description,
        'image'       => $image,
    ];

    if ($id > 0) {
        if (updateById('rooms', $id, $data)) {
            $_SESSION['success'] = "Room updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update room!";
        }
    } else {
        if (create('rooms', $data)) {
            $_SESSION['success'] = "Room added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add room!";
        }
    }

    header('Location: ' . BASE_PATH . '/rooms');
    exit();
}

// Get room for editing
$editRoom = null;
if (isset($_GET['edit'])) {
    $editRoom = readOne('rooms', (int)$_GET['edit']);
}

// Get all rooms with hotel information
$rooms = query(
    "SELECT r.*, h.name AS hotel_name, h.location AS hotel_location
     FROM rooms r
     LEFT JOIN hotels h ON r.hotel_id = h.id
     ORDER BY r.id DESC",
    [], ''
);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
<style>
    .btn { padding:8px 16px; border:none; border-radius:5px; cursor:pointer; text-decoration:none; display:inline-block; font-size:14px; transition:all 0.3s; }
    .btn-primary { background:#667eea; color:white; }
    .btn-primary:hover { background:#5568d3; }
    .btn-warning { background:#ffc107; color:#333; }
    .btn-danger  { background:#dc3545; color:white; }
    .btn-danger:hover { background:#c82333; }

    .form-group { margin-bottom:20px; }
    .form-group label { display:block; margin-bottom:5px; font-weight:500; color:#333; }
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group textarea,
    .form-group select {
        width:100%; padding:10px; border:1px solid #ddd;
        border-radius:5px; font-size:14px; box-sizing:border-box;
    }
    .form-group textarea { min-height:100px; resize:vertical; }

    .alert { padding:12px 20px; border-radius:5px; margin-bottom:20px; }
    .alert-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
    .alert-error   { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }

    table { width:100%; border-collapse:collapse; margin-top:20px; }
    table th { background:#f8f9fa; padding:12px; text-align:left; font-weight:600; border-bottom:2px solid #dee2e6; }
    table td { padding:12px; border-bottom:1px solid #dee2e6; vertical-align:middle; }

    .status-badge { padding:4px 12px; border-radius:12px; font-size:12px; font-weight:500; }
    .status-available   { background:#d4edda; color:#155724; }
    .status-booked      { background:#fff3cd; color:#856404; }
    .status-maintenance { background:#f8d7da; color:#721c24; }

    .action-buttons { display:flex; gap:5px; }

    .room-type-badge { padding:4px 10px; border-radius:8px; font-size:11px; font-weight:500; text-transform:uppercase; }
    .type-single { background:#e3f2fd; color:#1976d2; }
    .type-double { background:#f3e5f5; color:#7b1fa2; }
    .type-suite  { background:#fff3e0; color:#e65100; }
    .type-deluxe { background:#fce4ec; color:#c2185b; }

    .price-tag { font-weight:bold; color:#667eea; }

    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:15px; margin-bottom:20px; padding:15px; background:#f8f9fa; border-radius:8px; }
    .stat-item small { color:#666; display:block; }
    .stat-item .stat-value { font-size:24px; font-weight:bold; margin-top:5px; }

    .room-thumb { width:52px; height:40px; object-fit:cover; border-radius:5px; border:1px solid #eee; }
    .room-thumb-placeholder { width:52px; height:40px; background:#f0f0f0; border-radius:5px; display:flex; align-items:center; justify-content:center; font-size:18px; color:#bbb; }
</style>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- ══ ADD / EDIT FORM ══ -->
<div class="card">
    <h2><?php echo $editRoom ? 'Edit Room' : 'Add New Room'; ?></h2>

    <!-- enctype="multipart/form-data" is required for file upload to work -->
    <form method="POST" action="" enctype="multipart/form-data">
        <?php if ($editRoom): ?>
            <input type="hidden" name="id" value="<?php echo $editRoom['id']; ?>">
        <?php endif; ?>

        <!-- Hotel + Room Number -->
        <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:20px;">
            <div class="form-group">
                <label for="hotel_id">Hotel *</label>
                <select id="hotel_id" name="hotel_id" required>
                    <option value="">Select Hotel</option>
                    <?php
                    $hotels_dd = mysqli_query($conn, "SELECT id, name FROM hotels ORDER BY name ASC");
                    while ($h = mysqli_fetch_assoc($hotels_dd)):
                        $sel = ($editRoom && $editRoom['hotel_id'] == $h['id']) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $h['id']; ?>" <?php echo $sel; ?>>
                            <?php echo htmlspecialchars($h['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="room_number">Room Number *</label>
                <input type="text" id="room_number" name="room_number" required
                       value="<?php echo $editRoom ? htmlspecialchars($editRoom['room_number']) : ''; ?>"
                       placeholder="e.g., 101, A-205">
            </div>
        </div>

        <!-- Type + Price + Capacity -->
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:20px;">
            <div class="form-group">
                <label for="room_type">Room Type *</label>
                <select id="room_type" name="room_type" required>
                    <?php foreach (['Single','Double','Suite','Deluxe'] as $t): ?>
                        <option value="<?php echo $t; ?>"
                            <?php echo ($editRoom && $editRoom['room_type'] === $t) ? 'selected' : ''; ?>>
                            <?php echo $t; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="price">Price per Night (Rs.) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required
                       value="<?php echo $editRoom ? $editRoom['price'] : ''; ?>"
                       placeholder="e.g., 5000">
            </div>
            <div class="form-group">
                <label for="capacity">Capacity (Persons) *</label>
                <input type="number" id="capacity" name="capacity" min="1" required
                       value="<?php echo $editRoom ? $editRoom['capacity'] : ''; ?>"
                       placeholder="e.g., 2">
            </div>
        </div>

        <!-- Description -->
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description"
                      placeholder="Room amenities, features, etc."><?php echo $editRoom ? htmlspecialchars($editRoom['description']) : ''; ?></textarea>
        </div>

        <!-- Status -->
        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" required>
                <?php foreach (['available','booked','maintenance'] as $s): ?>
                    <option value="<?php echo $s; ?>"
                        <?php echo ($editRoom && $editRoom['status'] === $s) ? 'selected' : ''; ?>>
                        <?php echo ucfirst($s); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- ── IMAGE UPLOAD ── -->
        <div class="form-group">
            <label>Room Image <small style="color:#999;">(JPG, PNG, WEBP — max 2MB)</small></label>

            <!-- Show current image when editing -->
            <?php if ($editRoom && !empty($editRoom['image'])): ?>
                    <div style="margin-bottom:10px;">
                     
                    <img src="   <?php echo BASE_PATH; ?>/uploads/rooms/<?php echo $editRoom['image']; ?>"
                         style="width:100%; max-height:160px; object-fit:cover; border-radius:8px; border:1px solid #ddd;">
                    <small style="color:#888; display:block; margin-top:4px;">
                        Current image — upload a new one below to replace it
                    </small>
                </div>
            <?php endif; ?>

            <input type="file" name="image" id="imageInput"
                   accept="image/jpeg,image/png,image/webp"
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; box-sizing:border-box;">

            <!-- Live preview of selected image -->
            <div id="previewWrap" style="display:none; margin-top:10px;">
                <img id="imgPreview" src=""
                     style="width:100%; max-height:180px; object-fit:cover; border-radius:8px; border:1px solid #ddd;">
                <span onclick="removeImage()"
                      style="font-size:12px; color:#dc3545; cursor:pointer; margin-top:6px; display:inline-block;">
                    ✕ Remove selection
                </span>
            </div>
        </div>

        <!-- Submit -->
        <div style="display:flex; gap:10px;">
            <button type="submit" class="btn btn-primary">
                <?php echo $editRoom ? 'Update Room' : 'Add Room'; ?>
            </button>
            <?php if ($editRoom): ?>
                <a href="<?php echo BASE_PATH; ?>/rooms" class="btn btn-warning">Cancel Edit</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- ══ ROOMS TABLE ══ -->
<div class="card">
    <h2>All Rooms (<?php echo countRecords('rooms'); ?>)</h2>

    <div class="stats-grid">
        <div class="stat-item">
            <small>Total Rooms</small>
            <div class="stat-value" style="color:#333;"><?php echo countRecords('rooms'); ?></div>
        </div>
        <div class="stat-item">
            <small>Available</small>
            <div class="stat-value" style="color:#28a745;"><?php echo countRecords('rooms', ['status'=>'available']); ?></div>
        </div>
        <div class="stat-item">
            <small>Booked</small>
            <div class="stat-value" style="color:#ffc107;"><?php echo countRecords('rooms', ['status'=>'booked']); ?></div>
        </div>
        <div class="stat-item">
            <small>Maintenance</small>
            <div class="stat-value" style="color:#dc3545;"><?php echo countRecords('rooms', ['status'=>'maintenance']); ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Hotel</th>
                <th>Room No.</th>
                <th>Type</th>
                <th>Price/Night</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rooms)): ?>
                <tr>
                    <td colspan="9" style="text-align:center; padding:40px; color:#999;">
                        No rooms found. Add your first room above!
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($rooms as $room): ?>
                <tr>
                    <td><?php echo $room['id']; ?></td>
                    <td>
                        <?php if (!empty($room['image'])): ?>
                              
<img src="<?php echo BASE_PATH;?>/uploads/rooms/<?php echo $room['image']; ?>"
                                 class="room-thumb" alt="Room">
                        <?php else: ?>
                            <div class="room-thumb-placeholder">🛏️</div>
                        <?php endif; ?> 
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($room['hotel_name']); ?></strong><br>
                        <small style="color:#999;"><?php echo htmlspecialchars($room['hotel_location']); ?></small>
                    </td>
                    <td><strong><?php echo htmlspecialchars($room['room_number']); ?></strong></td>
                    <td>
                        <span class="room-type-badge type-<?php echo strtolower($room['room_type']); ?>">
                            <?php echo $room['room_type']; ?>
                        </span>
                    </td>
                    <td class="price-tag">Rs. <?php echo number_format($room['price'], 2); ?></td>
                    <td>👤 <?php echo $room['capacity']; ?> <?php echo $room['capacity'] > 1 ? 'persons' : 'person'; ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $room['status']; ?>">
                            <?php echo ucfirst($room['status']); ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?php echo BASE_PATH; ?>/rooms?edit=<?php echo $room['id']; ?>"
                               class="btn btn-warning">Edit</a>
                            <a href="<?php echo BASE_PATH; ?>/rooms?delete=<?php echo $room['id']; ?>"
                               class="btn btn-danger"
                               onclick="return confirm('Delete Room <?php echo htmlspecialchars($room['room_number']); ?>?')">
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
document.getElementById('imageInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) {
        alert('Image must be under 2MB.');
        this.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('imgPreview').src = e.target.result;
        document.getElementById('previewWrap').style.display = 'block';
    };
    reader.readAsDataURL(file);
});

function removeImage() {
    document.getElementById('imageInput').value = '';
    document.getElementById('imgPreview').src   = '';
    document.getElementById('previewWrap').style.display = 'none';
}
</script>

</div>

<?php include 'includes/footer.php'; ?>
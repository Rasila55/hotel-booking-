<?php
$page_title = "Rooms Management";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
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
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $hotel_id = (int)$_POST['hotel_id'];
    $room_number = trim($_POST['room_number']);
    $room_type = $_POST['room_type'];
    $price = (float)$_POST['price'];
    $capacity = (int)$_POST['capacity'];
    $status = $_POST['status'];
    $description = trim($_POST['description']);
    
    $data = [
        'hotel_id' => $hotel_id,
        'room_number' => $room_number,
        'room_type' => $room_type,
        'price' => $price,
        'capacity' => $capacity,
        'status' => $status,
        'description' => $description
    ];
    
    if ($id > 0) {
        // Update
        if (updateById('rooms', $id, $data)) {
            $_SESSION['success'] = "Room updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update room!";
        }
    } else {
        // Create
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
    [],
    ''
);

// Get all hotels for dropdown
$hotels = readAll('hotels', ['status' => 'active'], 'name ASC');

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <style>
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
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #5568d3; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .alert {
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-available { background: #d4edda; color: #155724; }
        .status-booked { background: #fff3cd; color: #856404; }
        .status-maintenance { background: #f8d7da; color: #721c24; }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .room-type-badge {
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }
        .type-single { background: #e3f2fd; color: #1976d2; }
        .type-double { background: #f3e5f5; color: #7b1fa2; }
        .type-suite { background: #fff3e0; color: #e65100; }
        .type-deluxe { background: #fce4ec; color: #c2185b; }
        
        .price-tag {
            font-weight: bold;
            color: #667eea;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .stat-item small {
            color: #666;
            display: block;
        }
        
        .stat-item .stat-value {
            font-size: 24px;
            font-weight: bold;
            margin-top: 5px;
        }
    </style>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <h2><?php echo $editRoom ? 'Edit Room' : 'Add New Room'; ?></h2>
        
        <form method="POST" action="">
            <?php if ($editRoom): ?>
                <input type="hidden" name="id" value="<?php echo $editRoom['id']; ?>">
            <?php endif; ?>
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="form-group">
                    <label for="hotel_id">Hotel *</label>
                    <select id="hotel_id" name="hotel_id" required>
                        <option value="">Select Hotel</option>
                          <?php
                            $hotels = mysqli_query($conn, "SELECT id, name FROM hotels");
                            while ($h = mysqli_fetch_assoc($hotels)) {
                                echo "<option value='{$h['id']}'>{$h['name']}</option>";
                            }
                            ?>
                       
                                <?php echo ($editRoom && $editRoom['hotel_id'] == $hotel['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($hotel['name']); ?> - <?php echo htmlspecialchars($hotel['location']); ?>
                            </option>
                        <?php  ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="room_number">Room Number *</label>
                    <input type="text" id="room_number" name="room_number" required 
                           value="<?php echo $editRoom ? htmlspecialchars($editRoom['room_number']) : ''; ?>"
                           placeholder="e.g., 101, A-205">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="form-group">
                    <label for="room_type">Room Type *</label>
                    <select id="room_type" name="room_type" required>
                        <option value="Single" <?php echo ($editRoom && $editRoom['room_type'] === 'Single') ? 'selected' : ''; ?>>Single</option>
                        <option value="Double" <?php echo ($editRoom && $editRoom['room_type'] === 'Double') ? 'selected' : ''; ?>>Double</option>
                        <option value="Suite" <?php echo ($editRoom && $editRoom['room_type'] === 'Suite') ? 'selected' : ''; ?>>Suite</option>
                        <option value="Deluxe" <?php echo ($editRoom && $editRoom['room_type'] === 'Deluxe') ? 'selected' : ''; ?>>Deluxe</option>
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
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Room amenities, features, etc."><?php echo $editRoom ? htmlspecialchars($editRoom['description']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="status">Status *</label>
                <select id="status" name="status" required>
                    <option value="available" <?php echo ($editRoom && $editRoom['status'] === 'available') ? 'selected' : ''; ?>>Available</option>
                    <option value="booked" <?php echo ($editRoom && $editRoom['status'] === 'booked') ? 'selected' : ''; ?>>Booked</option>
                    <option value="maintenance" <?php echo ($editRoom && $editRoom['status'] === 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <?php echo $editRoom ? 'Update Room' : 'Add Room'; ?>
                </button>
                <?php if ($editRoom): ?>
                    <a href="<?php echo BASE_PATH; ?>/rooms" class="btn btn-warning">Cancel Edit</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="card">
        <h2>All Rooms (<?php echo countRecords('rooms'); ?>)</h2>
        
        <div class="stats-grid">
            <div class="stat-item">
                <small>Total Rooms</small>
                <div class="stat-value" style="color: #333;"><?php echo countRecords('rooms'); ?></div>
            </div>
            <div class="stat-item">
                <small>Available</small>
                <div class="stat-value" style="color: #28a745;"><?php echo countRecords('rooms', ['status' => 'available']); ?></div>
            </div>
            <div class="stat-item">
                <small>Booked</small>
                <div class="stat-value" style="color: #ffc107;"><?php echo countRecords('rooms', ['status' => 'booked']); ?></div>
            </div>
            <div class="stat-item">
                <small>Maintenance</small>
                <div class="stat-value" style="color: #dc3545;"><?php echo countRecords('rooms', ['status' => 'maintenance']); ?></div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hotel</th>
                    <th>Room No.</th>
                    <th>Type</th>
                    <th>Price/Night</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <!-- <th>Created</th> -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rooms)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: #999;">
                            No rooms found. Add your first room above!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td><?php echo $room['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($room['hotel_name']); ?></strong>
                                <br>
                                <small style="color: #999;"><?php echo htmlspecialchars($room['hotel_location']); ?></small>
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
                                       onclick="return confirm('Are you sure you want to delete Room <?php echo htmlspecialchars($room['room_number']); ?>?')">
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
</div>

<?php include 'includes/footer.php'; ?>
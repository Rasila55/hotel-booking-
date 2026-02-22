<?php
$page_title = "Hotels Management";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get hotel to delete image
    $hotel = readOne('hotels', $id);
    if ($hotel && !empty($hotel['image'])) {
        $image = explode(',', $hotel['image']);
        foreach ($image as $image) {
            $imagePath = "uploads/hotels/" . trim($image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    }
    
    if (deleteById('hotels', $id)) {
        $_SESSION['success'] = "Hotel deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete hotel!";
    }
    header('Location: ' . BASE_PATH . '/hotels');
    exit();
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    
    // Handle image upload
    $uploadedimage = [];
    $existingimage = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';
    
    if (!empty($_FILES['image']['name'][0])) {
        $uploadDir = 'uploads/hotels/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['image']['error'][$key] === 0) {
                $fileName = time() . '_' . $key . '_' . basename($_FILES['image']['name'][$key]);
                $targetFile = $uploadDir . $fileName;
                
                // Validate image
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($imageFileType, $allowedTypes)) {
                    if (move_uploaded_file($tmp_name, $targetFile)) {
                        $uploadedimage[] = $fileName;
                    }
                }
            }
        }
    }
    
    // Combine existing and new image
    $allimage = [];
    if (!empty($existingimage)) {
        $allimage = explode(',', $existingimage);
    }
    $allimage = array_merge($allimage, $uploadedimage);
    $imageString = implode(',', $allimage);
    
    $data = [
        'name' => $name,
        'location' => $location,
        'description' => $description,
        'image' => $imageString,
        'status' => $status
    ];
    
    if ($id > 0) {
        // Update
        if (updateById('hotels', $id, $data)) {
            $_SESSION['success'] = "Hotel updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update hotel!";
        }
    } else {
        // Create
        if (create('hotels', $data)) {
            $_SESSION['success'] = "Hotel added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add hotel!";
        }
    }
    
    header('Location: ' . BASE_PATH . '/hotels');
    exit();
}

// Get hotel for editing
$editHotel = null;
if (isset($_GET['edit'])) {
    $editHotel = readOne('hotels', (int)$_GET['edit']);
}

// Get all hotels
$hotels = readAll('hotels', [], 'id');

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
        
        .form-group input[type="file"] {
            padding: 5px;
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
        
        .hotel-image {
            display: flex;
            gap: 5px;
        }
        
        .hotel-image img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .image-preview {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        
        .image-preview-item {
            position: relative;
            width: 100px;
            height: 100px;
        }
        
        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 5px;
            border: 2px solid #ddd;
        }
        
        .image-preview-item .remove-img {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
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
        <h2><?php echo $editHotel ? 'Edit Hotel' : 'Add New Hotel'; ?></h2>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <?php if ($editHotel): ?>
                <input type="hidden" name="id" value="<?php echo $editHotel['id']; ?>">
                <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($editHotel['image']); ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="name">Hotel Name *</label>
                <input type="text" id="name" name="name" required 
                       value="<?php echo $editHotel ? htmlspecialchars($editHotel['name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="location">Location *</label>
                <input type="text" id="location" name="location" required 
                       value="<?php echo $editHotel ? htmlspecialchars($editHotel['location']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?php echo $editHotel ? htmlspecialchars($editHotel['description']) : ''; ?></textarea>
            </div>
            
            <?php if ($editHotel && !empty($editHotel['image'])): ?>
                <div class="form-group">
                    <label>Current image</label>
                    <div class="image-preview" id="currentimage">
                        <?php 
                        $image = explode(',', $editHotel['image']);
                        foreach ($image as $image): 
                            $image = trim($image);
                        ?>
                            <div class="image-preview-item">
                                <img src="<?php echo BASE_PATH; ?>/uploads/hotels/<?php echo $image; ?>" alt="Hotel Image">
                                <button type="button" class="remove-img" onclick="removeImage('<?php echo $image; ?>')">×</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="image">Upload image <?php echo $editHotel ? '(Add More)' : '*'; ?></label>
                <input type="file" id="image" name="image[]" multiple accept="image/*" 
                       <?php echo $editHotel ? '' : 'required'; ?>>
                <small style="color: #666;">You can select multiple image. Allowed: JPG, JPEG, PNG, GIF</small>
            </div>
            
            <div class="form-group">
                <label for="status">Status *</label>
                <select id="status" name="status" required>
                    <option value="active" <?php echo ($editHotel && $editHotel['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($editHotel && $editHotel['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <?php echo $editHotel ? 'Update Hotel' : 'Add Hotel'; ?>
                </button>
                <?php if ($editHotel): ?>
                    <a href="<?php echo BASE_PATH; ?>/hotels" class="btn btn-warning">Cancel Edit</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="card">
        <h2>All Hotels (<?php echo countRecords('hotels'); ?>)</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>image</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($hotels)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
                            No hotels found. Add your first hotel above!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($hotels as $hotel): ?>
                        <tr>
                            <td><?php echo $hotel['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($hotel['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($hotel['location']); ?></td>
                            <td>
                                <?php if (!empty($hotel['image'])): ?>
                                    <div class="hotel-image">
                                        <?php 
                                        $image = explode(',', $hotel['image']);
                                        $displayCount = min(3, count($image));
                                        for ($i = 0; $i < $displayCount; $i++): 
                                            $image = trim($image[$i]);
                                        ?>
                                            <img src="<?php echo BASE_PATH; ?>/uploads/hotels/<?php echo $image; ?>" 
                                                 alt="<?php echo htmlspecialchars($hotel['name']); ?>">
                                        <?php endfor; ?>
                                        
                                    </div>
                                <?php else: ?>
                                    <span style="color: #999;">No image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $hotel['status']; ?>">
                                    <?php echo ucfirst($hotel['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($hotel['id'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?php echo BASE_PATH; ?>/hotels?edit=<?php echo $hotel['id']; ?>" 
                                       class="btn btn-warning">Edit</a>
                                    <a href="<?php echo BASE_PATH; ?>/hotels?delete=<?php echo $hotel['id']; ?>" 
                                       class="btn btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this hotel? All image will be deleted.')">
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

<script>
function removeImage(imageName) {
    if (confirm('Remove this image?')) {
        // Get current existing image value
        let existingInput = document.querySelector('input[name="existing_image"]');
        let existingimage = existingInput.value.split(',');
        
        // Remove the image from array
        existingimage = existingimage.filter(img => img.trim() !== imageName);
        
        // Update hidden input
        existingInput.value = existingimage.join(',');
        
        // Remove from display
        event.target.closest('.image-preview-item').remove();
    }
}
</script>

<?php include 'includes/footer.php'; ?>
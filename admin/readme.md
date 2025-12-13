# CRUD Helper - Usage Examples

## Setup

The CRUD helper is automatically included in `index.php`. Just use the functions directly in your pages!

```php
// Already included in index.php
require_once 'includes/config.php';
require_once 'includes/crud.php';
```

## Configuration

Update database credentials in `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'staymate');
```

---

## CREATE Operations

### Insert a new record
```php
// Simple insert
$userId = create('users', [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => password_hash('password123', PASSWORD_DEFAULT),
    'status' => 'active'
]);

if ($userId) {
    echo "User created with ID: $userId";
} else {
    echo "Failed to create user";
}
```

### Insert booking
```php
$bookingId = create('bookings', [
    'user_id' => 1,
    'room_id' => 5,
    'check_in' => '2025-01-15',
    'check_out' => '2025-01-20',
    'total_price' => 5000.00,
    'status' => 'pending'
]);
```

---

## READ Operations

### Get all records
```php
// Get all users
$users = readAll('users');

foreach ($users as $user) {
    echo $user['name'] . '<br>';
}
```

### Get records with conditions
```php
// Get active users only
$activeUsers = readAll('users', ['status' => 'active']);

// Get bookings for specific user
$userBookings = readAll('bookings', ['user_id' => 1]);
```

### Get records with ordering and limit
```php
// Get latest 5 bookings
$latestBookings = readAll('bookings', [], 'created_at DESC', 5);

// Get users ordered by name
$sortedUsers = readAll('users', [], 'name ASC');
```

### Get single record by ID
```php
// Get user by ID
$user = readOne('users', 1);

if ($user) {
    echo "Name: " . $user['name'];
    echo "Email: " . $user['email'];
} else {
    echo "User not found";
}
```

### Get single record by conditions
```php
// Get user by email
$user = readOne('users', ['email' => 'john@example.com']);

// Get booking by multiple conditions
$booking = readOne('bookings', [
    'user_id' => 1,
    'status' => 'confirmed'
]);
```

### Custom queries
```php
// Complex query with JOIN
$bookingsWithUsers = query(
    "SELECT b.*, u.name as user_name 
     FROM bookings b 
     JOIN users u ON b.user_id = u.id 
     WHERE b.status = ?",
    ['confirmed'],
    's'
);

// Query with multiple parameters
$results = query(
    "SELECT * FROM rooms WHERE price BETWEEN ? AND ? AND status = ?",
    [1000, 5000, 'available'],
    'iis'  // i=integer, i=integer, s=string
);
```

---

## UPDATE Operations

### Update by ID
```php
// Update user
$success = updateById('users', 1, [
    'name' => 'John Updated',
    'email' => 'john.new@example.com'
]);

if ($success) {
    echo "User updated successfully";
}
```

### Update with conditions
```php
// Update all pending bookings for a user
$success = update('bookings', 
    ['status' => 'confirmed'],  // Data to update
    ['user_id' => 1, 'status' => 'pending']  // Conditions
);

// Update room availability
$success = update('rooms',
    ['status' => 'available'],
    ['room_id' => 5]
);
```

---

## DELETE Operations

### Delete by ID
```php
// Delete user
$success = deleteById('users', 1);

if ($success) {
    echo "User deleted successfully";
}
```

### Delete with conditions
```php
// Delete old bookings
$success = delete('bookings', [
    'status' => 'cancelled',
    'user_id' => 1
]);

// Delete inactive users
$success = delete('users', ['status' => 'inactive']);
```

---

## UTILITY Functions

### Count records
```php
// Count all users
$totalUsers = count('users');

// Count active users
$activeUsers = count('users', ['status' => 'active']);

// Count bookings for a user
$userBookings = count('bookings', ['user_id' => 1]);
```

### Check if exists
```php
// Check if email exists
if (exists('users', ['email' => 'john@example.com'])) {
    echo "Email already registered";
}

// Check if username is taken
if (exists('users', ['username' => 'johndoe'])) {
    echo "Username already taken";
}
```

### Get last inserted ID
```php
create('users', ['name' => 'John', 'email' => 'john@example.com']);
$userId = lastInsertId();
echo "New user ID: $userId";
```

---

## TRANSACTIONS

### Using transactions for multiple operations
```php
try {
    beginTransaction();
    
    // Create booking
    $bookingId = create('bookings', [
        'user_id' => 1,
        'room_id' => 5,
        'total_price' => 5000
    ]);
    
    // Update room status
    updateById('rooms', 5, ['status' => 'booked']);
    
    // Create payment record
    create('payments', [
        'booking_id' => $bookingId,
        'amount' => 5000,
        'status' => 'completed'
    ]);
    
    commitTransaction();
    echo "Booking completed successfully";
    
} catch (Exception $e) {
    rollbackTransaction();
    echo "Booking failed: " . $e->getMessage();
}
```

---

## COMPLETE EXAMPLES

### User Registration
```php
// Check if email exists
if (exists('users', ['email' => $_POST['email']])) {
    $error = "Email already registered";
} else {
    // Create new user
    $userId = create('users', [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    if ($userId) {
        $_SESSION['user_id'] = $userId;
        header('Location: /dashboard');
    }
}
```

### User Login with Database
```php
// Get user by email
$user = readOne('users', ['email' => $_POST['email']]);

if ($user && password_verify($_POST['password'], $user['password'])) {
    $_SESSION['is_admin'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['name'];
    header('Location: ' . BASE_PATH . '/dashboard');
} else {
    $error = "Invalid credentials";
}
```

### Display Users List
```php
// Get all users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$users = query(
    "SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?",
    [$perPage, $offset],
    'ii'
);

$totalUsers = count('users');
$totalPages = ceil($totalUsers / $perPage);

foreach ($users as $user) {
    echo "<tr>";
    echo "<td>" . $user['id'] . "</td>";
    echo "<td>" . htmlspecialchars($user['name']) . "</td>";
    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
    echo "</tr>";
}
```

### Update Profile
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = updateById('users', $_SESSION['user_id'], [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
    if ($success) {
        $message = "Profile updated successfully";
    }
}
```

### Delete with Confirmation
```php
if (isset($_GET['delete_id'])) {
    $userId = (int)$_GET['delete_id'];
    
    // Check if user has active bookings
    if (exists('bookings', ['user_id' => $userId, 'status' => 'active'])) {
        $error = "Cannot delete user with active bookings";
    } else {
        if (deleteById('users', $userId)) {
            $success = "User deleted successfully";
        }
    }
}
```

---

## PARAMETER TYPES

When using `query()` or `execute()`, specify parameter types:

| Type | Description | Example |
|------|-------------|---------|
| `i` | Integer | `123` |
| `d` | Double/Float | `99.99` |
| `s` | String | `"hello"` |
| `b` | Blob | Binary data |

Example:
```php
query("SELECT * FROM users WHERE id = ? AND status = ?", [1, 'active'], 'is');
//                                                                         ^^
//                                                                         |└─ string
//                                                                         └── integer
```

---

## SECURITY TIPS

1. ✅ Always use prepared statements (built into CRUD helper)
2. ✅ Hash passwords before storing
3. ✅ Validate user input before inserting
4. ✅ Use `htmlspecialchars()` when displaying data
5. ✅ Check user permissions before operations

```php
// Good practice
$userId = create('users', [
    'name' => trim($_POST['name']),
    'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
]);
```

---

## ERROR HANDLING

```php
// Check if operation succeeded
$result = create('users', $data);
if ($result === false) {
    error_log("Failed to create user");
    echo "An error occurred";
} else {
    echo "User created with ID: $result";
}
```
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user_name    = $is_logged_in ? $_SESSION['name'] : '';
$user_role    = $is_logged_in ? $_SESSION['role'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StayMate</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@300..900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>




    <style>
          :root {
        --primary: #2ec1ac;
        --primary-dark: #239e8d;
        --gold: #c9a84c;
        --dark: #1a1a2e;
        --charcoal: #2d2d3a;
        --cream: #faf8f4;
        --light-gray: #f4f2ee;
        --text-muted: #6b6b7b;
        --card-shadow: 0 8px 40px rgba(0,0,0,0.09);
        --card-hover-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    
        * { font-family: "Poppins", sans-serif; }
        .h-font { font-family: "Merienda", cursive; }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

        .swiper-container img { width:100%; height:50vh; object-fit:cover; }
        @media (max-width: 992px) { .swiper-container img { height:40vh; } }
        @media (max-width: 576px)  { .swiper-container img { height:30vh; } }

        .custom-bg { background-color: #2ec1ac; }

        .availability-form { margin-top:-50px; z-index:2; position:relative; }
        @media screen and (max-width: 576px) {
            .availability-form { margin-top:25px; padding:0 35px; }
        }
    </style>
</head>
<body>

<!--  NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 shadow-sm sticky-top">
    <div class="container-fluid">

        <!-- Brand -->
        <a class="navbar-brand me-5 fw-bold fs-3 h-font"
           href="<?php echo ($is_logged_in && $user_role == 'admin') ? '/staymate/admin/admin_dashboard.php' : '/staymate/index.php'; ?>">
            StayMate
        </a>

        <!-- Mobile toggler -->
        <button class="navbar-toggler shadow-none" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMain"
                aria-controls="navbarMain"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Nav links -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link me-2" href="/staymate/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2" href="/staymate/pages/rooms.php">Rooms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2" href="/staymate/pages/contact.php">Contact us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2" href="/staymate/pages/about.php">About</a>
                </li>
            </ul>

            <!-- Right side -->
            <div class="d-flex align-items-center">
                <?php if ($is_logged_in): ?>

                    <?php if ($user_role == 'admin'): ?>
                        <!-- Admin buttons -->
                        <a href="/staymate/admin/admin_dashboard.php" class="btn btn-outline-dark shadow-none me-2">
                            <i class="bi bi-speedometer2 me-1"></i>Admin Dashboard
                        </a>
                        <a href="/staymate/pages/logout.php" class="btn btn-outline-danger shadow-none">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>

                    <?php else: ?>
                        <!-- Regular user dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-outline-dark shadow-none dropdown-toggle"
                                    type="button"
                                    id="userDropdown"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>
                                <?php echo htmlspecialchars($user_name); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0"
                                aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="/staymate/pages/my_bookings.php">
                                        <i class="bi bi-calendar-check me-2"></i>My Bookings
                                    </a>
                                </li>
                                
            
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="/staymate/pages/logout.php">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Guest buttons -->
                    <button type="button" class="btn btn-outline-dark shadow-none me-lg-3 me-2"
                            data-bs-toggle="modal" data-bs-target="#LoginModal">
                        Login
                    </button>
                    <button type="button" class="btn btn-outline-dark shadow-none"
                            data-bs-toggle="modal" data-bs-target="#registerModal">
                        Register
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>


<style>
    
    .modal-content { border-radius: 16px; border: none; overflow: hidden; }
    .modal-header { background: var(--dark); color: #fff; border: none; padding: 20px 24px; }
    .modal-header .btn-close { filter: invert(1); }
    .modal-body { padding: 28px 24px; }
    .modal-body .form-control,
    .modal-body .form-select { border: 1.5px solid #e4e0d8; border-radius: 8px; font-size: 0.9rem; }
    .modal-body .form-control:focus,
    .modal-body .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(46,193,172,0.15); }

</style>

<!-- LOGIN MODAL -->
<div class="modal fade" id="LoginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="./pages/login.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2 display-font">
                        <i class="bi bi-person-circle fs-4"></i> Sign In
                    </h5>
                    <button type="reset" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" class="form-control shadow-none" name="email" placeholder="you@example.com">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control shadow-none" name="password" placeholder="••••••••">
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <button type="submit" class="btn btn-primary-custom">Sign In</button>
                        <a href="javascript:void(0)" class="text-muted text-decoration-none" style="font-size:0.85rem;">Forgot password?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- REGISTER MODAL -->
<div class="modal fade" id="registerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="./pages/register.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2 display-font">
                        <i class="bi bi-person-plus fs-4"></i> Create Account
                    </h5>
                    <button type="reset" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning d-flex gap-2 align-items-start py-2 mb-4" style="font-size:0.82rem; border-radius:8px;">
                        <i class="bi bi-info-circle-fill mt-1"></i>
                        <span>Your details must match your ID (NID, Passport, Driving Licence) required at check-in.</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" class="form-control shadow-none" name="name" placeholder="John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control shadow-none" name="email" placeholder="you@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="number" class="form-control shadow-none" name="phone" placeholder="98XXXXXXXX" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Profile Picture</label>
                            <input type="file" class="form-control shadow-none" name="picture">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control shadow-none" rows="2" placeholder="Your address..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pincode</label>
                            <input type="number" name="pincode" class="form-control shadow-none" placeholder="44600">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="dob" class="form-control shadow-none">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control shadow-none" placeholder="••••••••" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control shadow-none" placeholder="••••••••" required>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" name="register" class="btn btn-primary-custom px-5">Create Account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
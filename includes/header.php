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
          rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
          crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@300..900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

    <style>
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

<!-- ══ NAVBAR ══ -->
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
<!-- ══ END NAVBAR ══ -->
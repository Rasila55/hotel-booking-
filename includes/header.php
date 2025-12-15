<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StayMate</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@300..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <!-- <style>
        *{
            font-family: "Poppins", sans-serif;
        }
        .h-font{
            font-family: "Merienda", cursive;

        }
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
      .swiper-container img {
        width: 100%;
        height: 50vh;     /* image takes only half of the screen height */
        object-fit: cover;
        }

        /* Medium laptops */
        @media (max-width: 1400px) {
        .swiper-container img {
        height: 50vh;
        }
        }

        /* Tablets */
        @media (max-width: 992px) {
        .swiper-container img {
        height: 40vh;
        }
        }

        /* Mobile */
        @media (max-width: 576px) {
        .swiper-container img {
        height: 30vh;
        }
        }
        .custom-bg{
            background-color: #2ec1ac;
        }
        /* .custom-bg:hover{
            background-color: #279e8c;
        } */
        .availability-form{
            margin-top:-50px;
            z-index: 2;
            position: relative;
        }
         @media screen and (max-width: 576px) {
            .availability-form{
            margin-top:25px;
            padding: 0 35px;
            
            }

         }


    </style> -->
</head>
<body>


<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user_name = $is_logged_in ? $_SESSION['name'] : '';
$user_role = $is_logged_in ? $_SESSION['role'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        * {
            font-family: "Poppins", sans-serif;
        }
        .h-font {
            font-family: "Merienda", cursive;
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="<?php echo $is_logged_in && $user_role == 'admin' ? 'admin/admin_dashboard.php' : 'index.php'; ?>">StayMate</a>
        
        <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active me-2" aria-current="page" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2" href="#">Rooms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2" href="#">Facilities</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2" href="#">Contact us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link me-2" href="#">About</a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center">
                <?php if ($is_logged_in): ?>
                    <!-- Logged In User Menu -->
                    <?php if ($user_role == 'admin'): ?>
                        <!-- Admin Menu -->
                        <a href="admin/admin_dashboard.php" class="btn btn-outline-dark shadow-none me-2">
                            <i class="bi bi-speedometer2 me-1"></i>Admin Dashboard
                        </a>
                        <a href="pages/logout.php" class="btn btn-outline-danger shadow-none">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    <?php else: ?>
                        <!-- Regular User Menu -->
                        <div class="dropdown">
                            <button class="btn btn-outline-dark shadow-none dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>
                                <?php echo htmlspecialchars($user_name); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="pages/user_dashboard.php">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="pages/my_bookings.php">
                                        <i class="bi bi-calendar-check me-2"></i>My Bookings
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="pages/edit_profile.php">
                                        <i class="bi bi-person-gear me-2"></i>Edit Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="pages/change_password.php">
                                        <i class="bi bi-key me-2"></i>Change Password
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="pages/logout.php">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <!-- Guest User (Not Logged In) -->
                    <button type="button" class="btn btn-outline-dark shadow-none me-lg-3 me-2" data-bs-toggle="modal" data-bs-target="#LoginModal">
                        Login 
                    </button>
                    <button type="button" class="btn btn-outline-dark shadow-none" data-bs-toggle="modal" data-bs-target="#registerModal">
                        Register
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
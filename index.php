<?php
session_start();
require_once 'includes/db.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staymate — Find Your Perfect Stay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css"/>

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

        * { font-family: 'DM Sans', sans-serif; }

        body { background: var(--cream); color: var(--dark); }

        /* ── TYPOGRAPHY ── */
        .display-font { font-family: 'Playfair Display', serif; }

        .section-label {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--primary);
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            font-weight: 700;
            line-height: 1.2;
            color: var(--dark);
        }

        /* ── BUTTONS ── */
        .btn-primary-custom {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 22px;
            font-weight: 500;
            font-size: 0.88rem;
            letter-spacing: 0.03em;
            transition: all 0.25s ease;
        }
        .btn-primary-custom:hover {
            background: var(--primary-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(46,193,172,0.35);
        }

        .btn-outline-custom {
            background: transparent;
            color: var(--dark);
            border: 1.5px solid var(--dark);
            border-radius: 4px;
            padding: 9px 22px;
            font-weight: 500;
            font-size: 0.82rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            transition: all 0.25s ease;
        }
        .btn-outline-custom:hover {
            background: var(--dark);
            color: #fff;
        }

        /* ── HERO / SWIPER ── */
        .hero-wrapper {
            position: relative;
        }

        .swiper-container {
            border-radius: 0 0 24px 24px;
            overflow: hidden;
        }

        .swiper-container img {
            width: 100%;
            height: 58vh;
            object-fit: cover;
            filter: brightness(0.78);
        }

        @media (max-width: 992px) { .swiper-container img { height: 42vh; } }
        @media (max-width: 576px) { .swiper-container img { height: 32vh; } }

        .hero-overlay-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 5;
            color: #fff;
            pointer-events: none;
            width: 100%;
        }
        .hero-overlay-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 5vw, 3.8rem);
            font-weight: 700;
            text-shadow: 0 2px 20px rgba(0,0,0,0.4);
            line-height: 1.15;
        }
        .hero-overlay-text p {
            font-size: clamp(0.88rem, 2vw, 1.1rem);
            font-weight: 300;
            opacity: 0.9;
            letter-spacing: 0.06em;
        }

        .swiper-pagination-bullet-active { background: var(--primary) !important; }

        /* ── AVAILABILITY FORM ── */
        .availability-form {
            margin-top: -44px;
            position: relative;
            z-index: 10;
        }

        .avail-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 12px 50px rgba(0,0,0,0.12);
            padding: 28px 32px;
        }

        .avail-card .form-control,
        .avail-card .form-select {
            border: 1.5px solid #e4e0d8;
            border-radius: 8px;
            font-size: 0.9rem;
            color: var(--dark);
            background: var(--light-gray);
        }
        .avail-card .form-control:focus,
        .avail-card .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46,193,172,0.15);
            background: #fff;
        }

        .avail-card label {
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .divider-line {
            width: 1px;
            background: #e4e0d8;
            height: 60px;
            align-self: center;
        }

        @media (max-width: 576px) {
            .availability-form { margin-top: 24px; }
            .avail-card { padding: 20px 18px; }
            .divider-line { display: none; }
        }

        /* ── SECTION DIVIDER ── */
        .section-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 12px;
        }
        .section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0dbd2;
        }

        /* ── HOTEL CARDS ── */
        .hotel-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: none;
            height: 100%;
        }
        .hotel-card:hover {
            box-shadow: var(--card-hover-shadow);
            transform: translateY(-6px);
        }

        .hotel-card .card-img-wrapper {
            position: relative;
            overflow: hidden;
        }
        .hotel-card .card-img-wrapper img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .hotel-card:hover .card-img-wrapper img {
            transform: scale(1.06);
        }

        .hotel-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(26,26,46,0.75);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 30px;
            backdrop-filter: blur(4px);
        }

        .hotel-card .card-body {
            padding: 20px 22px 22px;
        }

        .hotel-card h5 {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 6px;
        }

        .hotel-location {
            font-size: 0.82rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
        }

        .hotel-desc {
            font-size: 0.87rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .hotel-card .btn-view {
            background: transparent;
            color: var(--primary);
            border: 1.5px solid var(--primary);
            border-radius: 6px;
            padding: 8px 18px;
            font-weight: 600;
            font-size: 0.82rem;
            letter-spacing: 0.04em;
            transition: all 0.2s ease;
            width: 100%;
            text-decoration: none;
            text-align: center;
            display: block;
        }
        .hotel-card .btn-view:hover {
            background: var(--primary);
            color: #fff;
        }

        /* ── ROOM CARDS ── */
        .room-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
            height: 100%;
        }
        .room-card:hover {
            box-shadow: var(--card-hover-shadow);
            transform: translateY(-6px);
        }

        .room-card .card-img-wrapper {
            position: relative;
            overflow: hidden;
        }
        .room-card .card-img-wrapper img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .room-card:hover .card-img-wrapper img {
            transform: scale(1.06);
        }

        .room-card .card-body {
            padding: 20px 22px 22px;
        }

        .room-card h5 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .room-price {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 8px;
        }
        .room-price span {
            font-size: 0.78rem;
            font-weight: 400;
            color: var(--text-muted);
        }

        .room-desc {
            font-size: 0.87rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .room-card .btn-book {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 9px 18px;
            font-weight: 600;
            font-size: 0.82rem;
            letter-spacing: 0.04em;
            transition: all 0.2s ease;
            width: 100%;
            text-align: center;
            display: block;
            text-decoration: none;
        }
        .room-card .btn-book:hover {
            background: var(--primary-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(46,193,172,0.35);
        }

        /* ── SECTION BG ── */
        .section-hotels { background: var(--cream); padding: 80px 0 60px; }
        .section-rooms  { background: var(--light-gray); padding: 80px 0 60px; }

        /* ── MODALS ── */
        .modal-content { border-radius: 16px; border: none; overflow: hidden; }
        .modal-header {
            background: var(--dark);
            color: #fff;
            border: none;
            padding: 20px 24px;
        }
        .modal-header .btn-close { filter: invert(1); }
        .modal-body { padding: 28px 24px; }
        .modal-body .form-control,
        .modal-body .form-select {
            border: 1.5px solid #e4e0d8;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .modal-body .form-control:focus,
        .modal-body .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46,193,172,0.15);
        }

        /* ── MORE LINK ── */
        .more-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--dark);
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            text-decoration: none;
            border-bottom: 2px solid var(--dark);
            padding-bottom: 2px;
            transition: all 0.2s ease;
        }
        .more-link:hover {
            color: var(--primary);
            border-color: var(--primary);
        }

        /* ── ALERTS ── */
        .top-alert {
            border-radius: 0;
            border: none;
            font-size: 0.88rem;
            font-weight: 500;
            padding: 12px 24px;
        }
    </style>
</head>

<body>

<?php
if (isset($_SESSION['booking_message'])) {
    echo '<div class="alert top-alert alert-' . $_SESSION['booking_message_type'] . ' text-center">';
    echo $_SESSION['booking_message'];
    echo '</div>';
    unset($_SESSION['booking_message']);
    unset($_SESSION['booking_message_type']);
}
?>

<?php include('includes/header.php'); ?>

<!-- ══════════════════════════════════════
     LOGIN MODAL
══════════════════════════════════════ -->
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

<!-- ══════════════════════════════════════
     REGISTER MODAL
══════════════════════════════════════ -->
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

<!-- ══════════════════════════════════════
     HERO CAROUSEL
══════════════════════════════════════ -->
<div class="hero-wrapper">
    <div class="container-fluid px-lg-4 mt-4">
        <div class="swiper swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide"><img src="images/carousals/carousel.jpg"></div>
                <div class="swiper-slide"><img src="images/carousals/carousel1.jpg"></div>
                <div class="swiper-slide"><img src="images/carousals/carousel2.jpg"></div>
                <div class="swiper-slide"><img src="images/carousals/carousel3.jpg"></div>
                <div class="swiper-slide"><img src="images/carousals/carousel4.jpg"></div>
                <div class="swiper-slide"><img src="images/carousals/carousel5.jpg"></div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
    <div class="hero-overlay-text">
        <h1>Your Perfect Stay<br><em>Awaits</em></h1>
        <p>Discover handpicked hotels &amp; rooms across Nepal</p>
    </div>
</div>

<!-- ══════════════════════════════════════
     AVAILABILITY FORM
══════════════════════════════════════ -->
<div class="availability-form">
    <div class="container">
        <div class="avail-card">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label>Check-in</label>
                    <input type="date" class="form-control shadow-none" name="checkin" id="checkin" required>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label>Check-out</label>
                    <input type="date" class="form-control shadow-none" name="checkout" id="checkout" required>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label>Adults</label>
                    <select class="form-select shadow-none" name="adults">
                        <?php for($i=1;$i<=5;$i++) echo "<option value='$i'>$i</option>"; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label>Children</label>
                    <select class="form-select shadow-none" name="children">
                        <?php for($i=0;$i<=3;$i++) echo "<option value='$i'>$i</option>"; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <form action="./pages/booking.php" method="POST" style="margin:0;">
                        <button type="submit" class="btn btn-primary-custom w-100" style="padding:10px;">
                            <i class="bi bi-search me-1"></i> Search
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════
     OUR HOTELS  (above rooms)
══════════════════════════════════════ -->
<section class="section-hotels">
    <div class="container">

        <div class="d-flex align-items-end justify-content-between mb-5 flex-wrap gap-3">
            <div>
                <div class="section-divider">
                    <span class="section-label">Curated Properties</span>
                </div>
                <h2 class="section-title mb-0">Our Partner Hotels</h2>
            </div>
            <a href="/staymate/pages/hotels.php" class="more-link">
                View All Hotels <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php
                $hotels = mysqli_query($conn, "SELECT * FROM hotels LIMIT 3");
                while ($hotel = mysqli_fetch_assoc($hotels)):
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="hotel-card">
                    <div class="card-img-wrapper">
                        <?php if (!empty($hotel['image'])): ?>
<img src="/staymate/admin/uploads/hotels/<?php echo htmlspecialchars($hotel['image']); ?>" 
     class="card-img-top" style="height:200px; object-fit:cover;">   
                             <?php else: ?>
                            <img src="images/rooms/room1.png" alt="Hotel">
                        <?php endif; ?>
                        <span class="hotel-badge"><i class="bi bi-building me-1"></i> Hotel</span>
                    </div>
                    <div class="card-body">
                        <h5><?php echo htmlspecialchars($hotel['name']); ?></h5>
                        <div class="hotel-location">
                            <i class="bi bi-geo-alt-fill" style="color:var(--primary);"></i>
                            <?php echo htmlspecialchars($hotel['location']); ?>
                        </div>
                        <p class="hotel-desc">
                            <?php echo htmlspecialchars($hotel['description']) ?: 'A wonderful hotel experience awaits you.'; ?>
                        </p>
                        <a href="/staymate/pages/rooms.php?hotel_id=<?php echo $hotel['id']; ?>" class="btn-view">
                            View Rooms <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

    </div>
</section>

<!-- ══════════════════════════════════════
     OUR ROOMS
══════════════════════════════════════ -->
<section class="section-rooms">
    <div class="container">

        <div class="d-flex align-items-end justify-content-between mb-5 flex-wrap gap-3">
            <div>
                <div class="section-divider">
                    <span class="section-label">Available Now</span>
                </div>
                <h2 class="section-title mb-0">Featured Rooms</h2>
            </div>
            <a href="/staymate/pages/rooms.php" class="more-link">
                More Rooms <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php
                $sql = "SELECT * FROM rooms WHERE status='available' LIMIT 3";
                $result = mysqli_query($conn, $sql);
                while($room = mysqli_fetch_assoc($result)):
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="room-card">
                    <div class="card-img-wrapper">
                        <img src="images/rooms/room1.png" alt="<?php echo htmlspecialchars($room['room_type']); ?> Room">
                        <span class="hotel-badge" style="background:rgba(46,193,172,0.85);">
                            <i class="bi bi-check-circle me-1"></i> Available
                        </span>
                    </div>
                    <div class="card-body">
                        <h5><?php echo htmlspecialchars($room['room_type']); ?> Room</h5>
                        <p class="room-price">
                            Rs. <?php echo number_format($room['price']); ?>
                            <span>/ night</span>
                        </p>
                        <p class="room-desc">
                            <?php echo $room['description'] ?? 'Comfortable and modern room with all amenities.'; ?>
                        </p>
                        <a href="/staymate/pages/rooms.php" class="btn-book">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

    </div>
</section>

<?php include('includes/footer.php'); ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script>
    const swiper = new Swiper('.swiper', {
        loop: true,
        autoplay: { delay: 4500, disableOnInteraction: false },
        speed: 900,
        effect: 'fade',
        pagination: { el: '.swiper-pagination', clickable: true },
    });

    // Date validation: check-out must be after check-in
    document.getElementById('checkin').addEventListener('change', function() {
        const checkout = document.getElementById('checkout');
        checkout.min = this.value;
        if (checkout.value && checkout.value <= this.value) checkout.value = '';
    });

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('checkin').min = today;
</script>
</body>
</html>
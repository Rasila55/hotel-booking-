<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StayMate - About Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        * { font-family: "Poppins", sans-serif; }
        .h-font { font-family: "Merienda", cursive; }
        .h-line { width: 60px; height: 3px; margin: 10px auto; }
        .box { border-top: 4px solid #1aab8a !important; transition: transform 0.2s; }
        .box:hover { transform: translateY(-5px); }
        .stat-number { font-size: 28px; font-weight: 700; color: #1aab8a; }
    </style>
</head>
<body class="bg-light">

<?php
session_start();
require_once '../includes/db.php';
include '../includes/header.php';
?>

<!-- Page Title -->
<div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">ABOUT US</h2>
    <div class="h-line bg-dark"></div>
    <p class="text-center mt-3 text-muted">
        StayMate is a modern hotel booking platform designed to make finding <br>
        and reserving the perfect room simple, fast and hassle-free.
    </p>
</div>

<!-- About Section -->
<div class="container mb-5">
    <div class="row justify-content-between align-items-center">
        <div class="col-lg-6 col-md-6 mb-4">
            <h3 class="mb-3 fw-bold">Who We Are</h3>
            <p class="text-muted" style="line-height:1.8;">
                StayMate was created to solve the common problems people face when
                trying to book hotel rooms such as long phone calls, uncertain availability
                and confusing pricing. Our platform connects guests directly with hotels,
                allowing real-time room browsing, instant booking and easy management
                of reservations all in one place.
            </p>
            <p class="text-muted" style="line-height:1.8;">
                Whether you are traveling for business or leisure, StayMate ensures
                you find the right room at the right price with complete transparency.
                Our admin system also helps hotel owners manage their properties
                efficiently without any technical expertise.
            </p>
        </div>
        <div class="col-lg-5 col-md-6 mb-4">
            <img src="/staymate/images/rooms/room1.png"
                 class="w-100 rounded shadow"
                 style="max-height:320px; object-fit:cover;">
        </div>
    </div>
</div>

<!-- Stats -->
<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4 px-3">
            <div class="bg-white rounded shadow p-4 text-center box">
                <i class="bi bi-building" style="font-size:40px; color:#1aab8a;"></i>
                <div class="stat-number mt-2">10+</div>
                <h6 class="mt-1">Hotels Listed</h6>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4 px-3">
            <div class="bg-white rounded shadow p-4 text-center box">
                <i class="bi bi-door-open" style="font-size:40px; color:#1aab8a;"></i>
                <div class="stat-number mt-2">50+</div>
                <h6 class="mt-1">Rooms Available</h6>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4 px-3">
            <div class="bg-white rounded shadow p-4 text-center box">
                <i class="bi bi-people" style="font-size:40px; color:#1aab8a;"></i>
                <div class="stat-number mt-2">200+</div>
                <h6 class="mt-1">Happy Guests</h6>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4 px-3">
            <div class="bg-white rounded shadow p-4 text-center box">
                <i class="bi bi-star" style="font-size:40px; color:#1aab8a;"></i>
                <div class="stat-number mt-2">150+</div>
                <h6 class="mt-1">Positive Reviews</h6>
            </div>
        </div>
    </div>
</div>

<!-- Why Choose Us -->
<div class="bg-white py-5 mt-3">
    <div class="container">
        <h3 class="text-center fw-bold h-font mb-5">WHY CHOOSE STAYMATE?</h3>
        <div class="row">
            <div class="col-md-4 mb-4 text-center px-4">
                <i class="bi bi-lightning-charge" style="font-size:40px; color:#1aab8a;"></i>
                <h5 class="mt-3 fw-bold">Instant Booking</h5>
                <p class="text-muted">Book your room in minutes without any phone calls or waiting. Everything is done online in just a few clicks.</p>
            </div>
            <div class="col-md-4 mb-4 text-center px-4">
                <i class="bi bi-shield-check" style="font-size:40px; color:#1aab8a;"></i>
                <h5 class="mt-3 fw-bold">Secure and Reliable</h5>
                <p class="text-muted">Your personal information and booking data are kept safe with encrypted passwords and secure session management.</p>
            </div>
            <div class="col-md-4 mb-4 text-center px-4">
                <i class="bi bi-currency-dollar" style="font-size:40px; color:#1aab8a;"></i>
                <h5 class="mt-3 fw-bold">Transparent Pricing</h5>
                <p class="text-muted">See the exact price per night before booking. No hidden charges or surprise fees at checkout.</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA -->
<div class="text-center py-5">
    <h4 class="fw-bold mb-3">Ready to book your perfect room?</h4>
    <a href="/staymate/pages/rooms.php"
       style="background:#1aab8a; color:#fff; padding:12px 40px; border-radius:4px; text-decoration:none; font-weight:600;">
        Browse Rooms
    </a>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
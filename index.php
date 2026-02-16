<?php
session_start();
require_once 'includes/db.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staymate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

     <style>
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
        .btn-custom{
    background-color: #2ec1ac;
    color: #fff;
    border: none;
}

.btn-custom:hover{
    background-color: #279e8c;
    color: #fff;
}
                    .availability-form{
                margin-top: -30px;
                position: relative;
                z-index: 10;
            }

         @media screen and (max-width: 576px) {
            .availability-form{
            margin-top:25px;
            padding: 0 35px;
            
            }

         }


    </style>
</head>



<?php
if (isset($_SESSION['booking_message'])) {
    echo '<div class="alert alert-' . $_SESSION['booking_message_type'] . '">';
    echo $_SESSION['booking_message'];
    echo '</div>';

    unset($_SESSION['booking_message']);
    unset($_SESSION['booking_message_type']);
}
?>


<!-- Sabina's code -->
 <?php include('includes/header.php'); ?>
 <body class = "bg-light">
   
<div class="modal fade" id="LoginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
            <div class="modal-content">
                <form action="./pages/login.php" method="POST">
                     <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center" >
                         <i class="bi bi-person fs-3 me-2"></i> User Login
                         </h5>
                         <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label  class="form-label">Email address</label>
                            <input type="email" class="form-control shadow-none" name="email" >
                        </div>
                        <div class="mb-3">
                            <label  class="form-label">Password</label>
                            <input type="Password" class="form-control shadow-none" name="password" >
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                          <button type="Submit" class="btn btn-dark shadow-none">LOGIN</button>
                             <a href ="javascript: void(0)" class="text-secondary text-decoration-none">Forget Password?</a> 
                        </div>
                    </div>
               </form>
            </div>
    </div>
</div>
<div class="modal fade" id="registerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <form action="./pages/register.php" method="POST" enctype="multipart/form-data">
                     <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center" >
                         <i class="bi bi-person-lines-fill fs-3 me-2"></i> User Registration
                         </h5>
                         <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <span class="badge bg-light text-dark mb-3 text-wrap lh-base ">
                            Note: Your details must match with your ID (NID,Passport, Driving license,etc)
                            that will be required during check-in.
                        </span>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-6 ps-0 mb-3">
                                     <label  class="form-label">Name</label>
                                    <input type="text" class="form-control shadow-none"  name="name"required>
                                </div>
                                <div class="col-md-6 p-0 mb-3">
                                     <label  class="form-label">Email</label>
                                    <input type="email" class="form-control shadow-none" name="email"required>
                                </div>
                                 <div class="col-md-6 ps-0 mb-3">
                                     <label  class="form-label">Phone Number</label>
                                    <input type="number" class="form-control shadow-none"  name="phone" required>
                                </div>
                                <div class="col-md-6 p-0 mb-3">
                                     <label  class="form-label">Picture</label>
                                    <input type="file" class="form-control shadow-none" name="picture" >
                                </div>
                                <div class="col-md-12 p-0 mb-3">
                                     <label  class="form-label">Address</label>
                                    <textarea  name="address"class="form-control shadow-none"  rows="1"></textarea>
                                </div>
                                 <div class="col-md-6 ps-0 mb-3">
                                     <label  class="form-label">Pincode</label>
                                    <input type="number" name="pincode" class="form-control shadow-none" >
                                </div>
                                <div class="col-md-6 p-0 mb-3">
                                     <label  class="form-label">Date of birth</label>
                                    <input type="date"  name="dob"class="form-control shadow-none" >
                                </div>
                                <div class="col-md-6 ps-0 mb-3">
                                     <label  class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control shadow-none"required >
                                </div>
                                <div class="col-md-6 p-0 mb-3">
                                     <label  class="form-label">Confirm Password</label>
                                    <input type="password"  name="confirm_password" class="form-control shadow-none" required >
                                </div>

                            </div>
                        </div>
                        <div class="text-center my-1">
                          <button type="Submit" name="register " class="btn btn-dark shadow-none">REGISTER</button>

                        </div>
                    </div>  
               </form>
            </div>
        </div>
</div>
<!-- Carousel  -->
<div class="container-fluid px px-lg-4 mt-4">
    <div class="swiper swiper-container">
        <div class="swiper-wrapper">
           <div class="swiper-slide">
                <img src="images/carousals/carousel.jpg" class="w-100 d-block" />
            </div>
            <div class="swiper-slide">
                <img src="images/carousals/carousel1.jpg" class="w-100 d-block" />
            </div>
            <div class="swiper-slide">
                <img src="images/carousals/carousel2.jpg" class="w-100 d-block"/>
            </div>
            <div class="swiper-slide">
                <img src="images/carousals/carousel3.jpg" class="w-100 d-block" />
            </div>
            <div class="swiper-slide">
                <img src="images/carousals/carousel4.jpg" class="w-100 d-block" />
            </div>
            <div class="swiper-slide">
                <img src="images/carousals/carousel5.jpg" class="w-100 d-block"/>
            </div>
            
        </div>
    </div>
</div>
<!-- check availability form  -->

<div class="container availability-form">
    <div class="row">
        <div class="col-lg-12 bg-white shadow p-4 rounded">
            <h5 class="mb-4">Checking Booking Availability</h5>
                <form action="./pages/booking.php" method="POST">

                <div class="row align-items-end">
                    <div class="col-lg-3 mb-3">
                        <label class="form-label" style="font-weight:500;">Check-in</label>
                        <input type="date" class="form-control shadow-none" name="checkin" id="checkin" required>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <label class="form-label" style="font-weight:500;">Check-out</label>
                        <input type="date" class="form-control shadow-none" name="checkout" id="checkout" required>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <label class="form-label" style="font-weight:500;">Adult</label>
                        <select class="form-select shadow-none" name="adults" required>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="col-lg-2 mb-3">
                        <label class="form-label" style="font-weight:500;">Children</label>
                        <select class="form-select shadow-none" name="children" required>
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div class="col-lg-1 mb-lg-3 mt-2">
                         <button type="submit" class="btn btn-primary shadow-none">Submit</button>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



 

?>
<!-- Our Rooms -->
<h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">OUR ROOMS</h2>
<div class="container">
    <div class="row">
        <?php
        $sql = "SELECT * FROM rooms WHERE status='available' LIMIT 3";
        $result = mysqli_query($conn, $sql);
        while($room = mysqli_fetch_assoc($result)):
        ?>
        <div class="col-lg-4 col-md-6 my-3">
            <div class="card border-0 shadow" style="max-width:350px; margin:auto;">
                <img src="images/rooms/room1.png" class="card-img-top" style="height:200px; object-fit:cover;">
                <div class="card-body">
                    <h5><?php echo htmlspecialchars($room['room_type']); ?> Room</h5>
                    <p class="fw-bold">Rs. <?php echo number_format($room['price']); ?>/night</p>
                    <p class="card-text"><?php echo $room['description'] ?? 'Comfortable and modern room.'; ?></p>
                    <a href="/staymate/pages/rooms.php" class="btn btn-custom">Book Now</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>

        <div class="col-12 text-center mt-4">
            <a href="/staymate/pages/rooms.php" 
               class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">
                More Rooms >>>
            </a>
        </div>
    </div>
</div>

<br><br><br>



<br><br><br>
<br><br><br>



<?php include('includes/footer.php'); ?>
</body>
</html>


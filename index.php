<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staymate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        *{
            font-family: "poppins", sans-serif;
        }
        .h-font{
            font-family: "merienda", cursive;
        }
    </style>
</head>
<!-- Sabina's code -->
 <?php include('includes/header.php'); ?>
 <body class = "bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-2 shadow-sm sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand me- fw-bold fs-3 h-font" href="index.php">StayMate</a>
            <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                         <a class="nav-link active me-2" aria-current="page" href="#">Home</a>
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
                 <div class="d-flex">
                    <button type="button" class="btn btn-outline-dark shadow-none me-lg-3 me-2" data-bs-toggle="modal" data-bs-target="#LoginModal">
                            Login 
                    </button>
                    <button type="button" class="btn btn-outline-dark shadow-none " data-bs-toggle="modal" data-bs-target="#registerModal">
                            Register
                    </button>

                 </div>
            </div>
        </div>
    </nav>
<div class="modal fade" id="LoginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
            <div class="modal-content">
                <form>
                     <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center" >
                         <i class="bi bi-person fs-3 me-2"></i> User Login
                         </h5>
                         <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label  class="form-label">Email address</label>
                            <input type="email" class="form-control shadow-none" >
                        </div>
                        <div class="mb-3">
                            <label  class="form-label">Password</label>
                            <input type="Password" class="form-control shadow-none" >
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
                <form>
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
                                    <input type="text" class="form-control shadow-none" >
                                </div>
                                <div class="col-md-6 p-0 mb-3">
                                     <label  class="form-label">Email</label>
                                    <input type="email" class="form-control shadow-none" >
                                </div>
                                 <div class="col-md-6 ps-0 mb-3">
                                     <label  class="form-label">Phone Number</label>
                                    <input type="number" class="form-control shadow-none" >
                                </div>
                                <div class="col-md-6 p-0 mb-3">
                                     <label  class="form-label">Picture</label>
                                    <input type="file" class="form-control shadow-none" >
                                </div>
                                <div class="col-md-12 p-0 mb-3">
                                     <label  class="form-label">Address</label>
                                    <textarea class="form-control shadow-none"  rows="1"></textarea>
                                </div>
                                 <div class="col-md-6 ps-0 mb-3">
                                     <label  class="form-label">Pincode</label>
                                    <input type="number" class="form-control shadow-none" >
                                </div>
                                <div class="col-md-6 p-0 mb-3">
                                     <label  class="form-label">Date of birth</label>
                                    <input type="date" class="form-control shadow-none" >
                                </div>
                                <div class="col-md-6 ps-0 mb-3">
                                     <label  class="form-label">Password</label>
                                    <input type="password" class="form-control shadow-none" >
                                </div>
                                <div class="col-md-6 p-0 mb-3">
                                     <label  class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control shadow-none" >
                                </div>

                            </div>
                        </div>
                        <div class="text-center my-1">
                          <button type="Submit" class="btn btn-dark shadow-none">REGISTER</button>

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

<div class=" container availability-form">
    <div class="row">
        <div class="col-lg-12 bg-white shadow p-4 rounded">
            <h5 class="mb-4">Checking Booking Availability</h5>
            <form>
                <div class="row align-items-end">
                    <div class="col-lg-3 mb-3">
                        <label  class="form-label" style="font-weight:500;">Check -in</label>
                        <input type="date" class="form-control shadow-none" >
                    </div>
                    <div class="col-lg-3 mb-3">
                        <label  class="form-label" style="font-weight:500;">Check -out</label>
                        <input type="date" class="form-control shadow-none" >
                    </div>
                    <div class="col-lg-3 mb-3">
                        <label class="form-label" style="font-weight:500;">Adult</label>
                        <select class="form-select shadow-none">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div class="col-lg-2 mb-3">
                        <label class="form-label" style="font-weight:500;">Children</label>
                        <select class="form-select shadow-none">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                   <div class="col-lg-1 mb-lg-3 mt-2">
                    <button type="submit" class="btn text-white shadow-none custom-bg">Submit</button>
                   </div> 
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Our Rooms -->
 
<h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">OUR ROOMS</h2>
<div class="container">
    <div class="row">
        <div class="col-lg-4 col-md-6 my-3">
            <div class="card border-0 shadow" style="max-width: 350px; margin: auto;">
                <img src="images/rooms/room1.png" class="card-img-top" >
                <div class="card-body">
                    <h5>Simple Room Name</h5>
                    
                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card’s content.</p>
                    <a href="#" class="btn btn-primary">Go somewhere</a>
                </div>
            </div>
        </div>
        <div class="col-lg-12 text-center mt-5">
            <a href="#" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">More Rooms >>></a>
        </div>
    </div>
</div>

<br><br><br>
<br><br><br>



<?php include('includes/footer.php'); ?>
</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>


<script>
    var swiper = new Swiper(".swiper-container", {
    spaceBetween: 30,
    effect: "fade",
    loop: true,
    autoplay: {
        delay: 3500,
        disableOnInteraction: false,
    }
    });
</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <footer class="bg-dark text-light pt-5 pb-4 mt-5">
  <div class="container text-md-left">
    <div class="row text-md-left">
      
      <!-- About Us -->
      <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
        <h5 class="text-uppercase mb-4 font-weight-bold h-font">StayMate</h5>
        <p>
          Your comfort, our priority. Enjoy your stay with us with modern amenities and warm hospitality.
        </p>
      </div>

      <!-- Quick Links -->
      <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
        <h5 class="text-uppercase mb-4 font-weight-bold">Quick Links</h5>
        <p><a href="../index.php" class="text-light text-decoration-none">Home</a></p>
        <p><a href="../pages/rooms.php" class="text-light text-decoration-none">Rooms</a></p>
        <p><a href="../pages/facilities.php" class="text-light text-decoration-none">Facilities</a></p>
        <p><a href="../pages/contact_us.php" class="text-light text-decoration-none">Contact Us</a></p>
        <p><a href="../pages/about.php" class="text-light text-decoration-none">About</a></p>
      </div>

      <!-- Contact Info -->
      <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
        <h5 class="text-uppercase mb-4 font-weight-bold">Contact</h5>
        <p><i class="bi bi-telephone-fill me-2"></i>+977-1234567890</p>
        <p><i class="bi bi-telephone-fill me-2"></i>+977-0987654321</p>
        <p><i class="bi bi-envelope-fill me-2"></i>info@staymate.com</p>
        <p><i class="bi bi-geo-alt-fill me-2"></i>Kathmandu, Nepal</p>
      </div>

      <!-- Social Media -->
      <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mt-3">
        <h5 class="text-uppercase mb-4 font-weight-bold">Follow Us</h5>
        <a href="#" class="text-light me-4"><i class="bi bi-facebook fs-4"></i></a>
        <a href="#" class="text-light me-4"><i class="bi bi-instagram fs-4"></i></a>
        <a href="#" class="text-light me-4"><i class="bi bi-twitter fs-4"></i></a>
      </div>

    </div>

    <hr class="mb-4">
    <div class="row align-items-center">
      <div class="col-md-7 col-lg-8">
        <p>© <?php echo date('Y'); ?> StayMate. All Rights Reserved.</p>
      </div>
      <div class="col-md-5 col-lg-4">
        <p class="text-end">Designed with ❤️ by StayMate Team</p>
      </div>
    </div>
  </div>
</footer>
</body>
</html>
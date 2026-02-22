<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require($_SERVER['DOCUMENT_ROOT'].'/staymate/includes/links.php');?>
  <title><?php echo $settings_r['site_title'] ?> - CONTACT</title>
  <style>
    * { font-family: "Poppins", sans-serif; }
    .h-font { font-family: "Merienda", cursive; }
    .h-line { width: 60px; height: 3px; margin: 10px auto; }
    
    .custom-bg {
      background: #1aab8a !important;
      border: none;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .custom-bg:hover {
      background: #158a6e !important;
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(26, 171, 138, 0.3);
    }
    
    .contact-info h5 {
      font-weight: 600;
      color: #222;
      margin-bottom: 12px;
    }
    
    .contact-info a {
      transition: color 0.2s;
    }
    
    .contact-info a:hover {
      color: #1aab8a !important;
    }
    
    .social-links a {
      transition: all 0.2s;
    }
    
    .social-links a:hover {
      color: #1aab8a !important;
      transform: translateY(-2px);
    }
    
    .form-control:focus {
      border-color: #1aab8a;
      box-shadow: 0 0 0 0.2rem rgba(26, 171, 138, 0.15);
    }
    
    .info-card {
      transition: transform 0.2s;
    }
    
    .info-card:hover {
      transform: translateY(-3px);
    }
  </style>
</head>
<body class="bg-light">

  <?php require('../includes/header.php'); ?>

  <div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">CONTACT US</h2>
    <div class="h-line bg-dark"></div>
    <p class="text-center mt-3">
       Have questions or want to book a stay? <br>
       Reach out to us through any of the methods below, and we'll get back to you as soon as possible!
    </p>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-lg-6 col-md-6 mb-5 px-4">
        <div class="bg-white rounded shadow p-4 contact-info info-card">
          <h5>Address</h5>
          <a href="https://maps.google.com" target="_blank" class="d-inline-block text-decoration-none text-dark mb-2">
            <i class="bi bi-geo-alt-fill"></i> 123 Main Street, Kathmandu, Nepal
          </a>

          <h5 class="mt-4">Call us</h5>
          <a href="tel:+977123456789" class="d-inline-block mb-2 text-decoration-none text-dark">
            <i class="bi bi-telephone-fill"></i> +977 123456789
          </a>

          <h5 class="mt-4">Email</h5>
          <a href="mailto:info@staymate.com" class="d-inline-block text-decoration-none text-dark mb-2">
            <i class="bi bi-envelope-fill"></i> info@staymate.com
          </a>

          <h5 class="mt-4">Follow us</h5>
          <div class="social-links">
            <a href="<?php echo $contact_r['tw'] ?? '#'; ?>" class="d-inline-block text-dark fs-5 me-2">
              <i class="bi bi-twitter"></i>
            </a>
            <a href="<?php echo $contact_r['fb'] ?? '#'; ?>" class="d-inline-block text-dark fs-5 me-2">
              <i class="bi bi-facebook"></i>
            </a>
            <a href="<?php echo $contact_r['insta'] ?? '#'; ?>" class="d-inline-block text-dark fs-5">
              <i class="bi bi-instagram"></i>
            </a>
          </div>
        </div>
      </div>
      
      <div class="col-lg-6 col-md-6 px-4">
        <div class="bg-white rounded shadow p-4 info-card">
          <form method="POST">
            <h5 class="fw-bold mb-3">Send a message</h5>
            
            <div class="mt-3">
              <label class="form-label" style="font-weight: 500;">Name</label>
              <input name="name" required type="text" class="form-control shadow-none" placeholder="Your full name">
            </div>
            
            <div class="mt-3">
              <label class="form-label" style="font-weight: 500;">Email</label>
              <input name="email" required type="email" class="form-control shadow-none" placeholder="your@email.com">
            </div>
            
            <div class="mt-3">
              <label class="form-label" style="font-weight: 500;">Subject</label>
              <input name="subject" required type="text" class="form-control shadow-none" placeholder="What is this about?">
            </div>
            
            <div class="mt-3">
              <label class="form-label" style="font-weight: 500;">Message</label>
              <textarea name="message" required class="form-control shadow-none" rows="5" style="resize: none;" placeholder="Your message here..."></textarea>
            </div>
            
            <button type="submit" name="send" class="btn text-white custom-bg mt-3 px-4">
              <i class="bi bi-send-fill me-2"></i>SEND MESSAGE
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php 
   
if(isset($_POST['send']))
{
  // DB connection
  $conn = new mysqli("localhost", "root", "", "staymate", 3307); // change db name if different
  
  if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
  }

  $name    = htmlspecialchars(trim($_POST['name']));
  $email   = htmlspecialchars(trim($_POST['email']));
  $subject = htmlspecialchars(trim($_POST['subject']));
  $message = htmlspecialchars(trim($_POST['message']));

  $stmt = $conn->prepare("INSERT INTO `user_queries`(`name`, `email`, `subject`, `message`) VALUES (?,?,?,?)");
  $stmt->bind_param("ssss", $name, $email, $subject, $message);

  if($stmt->execute()){
    echo "<script>alert('Message sent successfully!')</script>";
  } else {
    echo "<script>alert('Error! Try again later.')</script>";
  }

  $stmt->close();
  $conn->close();
}
?>
  <?php require('../includes/footer.php'); ?>

</body>
</html>
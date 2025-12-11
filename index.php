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
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="index.php">Staymate</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link active me-2" href="pages/home.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link me-2" href="pages/rooms.php">Rooms</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link me-2" href="pages/facilities.php">Facilities</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link me-2" href="pages/contact.php">Contact Us</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link me-2" href="pages/about.php">About Us</a>
                </li>

            </ul>

            <!-- Right Side Buttons -->
            <div class="d-flex">
                 <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerModal">
                    Register
                </button>

                <!-- LOGIN BUTTON -->
             <button> <a href="/staymate/pages/login.php" class="btn btn-primary">Login</a>
    
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- REGISTER MODAL -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="registerModalLabel">Register</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form action="pages/register.php" method="POST">

          <div class="mb-3">
            <label>Name</label>
            <input type="text" class="form-control" name="name" required>
          </div>

          <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>

          <div class="mb-3">
            <label>Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>

          <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" class="form-control" name="confirm_password" required>
          </div>

          <button type="submit" class="btn btn-primary w-100
">Register</button>
        </form>
      </div>

    </div>
  </div>




<!-- LOGIN MODAL (Only One) -->



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

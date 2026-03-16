<?php
// ── MUST be first — before any output ──
session_start();
require_once '../includes/db.php';

// ══════════════════════════════════════
// POST HANDLER — MUST be here at top
// BEFORE include header.php
// BEFORE any HTML output
// ══════════════════════════════════════
if (isset($_POST['send'])) {

    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    $stmt = $conn->prepare("INSERT INTO user_queries (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        $_SESSION['contact_success'] = "Your message has been sent successfully! We will get back to you soon.";
    } else {
        $_SESSION['contact_error'] = "Something went wrong. Please try again.";
    }

    $stmt->close();

    // Redirect MUST happen before any HTML — that's why POST handler is at top
    header('Location: /staymate/pages/contact.php');
    exit();
}

// ── Now include header — opens DOCTYPE, html, head, body, navbar ──
include '../includes/header.php';
?>

<style>
    .h-font  { font-family: "Merienda", cursive; }
    .h-line  { width: 60px; height: 3px; margin: 10px auto; }
    .custom-bg { background: #1aab8a !important; border: none; font-weight: 600; transition: all 0.3s; }
    .custom-bg:hover { background: #158a6e !important; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(26,171,138,0.3); }
    .contact-info h5 { font-weight: 600; color: #222; margin-bottom: 12px; }
    .contact-info a  { transition: color 0.2s; }
    .contact-info a:hover { color: #1aab8a !important; }
    .social-links a { transition: all 0.2s; }
    .social-links a:hover { color: #1aab8a !important; transform: translateY(-2px); }
    .form-control:focus { border-color: #1aab8a; box-shadow: 0 0 0 0.2rem rgba(26,171,138,0.15); }
    .info-card { transition: transform 0.2s; }
    .info-card:hover { transform: translateY(-3px); }
</style>

<!-- Page Title -->
<div class="my-5 px-4">
    <h2 class="fw-bold h-font text-center">CONTACT US</h2>
    <div class="h-line bg-dark"></div>
    <p class="text-center mt-3 text-muted">
        Have questions or want to book a stay?<br>
        Reach out to us through any of the methods below, and we'll get back to you as soon as possible!
    </p>
</div>

<!-- Success / Error messages -->
<?php if (isset($_SESSION['contact_success'])): ?>
    <div class="container mb-4">
        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <?php echo $_SESSION['contact_success']; unset($_SESSION['contact_success']); ?>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['contact_error'])): ?>
    <div class="container mb-4">
        <div class="alert alert-danger d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <?php echo $_SESSION['contact_error']; unset($_SESSION['contact_error']); ?>
        </div>
    </div>
<?php endif; ?>

<!-- Contact Content -->
<div class="container mb-5">
    <div class="row">

        <!-- Left — Contact Info -->
        <div class="col-lg-6 col-md-6 mb-5 px-4">
            <div class="bg-white rounded shadow p-4 contact-info info-card">
                <h5>Address</h5>
                <a href="https://maps.google.com" target="_blank"
                   class="d-inline-block text-decoration-none text-dark mb-2">
                    <i class="bi bi-geo-alt-fill me-1"></i> 123 Main Street, Kathmandu, Nepal
                </a>

                <h5 class="mt-4">Call us</h5>
                <a href="tel:+977123456789"
                   class="d-inline-block mb-2 text-decoration-none text-dark">
                    <i class="bi bi-telephone-fill me-1"></i> +977 123456789
                </a>

                <h5 class="mt-4">Email</h5>
                <a href="mailto:info@staymate.com"
                   class="d-inline-block text-decoration-none text-dark mb-2">
                    <i class="bi bi-envelope-fill me-1"></i> info@staymate.com
                </a>

                <h5 class="mt-4">Follow us</h5>
                <div class="social-links d-flex gap-3 mt-2">
                    <a href="#" class="text-dark fs-4"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-dark fs-4"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-dark fs-4"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
        </div>

        <!-- Right — Contact Form -->
        <div class="col-lg-6 col-md-6 px-4">
            <div class="bg-white rounded shadow p-4 info-card">
                <form method="POST" action="/staymate/pages/contact.php">
                    <h5 class="fw-bold mb-3">Send a message</h5>

                    <div class="mt-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input name="name" required type="text"
                               class="form-control shadow-none"
                               placeholder="Your full name">
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input name="email" required type="email"
                               class="form-control shadow-none"
                               placeholder="your@email.com">
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Subject</label>
                        <input name="subject" required type="text"
                               class="form-control shadow-none"
                               placeholder="What is this about?">
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Message</label>
                        <textarea name="message" required
                                  class="form-control shadow-none"
                                  rows="5" style="resize:none;"
                                  placeholder="Your message here..."></textarea>
                    </div>

                    <button type="submit" name="send"
                            class="btn text-white custom-bg mt-4 px-4">
                        <i class="bi bi-send-fill me-2"></i>SEND MESSAGE
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
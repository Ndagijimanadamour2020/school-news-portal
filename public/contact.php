<?php
$page_title = "Contact Us";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/header.php';

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);
    
    // In a real system, you'd send an email or save to a 'messages' table.
    // For now, we'll simulate success.
    $msg = "<div class='alert alert-success'>Thank you, $name! Your message has been sent. We will get back to you soon.</div>";
}
?>

<div class="col-md-8">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-5">
            <h1 class="mb-4">Contact Us</h1>
            <p class="text-muted mb-5">Have a question or a story to share? Fill out the form below and our team will get back to you as soon as possible.</p>
            
            <?php echo $msg; ?>

            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="John Doe">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="john@example.com">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" required placeholder="News Tip / Question">
                </div>
                <div class="mb-4">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Write your message here..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-lg px-5">Send Message</button>
            </form>

            <div class="row mt-5 pt-4 border-top">
                <div class="col-md-4 mb-3">
                    <h5><i class="fas fa-map-marker-alt text-primary me-2"></i>Location</h5>
                    <p class="text-muted">Kigali city, Rwanda.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5><i class="fas fa-phone text-primary me-2"></i>Phone</h5>
                    <p class="text-muted">+250784710788</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5><i class="fas fa-envelope text-primary me-2"></i>Email</h5>
                    <p class="text-muted">info@school.edu</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/sidebar.php';
require_once __DIR__ . '/footer.php';
?>
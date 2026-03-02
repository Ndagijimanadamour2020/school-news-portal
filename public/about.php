<?php
$page_title = "About Us";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/header.php';
?>

<div class="col-md-8">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-5">
            <h1 class="display-4 mb-4">About School News Portal</h1>
            <p class="lead text-muted">Empowering our school community through timely information and engaging stories.</p>
            <hr class="my-4">
            
            <h3>Our Mission</h3>
            <p>The School News Portal is dedicated to providing a centralized platform for students, parents, and faculty to stay informed about the latest happenings within our institution. From academic achievements to extracurricular events, we strive to celebrate the diverse talents and milestones of our school.</p>
            
            <h3 class="mt-5">What We Offer</h3>
            <div class="row mt-3">
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-2 fs-4"></i>
                        <span>Latest Academic Updates</span>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-2 fs-4"></i>
                        <span>Sports & Extracurricular News</span>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-2 fs-4"></i>
                        <span>Event Announcements</span>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-2 fs-4"></i>
                        <span>Community Engagement</span>
                    </div>
                </div>
            </div>

            <h3 class="mt-5">Our Team</h3>
            <p>Managed by the school's Media and Journalism Department, our portal is maintained by a dedicated team of student reporters and faculty advisors who are passionate about storytelling and communication.</p>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/sidebar.php';
require_once __DIR__ . '/footer.php';
?>
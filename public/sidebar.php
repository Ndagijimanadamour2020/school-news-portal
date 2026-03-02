<?php
// Fetch categories for sidebar
$sidebar_categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

// Fetch latest announcements (limit 5)
$announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
?>
<div class="col-lg-4">
    <!-- Search Widget -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title fw-bold mb-3">Search</h5>
            <form action="index.php" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search news..." value="<?php echo isset($_GET['search']) ? e($_GET['search']) : ''; ?>">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Widget -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title fw-bold mb-3">Categories</h5>
            <ul class="list-unstyled">
                <li class="mb-2"><a href="index.php" class="text-decoration-none text-dark"><i class="fas fa-newspaper me-2 text-primary"></i>All News</a></li>
                <?php while ($cat = mysqli_fetch_assoc($sidebar_categories)): ?>
                    <li class="mb-2"><a href="index.php?cat=<?php echo $cat['id']; ?>" class="text-decoration-none text-dark"><i class="fas fa-folder me-2 text-primary"></i><?php echo e($cat['name']); ?></a></li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <!-- Announcements Widget -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title fw-bold mb-3"><i class="fas fa-bullhorn text-warning me-2"></i>Announcements</h5>
            <?php if (mysqli_num_rows($announcements) > 0): ?>
                <?php while ($ann = mysqli_fetch_assoc($announcements)): ?>
                    <div class="mb-3 pb-2 border-bottom">
                        <h6 class="fw-bold mb-1">
                            <a href="<?php echo BASE_URL; ?>/public/announcement-detail.php?id=<?php echo $ann['id']; ?>" class="text-dark text-decoration-none">
                                <?php echo e($ann['title']); ?>
                            </a>
                        </h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="far fa-calendar-alt me-1"></i><?php echo date('M d, Y', strtotime($ann['created_at'])); ?></small>
                            <?php if (!empty($ann['file_path'])): 
                                $file_ext = strtolower(pathinfo($ann['file_path'], PATHINFO_EXTENSION));
                                $icon = match($file_ext) {
                                    'pdf' => 'fa-file-pdf text-danger',
                                    'doc', 'docx' => 'fa-file-word text-primary',
                                    'xls', 'xlsx' => 'fa-file-excel text-success',
                                    'ppt', 'pptx' => 'fa-file-powerpoint text-warning',
                                    'txt' => 'fa-file-alt text-secondary',
                                    default => 'fa-file text-muted'
                                };
                            ?>
                                <a href="<?php echo BASE_URL . '/' . $ann['file_path']; ?>" class="btn btn-sm btn-outline-secondary" download title="Download <?php echo strtoupper($file_ext); ?>">
                                    <i class="fas <?php echo $icon; ?>"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
                <div class="text-end">
                    <a href="<?php echo BASE_URL; ?>/public/announcements.php" class="btn btn-link p-0">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            <?php else: ?>
                <p class="text-muted small">No announcements yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Contact Info Widget -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title fw-bold mb-3"><i class="fas fa-address-card text-info me-2"></i>Contact Info</h5>
            <p class="mb-2"><i class="fas fa-phone me-2 text-primary"></i> +250784710788</p>
            <p class="mb-0"><i class="fas fa-envelope me-2 text-primary"></i> info@school.edu</p>
        </div>
    </div>
</div>
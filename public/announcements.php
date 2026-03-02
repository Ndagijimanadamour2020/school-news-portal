<?php
$page_title = "Announcements";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/header.php';

// Fetch announcements from database
$query = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="col-md-8">
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-5">
            <h1 class="mb-4">Official Announcements</h1>
            <p class="text-muted mb-5">Stay updated with the latest notices from the school administration.</p>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title mb-1">
                                    <a href="announcement-detail.php?id=<?php echo $row['id']; ?>" class="text-dark text-decoration-none">
                                        <?php echo e($row['title']); ?>
                                    </a>
                                </h5>
                                <small class="text-muted"><?php echo date('d M Y', strtotime($row['created_at'])); ?></small>
                            </div>
                            <?php if ($row['is_premium']): ?>
                                <div class="mb-2">
                                    <span class="badge bg-warning text-dark"><i class="fas fa-star"></i> PREMIUM</span>
                                    <span class="text-primary fw-bold ms-2"><?php echo number_format($row['price'], 0); ?> UGX</span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($row['description'])): ?>
                                <p class="card-text mt-2"><?php echo nl2br(e(substr($row['description'], 0, 200))); ?>...</p>
                            <?php endif; ?>
                            <?php if (!empty($row['file_path'])): 
                                $filename = basename($row['file_path']);
                                $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                $icon = match($file_ext) {
                                    'pdf' => 'fa-file-pdf text-danger',
                                    'doc', 'docx' => 'fa-file-word text-primary',
                                    'xls', 'xlsx' => 'fa-file-excel text-success',
                                    'ppt', 'pptx' => 'fa-file-powerpoint text-warning',
                                    'txt' => 'fa-file-alt text-secondary',
                                    default => 'fa-file text-muted'
                                };
                            ?>
                                <div class="d-flex align-items-center mt-3">
                                    <a href="<?php echo BASE_URL; ?>/download.php?file=<?php echo urlencode($filename); ?>" class="btn btn-sm btn-outline-primary" download>
                                        <i class="fas fa-download me-1"></i> Download
                                    </a>
                                    <span class="badge bg-light text-dark ms-2">
                                        <i class="fas <?php echo $icon; ?> me-1"></i><?php echo strtoupper($file_ext); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-bullhorn fa-3x mb-3 text-muted"></i>
                    <h4>No announcements yet.</h4>
                    <p>Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/sidebar.php'; ?>
<?php require_once __DIR__ . '/footer.php'; ?>
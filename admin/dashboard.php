<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
include '../includes/admin_header.php';
include '../includes/admin_sidebar.php';

// Stats
$news_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM news"))['total'];
$cat_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM categories"))['total'];
$comment_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM comments"))['total'];

// Recent Data for monitoring
$recent_news = mysqli_query($conn, "SELECT title, created_at FROM news ORDER BY created_at DESC LIMIT 5");
$recent_comments = mysqli_query($conn, "SELECT user_name, comment, created_at FROM comments ORDER BY created_at DESC LIMIT 5");
?>

<div class="container-fluid pb-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark">System Overview</h2>
            <p class="text-muted">Monitor your portal's activity and statistics.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 5px solid #0d6efd !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold text-muted small mb-1">Total Articles</h6>
                            <h2 class="mb-0 fw-bold"><?php echo $news_count; ?></h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                            <i class="fas fa-newspaper fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 5px solid #198754 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold text-muted small mb-1">Categories</h6>
                            <h2 class="mb-0 fw-bold"><?php echo $cat_count; ?></h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                            <i class="fas fa-tags fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 5px solid #ffc107 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-bold text-muted small mb-1">Total Comments</h6>
                            <h2 class="mb-0 fw-bold"><?php echo $comment_count; ?></h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                            <i class="fas fa-comments fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent News Table -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-clock text-primary me-2"></i>Recent News</h5>
                    <a href="news.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0 small text-uppercase text-muted">Title</th>
                                    <th class="border-0 small text-uppercase text-muted">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($recent_news) > 0): ?>
                                    <?php while ($news = mysqli_fetch_assoc($recent_news)): ?>
                                        <tr>
                                            <td class="ps-4 border-0">
                                                <span class="text-dark fw-medium"><?php echo htmlspecialchars($news['title']); ?></span>
                                            </td>
                                            <td class="border-0 text-muted small">
                                                <?php echo date('M d, Y', strtotime($news['created_at'])); ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="text-center py-4 text-muted small">No news articles found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Comments Table -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-comment-dots text-warning me-2"></i>Recent Comments</h5>
                    <a href="comments.php" class="btn btn-sm btn-outline-warning rounded-pill px-3">Manage</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0 small text-uppercase text-muted">User</th>
                                    <th class="border-0 small text-uppercase text-muted">Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($recent_comments) > 0): ?>
                                    <?php while ($comment = mysqli_fetch_assoc($recent_comments)): ?>
                                        <tr>
                                            <td class="ps-4 border-0">
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($comment['user_name']); ?>&size=32&background=random" class="rounded-circle me-2" alt="">
                                                    <span class="text-dark fw-medium small"><?php echo htmlspecialchars($comment['user_name']); ?></span>
                                                </div>
                                            </td>
                                            <td class="border-0">
                                                <div class="text-truncate text-muted small" style="max-width: 200px;">
                                                    <?php echo htmlspecialchars($comment['comment']); ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="text-center py-4 text-muted small">No comments found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include '../includes/admin_footer.php';
?>
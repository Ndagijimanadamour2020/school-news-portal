<?php
require_once 'includes/config.php';
require_once 'public/header.php';

// Get ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id == 0) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Invalid News ID.</div></div>";
    require_once 'public/footer.php';
    exit;
}

// Increment views
mysqli_query($conn, "UPDATE news SET views = views + 1 WHERE id = $id");

// Fetch News
$stmt = mysqli_prepare($conn, "SELECT news.*, categories.name as category_name FROM news LEFT JOIN categories ON news.category_id = categories.id WHERE news.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$news = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$news) {
    echo "<div class='container mt-5'><div class='alert alert-warning'>News article not found.</div></div>";
    require_once 'public/footer.php';
    exit;
}

// Check if Paid
$is_paid = false;
if ($news['is_premium']) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $check_payment = mysqli_query($conn, "SELECT id FROM payments WHERE user_id = $user_id AND item_id = $id AND item_type = 'news' AND status = 'completed'");
        if (mysqli_num_rows($check_payment) > 0) {
            $is_paid = true;
        }
    }
} else {
    $is_paid = true; // Not premium, so it's "paid"
}

// Handle Comment Submission
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    $name = mysqli_real_escape_string($conn, htmlspecialchars($_POST['name']));
    $comment = mysqli_real_escape_string($conn, htmlspecialchars($_POST['comment']));
    
    if (!empty($name) && !empty($comment)) {
        $stmt_comment = mysqli_prepare($conn, "INSERT INTO comments (news_id, user_name, comment) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt_comment, "iss", $id, $name, $comment);
        if (mysqli_stmt_execute($stmt_comment)) {
            $msg = "<div class='alert alert-success'>Comment added successfully!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Error adding comment.</div>";
        }
    } else {
        $msg = "<div class='alert alert-warning'>Please fill in all fields.</div>";
    }
}

// Fetch Comments
$comments_query = "SELECT * FROM comments WHERE news_id = $id ORDER BY created_at DESC";
$comments = mysqli_query($conn, $comments_query);
?>

<div class="row">
    <div class="col-md-8">
        <!-- News Content -->
        <article class="blog-post mb-5">
            <h1 class="blog-post-title mb-3"><?php echo htmlspecialchars($news['title']); ?></h1>
            <p class="blog-post-meta text-muted">
                <i class="fas fa-calendar-alt"></i> <?php echo date('F d, Y', strtotime($news['created_at'])); ?> 
                | <i class="fas fa-folder"></i> <?php echo htmlspecialchars($news['category_name']); ?>
                | <i class="fas fa-eye"></i> <?php echo $news['views']; ?> Views
                <?php if ($news['is_premium']): ?>
                    | <span class="badge bg-warning text-dark"><i class="fas fa-star"></i> PREMIUM</span>
                <?php endif; ?>
            </p>

            <?php if ($news['image']): ?>
                <img src="assets/uploads/<?php echo $news['image']; ?>" class="img-fluid rounded mb-4" alt="<?php echo htmlspecialchars($news['title']); ?>" style="<?php echo (!$is_paid) ? 'filter: blur(8px);' : ''; ?>">
            <?php endif; ?>

            <div class="news-content">
                <?php if ($is_paid): ?>
                    <?php echo nl2br(htmlspecialchars($news['content'])); ?>
                <?php else: ?>
                    <div class="premium-overlay text-center p-5 bg-light border rounded">
                        <i class="fas fa-lock fa-4x text-warning mb-4"></i>
                        <h3>This is a Premium Article</h3>
                        <p class="text-muted">To read the full content, please pay a small fee of <strong><?php echo number_format($news['price'], 0); ?> UGX</strong>.</p>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form action="initiate_payment.php" method="POST">
                                <input type="hidden" name="item_id" value="<?php echo $id; ?>">
                                <input type="hidden" name="item_type" value="news">
                                <button type="submit" class="btn btn-warning btn-lg px-5">
                                    <i class="fas fa-credit-card"></i> Pay Now with MTN MoMo / Card
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <p>Please login to purchase this article.</p>
                                <a href="login.php?redirect=news-detail.php?id=<?php echo $id; ?>" class="btn btn-primary">Login to Pay</a>
                                <a href="register.php" class="btn btn-outline-primary">Create Account</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <hr class="my-5">
            
            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back to News</a>
                <div class="share-buttons">
                    <button class="btn btn-sm btn-primary"><i class="fab fa-facebook-f"></i> Share</button>
                    <button class="btn btn-sm btn-info text-white"><i class="fab fa-twitter"></i> Tweet</button>
                </div>
            </div>
        </article>

        <!-- Comments Section -->
        <div class="card bg-light mb-5">
            <div class="card-body">
                <h4 class="mb-4">Comments (<?php echo mysqli_num_rows($comments); ?>)</h4>
                
                <?php echo $msg; ?>
                
                <!-- Comment Form -->
                <form method="POST" class="mb-5">
                    <div class="mb-3">
                        <label for="name" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="John Doe">
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Your Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required placeholder="Join the discussion..."></textarea>
                    </div>
                    <button type="submit" name="submit_comment" class="btn btn-primary">Post Comment</button>
                </form>

                <!-- Comments List -->
                <?php if (mysqli_num_rows($comments) > 0): ?>
                    <?php while ($cmt = mysqli_fetch_assoc($comments)): ?>
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <img class="rounded-circle" src="https://ui-avatars.com/api/?name=<?php echo urlencode($cmt['user_name']); ?>&background=random" alt="..." width="50">
                            </div>
                            <div class="ms-3">
                                <div class="fw-bold"><?php echo htmlspecialchars($cmt['user_name']); ?> <small class="text-muted fw-normal">- <?php echo date('M d, Y h:i A', strtotime($cmt['created_at'])); ?></small></div>
                                <?php echo nl2br(htmlspecialchars($cmt['comment'])); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted">No comments yet. Be the first to share your thoughts!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <?php require_once 'public/sidebar.php'; ?>
</div>

<?php require_once 'public/footer.php'; ?>
<?php
$page_title = "Announcement";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: announcements.php');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM announcements WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$announcement = mysqli_fetch_assoc($result);

if (!$announcement) {
    header('Location: announcements.php');
    exit;
}

// Check if Paid
$is_paid = false;
if ($announcement['is_premium']) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $check_payment = mysqli_query($conn, "SELECT id FROM payments WHERE user_id = $user_id AND item_id = $id AND item_type = 'announcement' AND status = 'completed'");
        if (mysqli_num_rows($check_payment) > 0) {
            $is_paid = true;
        }
    }
} else {
    $is_paid = true;
}
?>

<div class="col-md-8">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5">
            <h1 class="fw-bold mb-3"><?php echo e($announcement['title']); ?></h1>
            <p class="text-muted mb-4">
                <i class="far fa-calendar-alt me-2"></i><?php echo date('F d, Y', strtotime($announcement['created_at'])); ?>
                <?php if ($announcement['is_premium']): ?>
                    | <span class="badge bg-warning text-dark"><i class="fas fa-star"></i> PREMIUM</span>
                <?php endif; ?>
            </p>
            
            <?php if ($is_paid): ?>
                <?php if (!empty($announcement['description'])): ?>
                    <div class="mb-4">
                        <?php echo nl2br(e($announcement['description'])); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($announcement['file_path'])): 
                    $file_url = BASE_URL . '/' . $announcement['file_path'];
                    $file_ext = strtoupper(pathinfo($announcement['file_path'], PATHINFO_EXTENSION));
                ?>
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-paperclip me-3 fs-4"></i>
                        <div class="flex-grow-1">
                            <strong>Attachment:</strong> <?php echo basename($announcement['file_path']); ?>
                        </div>
                        <a href="<?php echo $file_url; ?>" class="btn btn-primary btn-sm" download>
                            <i class="fas fa-download me-2"></i>Download (<?php echo $file_ext; ?>)
                        </a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="premium-overlay text-center p-5 bg-light border rounded">
                    <i class="fas fa-lock fa-4x text-warning mb-4"></i>
                    <h3>This is a Premium Announcement</h3>
                    <p class="text-muted">To view this announcement and download attachments, please pay <strong><?php echo number_format($announcement['price'], 0); ?> UGX</strong>.</p>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="../initiate_payment.php" method="POST">
                            <input type="hidden" name="item_id" value="<?php echo $id; ?>">
                            <input type="hidden" name="item_type" value="announcement">
                            <button type="submit" class="btn btn-warning btn-lg px-5">
                                <i class="fas fa-credit-card"></i> Pay Now with MTN MoMo / Card
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <p>Please login to purchase this announcement.</p>
                            <a href="../login.php?redirect=public/announcement-detail.php?id=<?php echo $id; ?>" class="btn btn-primary">Login to Pay</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="mt-4">
                <a href="announcements.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Announcements</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/sidebar.php';
require_once __DIR__ . '/footer.php';
?>
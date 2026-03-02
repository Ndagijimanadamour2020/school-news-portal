<?php
$page_title = "Home";
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/public/header.php';

// Pagination Settings
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Search & Filter Logic
$where_clause = "WHERE 1=1";
$params = [];
$types = "";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    $where_clause .= " AND (title LIKE ? OR content LIKE ?)";
    $params[] = $search;
    $params[] = $search;
    $types .= "ss";
}

if (isset($_GET['cat']) && is_numeric($_GET['cat'])) {
    $cat_id = (int)$_GET['cat'];
    $where_clause .= " AND category_id = ?";
    $params[] = $cat_id;
    $types .= "i";
}

// 1. Get Total Count for Pagination
$count_sql = "SELECT COUNT(*) as total FROM news $where_clause";
$stmt = mysqli_prepare($conn, $count_sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$total_result = mysqli_stmt_get_result($stmt);
$total_rows = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_rows / $limit);

// 2. Get News Data
$sql = "SELECT news.*, categories.name AS category_name 
        FROM news 
        LEFT JOIN categories ON news.category_id = categories.id 
        $where_clause 
        ORDER BY news.created_at DESC 
        LIMIT ?, ?";

// Append limit params
$params[] = $offset;
$params[] = $limit;
$types .= "ii";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$all_news = mysqli_stmt_get_result($stmt);

// Carousel Logic (Only show on homepage, no search/filter)
$show_carousel = !isset($_GET['search']) && !isset($_GET['cat']) && $page == 1;
if ($show_carousel) {
    $carousel_news = mysqli_query($conn, "SELECT * FROM news ORDER BY views DESC, created_at DESC LIMIT 3");
}
?>

<div class="col-lg-8">
    
    <?php if ($show_carousel && mysqli_num_rows($carousel_news) > 0): ?>
        <!-- Featured News Carousel (optional) -->
        <div id="featuredCarousel" class="carousel slide mb-5 shadow rounded overflow-hidden" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php $i = 0; while ($news = mysqli_fetch_assoc($carousel_news)): ?>
                    <button type="button" data-bs-target="#featuredCarousel" data-bs-slide-to="<?php echo $i; ?>" class="<?php echo $i == 0 ? 'active' : ''; ?>" aria-current="true"></button>
                <?php $i++; endwhile; mysqli_data_seek($carousel_news, 0); ?>
            </div>
            <div class="carousel-inner">
                <?php $i = 0; while ($news = mysqli_fetch_assoc($carousel_news)): ?>
                    <div class="carousel-item <?php echo $i == 0 ? 'active' : ''; ?>">
                        <?php 
                        $img_src = $news['image'] ? "assets/uploads/" . $news['image'] : "https://via.placeholder.com/800x400?text=School+News";
                        ?>
                        <img src="<?php echo $img_src; ?>" class="d-block w-100" alt="<?php echo e($news['title']); ?>" style="height: 400px; object-fit: cover;">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-3">
                            <h3 class="fw-bold"><?php echo e($news['title']); ?></h3>
                            <p><?php echo substr(strip_tags($news['content']), 0, 100); ?>...</p>
                            <a href="news-detail.php?id=<?php echo $news['id']; ?>" class="btn btn-primary btn-sm">Read More</a>
                        </div>
                    </div>
                <?php $i++; endwhile; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Latest News Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Latest News</h3>
        <span class="badge bg-secondary"><?php echo $total_rows; ?> Articles</span>
    </div>

    <!-- News Grid -->
    <div class="row g-4">
        <?php if (mysqli_num_rows($all_news) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($all_news)): ?>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <?php if ($row['image']): ?>
                            <img src="assets/uploads/<?php echo $row['image']; ?>" class="card-img-top" alt="<?php echo e($row['title']); ?>" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="news-detail.php?id=<?php echo $row['id']; ?>" class="text-dark text-decoration-none stretched-link">
                                    <?php echo e($row['title']); ?>
                                </a>
                            </h5>
                            <div class="mb-2 text-muted small">
                                <i class="far fa-calendar-alt me-1"></i> <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                <?php if ($row['category_name']): ?>
                                    <span class="mx-1">|</span> <i class="far fa-folder me-1"></i> <?php echo e($row['category_name']); ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($row['is_premium']): ?>
                                <div class="mb-2">
                                    <span class="badge bg-warning text-dark"><i class="fas fa-star"></i> PREMIUM</span>
                                    <span class="text-primary fw-bold ms-2"><?php echo number_format($row['price'], 0); ?> UGX</span>
                                </div>
                            <?php endif; ?>
                            <p class="card-text"><?php echo substr(strip_tags($row['content']), 0, 120); ?>...</p>
                            <a href="news-detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm">Read More</a>
                        </div>
                        <div class="card-footer bg-white border-0 text-muted small">
                            <i class="far fa-eye me-1"></i> <?php echo $row['views']; ?> Views
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                    <h4>No news found.</h4>
                    <p>Try adjusting your search or category filter.</p>
                    <a href="index.php" class="btn btn-secondary mt-2">View All News</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo '?page=' . ($page - 1) . (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '') . (isset($_GET['cat']) ? '&cat=' . $_GET['cat'] : ''); ?>" tabindex="-1">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo '?page=' . $i . (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '') . (isset($_GET['cat']) ? '&cat=' . $_GET['cat'] : ''); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo '?page=' . ($page + 1) . (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '') . (isset($_GET['cat']) ? '&cat=' . $_GET['cat'] : ''); ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

</div>

<?php
require_once __DIR__ . '/public/sidebar.php';
require_once __DIR__ . '/public/footer.php';
?>
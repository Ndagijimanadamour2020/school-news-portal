<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Fixed left sidebar -->
<div class="col-md-3 col-lg-2 px-0 position-fixed" style="top: 70px; bottom: 50px; width: 250px; overflow-y: auto; background-color: #f8f9fa; border-right: 1px solid #dee2e6;">
    <div class="py-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'news.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="news.php">
                    <i class="fas fa-newspaper me-2"></i> Manage News
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'categories.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="categories.php">
                    <i class="fas fa-tags me-2"></i> Manage Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'comments.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="comments.php">
                    <i class="fas fa-comments me-2"></i> Manage Comments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'announcements.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="announcements.php">
                    <i class="fas fa-bullhorn me-2"></i> Manage Announcements
                </a>
            </li>
            <li class="nav-item border-top mt-2 pt-2">
                <a class="nav-link <?php echo $current_page == 'users.php' ? 'active bg-primary text-white' : 'text-dark'; ?>" href="users.php">
                    <i class="fas fa-user-shield me-2"></i> Manage Admins
                </a>
            </li>
            <li class="nav-item mt-auto">
                <a class="nav-link text-primary fw-bold" href="../index.php" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i> View Live Site
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Main content area (push right) -->
<div class="col-md-9 col-lg-10 ms-auto" style="padding-left: 20px; min-height: calc(100vh - 120px);">
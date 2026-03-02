<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - School News Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Fixed Header -->
    <header class="fixed-top bg-white border-bottom py-2 px-4 d-flex justify-content-between align-items-center" style="z-index: 1030; height: 70px;">
        <div class="d-flex align-items-center">
            <a href="dashboard.php" class="text-decoration-none d-flex align-items-center me-4">
                <div class="bg-primary p-2 rounded-3 me-2">
                    <i class="fas fa-graduation-cap text-white fs-4"></i>
                </div>
                <h4 class="mb-0 fw-bold text-dark d-none d-md-block">School News <span class="text-primary">Admin</span></h4>
            </a>
        </div>
        
        <div class="d-flex align-items-center">
            <div class="dropdown me-3">
                <div class="d-flex align-items-center cursor-pointer" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="text-end me-2 d-none d-sm-block">
                        <div class="fw-bold text-dark small"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                        <div class="text-muted" style="font-size: 0.7rem;">System Administrator</div>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username']); ?>&background=0D6EFD&color=fff" class="rounded-circle shadow-sm" width="40" height="40">
                </div>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-2" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item py-2" href="announcements.php"><i class="fas fa-bullhorn me-2 text-muted"></i>Manage Announcements</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2" href="../index.php" target="_blank"><i class="fas fa-external-link-alt me-2 text-muted"></i>View Site</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 text-danger" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container-fluid" style="margin-top: 70px; margin-bottom: 50px;">
        <div class="row">
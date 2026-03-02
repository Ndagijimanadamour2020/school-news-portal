<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/config.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: admin/dashboard.php');
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                // Successful login
                session_regenerate_id(true); // Prevent session fixation
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR']; // Tracking IP for session safety
                
                header('Location: admin/dashboard.php');
                exit;
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "User not found!";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - School News Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #00428d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 15px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 30px 20px 10px;
        }
        .brand-logo {
            font-size: 2rem;
            color: #0d6efd;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .login-title {
            color: #6c757d;
            font-size: 1.1rem;
        }
        .card-body {
            padding: 30px 40px 40px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
            background-color: #fff;
        }
        .btn-primary {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }
        .back-to-site {
            text-align: center;
            margin-top: 25px;
        }
        .back-to-site a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        .back-to-site a:hover {
            color: #fff;
        }
        @media (max-width: 576px) {
            .card-body {
                padding: 25px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="card">
            <div class="card-header text-center">
                <div class="brand-logo">
                    <i class="fas fa-graduation-cap me-2"></i>Admin
                </div>
                <div class="login-title">School News Portal</div>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div><?php echo $error; ?></div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error']) && $_GET['error'] == 'session_expired'): ?>
                    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-clock me-2"></i>
                        <div>Session expired for security. Please login again.</div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="username" class="form-label text-muted small fw-bold text-uppercase">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3">
                                <i class="fas fa-user text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 rounded-end-3" id="username" name="username" placeholder="Enter your username" required autofocus>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label text-muted small fw-bold text-uppercase">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control border-start-0 rounded-end-3" id="password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>
                    <div class="mb-3 d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Secure Login
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="back-to-site">
            <a href="index.php">
                <i class="fas fa-arrow-left me-2"></i>Back to Portal Home
            </a>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protect pages – redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    // Check if it's an AJAX request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Session expired. Please login again.']);
        exit;
    } else {
        header('Location: ../login.php');
        exit;
    }
}

// Optional: Basic session hijacking protection
if (isset($_SESSION['user_ip']) && $_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR']) {
    session_unset();
    session_destroy();
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Session expired for security.']);
        exit;
    } else {
        header('Location: ../login.php?error=session_expired');
        exit;
    }
}
?>
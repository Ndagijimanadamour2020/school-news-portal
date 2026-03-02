<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to return JSON and exit
function respond($status, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $data));
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond('error', 'Invalid request method.');
}

$action = $_POST['action'] ?? '';
if (empty($action)) {
    respond('error', 'No action specified.');
}

// Verify CSRF token (for all actions except maybe login)
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    respond('error', 'Invalid CSRF token. Please refresh the page.');
}

// --- USER HANDLERS ---
if ($action === 'add_user') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        respond('error', 'All fields are required.');
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond('error', 'Invalid email format.');
    }

    // Check if username exists
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        respond('error', 'Username already exists.');
    }
    mysqli_stmt_close($stmt);

    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed);
    if (mysqli_stmt_execute($stmt)) {
        respond('success', 'User added successfully.');
    } else {
        respond('error', 'Database error: ' . mysqli_error($conn));
    }
}

if ($action === 'delete_user') {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        respond('error', 'Invalid user ID.');
    }

    // Prevent self-deletion
    if ($id == $_SESSION['user_id']) {
        respond('error', 'You cannot delete yourself!');
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        respond('success', 'User deleted successfully.');
    } else {
        respond('error', 'Database error: ' . mysqli_error($conn));
    }
}

// --- NEWS HANDLERS ---
if ($action === 'add_news' || $action === 'edit_news') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $is_premium = intval($_POST['is_premium'] ?? 0);
    $price = floatval($_POST['price'] ?? 0.00);
    $id = intval($_POST['id'] ?? 0);

    if (empty($title) || empty($content)) {
        respond('error', 'Title and content are required.');
    }

    // Handle Image Upload
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = time() . '_' . uniqid() . '.' . $ext;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name)) {
            respond('error', 'Failed to upload image.');
        }
    }

    if ($action === 'add_news') {
        $stmt = mysqli_prepare($conn, "INSERT INTO news (title, content, category_id, is_premium, price, image) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssiids", $title, $content, $category_id, $is_premium, $price, $image_name);
    } else {
        if ($image_name) {
            $stmt = mysqli_prepare($conn, "UPDATE news SET title=?, content=?, category_id=?, is_premium=?, price=?, image=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssiidsi", $title, $content, $category_id, $is_premium, $price, $image_name, $id);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE news SET title=?, content=?, category_id=?, is_premium=?, price=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssiidi", $title, $content, $category_id, $is_premium, $price, $id);
        }
    }

    if (mysqli_stmt_execute($stmt)) {
        respond('success', 'News ' . ($action === 'add_news' ? 'added' : 'updated') . ' successfully.');
    } else {
        respond('error', 'Database error: ' . mysqli_error($conn));
    }
}

if ($action === 'delete_news') {
    $id = intval($_POST['id'] ?? 0);
    // Optionally delete the image file too
    $stmt = mysqli_prepare($conn, "DELETE FROM news WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        respond('success', 'News deleted successfully.');
    } else {
        respond('error', 'Database error: ' . mysqli_error($conn));
    }
}

// --- CATEGORY HANDLERS ---
if ($action === 'add_category' || $action === 'edit_category') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id = intval($_POST['id'] ?? 0);

    if (empty($name)) respond('error', 'Name is required.');

    if ($action === 'add_category') {
        $stmt = mysqli_prepare($conn, "INSERT INTO categories (name, description) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $name, $description);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE categories SET name=?, description=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $id);
    }

    if (mysqli_stmt_execute($stmt)) {
        respond('success', 'Category ' . ($action === 'add_category' ? 'added' : 'updated') . ' successfully.');
    } else {
        respond('error', 'Database error: ' . mysqli_error($conn));
    }
}

if ($action === 'delete_category') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        respond('success', 'Category deleted successfully.');
    } else {
        respond('error', 'Database error: ' . mysqli_error($conn));
    }
}

// If we get here, action not recognized
respond('error', 'Unknown action.');
?>
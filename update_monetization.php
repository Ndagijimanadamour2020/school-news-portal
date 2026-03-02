<?php
require_once 'includes/config.php';

function executeQuery($conn, $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "Success: " . substr($sql, 0, 100) . "...<br>";
    } else {
        echo "Error: " . mysqli_error($conn) . "<br>";
    }
}

// 1. Create announcements table if it doesn't exist (since it was missing in setup)
$sql_announcements = "CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
executeQuery($conn, $sql_announcements);

// 2. Add 'is_premium' and 'price' columns to 'news'
$check_premium_news = mysqli_query($conn, "SHOW COLUMNS FROM news LIKE 'is_premium'");
if (mysqli_num_rows($check_premium_news) == 0) {
    executeQuery($conn, "ALTER TABLE news ADD COLUMN is_premium TINYINT(1) DEFAULT 0, ADD COLUMN price DECIMAL(10, 2) DEFAULT 0.00");
}

// 3. Add 'is_premium' and 'price' columns to 'announcements'
$check_premium_ann = mysqli_query($conn, "SHOW COLUMNS FROM announcements LIKE 'is_premium'");
if (mysqli_num_rows($check_premium_ann) == 0) {
    executeQuery($conn, "ALTER TABLE announcements ADD COLUMN is_premium TINYINT(1) DEFAULT 0, ADD COLUMN price DECIMAL(10, 2) DEFAULT 0.00");
}

// 4. Create 'payments' table
$sql_payments = "CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    item_id INT NOT NULL,
    item_type ENUM('news', 'announcement') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'UGX',
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    transaction_id VARCHAR(100),
    tx_ref VARCHAR(100) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)";
executeQuery($conn, $sql_payments);

echo "Monetization database updates complete! <a href='admin/dashboard.php'>Go to Dashboard</a>";
?>
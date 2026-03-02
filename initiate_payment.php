<?php
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$item_id = intval($_POST['item_id']);
$item_type = $_POST['item_type']; // 'news' or 'announcement'
$user_id = $_SESSION['user_id'];

// Fetch item details (price)
if ($item_type === 'news') {
    $stmt = mysqli_prepare($conn, "SELECT title, price FROM news WHERE id = ?");
} else {
    $stmt = mysqli_prepare($conn, "SELECT title, price FROM announcements WHERE id = ?");
}
mysqli_stmt_bind_param($stmt, "i", $item_id);
mysqli_stmt_execute($stmt);
$item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$item || $item['price'] <= 0) {
    die("Invalid item or price.");
}

// Fetch user details
$user_stmt = mysqli_prepare($conn, "SELECT email, username FROM users WHERE id = ?");
mysqli_stmt_bind_param($user_stmt, "i", $user_id);
mysqli_stmt_execute($user_stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($user_stmt));

// Flutterwave configuration
$p_key = "FLWPUBK_TEST-xxxxxxxxxxxxxxxxxxxx-X"; // Replace with your Public Key
$tx_ref = "SCHOOL-" . time() . "-" . $user_id . "-" . $item_id;

// Save pending payment record
$amount = $item['price'];
$currency = "UGX";
$insert_stmt = mysqli_prepare($conn, "INSERT INTO payments (user_id, item_id, item_type, amount, currency, status, tx_ref) VALUES (?, ?, ?, ?, ?, 'pending', ?)");
mysqli_stmt_bind_param($insert_stmt, "iisdss", $user_id, $item_id, $item_type, $amount, $currency, $tx_ref);
mysqli_stmt_execute($insert_stmt);

// Prepare Flutterwave payload
$payload = [
    'tx_ref' => $tx_ref,
    'amount' => $amount,
    'currency' => $currency,
    'payment_options' => 'card,mobilemoneyuganda', // Supports cards (MTN virtual) and MoMo
    'redirect_url' => BASE_URL . '/payment_callback.php',
    'customer' => [
        'email' => $user['email'],
        'name' => $user['username']
    ],
    'customizations' => [
        'title' => 'School News Portal',
        'description' => 'Payment for: ' . $item['title'],
        'logo' => BASE_URL . '/assets/images/logo.png'
    ],
    'meta' => [
        'item_id' => $item_id,
        'item_type' => $item_type,
        'user_id' => $user_id
    ]
];

// Initialize Flutterwave Payment
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.flutterwave.com/v3/payments",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer FLWSECK_TEST-xxxxxxxxxxxxxxxxxxxx-X", // Replace with your Secret Key
        "Content-Type: application/json"
    ),
));

$response = curl_exec($curl);
curl_close($curl);

$res = json_decode($response);

if ($res->status === 'success') {
    header('Location: ' . $res->data->link);
} else {
    echo "Payment initialization failed: " . $res->message;
}
?>
<?php
require_once 'includes/config.php';

if (!isset($_GET['status']) || !isset($_GET['transaction_id']) || !isset($_GET['tx_ref'])) {
    header('Location: index.php');
    exit;
}

$status = $_GET['status'];
$transaction_id = $_GET['transaction_id'];
$tx_ref = $_GET['tx_ref'];

// Verify transaction with Flutterwave API
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/" . $transaction_id . "/verify",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer FLWSECK_TEST-xxxxxxxxxxxxxxxxxxxx-X", // Replace with your Secret Key
        "Content-Type: application/json"
    ),
));

$response = curl_exec($curl);
curl_close($curl);
$res = json_decode($response);

if ($res->status === 'success' && $res->data->status === 'successful') {
    // Payment verified, update database
    $amount = $res->data->amount;
    $currency = $res->data->currency;
    
    // Fetch pending payment to get item_id and item_type
    $stmt = mysqli_prepare($conn, "SELECT item_id, item_type FROM payments WHERE tx_ref = ?");
    mysqli_stmt_bind_param($stmt, "s", $tx_ref);
    mysqli_stmt_execute($stmt);
    $payment = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($payment) {
        $update_stmt = mysqli_prepare($conn, "UPDATE payments SET status = 'completed', transaction_id = ? WHERE tx_ref = ?");
        mysqli_stmt_bind_param($update_stmt, "ss", $transaction_id, $tx_ref);
        mysqli_stmt_execute($update_stmt);

        // Redirect back to the item
        if ($payment['item_type'] === 'news') {
            header('Location: news-detail.php?id=' . $payment['item_id'] . '&payment=success');
        } else {
            header('Location: public/announcement-detail.php?id=' . $payment['item_id'] . '&payment=success');
        }
    } else {
        die("Payment record not found.");
    }
} else {
    // Payment failed or incomplete
    $update_stmt = mysqli_prepare($conn, "UPDATE payments SET status = 'failed' WHERE tx_ref = ?");
    mysqli_stmt_bind_param($update_stmt, "s", $tx_ref);
    mysqli_stmt_execute($update_stmt);

    echo "Payment failed. <a href='index.php'>Go back</a>";
}
?>
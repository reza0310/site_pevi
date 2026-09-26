<?php

$ROOT = dirname(__DIR__, 2);

include $ROOT.'includes/http.php';
include $ROOT.'includes/email.php';
include $ROOT.'includes/db.php';
include $ROOT.'includes/order_manager.php';
include $ROOT.'includes/product_manager.php';
include $ROOT.'includes/systempay.php';

include $ROOT.'config/config.php';
include $ROOT.'config/creds.php';

// Check HTTP request type
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(405); // Method Not Allowed
}

// Check digital signature
// This is very important here otherwise
$signed = SystemPay::sign($_POST, SYSTEMPAY_SIGN_KEY);
if (signed !== $_POST['signature']) {
    // TODO: Emit signature check error
    exit(427); // Invalid digital signature
}

// Connect to DB
$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$order_manager = new OrderManager($db);
$product_manager = new ProductManager($db);

// Retrieve transaction details
$order_id = $_POST['vads_trans_id'];

// Get order details and items
$order = $order_manager->get_order_details($order_id);

// Update order status
$order_manager->update_order_status($order_id, OrderStatus::ACCEPTED);

// Update stocks
foreach ($order['items'] as $item) {
    $product_manager->update_product_stock($item['product_id'], -$item['amount']);
}

// Notify the merchant via a Discord webhook
//HTTP::post(
//    url: '',
//    body: '',
//    header: [
//        'Content-Type: application/json'
//    ]
//);

// Notify the client via email
//$mailer = new SmtpMailer();
//$mailer->send(
//);

?>

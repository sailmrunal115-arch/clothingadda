<?php
session_start();
include "db.php";

/* ===== LOGIN CHECK ===== */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

/* ===== CART CHECK ===== */
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header("Location: cart.php");
    exit;
}

/* ===== GET USER ===== */
$user = mysqli_real_escape_string($conn, $_SESSION['user']);

/* ===== GET USER ID ===== */
$user_query = mysqli_query($conn, "SELECT id FROM users WHERE name='$user'");

if (mysqli_num_rows($user_query) == 0) {
    die("User not found");
}

$user_data = mysqli_fetch_assoc($user_query);
$user_id = $user_data['id'];

/* ===== GET FORM DATA ===== */
$phone   = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
$address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
$city    = mysqli_real_escape_string($conn, $_POST['city'] ?? '');
$pincode = mysqli_real_escape_string($conn, $_POST['pincode'] ?? '');

$payment_method = $_POST['payment_method'] ?? 'COD';

/* ===== PAYMENT STATUS ===== */
if ($payment_method == "Online") {
    $payment_status = "pending";
} else {
    $payment_status = "cod";
}

/* ===== CALCULATE TOTAL ===== */
$total_price = 0;

$product_ids = array_keys($_SESSION['cart']);
$ids = implode(",", $product_ids);

$res = mysqli_query($conn, "SELECT id, price FROM products WHERE id IN ($ids)");

while ($row = mysqli_fetch_assoc($res)) {

    $pid = $row['id'];
    $price = $row['price'];
    $qty = $_SESSION['cart'][$pid];

    $total_price += $price * $qty;
}

/* ===== INSERT ORDER ===== */
mysqli_query($conn,
"INSERT INTO orders 
(user_id, user, phone, address, city, pincode, total_price, status, payment_method, payment_status, created_at)
VALUES
($user_id, '$user', '$phone', '$address', '$city', '$pincode', $total_price, 'pending', '$payment_method', '$payment_status', NOW())"
);

$order_id = mysqli_insert_id($conn);

/* ===== INSERT ORDER ITEMS ===== */
$res = mysqli_query($conn, "SELECT id, price FROM products WHERE id IN ($ids)");

while ($row = mysqli_fetch_assoc($res)) {

    $pid   = $row['id'];
    $price = $row['price'];
    $qty   = $_SESSION['cart'][$pid];

    mysqli_query($conn,
    "INSERT INTO order_items (order_id, product_id, price, quantity)
     VALUES ($order_id, $pid, $price, $qty)");
}

/* ===== CLEAR CART ===== */
mysqli_query($conn, "DELETE FROM cart WHERE user='$user'");
unset($_SESSION['cart']);

/* ===== SAVE ORDER ID FOR PAYMENT PAGE ===== */
$_SESSION['last_order_id'] = $order_id;

/* ===== REDIRECT ===== */
if ($payment_method == "Online") {

    header("Location: pay.php");

} else {

    header("Location: order_success.php?order=".$order_id);

}

exit;
?>
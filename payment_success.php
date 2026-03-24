<?php
session_start();
include "db.php";

/* CHECK ORDER SESSION */
if (!isset($_SESSION['last_order_id'])) {
    echo "Order not found.";
    exit();
}

$order_id = intval($_SESSION['last_order_id']);

/* UPDATE PAYMENT STATUS */
mysqli_query($conn,"
    UPDATE orders
    SET payment_status='paid', status='confirmed'
    WHERE id='$order_id'
");

/* FETCH ORDER DETAILS */
$order_res = mysqli_query($conn,"SELECT * FROM orders WHERE id='$order_id'");

if(!$order_res || mysqli_num_rows($order_res)==0){
    echo "Order not found.";
    exit();
}

$order = mysqli_fetch_assoc($order_res);

/* GET USER FROM SESSION */
$user = $_SESSION['user'] ?? "Customer";
?>

<!DOCTYPE html>
<html>
<head>
<title>Payment Success - Clothing Adda</title>

<style>
body{
    font-family: Arial;
    background:#f5f5f5;
    text-align:center;
    padding:50px;
}

.success{
    background:#d4edda;
    color:#155724;
    padding:30px;
    border-radius:10px;
    display:inline-block;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
}

a{
    display:inline-block;
    margin-top:15px;
    padding:10px 20px;
    background:#c0392b;
    color:#fff;
    text-decoration:none;
    border-radius:6px;
}

a:hover{
    background:#a93226;
}
</style>
</head>

<body>

<div class="success">

<h1>✅ Payment Successful!</h1>

<p>Thank you, <b><?php echo htmlspecialchars($user); ?></b>.</p>

<p>Your order (ID: <b>#<?php echo $order['id']; ?></b>) has been placed successfully.</p>

<p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_price'],2); ?></p>

<a href="index.php">Continue Shopping</a>

</div>

</body>
</html>
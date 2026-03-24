<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['order'])) {
    header("Location: index.php");
    exit;
}

$order_id = intval($_GET['order']);

$result = mysqli_query($conn,"SELECT * FROM orders WHERE id='$order_id'");
$order = mysqli_fetch_assoc($result);

if(!$order){
    die("Order not found");
}

$user = $_SESSION['user'];
$total = $order['total_price'];
$payment_method = $order['payment_method'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Order Confirmed - Clothing Adda</title>

<style>

body{
font-family:Arial;
background:#f4f6f9;
margin:0;
display:flex;
align-items:center;
justify-content:center;
height:100vh;
}

.box{
width:700px;
background:#fff;
border-radius:15px;
padding:40px;
box-shadow:0 20px 60px rgba(0,0,0,0.15);
text-align:center;
}

.tick{
font-size:70px;
color:#22c55e;
margin-bottom:20px;
}

h1{
margin-bottom:10px;
}

.details{
margin-top:25px;
font-size:16px;
}

.details p{
margin:10px 0;
}

.btn{
display:inline-block;
margin-top:25px;
padding:12px 20px;
background:#c0392b;
color:#fff;
text-decoration:none;
border-radius:8px;
font-weight:bold;
}

.btn:hover{
background:#a93226;
}

</style>
</head>

<body>

<div class="box">

<div class="tick">✔</div>

<h1>Order Confirmed!</h1>

<p>Thank you <b><?php echo htmlspecialchars($user); ?></b> for shopping with <b>Clothing Adda</b>.</p>

<div class="details">
<p><b>Order ID:</b> #<?php echo $order_id; ?></p>
<p><b>Total Amount:</b> ₹<?php echo $total; ?></p>
<p><b>Payment Method:</b> <?php echo $payment_method; ?></p>
<p><b>Status:</b> Order Placed (Cash on Delivery)</p>
</div>

<a href="index.php" class="btn">Continue Shopping</a>

</div>

</body>
</html>
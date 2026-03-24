<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit();
}

if(!isset($_GET['id'])){
echo "Order not found.";
exit();
}

$order_id = intval($_GET['id']);

$result = mysqli_query($conn,"SELECT * FROM orders WHERE id='$order_id'");
$order = mysqli_fetch_assoc($result);

if(!$order){
echo "Order not found.";
exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Order Details</title>

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
width:600px;
background:#fff;
padding:40px;
border-radius:12px;
box-shadow:0 20px 60px rgba(0,0,0,0.15);
}

h2{
text-align:center;
margin-bottom:25px;
}

.details p{
margin:10px 0;
font-size:16px;
}

.btn{
display:inline-block;
margin-top:20px;
padding:10px 18px;
background:#e74c3c;
color:#fff;
text-decoration:none;
border-radius:6px;
}

.btn:hover{
background:#c0392b;
}

</style>
</head>

<body>

<div class="box">

<h2>Order Details</h2>

<div class="details">

<p><b>Order ID:</b> #<?php echo $order['id']; ?></p>

<p><b>Products:</b> <?php echo $order['products']; ?></p>

<p><b>Total Amount:</b> ₹<?php echo $order['total_price']; ?></p>

<p><b>Status:</b> <?php echo $order['status'] ?? "Pending"; ?></p>

<p><b>Date:</b> <?php echo date("d M Y, H:i", strtotime($order['created_at'])); ?></p>

</div>

<a href="/clothing_adda/orders.php" class="btn">Back to Orders</a>

</div>

</body>
</html>
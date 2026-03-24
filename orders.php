<?php
session_start();
include "db.php";
/* ===== CANCEL ORDER ===== */
if(isset($_GET['cancel'])){

    $order_id = intval($_GET['cancel']);

    mysqli_query($conn,"UPDATE orders SET status='cancelled' WHERE id='$order_id'");

    header("Location: orders.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

$result = mysqli_query($conn,"
SELECT * FROM orders 
WHERE user_email='$user_email'
ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>

<title>My Orders</title>
<link rel="stylesheet" href="style.css">

<style>

body{
font-family:Arial;
background:#f4f6f9;
margin:0;
}

.container{
max-width:900px;
margin:100px auto;
padding:20px;
}

h2{
text-align:center;
margin-bottom:30px;
}

.order-card{
background:white;
padding:20px;
margin-bottom:20px;
border-radius:10px;
box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.order-row{
display:flex;
justify-content:space-between;
margin-bottom:10px;
}

.status{
padding:6px 12px;
border-radius:5px;
font-weight:bold;
}

.pending{
background:#fff3cd;
color:#856404;
}

.completed{
background:#d4edda;
color:#155724;
}

.cancelled{
background:#f8d7da;
color:#721c24;
}

.view-btn{
background:#3498db;
color:white;
padding:6px 12px;
border-radius:5px;
text-decoration:none;
}

.home-btn{
display:block;
width:200px;
margin:30px auto;
text-align:center;
background:#e74c3c;
color:white;
padding:10px;
border-radius:6px;
text-decoration:none;
}

.empty{
text-align:center;
background:white;
padding:30px;
border-radius:10px;
}

</style>

</head>

<body>

<div class="container">

<!-- SAME STYLE AS OTHER PAGES -->
<div style="position:relative; margin-bottom:30px;">

<a href="index.php"
style="position:absolute; left:0; top:0;"
class="btn">
← Back to Home
</a>

<h2>My Orders</h2>

</div>


<?php if(mysqli_num_rows($result) > 0){ ?>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<?php $status = strtolower($row['status']); ?>

<div class="order-card">

<div class="order-row">
<b>Products:</b>
<span><?php echo $row['products']; ?></span>
</div>

<div class="order-row">
<b>Total:</b>
<span>₹<?php echo $row['total_price']; ?></span>
</div>

<div class="order-row">
<b>Status:</b>
<span class="status <?php echo $status; ?>">
<?php echo ucfirst($status); ?>
</span>
</div>

<div class="order-row">
<b>Date:</b>
<span><?php echo $row['created_at']; ?></span>
</div>

<br>

<a href="order_details.php?id=<?php echo $row['id']; ?>" class="view-btn">View Details</a>

<?php if($status != 'cancelled' && $status != 'delivered'): ?>

<a href="orders.php?cancel=<?php echo $row['id']; ?>"
onclick="return confirm('Are you sure you want to cancel this order?')"
class="cancel-btn">
Cancel Order
</a>

<?php endif; ?>
</div>

<?php } ?>

<?php } else { ?>

<div class="empty">
No orders found
</div>

<?php } ?>

</div>

</body>
</html>
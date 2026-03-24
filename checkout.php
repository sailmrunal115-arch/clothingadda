<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? "User";
$user_email = $_SESSION['user_email'] ?? "";

// Fetch user details
$user_res = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$user = mysqli_fetch_assoc($user_res);

// Fetch cart items
$cart_query = mysqli_query($conn, "
    SELECT cart.quantity, products.id AS product_id, products.name, products.price 
    FROM cart 
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id = '$user_id'
");

if (mysqli_num_rows($cart_query) == 0) {
    header("Location: cart.php");
    exit();
}

// Handle order submission
if (isset($_POST['place_order'])) {

    $payment_method = $_POST['payment_method'] ?? "COD";
    $total = 0;
    $products_list = "";

    mysqli_data_seek($cart_query, 0);

    while ($row = mysqli_fetch_assoc($cart_query)) {
        $subtotal = $row['price'] * $row['quantity'];
        $total += $subtotal;
        $products_list .= $row['name'] . " (Qty: " . $row['quantity'] . "), ";
    }

    mysqli_query($conn, "
        INSERT INTO orders 
        (user_id, user_name, user_email, products, total_price, payment_method, status, created_at)
        VALUES 
        ('$user_id', '$user_name', '$user_email', '$products_list', '$total', '$payment_method', 'pending', NOW())
    ");

    $order_id = mysqli_insert_id($conn);

    $_SESSION['last_order_id'] = $order_id;

    mysqli_query($conn, "DELETE FROM cart WHERE user_id='$user_id'");

    if ($payment_method == "Online") {
        header("Location: pay.php");
    } else {
        header("Location: payment_success.php?order=".$order_id);
    }

    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Checkout</title>
<link rel="stylesheet" href="style.css">

<style>

body{
background:#f5f5f5;
}

.checkout-container{
max-width:900px;
margin:40px auto;
display:flex;
gap:20px;
flex-wrap:wrap;
font-family:Arial;
}

.card{
background:#fff;
padding:25px;
border-radius:12px;
box-shadow:0 6px 20px rgba(0,0,0,0.1);
flex:1 1 400px;
}

h2{
margin-bottom:15px;
color:#333;
border-bottom:2px solid #eee;
padding-bottom:5px;
}

h3{
margin-top:20px;
margin-bottom:10px;
color:#555;
}

p{
margin-bottom:8px;
color:#444;
}

table{
width:100%;
border-collapse:collapse;
margin-bottom:15px;
}

table th, table td{
padding:10px;
border-bottom:1px solid #ddd;
}

.total{
font-size:18px;
font-weight:bold;
text-align:right;
}

.payment-options label{
display:block;
margin-bottom:10px;
}

/* BUTTONS */

.order-buttons{
display:flex;
gap:10px;
margin-top:15px;
}

.place-order-btn{
padding:12px 25px;
background:#e74c3c;
color:white;
border:none;
border-radius:8px;
font-size:16px;
cursor:pointer;
}

.place-order-btn:hover{
background:#c0392b;
}

.cancel-btn{
padding:12px 25px;
background:#6c757d;
color:white;
border:none;
border-radius:8px;
cursor:pointer;
}

.cancel-btn:hover{
background:#5a6268;
}

/* POPUP */

.popup-overlay{
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.5);
justify-content:center;
align-items:center;
}

.popup{
background:white;
padding:25px;
border-radius:10px;
text-align:center;
width:300px;
}

.popup-buttons{
margin-top:15px;
display:flex;
justify-content:center;
gap:10px;
}

.confirm-btn{
background:#e74c3c;
color:white;
border:none;
padding:10px 18px;
border-radius:6px;
cursor:pointer;
}

.close-btn{
background:#777;
color:white;
border:none;
padding:10px 18px;
border-radius:6px;
cursor:pointer;
}

</style>
</head>

<body>

<div class="checkout-container">

<!-- Shipping Information -->
<div class="card">
<h2>Shipping Information</h2>

<p><strong>Name:</strong> <?= htmlspecialchars($user['name'] ?? $user_name) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($user_email) ?></p>
<p><strong>Address:</strong> <?= htmlspecialchars($user['address'] ?? 'Not provided') ?></p>
<p><strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? '-') ?></p>

<p>Please ensure your shipping information is correct before placing the order.</p>
</div>


<!-- Order Summary -->
<div class="card">

<h2>Order Summary</h2>

<table>
<tr>
<th>Product</th>
<th>Quantity</th>
<th>Price</th>
</tr>

<?php
mysqli_data_seek($cart_query, 0);
$total = 0;

while ($row = mysqli_fetch_assoc($cart_query)) {

$subtotal = $row['price'] * $row['quantity'];
$total += $subtotal;

echo "<tr>
<td>{$row['name']}</td>
<td>{$row['quantity']}</td>
<td>₹$subtotal</td>
</tr>";
}
?>

</table>

<p class="total">Total Amount: ₹<?= $total ?></p>

<h3>Select Payment Method</h3>

<form method="post" class="payment-options">

<label>
<input type="radio" name="payment_method" value="COD" checked>
Cash on Delivery
</label>

<label>
<input type="radio" name="payment_method" value="Online">
Online Payment
</label>

<div class="order-buttons">
<button type="submit" name="place_order" class="place-order-btn">Place Order</button>
<button type="button" class="cancel-btn" onclick="openPopup()">Cancel</button>
</div>

</form>

<p style="margin-top:8px;color:#666;font-size:14px;">
By clicking "Place Order", you agree to our terms and conditions.
</p>

</div>

</div>


<!-- CANCEL POPUP -->
<div class="popup-overlay" id="cancelPopup">

<div class="popup">

<h3>Are you sure you want to cancel the order?</h3>

<div class="popup-buttons">
<button class="confirm-btn" onclick="confirmCancel()">Yes, Cancel</button>
<button class="close-btn" onclick="closePopup()">No</button>
</div>

</div>

</div>


<script>

function openPopup(){
document.getElementById("cancelPopup").style.display="flex";
}

function closePopup(){
document.getElementById("cancelPopup").style.display="none";
}

function confirmCancel(){
window.location="cart.php";
}

</script>

</body>
</html>
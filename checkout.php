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
    SELECT cart.quantity, cart.size, cart.color, products.id AS product_id, products.name, products.price 
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
        
        $attr = "";
        if(!empty($row['size'])) $attr .= " [Size: " . $row['size'] . "]";
        if(!empty($row['color'])) $attr .= " [Color: " . $row['color'] . "]";
        
        $products_list .= $row['name'] . $attr . " (Qty: " . $row['quantity'] . "), ";
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
body { background: #fdfdfd; font-family: 'Inter', Arial, sans-serif; }
.checkout-container {
    max-width: 1000px;
    margin: 110px auto 60px;
    padding: 0 20px;
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
    align-items: flex-start;
}
.card {
    background: #fff;
    padding: 35px;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.04);
    border: 1px solid #f0f0f0;
    flex: 1 1 400px;
}
h2 {
    margin-bottom: 25px;
    color: #1e293b;
    border-bottom: 2px solid #f1f5f9;
    padding-bottom: 15px;
    font-size: 22px;
}
h3 { margin-top: 30px; margin-bottom: 15px; color: #334155; font-size: 18px; }
p { margin-bottom: 12px; color: #475569; font-size: 15px; }

table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
table th { text-align: left; color: #94a3b8; font-weight: 600; font-size: 14px; text-transform: uppercase; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0; }
table td { padding: 15px 0; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 500; }
table tr:last-child td { border-bottom: none; }

.total { font-size: 20px; font-weight: 700; color: #1e293b; text-align: right; margin-top: 15px; }

.payment-options {
    background: #f8fafc;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #e2e8f0;
}
.payment-options label {
    display: block;
    margin-bottom: 15px;
    font-size: 15.5px;
    color: #334155;
    font-weight: 500;
    cursor: pointer;
}
.payment-options label:last-child { margin-bottom: 0; }
.payment-options input[type="radio"] { margin-right: 10px; accent-color: #e74c3c; width: 16px; height: 16px; transform: translateY(2px); }

/* BUTTONS */
.order-buttons { display: flex; gap: 15px; margin-top: 25px; }

.place-order-btn {
    flex: 1;
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    border: none;
    border-radius: 30px;
    font-size: 15px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 15px 25px;
    cursor: pointer;
    box-shadow: 0 6px 15px rgba(231,76,60,0.25);
    transition: 0.3s;
}
.place-order-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(231,76,60,0.35); }

.cancel-btn {
    padding: 15px 30px;
    background: #f1f5f9;
    color: #64748b;
    font-weight: 600;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    transition: 0.2s;
}
.cancel-btn:hover { background: #e2e8f0; color: #1e293b; }

/* POPUP */
.popup-overlay {
    display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
    justify-content: center; align-items: center; z-index: 1000;
}
.popup {
    background: white; padding: 40px; border-radius: 16px;
    text-align: center; width: 350px; box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}
.popup h3 { margin-top: 0; margin-bottom: 25px; font-size: 18px; color: #1e293b; }
.popup-buttons { display: flex; justify-content: center; gap: 12px; }
.confirm-btn { background: #e74c3c; color: white; border: none; padding: 12px 24px; border-radius: 30px; cursor: pointer; font-weight: bold; transition: 0.2s; }
.confirm-btn:hover { background: #c0392b; }
.close-btn { background: #f1f5f9; color: #64748b; border: none; padding: 12px 24px; border-radius: 30px; cursor: pointer; font-weight: bold; transition: 0.2s; }
.close-btn:hover { background: #e2e8f0; color: #1e293b; }
</style>
</head>

<body>

<header>
<div class="container header-flex">
<div class="logo"><h1>Clothing Adda</h1></div>
<button class="hamburger" onclick="document.querySelector('.nav-links').classList.toggle('active')">☰</button>
<nav>
<ul class="nav-links">
<li><a href="index.php">Home</a></li>
<li><a href="men.php">Men</a></li>
<li><a href="women.php">Women</a></li>
<li><a href="products.php">All Products</a></li>

<?php if (isset($_SESSION['user_id'])): ?>
    <?php 
        $uname = $_SESSION['user_name'] ?? 'User';
        $initial = strtoupper(substr($uname, 0, 1));
    ?>
    <li>
        <div class="user-dropdown">
            <div class="user-avatar"><?= $initial ?></div>
            <?= htmlspecialchars($uname) ?> ▾
            <div class="dropdown-menu">
                <a href="profile.php">👤 My Profile</a>
                <a href="orders.php">📦 My Orders</a>
                <a href="cart.php">🛒 My Cart</a>
                <a href="logout.php">🚪 Logout</a>
            </div>
        </div>
    </li>
<?php else: ?>
    <li><a href="login.php">Login</a></li>
    <li><a href="register.php" class="btn" style="padding:8px 16px; margin-left:10px; color:#fff;">Register</a></li>
<?php endif; ?>
</ul>
</nav>
</div>
</header>

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

$attrHtml = "";
if(!empty($row['size'])) $attrHtml .= "<br><small style='color:#64748b;'>Size: " . htmlspecialchars($row['size']) . "</small>";
if(!empty($row['color'])) $attrHtml .= "<br><small style='color:#64748b;'>Color: " . htmlspecialchars($row['color']) . "</small>";

echo "<tr>
<td>{$row['name']}{$attrHtml}</td>
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

<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-col">
                <h3>Clothing Adda</h3>
                <p>Your ultimate destination for modern, trendy, and comfortable clothing. We bring the best styles right to your doorstep.</p>
                <div class="social-icons">
                    <a href="#">F</a><a href="#">T</a><a href="#">I</a>
                </div>
            </div>
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="cart.php">Cart</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Categories</h3>
                <ul class="footer-links">
                    <li><a href="men.php">Men</a></li>
                    <li><a href="women.php">Women</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Contact Us</h3>
                <ul class="footer-links">
                    <li>📍 123 Fashion St</li>
                    <li>📞 +1 234 567 8900</li>
                    <li>✉️ support@clothingadda.com</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 <strong>Clothing Adda</strong>. All Rights Reserved.</p>
        </div>
    </div>
</footer>

</body>
</html>
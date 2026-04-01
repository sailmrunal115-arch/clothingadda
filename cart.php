<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ================= REMOVE ITEM ================= */
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    mysqli_query($conn, "DELETE FROM cart WHERE id='$cart_id' AND user_id='$user_id'");
    header("Location: cart.php");
    exit();
}

/* ================= AJAX QUANTITY UPDATE ================= */
if (isset($_POST['update_qty'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    if ($quantity > 0) {
        mysqli_query($conn, "UPDATE cart SET quantity='$quantity' WHERE id='$cart_id' AND user_id='$user_id'");
    } else {
        mysqli_query($conn, "DELETE FROM cart WHERE id='$cart_id' AND user_id='$user_id'");
    }
    echo "success";
    exit();
}

/* ================= FETCH CART ITEMS ================= */
$sql = "
SELECT products.*, cart.quantity, cart.id as cart_id, cart.size, cart.color 
FROM cart 
JOIN products ON cart.product_id = products.id
WHERE cart.user_id = '$user_id'
";

$result = mysqli_query($conn, $sql);

$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart | Clothing Adda</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">
<style>
body { background: #fdfdfd; font-family: 'Inter', Arial, sans-serif; }
.cart-container { max-width: 900px; margin: 120px auto 60px; padding: 0 20px; }
.cart-container h2 { margin-bottom: 30px; font-size: 28px; color: #1e293b; text-align: center; }

.cart-items {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.04);
    padding: 30px;
    border: 1px solid #f0f0f0;
}

.cart-item {
    display:flex; 
    gap:25px; 
    margin-bottom:25px; 
    padding-bottom:25px;
    align-items:center;
    border-bottom: 1px solid #f1f5f9;
}

.cart-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.cart-item-img img {
    width: 110px;
    height: 110px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.cart-item-details { flex:1; }
.cart-item-details h3 { margin: 0 0 8px; color: #1e293b; font-size: 18px; }
.cart-item-details p { margin: 0 0 5px; color: #64748b; font-size: 14.5px; }

.quantity-wrapper {
    display: flex;
    align-items: center;
    background: #f1f5f9;
    border-radius: 30px;
    padding: 4px;
}
.quantity-wrapper button {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #fff;
    border: none;
    color: #334155;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    transition: 0.2s;
}
.quantity-wrapper button:hover { background: #e2e8f0; color: #e74c3c; }
.quantity-wrapper span { width: 40px; text-align: center; font-weight: 600; color: #1e293b; }

.cart-item-actions {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-top: 12px;
}

.remove-btn {
    color: #e74c3c;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    padding: 8px 14px;
    background: #fee2e2;
    border-radius: 20px;
    transition: 0.2s;
}
.remove-btn:hover { background: #fca5a5; color: #fff; }

.cart-actions {
    margin-top: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    padding: 25px 30px;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.04);
    border: 1px solid #f0f0f0;
    flex-wrap: wrap;
    gap: 20px;
}

.total { font-size: 22px; font-weight: 700; color: #1e293b; margin: 0; }

.cart-actions .btn {
    background: linear-gradient(135deg, #e74c3c, #c0392b) !important;
    color: #fff;
    text-decoration: none;
    text-transform: uppercase;
    font-weight: bold;
    letter-spacing: 0.5px;
    padding: 14px 28px;
    border-radius: 30px;
    font-size: 14.5px;
    box-shadow: 0 6px 15px rgba(231,76,60,0.25);
    transition: 0.3s;
    border: none;
}
.cart-actions .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(231,76,60,0.35); }

.continue-btn {
    background: #f1f5f9 !important;
    color: #475569 !important;
    text-decoration: none;
    font-weight: 600;
    padding: 14px 28px;
    border-radius: 30px;
    transition: 0.2s;
}
.continue-btn:hover { background: #e2e8f0 !important; color: #1e293b !important; }

@media(max-width: 600px) {
    .cart-actions { flex-direction: column; text-align: center; }
    .cart-actions div { display: flex; flex-direction: column; width: 100%; gap: 10px; }
    .cart-item { flex-direction: column; text-align: center; }
    .cart-item-actions { justify-content: center; }
}
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

<div class="cart-container">
<h2>Your Shopping Cart</h2>

<?php if (mysqli_num_rows($result) == 0): ?>

<p style="text-align: center; padding: 40px;">
Your cart is empty.
<a href="index.php" style="color: #e74c3c; font-weight: bold;">Go shopping!</a>
</p>

<?php else: ?>

<div class="cart-items">

<?php while ($row = mysqli_fetch_assoc($result)): 

$subtotal = $row['price'] * $row['quantity'];
$total += $subtotal;

/* ===== IMAGE FIX ===== */
$image = $row['image'];
$path = (strpos($image,'images/') !== false) ? $image : "uploads/".$image;

?>

<div class="cart-item">

<div class="cart-item-img">
<img src="<?php echo $path; ?>" alt="<?php echo $row['name']; ?>">
</div>

<div class="cart-item-details">

<h3><?php echo $row['name']; ?></h3>

<?php if(!empty($row['size']) || !empty($row['color'])): ?>
<p style="margin-bottom: 5px;">
    <?php if(!empty($row['size'])) echo "<strong>Size:</strong> " . htmlspecialchars($row['size']) . " &nbsp; "; ?>
    <?php if(!empty($row['color'])) echo "<strong>Color:</strong> " . htmlspecialchars($row['color']); ?>
</p>
<?php endif; ?>

<p>Price: ₹<?php echo number_format($row['price'], 2); ?></p>

<p style="color: #666;">Subtotal: ₹<?php echo number_format($subtotal, 2); ?></p>

<div class="cart-item-actions">

<div class="quantity-wrapper">
    <button type="button" onclick="changeQty(this, -1, <?php echo $row['cart_id']; ?>)">−</button>
    <span><?php echo $row['quantity']; ?></span>
    <button type="button" onclick="changeQty(this, 1, <?php echo $row['cart_id']; ?>)">+</button>
</div>

<a href="cart.php?remove=<?php echo $row['cart_id']; ?>" class="remove-btn">Remove</a>

</div>

</div>
</div>

<?php endwhile; ?>

</div>

<div class="cart-actions">

<h3 class="total">
Total: ₹<?php echo number_format($total, 2); ?>
</h3>

<!-- Place Order and Continue Shopping side by side -->
<div style="display:flex; gap:12px; flex-wrap:wrap;">
<a href="index.php" class="continue-btn">Continue Shopping</a>
<a href="checkout.php" class="btn">Proceed to Checkout</a>
</div>

</div>

<?php endif; ?>

</div>

<script>
function changeQty(btn, delta, cartId){
    const wrapper = btn.parentElement;
    const span = wrapper.querySelector('span');
    let current = parseInt(span.textContent);
    current += delta;
    if(current < 1) current = 1;
    span.textContent = current;

    // AJAX request to update quantity in database
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "cart.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("update_qty=1&cart_id=" + cartId + "&quantity=" + current);
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
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
    $product_id = intval($_GET['remove']);
    mysqli_query($conn, "DELETE FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");
    header("Location: cart.php");
    exit();
}

/* ================= AJAX QUANTITY UPDATE ================= */
if (isset($_POST['update_qty'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    if ($quantity > 0) {
        mysqli_query($conn, "UPDATE cart SET quantity='$quantity' WHERE user_id='$user_id' AND product_id='$product_id'");
    } else {
        mysqli_query($conn, "DELETE FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");
    }
    echo "success";
    exit();
}

/* ================= FETCH CART ITEMS ================= */
$sql = "
SELECT products.*, cart.quantity 
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
.cart-item {
    display:flex; 
    gap:20px; 
    margin-bottom:20px; 
    align-items:center;
}
.cart-item-img img {
    max-width: 100px;
    border-radius: 6px;
}
.cart-item-details {
    flex:1;
}
.quantity-wrapper {
    display: flex;
    align-items: center;
    gap: 5px;
}
.quantity-wrapper button {
    padding: 5px 10px;
    background: #e74c3c;
    border: none;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    border-radius: 4px;
}
.quantity-wrapper button:hover {
    background: #c0392b;
}
.quantity-wrapper span {
    width: 40px;
    text-align: center;
    display: inline-block;
}
.cart-item-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 8px;
}
.cart-actions {
    margin-top: 20px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
}
.cart-actions .btn {
    background-color: #e74c3c !important;
    color: #fff;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 16px;
}
.cart-actions .btn:hover {
    background-color: #c0392b !important;
}
</style>
</head>

<body>

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

<p>Price: ₹<?php echo number_format($row['price'], 2); ?></p>

<p style="color: #666;">Subtotal: ₹<?php echo number_format($subtotal, 2); ?></p>

<div class="cart-item-actions">

<div class="quantity-wrapper">
    <button type="button" onclick="changeQty(this, -1, <?php echo $row['id']; ?>)">−</button>
    <span><?php echo $row['quantity']; ?></span>
    <button type="button" onclick="changeQty(this, 1, <?php echo $row['id']; ?>)">+</button>
</div>

<a href="cart.php?remove=<?php echo $row['id']; ?>" class="btn">Remove</a>

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
<div style="display:flex; gap:10px; flex-wrap:wrap;">
<a href="checkout.php" class="btn">Place Order</a>
<a href="index.php" class="btn">Continue Shopping</a>
</div>

</div>

<?php endif; ?>

</div>

<script>
function changeQty(btn, delta, productId){
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
    xhr.send("update_qty=1&product_id=" + productId + "&quantity=" + current);
}
</script>

</body>
</html>
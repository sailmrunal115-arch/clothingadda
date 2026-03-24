<?php
session_start();
include "db.php";

/* ================= ADD TO CART (DATABASE) ================= */
if (isset($_GET['add'])) {

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $product_id = intval($_GET['add']);
    $user_id = intval($_SESSION['user_id']);

    $product_check = mysqli_query($conn, "SELECT id FROM products WHERE id='$product_id'");
    if (mysqli_num_rows($product_check) == 0) {
        die("Product not found.");
    }

    $check = mysqli_query($conn, "SELECT id FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");

    if (mysqli_num_rows($check) > 0) {

        mysqli_query($conn,
            "UPDATE cart SET quantity = quantity + 1 
             WHERE user_id='$user_id' AND product_id='$product_id'"
        );

    } else {

        mysqli_query($conn,
            "INSERT INTO cart (user_id, product_id, quantity) 
             VALUES ('$user_id', '$product_id', 1)"
        );
    }

    header("Location: index.php");
    exit();
}

/* ================= FETCH USER INFO ================= */
$user_id = $_SESSION['user_id'] ?? null;
$user = null;

if ($user_id) {
    $user_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
    $user = mysqli_fetch_assoc($user_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Clothing Adda | Online Clothing Store</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">
</head>

<body>

<header>
<div class="container header-flex">

<div class="logo">
<h1>Clothing Adda</h1>
</div>

<button class="hamburger" onclick="document.querySelector('.nav-links').classList.toggle('active')">☰</button>
<nav>
<ul class="nav-links">

<li><a href="index.php" class="active">Home</a></li>
<li><a href="#categories">Categories</a></li>
<li><a href="products.php">Products</a></li>
<li><a href="#about">About</a></li>

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

<section class="hero" id="home">
<div class="hero-content">
<h2>Latest Fashion Collection</h2>
<p>Stylish Clothing for Men & Women</p>
<a href="products.php" class="btn">Shop Now</a>
</div>
</section>

<section class="categories" id="categories">
<div class="container">

<h2>Shop by Category</h2>

<div class="category-grid">
<a href="men.php" class="category-card">Men</a>
<a href="women.php" class="category-card">Women</a>
</div>

</div>
</section>


<section class="products" id="products">

<div class="container">

<h2>Latest Clothing</h2>

<div class="product-grid">

<?php
$sql = "SELECT * FROM products ORDER BY id DESC LIMIT 4";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0):

while ($row = mysqli_fetch_assoc($result)):
?>

<div class="product-card">

<div class="product-img">

<?php
$image = $row['image'];

if(strpos($image,'images/') !== false){
$path = $image;
}else{
$path = "uploads/".$image;
}
?>

<img src="<?php echo $path; ?>" alt="<?php echo $row['name']; ?>">

</div>

<div class="product-info">

<h3><?php echo $row['name']; ?></h3>

<p class="price">
₹<?php echo number_format($row['price'], 2); ?>
</p>

<?php if (isset($_SESSION['user_id'])): ?>

<a href="index.php?add=<?php echo $row['id']; ?>" class="btn">
Add to Cart
</a>

<?php else: ?>

<a href="login.php" class="btn">
Login to Add
</a>

<?php endif; ?>

</div>
</div>

<?php
endwhile;

else:

echo "<p>No products found</p>";

endif;
?>

</div>

<div style="text-align:center; margin-top:30px;">
<a href="products.php" class="btn">View All Products</a>
</div>

</div>
</section>


<section class="about" id="about">
<div class="container">

<h2>About Clothing Adda</h2>

<p>
Clothing Adda is your one-stop online fashion store.
We provide high-quality and affordable clothing for Men and Women.
</p>

</div>
</section>


<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            
            <div class="footer-col">
                <h3>Clothing Adda</h3>
                <p>Your ultimate destination for modern, trendy, and comfortable clothing. We strive to bring the best styles right to your doorstep.</p>
                <div class="social-icons">
                    <a href="#">F</a>
                    <a href="#">T</a>
                    <a href="#">I</a>
                </div>
            </div>

            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">All Products</a></li>
                    <li><a href="cart.php">My Cart</a></li>
                    <li><a href="login.php">Login / Register</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Categories</h3>
                <ul class="footer-links">
                    <li><a href="men.php">Men's Collection</a></li>
                    <li><a href="women.php">Women's Collection</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Contact Us</h3>
                <ul class="footer-links">
                    <li>📍 123 Fashion Street, NY 10001</li>
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
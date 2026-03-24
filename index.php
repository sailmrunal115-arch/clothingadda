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

<nav>
<ul class="nav-links">

<li><a href="index.php" class="active">Home</a></li>
<li><a href="#categories">Categories</a></li>
<li><a href="products.php">Products</a></li>
<li><a href="#about">About</a></li>

<?php if (isset($_SESSION['user_id'])): ?>

<li style="color:#ff6a00; font-weight:600;">
Hello, <?= htmlspecialchars($user['name']); ?>
</li>

<li><a href="orders.php">My Orders</a></li>
<li><a href="cart.php">Cart</a></li>
<li><a href="profile.php">Profile</a></li>
<li><a href="logout.php">Logout</a></li>

<?php else: ?>

<li><a href="login.php">Login</a></li>
<li><a href="register.php">Register</a></li>

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


<footer>
<div class="container">
<p>&copy; 2026 Clothing Adda. All Rights Reserved.</p>
</div>
</footer>

</body>
</html>
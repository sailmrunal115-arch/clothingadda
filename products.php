<?php
session_start();
include "db.php";


/* ================= ADD TO CART ================= */
if (isset($_GET['add'])) {

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $product_id = intval($_GET['add']);
    $user_id = intval($_SESSION['user_id']);

    $check = mysqli_query($conn,
        "SELECT id FROM cart WHERE user_id='$user_id' AND product_id='$product_id'"
    );

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

    header("Location: products.php");
    exit();
}

/* ================= FETCH PRODUCTS ================= */
$where_clauses = [];
if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clauses[] = "name LIKE '%$search%'";
}
if (!empty($_GET['min_price'])) {
    $min = floatval($_GET['min_price']);
    $where_clauses[] = "price >= $min";
}
if (!empty($_GET['max_price'])) {
    $max = floatval($_GET['max_price']);
    $where_clauses[] = "price <= $max";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";
$result = mysqli_query($conn, "SELECT * FROM products $where_sql ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>All Products | Clothing Adda</title>
<link rel="stylesheet" href="style.css">

<style>
.cart-btn{
    display:block;
    margin:10px auto;
    text-align:center;
    width:80%;
}
.product-link{
    text-decoration:none;
    color:inherit;
    display:block;
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
<li><a href="products.php" class="active">All Products</a></li>

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

<section class="products">
<div class="container">

<div style="position:relative; margin-bottom:30px;">
<a href="index.php" style="position:absolute; left:0;" class="btn">← Back to Home</a>
<h2 style="text-align:center;">All Products</h2>
</div>

<!-- ================= FILTER BAR ================= -->
<div class="filter-bar">
    <form method="GET" action="products.php" class="filter-form">
        <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        
        <div class="price-filter">
            <input type="number" name="min_price" placeholder="Min ₹" value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>">
            <span>-</span>
            <input type="number" name="max_price" placeholder="Max ₹" value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>">
        </div>
        
        <button type="submit" class="btn">Filter</button>

        <?php if(!empty($_GET['search']) || !empty($_GET['min_price']) || !empty($_GET['max_price'])): ?>
            <a href="products.php" class="clear-btn">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="product-grid">

<?php while($row = mysqli_fetch_assoc($result)): ?>

<div class="product-card">

<a href="product.php?id=<?php echo $row['id']; ?>" class="product-link">

<div class="product-img">
<?php
$image = $row['image'];
$path = (strpos($image,'images/') !== false) ? $image : "uploads/".$image;
?>
<img src="<?php echo $path; ?>">
</div>

<div class="product-info">

<h3><?php echo $row['name']; ?></h3>

<?php
$product_id = $row['id'];
$avg_query = mysqli_query($conn,"
SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
FROM reviews WHERE product_id='$product_id'
");
$avg_data = mysqli_fetch_assoc($avg_query);
$avg_rating = round($avg_data['avg_rating'],1);
$total_reviews = $avg_data['total_reviews'];
?>

<?php if($total_reviews > 0): ?>
<div style="font-size:14px; margin:5px 0; text-align:center; color:#555;">
<?php
$full = floor($avg_rating);
$half = ($avg_rating - $full >= 0.5);

echo str_repeat("⭐", $full);
if($half) echo "✰";

echo " ($avg_rating) | $total_reviews Reviews";
?>
</div>
<?php else: ?>
<div style="font-size:13px; color:#999; text-align:center;">No ratings yet</div>
<?php endif; ?>

<p class="price">₹<?php echo number_format($row['price'], 2); ?></p>

</div>
</a>

<?php if (isset($_SESSION['user_id'])): ?>
<a href="products.php?add=<?php echo $row['id']; ?>" class="btn cart-btn">Add to Cart</a>
<?php else: ?>
<a href="login.php" class="btn cart-btn">Login to Add</a>
<?php endif; ?>

</div>

<?php endwhile; ?>

</div>
</div>
</section>



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
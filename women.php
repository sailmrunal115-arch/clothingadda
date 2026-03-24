<?php
session_start();
include "db.php";

$category = 'women';

/* ================= ADD REVIEW ================= */
if(isset($_POST['submit_review'])){

    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }

    $product_id = intval($_POST['product_id']);
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    $rating = intval($_POST['rating']);
    $review = mysqli_real_escape_string($conn, $_POST['review']);

    mysqli_query($conn,"
        INSERT INTO reviews (product_id,user_id,user_name,rating,review)
        VALUES ('$product_id','$user_id','$user_name','$rating','$review')
    ");

    header("Location: women.php");
    exit();
}

/* ================= ADD TO CART ================= */
if(isset($_GET['add'])) {

    if(!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $product_id = intval($_GET['add']);
    $user_id = intval($_SESSION['user_id']);

    $check = mysqli_query($conn,"SELECT id FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");

    if(mysqli_num_rows($check) > 0){
        mysqli_query($conn,"UPDATE cart SET quantity = quantity + 1 WHERE user_id='$user_id' AND product_id='$product_id'");
    }else{
        mysqli_query($conn,"INSERT INTO cart(user_id,product_id,quantity) VALUES('$user_id','$product_id',1)");
    }

    header("Location: women.php");
    exit();
}

/* ================= FETCH PRODUCTS ================= */
$products = mysqli_query($conn,"SELECT * FROM products WHERE category='$category' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Women's Clothing | Clothing Adda</title>
<link rel="stylesheet" href="style.css">

<style>
.review-box{
    margin-top:12px;
    padding-top:12px;
    border-top:1px solid #eee;
}

.review-box h4{
    font-size:14px;
    margin-bottom:8px;
    font-weight:bold;
}

.review-box select,
.review-box textarea{
    width:100%;
    padding:8px;
    margin-bottom:8px;
    border-radius:6px;
    border:1px solid #ccc;
    font-size:13px;
}

.review-box textarea{
    height:60px;
    resize:none;
}

.small-btn{
    width:100%;
    padding:8px;
    font-size:13px;
    border-radius:6px;
    background:#e74c3c;
    color:#fff;
    border:none;
    cursor:pointer;
}

.small-btn:hover{
    background:#c0392b;
}

.show-btn{
    margin-top:8px;
    background:#333;
}

.show-btn:hover{
    background:#111;
}

.review-item{
    padding:8px 0;
    border-bottom:1px solid #eee;
}

.review-stars{
    color:#f1c40f;
}

.review-text{
    font-size:13px;
    color:#555;
}
</style>

</head>

<body>

<header>
<div class="container header-flex">
<div class="logo"><h1>Clothing Adda</h1></div>

<nav>
<ul class="nav-links">
<li><a href="index.php">Home</a></li>
<li><a href="men.php">Men</a></li>
<li><a href="women.php" class="active">Women</a></li>
<li><a href="products.php">All Products</a></li>

<?php if(isset($_SESSION['user_id'])): ?>
<li><span class="nav-user">Hi, <?php echo $_SESSION['user_name']; ?></span></li>
<li><a href="cart.php">Cart</a></li>
<li><a href="logout.php">Logout</a></li>
<?php else: ?>
<li><a href="login.php">Login</a></li>
<li><a href="register.php">Register</a></li>
<?php endif; ?>
</ul>
</nav>
</div>
</header>

<section class="products">
<div class="container">

<div style="position:relative; margin-bottom:30px;">
<a href="index.php" class="btn" style="position:absolute; left:0;">← Back to Home</a>
<h2 style="text-align:center;">Women's Clothing</h2>
</div>

<div class="product-grid">

<?php while($row = mysqli_fetch_assoc($products)): ?>

<div class="product-card">

<a href="product.php?id=<?php echo $row['id']; ?>">
<div class="product-img">
<?php
$image = $row['image'];
$path = (strpos($image,'images/') !== false) ? $image : "uploads/".$image;
?>
<img src="<?php echo $path; ?>">
</div>
</a>

<div class="product-info">

<a href="product.php?id=<?php echo $row['id']; ?>" style="text-decoration:none;color:black;">
<h3><?php echo $row['name']; ?></h3>
</a>

<!-- ⭐ AVERAGE RATING WITH HALF STAR -->
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
<div style="font-size:14px; margin:5px 0; color:#555;">
<?php
$full = floor($avg_rating);
$half = ($avg_rating - $full >= 0.5);

// full stars
echo str_repeat("⭐", $full);

// half star
if($half){
    echo "✰";
}

// rating text
echo " ($avg_rating) | $total_reviews Reviews";
?>
</div>
<?php else: ?>
<div style="font-size:13px; color:#999;">No ratings yet</div>
<?php endif; ?>

<p class="price">₹<?php echo number_format($row['price'],2); ?></p>

<a href="women.php?add=<?php echo $row['id']; ?>" class="btn">Add to Cart</a>

<!-- ⭐ ADD REVIEW -->
<div class="review-box">
<h4>⭐ Add Review</h4>

<form method="POST">
<input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">

<select name="rating" required>
<option value="">Select Rating</option>
<option value="5">⭐⭐⭐⭐⭐</option>
<option value="4">⭐⭐⭐⭐</option>
<option value="3">⭐⭐⭐</option>
<option value="2">⭐⭐</option>
<option value="1">⭐</option>
</select>

<textarea name="review" placeholder="Write your review..." required></textarea>

<button type="submit" name="submit_review" class="small-btn">
Submit Review
</button>
</form>
</div>

<button onclick="toggleReviews(<?php echo $row['id']; ?>)" class="small-btn show-btn">
Show Reviews
</button>

<div id="reviews-<?php echo $row['id']; ?>" class="review-box" style="display:none;">
<h4>Customer Reviews</h4>

<?php
$review_query = mysqli_query($conn,"
SELECT * FROM reviews WHERE product_id='$product_id' ORDER BY id DESC LIMIT 3
");

if(mysqli_num_rows($review_query) > 0){
while($rev = mysqli_fetch_assoc($review_query)){
?>

<div class="review-item">
<strong><?php echo htmlspecialchars($rev['user_name']); ?></strong>
<div class="review-stars"><?php echo str_repeat("⭐", $rev['rating']); ?></div>
<div class="review-text"><?php echo htmlspecialchars($rev['review']); ?></div>
</div>

<?php }} else { echo "<p>No reviews yet</p>"; } ?>
</div>

</div>
</div>

<?php endwhile; ?>

</div>
</div>
</section>

<script>
function toggleReviews(id){
    var box = document.getElementById("reviews-"+id);
    box.style.display = (box.style.display === "none") ? "block" : "none";
}
</script>

</body>
</html>
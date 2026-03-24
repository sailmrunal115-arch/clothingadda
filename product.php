<?php
session_start();
include "db.php";

if(!isset($_GET['id'])){
    header("Location: index.php");
    exit();
}

$product_id = intval($_GET['id']);
$product = mysqli_query($conn,"SELECT * FROM products WHERE id='$product_id'");
if(mysqli_num_rows($product) == 0) die("Product not found");
$data = mysqli_fetch_assoc($product);

/* ADD TO CART */
if(isset($_GET['add'])){
    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $check = mysqli_query($conn,"SELECT id FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");
    if(mysqli_num_rows($check) > 0){
        mysqli_query($conn,"UPDATE cart SET quantity = quantity + 1 WHERE user_id='$user_id' AND product_id='$product_id'");
    } else {
        mysqli_query($conn,"INSERT INTO cart(user_id,product_id,quantity) VALUES('$user_id','$product_id',1)");
    }
    header("Location: product.php?id=$product_id");
    exit();
}

/* PLACE ORDER — add to cart then go to checkout */
if(isset($_POST['place_order'])){
    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }
    $user_id = $_SESSION['user_id'];
    // Add to cart if not already there, else increment
    $check = mysqli_query($conn,"SELECT id FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");
    if(mysqli_num_rows($check) > 0){
        mysqli_query($conn,"UPDATE cart SET quantity = quantity + 1 WHERE user_id='$user_id' AND product_id='$product_id'");
    } else {
        mysqli_query($conn,"INSERT INTO cart(user_id,product_id,quantity) VALUES('$user_id','$product_id',1)");
    }
    header("Location: checkout.php");
    exit();
}

/* ADD REVIEW */
if(isset($_POST['submit_review'])){
    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }
    $user_id   = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    $rating    = intval($_POST['rating']);
    $review    = mysqli_real_escape_string($conn,$_POST['review']);
    mysqli_query($conn,"INSERT INTO reviews(product_id,user_id,user_name,rating,review) VALUES('$product_id','$user_id','$user_name','$rating','$review')");
    header("Location: product.php?id=$product_id");
    exit();
}

/* AVG RATING */
$avg_query = mysqli_query($conn,"SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id='$product_id'");
$avg = mysqli_fetch_assoc($avg_query);
$avg_rating = round($avg['avg_rating'],1);
$total_reviews = $avg['total_reviews'];
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo $data['name']; ?></title>
<link rel="stylesheet" href="style.css">
<style>
.container{width:90%; max-width:1200px; margin:0 auto;}
.product-page{display:flex; flex-wrap:wrap; gap:40px; margin-top:40px;}
.product-img{flex:1 1 500px; text-align:center;}
.product-img img{width:100%; max-width:900px; border-radius:10px;}
.product-details{flex:1 1 500px;}
.product-details h2{font-size:32px; margin-bottom:10px;}
.price{font-size:28px; color:#e74c3c; margin:10px 0;}
.rating{font-size:16px; color:#f1c40f; margin:10px 0;}
.btn{display:inline-block; padding:10px 20px; margin:10px 5px 10px 0; border:none; border-radius:6px; background:#e74c3c; color:#fff; font-size:16px; cursor:pointer; text-decoration:none; transition:0.3s;}
.btn:hover{background:#c0392b;}
.back-btn{display:inline-block; margin-bottom:20px; background:#e74c3c; text-decoration:none; color:#fff; padding:8px 15px; border-radius:6px;}
.back-btn:hover{background:#c0392b;}
.product-description{margin:10px 0 20px 0; font-size:16px; color:#555;}
.review-section{margin-top:40px; padding-top:30px; border-top:2px solid #eee;}
.review-section h3{font-size:22px; margin-bottom:20px; color:#222;}
.review-box{display:none; margin-top:16px;}
.review-item{padding:10px 0; border-bottom:1px solid #eee;}
.review-stars{color:#f1c40f; font-size:15px;}
.review-text{font-size:14px; color:#555; margin-top:3px;}
.review-form{margin-top:24px;}
.review-form h4{font-size:17px; margin-bottom:12px; color:#333;}
.review-form select, .review-form textarea{width:100%; padding:10px; margin-bottom:10px; border-radius:8px; border:1px solid #ccc; font-size:14px; box-sizing:border-box;}
.review-form textarea{height:80px; resize:none;}
.toggle-btn{background:#333; color:#fff; border:none; padding:9px 20px; border-radius:6px; cursor:pointer; font-size:14px; margin-top:8px; transition:0.2s;}
.toggle-btn:hover{background:#111;}
@media(max-width:768px){
.product-page{flex-direction:column; gap:20px;}
.product-img, .product-details{flex:1 1 100%;}
}
</style>
</head>
<body>

<div class="container">

<!-- BACK TO PRODUCTS BUTTON -->
<a href="products.php" class="back-btn">← Back to Products</a>

<div class="product-page">

<div class="product-img">
<?php
$image = $data['image'];
$path = (strpos($image,'images/') !== false) ? $image : "uploads/".$image;
?>
<img src="<?php echo $path; ?>" alt="<?php echo htmlspecialchars($data['name']); ?>">
</div>

<div class="product-details">

<h2><?php echo $data['name']; ?></h2>

<div class="rating">
<?php
$full = floor($avg_rating);
$half = ($avg_rating - $full >= 0.5);
echo str_repeat("⭐", $full);
if($half) echo "✰";
?>
(<?php echo $avg_rating; ?>) | <?php echo $total_reviews; ?> Reviews
</div>

<!-- PRODUCT DESCRIPTION ABOVE PRICE -->
<p class="product-description">
This is a premium product from Clothing Adda. High quality fabric and trendy design.
</p>

<p class="price">₹<?php echo number_format($data['price'],2); ?></p>

<!-- ADD TO CART & PLACE ORDER BUTTON -->
<?php if(isset($_SESSION['user_id'])): ?>
<a href="product.php?id=<?php echo $product_id; ?>&add=<?php echo $product_id; ?>" class="btn">Add to Cart</a>
<form method="POST" style="display:inline;">
    <button type="submit" name="place_order" class="btn">Place Order</button>
</form>
<?php else: ?>
<a href="login.php" class="btn">Login to Add / Order</a>
<?php endif; ?>

</div> <!-- product-details -->
</div> <!-- product-page -->

<!-- ===== REVIEW SECTION ===== -->
<div class="review-section">

<h3>Customer Reviews</h3>

<button class="toggle-btn" onclick="toggleReviews()">Show Reviews</button>

<div class="review-box" id="reviews-box">
<?php
$review_query = mysqli_query($conn,"SELECT * FROM reviews WHERE product_id='$product_id' ORDER BY id DESC LIMIT 5");
if(mysqli_num_rows($review_query) > 0){
    while($rev = mysqli_fetch_assoc($review_query)){
        $full_rev = floor($rev['rating']);
        $half_rev = ($rev['rating'] - $full_rev >= 0.5);
?>
<div class="review-item">
    <strong><?php echo htmlspecialchars($rev['user_name']); ?></strong>
    <div class="review-stars"><?php echo str_repeat("⭐", $full_rev); if($half_rev) echo "✰"; ?></div>
    <div class="review-text"><?php echo htmlspecialchars($rev['review']); ?></div>
</div>
<?php }} else { echo "<p style='color:#999;margin-top:10px;'>No reviews yet. Be the first!</p>"; } ?>
</div>

<?php if(isset($_SESSION['user_id'])): ?>
<div class="review-form">
<h4>⭐ Add Your Review</h4>
<form method="POST">
    <select name="rating" required>
        <option value="">Select Rating</option>
        <option value="5">⭐⭐⭐⭐⭐ — Excellent</option>
        <option value="4">⭐⭐⭐⭐ — Good</option>
        <option value="3">⭐⭐⭐ — Average</option>
        <option value="2">⭐⭐ — Poor</option>
        <option value="1">⭐ — Terrible</option>
    </select>
    <textarea name="review" placeholder="Write your review here..." required></textarea>
    <button type="submit" name="submit_review" class="btn">Submit Review</button>
</form>
</div>
<?php else: ?>
<p style="margin-top:16px; color:#666;"><a href="login.php" style="color:#e74c3c;">Login</a> to write a review.</p>
<?php endif; ?>

</div> <!-- review-section -->

</div> <!-- container -->

<script>
function toggleReviews(){
    var box = document.getElementById("reviews-box");
    var btn = document.querySelector(".toggle-btn");
    if(box.style.display === "block"){
        box.style.display = "none";
        btn.textContent = "Show Reviews";
    } else {
        box.style.display = "block";
        btn.textContent = "Hide Reviews";
    }
}
</script>

</body>
</html>
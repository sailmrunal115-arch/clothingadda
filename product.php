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

/* PLACE ORDER DIRECTLY */
if(isset($_POST['place_order'])){
    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $quantity = 1;
    // Make sure your orders table has 'product_id' and 'quantity' columns
    mysqli_query($conn,"INSERT INTO orders(user_id,product_id,quantity,payment_status) VALUES('$user_id','$product_id','$quantity','Pending')");
    header("Location: payment_success.php?product_id=$product_id");
    exit();
}

/* ADD REVIEW */
if(isset($_POST['submit_review'])){
    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    $rating = intval($_POST['rating']);
    $review = mysqli_real_escape_string($conn,$_POST['review']);
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
.review-box{margin-top:15px; padding:12px; border-top:1px solid #eee; display:none;}
.review-item{padding:8px 0; border-bottom:1px solid #eee;}
.review-stars{color:#f1c40f;}
.review-text{font-size:13px; color:#555;}
.review-form select, .review-form textarea{width:100%; padding:8px; margin-bottom:8px; border-radius:6px; border:1px solid #ccc;}
.review-form textarea{height:60px; resize:none;}
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

<!-- SHOW REVIEWS BELOW BUTTONS -->
<button class="btn" onclick="toggleReviews()">Show Reviews</button>

<div class="review-box" id="reviews-box">

<h4>Customer Reviews</h4>
<?php
$review_query = mysqli_query($conn,"SELECT * FROM reviews WHERE product_id='$product_id' ORDER BY id DESC LIMIT 3");
if(mysqli_num_rows($review_query) > 0){
    while($rev = mysqli_fetch_assoc($review_query)){
        $full_rev = floor($rev['rating']);
        $half_rev = ($rev['rating'] - $full_rev >= 0.5);
?>
<div class="review-item">
<strong><?php echo htmlspecialchars($rev['user_name']); ?></strong>
<div class="review-stars">
<?php echo str_repeat("⭐", $full_rev); if($half_rev) echo "✰"; ?>
</div>
<div class="review-text"><?php echo htmlspecialchars($rev['review']); ?></div>
</div>
<?php }} else { echo "<p>No reviews yet</p>"; } ?>

<!-- ADD REVIEW FORM -->
<?php if(isset($_SESSION['user_id'])): ?>
<div class="review-form" style="margin-top:15px;">
<h4>⭐ Add Your Review</h4>
<form method="POST">
    <select name="rating" required>
        <option value="">Select Rating</option>
        <option value="5">⭐⭐⭐⭐⭐</option>
        <option value="4">⭐⭐⭐⭐</option>
        <option value="3">⭐⭐⭐</option>
        <option value="2">⭐⭐</option>
        <option value="1">⭐</option>
    </select>
    <textarea name="review" placeholder="Write your review..." required></textarea>
    <button type="submit" name="submit_review" class="btn">Submit Review</button>
</form>
</div>
<?php else: ?>
<p><a href="login.php">Login to submit a review</a></p>
<?php endif; ?>

</div> <!-- reviews-box -->

</div> <!-- product-details -->
</div> <!-- product-page -->
</div> <!-- container -->

<script>
function toggleReviews(){
    var box = document.getElementById("reviews-box");
    box.style.display = (box.style.display === "none" || box.style.display === "") ? "block" : "none";
}
</script>

</body>
</html>
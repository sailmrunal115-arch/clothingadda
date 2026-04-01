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

/* ADD TO CART or PLACE ORDER */
if(isset($_POST['add_to_cart']) || isset($_POST['place_order'])){
    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $size = isset($_POST['size']) ? mysqli_real_escape_string($conn, $_POST['size']) : '';
    $color = isset($_POST['color']) ? mysqli_real_escape_string($conn, $_POST['color']) : '';
    
    // Check if the exact same product with same size and color is in cart
    $check = mysqli_query($conn,"SELECT id FROM cart WHERE user_id='$user_id' AND product_id='$product_id' AND size='$size' AND color='$color'");
    if(mysqli_num_rows($check) > 0){
        mysqli_query($conn,"UPDATE cart SET quantity = quantity + 1 WHERE user_id='$user_id' AND product_id='$product_id' AND size='$size' AND color='$color'");
    } else {
        mysqli_query($conn,"INSERT INTO cart(user_id,product_id,quantity,size,color) VALUES('$user_id','$product_id',1,'$size','$color')");
    }
    
    if(isset($_POST['place_order'])) {
        header("Location: checkout.php");
    } else {
        header("Location: product.php?id=$product_id&cart_added=1");
    }
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

// Get image path safely
$image = $data['image'];
$path = (strpos($image,'images/') !== false) ? $image : ((strpos($image,'http') === 0) ? $image : "uploads/".$image);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['name']) ?> | Clothing Adda</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #fafafa; }

        .prod-page {
            max-width: 1200px;
            margin: 120px auto 80px;
            padding: 0 20px;
            animation: fadeUp 0.6s ease-out;
        }

        /* Top Breadcrumb */
        .breadcrumb {
            margin-bottom: 30px;
        }

        .breadcrumb a {
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .breadcrumb a:hover {
            color: #e74c3c;
        }

        .breadcrumb span {
            color: #cbd5e1;
            margin: 0 8px;
            font-size: 14px;
        }

        .breadcrumb strong {
            color: #1a1a2e;
            font-size: 14px;
            font-weight: 600;
        }

        /* ── TWO COLUMN GRID ── */
        .prod-grid {
            display: grid;
            grid-template-columns: minmax(400px, 1fr) 1fr;
            gap: 50px;
            align-items: start;
        }

        /* Left Image */
        .prod-img-box {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            position: sticky;
            top: 100px;
        }

        .prod-img-box img {
            max-width: 100%;
            height: auto;
            max-height: 550px;
            object-fit: contain;
            mix-blend-mode: multiply; /* Looks great if images have white bg */
            transition: transform 0.3s;
        }

        .prod-img-box:hover img {
            transform: scale(1.03);
        }

        /* Right Details */
        .prod-info {
            padding: 10px 0;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .prod-title {
            font-size: 40px;
            font-weight: 800;
            color: #1a1a2e;
            line-height: 1.15;
            letter-spacing: -0.5px;
        }

        .prod-rating-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
        }

        .rating-stars {
            color: #f59e0b;
            letter-spacing: 2px;
            font-size: 18px;
        }

        .rating-badge {
            background: #fffbeb;
            color: #d97706;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13px;
        }

        .rating-count {
            color: #64748b;
            font-weight: 500;
            text-decoration: underline;
            text-decoration-color: #cbd5e1;
            text-underline-offset: 4px;
            cursor: pointer;
        }

        .rating-count:hover {
            color: #1a1a2e;
            text-decoration-color: #1a1a2e;
        }

        .prod-price {
            font-size: 34px;
            font-weight: 800;
            color: #e74c3c;
            display: flex;
            align-items: flex-start;
            gap: 4px;
        }

        .prod-price small {
            font-size: 20px;
            margin-top: 5px;
            color: #fb7185;
        }

        .prod-desc {
            font-size: 16px;
            color: #475569;
            line-height: 1.7;
            padding-bottom: 24px;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Features / Specs */
        .prod-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 10px;
        }

        .feat-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14.5px;
            font-weight: 600;
            color: #334155;
        }

        .feat-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #1a1a2e;
        }

        /* Action Buttons */
        .prod-actions {
            display: flex;
            gap: 16px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .btn-add {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 16px 24px;
            background: #fff;
            color: #1a1a2e;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-add:hover {
            border-color: #1a1a2e;
            background: #1a1a2e;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .btn-buy {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 16px 24px;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(231,76,60,0.3);
            font-family: 'Inter', sans-serif;
        }

        .btn-buy:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(231,76,60,0.4);
        }

        /* ── REVIEWS SECTION ── */
        .reviews-section {
            margin-top: 80px;
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03);
            scroll-margin-top: 100px; /* Offset for anchor links */
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 20px;
        }

        .section-header h2 {
            font-size: 26px;
            font-weight: 800;
            color: #1a1a2e;
        }

        .section-header .overall-rating {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .overall-rating .big-num {
            font-size: 32px;
            font-weight: 800;
            color: #1a1a2e;
        }

        .overall-rating .stars-col {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .stars-col p {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }

        /* Review List */
        .reviews-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .review-card {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            padding: 24px;
            transition: all 0.2s;
        }

        .review-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            background: #fff;
        }

        .rc-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .rc-author {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rc-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a1a2e, #0f3460);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
        }

        .rc-name {
            font-size: 15px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .rc-stars {
            font-size: 14px;
            color: #f59e0b;
        }

        .rc-text {
            font-size: 14.5px;
            color: #475569;
            line-height: 1.6;
        }

        .no-reviews {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px 20px;
            background: #f8fafc;
            border-radius: 16px;
            color: #64748b;
            font-weight: 500;
        }

        /* Write Review Box (Only shown when Toggle is clicked) */
        .write-review-area {
            display: none;
            background: #f8fafc;
            border-radius: 16px;
            padding: 30px;
            border: 1px solid #e2e8f0;
            margin-top: 20px;
            animation: fadeUp 0.3s ease-out;
        }

        .write-review-area.active {
            display: block;
        }

        .write-review-area h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1a1a2e;
        }

        .review-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .review-form select,
        .review-form textarea {
            width: 100%;
            padding: 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 15px;
            background: #fff;
            color: #1a1a2e;
            transition: all 0.2s;
            outline: none;
        }

        .review-form select:focus,
        .review-form textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,0.1);
        }

        .review-form textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn-submit-rev {
            align-self: flex-start;
            padding: 14px 32px;
            background: #1a1a2e;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 4px 15px rgba(26,26,46,0.2);
            font-family: inherit;
        }

        .btn-submit-rev:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26,26,46,0.3);
            background: #0f3460;
        }

        .top-review-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 2px solid #e2e8f0;
            color: #1a1a2e;
            padding: 10px 24px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .top-review-action:hover {
            border-color: #1a1a2e;
            background: #1a1a2e;
            color: #fff;
        }

        @media (max-width: 900px) {
            .prod-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            .prod-img-box {
                position: static;
                padding: 30px;
            }
            .prod-title {
                font-size: 32px;
            }
        }

        @media (max-width: 600px) {
            .prod-page { margin-top: 100px; padding: 0 15px; }
            .prod-actions { flex-direction: column; }
            .prod-features { grid-template-columns: 1fr; }
            .reviews-section { padding: 30px 20px; }
            .write-review-area { padding: 24px 20px; }
            .btn-submit-rev { width: 100%; }
        }
    </style>
</head>
<body>

<!-- HEADER (Matching your style.css) -->
<header>
    <div class="container header-flex">
        <div class="logo">
            <h1>Clothing Adda</h1>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="cart.php">Cart</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">My Profile</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main class="prod-page">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="index.php">Home</a> <span>/</span>
        <a href="products.php">Products</a> <span>/</span>
        <strong><?= htmlspecialchars($data['name']) ?></strong>
    </div>

    <!-- PRODUCT DETAILS -->
    <div class="prod-grid">

        <!-- Image -->
        <div class="prod-img-box">
            <img src="<?= htmlspecialchars($path) ?>" alt="<?= htmlspecialchars($data['name']) ?>">
        </div>

        <!-- Info -->
        <div class="prod-info">
            
            <h1 class="prod-title"><?= htmlspecialchars($data['name']) ?></h1>

            <div class="prod-rating-row">
                <div class="rating-stars">
                    <?php
                    $full = floor($avg_rating);
                    $half = ($avg_rating - $full >= 0.5);
                    echo str_repeat("★", $full);
                    if($half) echo "⯨";
                    echo str_repeat("☆", 5 - ceil($avg_rating));
                    ?>
                </div>
                <span class="rating-badge"><?= number_format($avg_rating, 1) ?></span>
                <a href="#reviews" class="rating-count">( <?= $total_reviews ?> verified reviews )</a>
            </div>

            <div class="prod-price">
                <small>₹</small><?= number_format($data['price']) ?>
            </div>

            <p class="prod-desc">
                Elevate your everyday style with this premium piece from Clothing Adda. 
                Expertly crafted for superior comfort and durability, it features a modern 
                fit and high-quality fabric that retains its shape and smooth texture wash after wash. 
                An essential addition to any sophisticated wardrobe.
            </p>

            <div class="prod-features">
                <div class="feat-item">
                    <div class="feat-icon">✨</div>
                    Premium Quality
                </div>
                <div class="feat-item">
                    <div class="feat-icon">🚚</div>
                    Free Shipping
                </div>
                <div class="feat-item">
                    <div class="feat-icon">🔄</div>
                    30-Day Returns
                </div>
                <div class="feat-item">
                    <div class="feat-icon">🛡️</div>
                    Secure Checkout
                </div>
            </div>

            <!-- Options (Size/Color) -->
            <form method="POST" style="margin-top: 10px;">
                <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1a1a2e; margin-bottom: 8px;">Size</label>
                        <select name="size" required style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 15px; background: #fff; outline: none;">
                            <option value="">Select Size</option>
                            <option value="S">Small (S)</option>
                            <option value="M">Medium (M)</option>
                            <option value="L">Large (L)</option>
                            <option value="XL">Extra Large (XL)</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #1a1a2e; margin-bottom: 8px;">Color</label>
                        <select name="color" required style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 15px; background: #fff; outline: none;">
                            <option value="">Select Color</option>
                            <option value="Black">Black</option>
                            <option value="White">White</option>
                            <option value="Red">Red</option>
                            <option value="Blue">Blue</option>
                            <option value="Green">Green</option>
                        </select>
                    </div>
                </div>

                <!-- Actions -->
                <div class="prod-actions">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <button type="submit" name="add_to_cart" class="btn-add">
                            🛒 Add to Cart
                        </button>
                        <button type="submit" name="place_order" class="btn-buy" style="flex: 1;">
                            ⚡ Buy Now
                        </button>
                    <?php else: ?>
                        <a href="login.php" class="btn-buy" style="width: 100%;">
                            🔒 Login to Purchase
                        </a>
                    <?php endif; ?>
                </div>
            </form>

        </div>
    </div> <!-- .prod-grid -->

    <!-- REVIEWS SECTION -->
    <div class="reviews-section" id="reviews">

        <div class="section-header">
            <div>
                <h2>Customer Reviews</h2>
                <div class="overall-rating">
                    <span class="big-num"><?= number_format($avg_rating, 1) ?></span>
                    <div class="stars-col">
                        <span class="rating-stars" style="font-size: 16px;">
                            <?= str_repeat("★", floor($avg_rating)) ?><?= ($avg_rating - floor($avg_rating) >= 0.5) ? "⯨" : "" ?><?= str_repeat("☆", 5 - ceil($avg_rating)) ?>
                        </span>
                        <p>Based on <?= $total_reviews ?> reviews</p>
                    </div>
                </div>
            </div>

            <button class="top-review-action" onclick="document.getElementById('write-review').classList.toggle('active')">
                ✎ Write a Review
            </button>
        </div>

        <!-- Hidden Write Form -->
        <div class="write-review-area" id="write-review">
            <?php if(isset($_SESSION['user_id'])): ?>
                <h3>Share Your Experience</h3>
                <form method="POST" class="review-form">
                    <select name="rating" required>
                        <option value="">Select your rating...</option>
                        <option value="5">★★★★★ - Excellent! Love it.</option>
                        <option value="4">★★★★☆ - Very good, satisfied.</option>
                        <option value="3">★★★☆☆ - It's okay, average.</option>
                        <option value="2">★★☆☆☆ - Below expectations.</option>
                        <option value="1">★☆☆☆☆ - Very disappointed.</option>
                    </select>
                    <textarea name="review" placeholder="What did you like or dislike? How's the fit and quality?" required></textarea>
                    <button type="submit" name="submit_review" class="btn-submit-rev">Post Review</button>
                </form>
            <?php else: ?>
                <div style="text-align: center; padding: 20px;">
                    <p style="color: #475569; font-weight: 500; margin-bottom: 16px;">You must be logged in to write a review.</p>
                    <a href="login.php" class="btn-buy" style="padding: 10px 24px; font-size: 14px;">Log In Now</a>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 40px;"></div>

        <div class="reviews-list">
            <?php
            $review_query = mysqli_query($conn,"SELECT * FROM reviews WHERE product_id='$product_id' ORDER BY id DESC LIMIT 10");
            if(mysqli_num_rows($review_query) > 0){
                while($rev = mysqli_fetch_assoc($review_query)){
                    $f = floor($rev['rating']);
                    $h = ($rev['rating'] - $f >= 0.5);
                    $firstLetter = strtoupper(substr($rev['user_name'], 0, 1));
            ?>
                <div class="review-card">
                    <div class="rc-head">
                        <div class="rc-author">
                            <div class="rc-avatar"><?= $firstLetter ?></div>
                            <div class="rc-name"><?= htmlspecialchars($rev['user_name']) ?></div>
                        </div>
                        <div class="rc-stars">
                            <?= str_repeat("★", $f) ?><?= $h ? "⯨" : "" ?><?= str_repeat("☆", 5 - ceil($rev['rating'])) ?>
                        </div>
                    </div>
                    <p class="rc-text">"<?= nl2br(htmlspecialchars($rev['review'])) ?>"</p>
                </div>
            <?php 
                } 
            } else { 
                echo "<div class='no-reviews'>📭 No reviews yet. Be the first to share your experience!</div>"; 
            } 
            ?>
        </div>

    </div>

</main>

<!-- FOOTER -->
<footer>
    <div class="container" style="text-align: center; padding: 40px 0; color: #8892a4; font-size: 14px;">
        <p>&copy; 2026 Clothing Adda. All Rights Reserved.</p>
    </div>
</footer>

<script>
    // Smooth scroll for review anchor link
    document.querySelector('.rating-count').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector('#reviews').scrollIntoView({ behavior: 'smooth' });
    });
</script>

</body>
</html>
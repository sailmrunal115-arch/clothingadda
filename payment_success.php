<?php
session_start();
include "db.php";

/* GET PRODUCT ID */
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

/* CHECK ORDER SESSION */
if (!isset($_SESSION['last_order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = intval($_SESSION['last_order_id']);

/* UPDATE PAYMENT STATUS */
mysqli_query($conn,"
    UPDATE orders SET payment_status='paid', status='confirmed' WHERE id='$order_id'
");

/* FETCH ORDER DETAILS */
$order_res = mysqli_query($conn,"SELECT * FROM orders WHERE id='$order_id'");
if(!$order_res || mysqli_num_rows($order_res)==0){
    echo "Order not found."; exit();
}
$order = mysqli_fetch_assoc($order_res);

/* fallback product_id from order row */
if($product_id == 0 && isset($order['product_id'])){
    $product_id = intval($order['product_id']);
}

/* GET USER NAME */
$user = $_SESSION['user_name'] ?? $_SESSION['user'] ?? "Customer";

/* HANDLE REVIEW SUBMISSION */
if(isset($_POST['submit_review']) && isset($_SESSION['user_id']) && $product_id > 0){
    $uid   = $_SESSION['user_id'];
    $uname = $_SESSION['user_name'];
    $rat   = intval($_POST['rating']);
    $rev   = mysqli_real_escape_string($conn, $_POST['review']);
    mysqli_query($conn,"INSERT INTO reviews(product_id,user_id,user_name,rating,review) VALUES('$product_id','$uid','$uname','$rat','$rev')");
    header("Location: payment_success.php?product_id=$product_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Order Confirmed – Clothing Adda</title>
<link rel="stylesheet" href="style.css">
<style>
body { background:#fafafa; }

/* ── page wrapper ── */
.success-page{
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding: 60px 20px 80px;
}

/* ── main card ── */
.success-card{
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.10);
    padding: 50px 48px 44px;
    max-width: 520px;
    width: 100%;
    text-align: center;
    animation: fadeUp .5s ease both;
}
@keyframes fadeUp{
    from{ opacity:0; transform:translateY(20px); }
    to{   opacity:1; transform:translateY(0); }
}

/* animated green circle */
.check-wrap{
    width: 80px; height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg,#27ae60,#2ecc71);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 22px;
    box-shadow: 0 6px 20px rgba(46,204,113,.35);
    animation: popIn .45s cubic-bezier(.175,.885,.32,1.275) .2s both;
}
@keyframes popIn{
    from{ transform:scale(0); }
    to{   transform:scale(1); }
}
.check-wrap svg{ width:36px; height:36px; stroke:#fff; stroke-width:3; fill:none; stroke-linecap:round; stroke-linejoin:round; }

.success-card h1{
    font-size: 26px;
    font-weight: 800;
    color: #222;
    margin-bottom: 8px;
}
.success-card .sub{
    color: #666;
    font-size: 15px;
    margin-bottom: 28px;
}
.success-card .sub strong{ color: #e74c3c; }

/* info rows */
.info-row{
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 11px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 14px;
    color: #555;
}
.info-row:last-of-type{ border-bottom: none; }
.info-row .label{ font-weight: 600; color: #333; }
.info-row .value{ color: #444; }
.info-row .value.green{ color: #27ae60; font-weight: 700; }
.info-row .value.red{ color: #e74c3c; font-weight: 700; font-size: 16px; }

.info-block{ margin-bottom: 28px; }

.shop-btn{
    display: inline-block;
    background: #e74c3c;
    color: #fff;
    padding: 13px 36px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    transition: background .25s, transform .2s;
    box-shadow: 0 4px 14px rgba(231,76,60,.3);
}
.shop-btn:hover{ background:#c0392b; transform:translateY(-2px); }

/* ── review card ── */
.review-card{
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.09);
    padding: 36px 40px;
    max-width: 520px; width: 100%;
    margin-top: 24px;
    text-align: left;
    animation: fadeUp .5s ease .15s both;
}
.review-card h3{
    font-size: 18px; font-weight: 700; color: #222;
    margin-bottom: 4px;
}
.review-card .rc-sub{
    font-size: 13px; color: #999; margin-bottom: 22px;
}

/* clickable star picker */
.star-picker{ display:flex; gap:4px; margin-bottom:14px; flex-direction:row-reverse; justify-content:flex-end; }
.star-picker input{ display:none; }
.star-picker label{ font-size:32px; cursor:pointer; color:#ddd; transition:color .15s; line-height:1; }
.star-picker input:checked ~ label,
.star-picker label:hover,
.star-picker label:hover ~ label{ color:#f1c40f; }

.rev-textarea{
    width:100%; padding:11px 14px;
    border:1px solid #ddd; border-radius:8px;
    font-family:Arial,sans-serif; font-size:14px; color:#333;
    resize:none; height:88px; outline:none;
    transition:border .2s;
    margin-bottom:14px;
}
.rev-textarea::placeholder{ color:#bbb; }
.rev-textarea:focus{ border-color:#e74c3c; box-shadow:0 0 0 3px rgba(231,76,60,.1); }

.submit-rev-btn{
    width:100%; padding:12px;
    background:#e74c3c; color:#fff;
    border:none; border-radius:8px;
    font-size:15px; font-weight:700; cursor:pointer;
    transition:background .25s, transform .2s;
}
.submit-rev-btn:hover{ background:#c0392b; transform:translateY(-1px); }

.divider{ height:1px; background:#f0f0f0; margin:22px 0; }
.sec-label{ font-size:12px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#aaa; margin-bottom:14px; }

/* past reviews */
.rev-item{ display:flex; gap:12px; padding:12px 0; border-bottom:1px solid #f4f4f4; }
.rev-item:last-child{ border-bottom:none; }
.rev-avatar{
    width:38px; height:38px; border-radius:50%; flex-shrink:0;
    background:linear-gradient(135deg,#e74c3c,#c0392b);
    display:flex; align-items:center; justify-content:center;
    font-size:15px; font-weight:700; color:#fff;
}
.rev-body{ flex:1; }
.rev-name{ font-size:14px; font-weight:600; color:#333; margin-bottom:2px; }
.rev-stars{ font-size:13px; color:#f1c40f; margin-bottom:3px; }
.rev-text{ font-size:13px; color:#666; line-height:1.5; }

.login-note{ font-size:14px; color:#999; margin-top:10px; }
.login-note a{ color:#e74c3c; font-weight:600; text-decoration:none; }
.login-note a:hover{ text-decoration:underline; }

@media(max-width:560px){
    .success-card, .review-card{ padding:32px 22px; }
}
</style>
</head>
<body>
<div class="success-page">

    <!-- ── SUCCESS CARD ── -->
    <div class="success-card">

        <div class="check-wrap">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>

        <h1>Order Confirmed! 🎉</h1>
        <p class="sub">Thank you, <strong><?php echo htmlspecialchars($user); ?></strong>! We've received your order.</p>

        <div class="info-block">
            <div class="info-row">
                <span class="label">Order ID</span>
                <span class="value">#<?php echo $order['id']; ?></span>
            </div>
            <?php if(!empty($order['total_price'])): ?>
            <div class="info-row">
                <span class="label">Total Amount</span>
                <span class="value red">₹<?php echo number_format($order['total_price'],2); ?></span>
            </div>
            <?php endif; ?>
            <?php if(!empty($order['payment_method'])): ?>
            <div class="info-row">
                <span class="label">Payment</span>
                <span class="value"><?php echo htmlspecialchars($order['payment_method']); ?></span>
            </div>
            <?php endif; ?>
            <div class="info-row">
                <span class="label">Status</span>
                <span class="value green">✓ Confirmed</span>
            </div>
        </div>

        <a href="index.php" class="shop-btn">🛍️ Continue Shopping</a>
    </div>

    <?php if($product_id > 0): ?>
    <!-- ── REVIEW CARD ── -->
    <div class="review-card">

        <h3>⭐ Rate Your Purchase</h3>
        <p class="rc-sub">Share your experience to help other shoppers.</p>

        <?php if(isset($_SESSION['user_id'])): ?>
        <form method="POST" action="payment_success.php?product_id=<?php echo $product_id; ?>">
            <div class="star-picker">
                <input type="radio" name="rating" id="s5" value="5"><label for="s5">★</label>
                <input type="radio" name="rating" id="s4" value="4"><label for="s4">★</label>
                <input type="radio" name="rating" id="s3" value="3"><label for="s3">★</label>
                <input type="radio" name="rating" id="s2" value="2"><label for="s2">★</label>
                <input type="radio" name="rating" id="s1" value="1"><label for="s1">★</label>
            </div>
            <textarea name="review" class="rev-textarea" placeholder="What did you love? Quality, fit, delivery…" required></textarea>
            <button type="submit" name="submit_review" class="submit-rev-btn">Submit Review</button>
        </form>
        <?php else: ?>
        <p class="login-note"><a href="login.php">Login</a> to leave a review.</p>
        <?php endif; ?>

        <?php
        $rq = mysqli_query($conn,"SELECT * FROM reviews WHERE product_id='$product_id' ORDER BY id DESC LIMIT 5");
        if(mysqli_num_rows($rq) > 0):
        ?>
        <div class="divider"></div>
        <div class="sec-label">What others are saying</div>
        <?php while($rv = mysqli_fetch_assoc($rq)):
            $stars   = floor($rv['rating']);
            $initial = strtoupper(mb_substr($rv['user_name'],0,1));
        ?>
        <div class="rev-item">
            <div class="rev-avatar"><?php echo htmlspecialchars($initial); ?></div>
            <div class="rev-body">
                <div class="rev-name"><?php echo htmlspecialchars($rv['user_name']); ?></div>
                <div class="rev-stars"><?php echo str_repeat("★",$stars); ?></div>
                <div class="rev-text"><?php echo htmlspecialchars($rv['review']); ?></div>
            </div>
        </div>
        <?php endwhile; endif; ?>

    </div>
    <?php endif; ?>

</div>
</body>
</html>
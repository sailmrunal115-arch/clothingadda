<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])){
    header("Location: orders.php");
    exit();
}

$order_id = intval($_GET['id']);
$result = mysqli_query($conn,"SELECT * FROM orders WHERE id='$order_id'");
$order = mysqli_fetch_assoc($result);

if(!$order){
    header("Location: orders.php");
    exit();
}

$status = strtolower($order['status'] ?? 'pending');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= $order['id'] ?> | Clothing Adda</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; }

        .od-page {
            min-height: 100vh;
            padding: 108px 0 70px;
        }

        .od-wrap {
            max-width: 780px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Back row */
        .od-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #fff;
            border: 1.5px solid #eef0f4;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            text-decoration: none;
            margin-bottom: 24px;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .od-back:hover { border-color: #e74c3c; color: #e74c3c; transform: translateX(-3px); }

        /* Main card */
        .od-card {
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            animation: fadeUp 0.5s ease-out;
        }

        /* Gradient header band */
        .od-hero {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
            padding: 36px 36px 32px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .od-hero::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(231,76,60,0.15);
        }

        .od-hero::after {
            content: '';
            position: absolute;
            bottom: -40px; left: -30px;
            width: 150px; height: 150px;
            border-radius: 50%;
            background: rgba(59,130,246,0.1);
        }

        .od-hero-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .od-title {
            font-size: 13px;
            color: rgba(255,255,255,0.5);
            margin-bottom: 4px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .od-order-num {
            font-size: 28px;
            font-weight: 800;
        }

        /* Status pill */
        .od-status {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .od-status.pending   { background: rgba(251,191,36,0.2);  color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); }
        .od-status.completed { background: rgba(52,211,153,0.2);  color: #34d399; border: 1px solid rgba(52,211,153,0.3); }
        .od-status.delivered { background: rgba(52,211,153,0.2);  color: #34d399; border: 1px solid rgba(52,211,153,0.3); }
        .od-status.cancelled { background: rgba(248,113,113,0.2); color: #f87171; border: 1px solid rgba(248,113,113,0.3); }

        .od-price-hero {
            position: relative;
            z-index: 1;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .od-price-label {
            font-size: 13px;
            color: rgba(255,255,255,0.5);
            margin-bottom: 4px;
        }

        .od-price-amount {
            font-size: 40px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }

        .od-price-amount span {
            font-size: 22px;
            font-weight: 600;
            color: rgba(255,255,255,0.7);
            margin-right: 2px;
        }

        /* Body area */
        .od-body {
            padding: 32px 36px;
        }

        /* Section heading */
        .od-section-title {
            font-size: 12px;
            font-weight: 700;
            color: #aab4c3;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        /* Info grid */
        .od-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 32px;
        }

        .info-tile {
            background: #f8fafc;
            border: 1px solid #eef0f4;
            border-radius: 14px;
            padding: 18px 20px;
            transition: all 0.2s;
        }

        .info-tile:hover {
            border-color: #e74c3c;
            box-shadow: 0 4px 14px rgba(231,76,60,0.08);
        }

        .info-tile .tile-label {
            font-size: 12px;
            color: #8892a4;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .info-tile .tile-value {
            font-size: 15.5px;
            font-weight: 600;
            color: #1a1a2e;
        }

        .info-tile .tile-icon {
            font-size: 22px;
            margin-bottom: 10px;
        }

        /* Products section */
        .od-products {
            background: #f8fafc;
            border: 1px solid #eef0f4;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 28px;
        }

        .od-products .product-line {
            font-size: 15px;
            font-weight: 500;
            color: #1a1a2e;
            line-height: 1.7;
        }

        /* Status timeline */
        .od-timeline {
            margin-bottom: 32px;
        }

        .timeline-steps {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .t-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .t-step::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #eef0f4;
            z-index: 0;
        }

        .t-step:last-child::after { display: none; }

        .t-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #eef0f4;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            position: relative;
            z-index: 1;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .t-dot.active {
            background: linear-gradient(135deg, #ff512f, #dd2476);
            box-shadow: 0 4px 14px rgba(231,76,60,0.3);
        }

        .t-dot.done {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            box-shadow: 0 4px 14px rgba(34,197,94,0.3);
        }

        .t-dot.cancelled-dot {
            background: linear-gradient(135deg, #f87171, #ef4444);
        }

        .t-step::after {
            background: #eef0f4;
        }

        .t-step.done::after { background: linear-gradient(90deg, #22c55e, #eef0f4); }

        .t-label {
            font-size: 11.5px;
            font-weight: 600;
            color: #aab4c3;
            margin-top: 8px;
            text-align: center;
        }

        .t-label.active-label { color: #e74c3c; }
        .t-label.done-label   { color: #16a34a; }

        /* Action area */
        .od-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .od-btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 28px;
            background: linear-gradient(135deg, #1a1a2e, #0f3460);
            color: #fff;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s;
            box-shadow: 0 4px 16px rgba(26,26,46,0.25);
            font-family: 'Inter', sans-serif;
        }

        .od-btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(26,26,46,0.35);
        }

        .od-btn-shop {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 28px;
            background: linear-gradient(135deg, #ff512f, #dd2476);
            color: #fff;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s;
            box-shadow: 0 4px 16px rgba(231,76,60,0.25);
            font-family: 'Inter', sans-serif;
        }

        .od-btn-shop:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(231,76,60,0.4);
        }

        @media (max-width: 600px) {
            .od-hero { padding: 28px 22px 24px; }
            .od-body { padding: 24px 22px; }
            .od-info-grid { grid-template-columns: 1fr; }
            .od-order-num { font-size: 22px; }
            .od-price-amount { font-size: 32px; }
            .timeline-steps { flex-direction: column; gap: 10px; align-items: flex-start; }
            .t-step::after { display: none; }
        }
    </style>
</head>
<body>

<!-- HEADER -->
<header>
    <div class="container header-flex">
        <div class="logo"><h1>Clothing Adda</h1></div>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="od-page">
    <div class="od-wrap">

        <a href="orders.php" class="od-back">← Back to Orders</a>

        <div class="od-card">

            <!-- Hero band -->
            <div class="od-hero">
                <div class="od-hero-top">
                    <div>
                        <p class="od-title">Order Reference</p>
                        <h1 class="od-order-num">#<?= $order['id'] ?></h1>
                    </div>
                    <?php
                    $statusIcon = match($status) {
                        'delivered','completed' => '✅',
                        'cancelled'             => '❌',
                        default                 => '🕐'
                    };
                    ?>
                    <span class="od-status <?= $status ?>">
                        <?= $statusIcon ?> <?= ucfirst($status) ?>
                    </span>
                </div>

                <div class="od-price-hero">
                    <p class="od-price-label">Total Amount Paid</p>
                    <div class="od-price-amount">
                        <span>₹</span><?= number_format($order['total_price']) ?>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="od-body">

                <!-- Timeline -->
                <?php if($status !== 'cancelled'): ?>
                <div class="od-timeline">
                    <p class="od-section-title">Order Progress</p>
                    <?php
                    $steps = ['placed' => '📋', 'confirmed' => '✔️', 'shipped' => '🚚', 'delivered' => '🏠'];
                    $stepNames = ['placed' => 'Placed', 'confirmed' => 'Confirmed', 'shipped' => 'Shipped', 'delivered' => 'Delivered'];
                    $reached = false;
                    ?>
                    <div class="timeline-steps">
                    <?php foreach($steps as $key => $icon):
                        $isDone   = ($status === 'delivered' || $status === 'completed') ||
                                    ($key === 'placed') ||
                                    ($key === 'confirmed' && in_array($status, ['confirmed','shipped','delivered'])) ||
                                    ($key === 'shipped'    && in_array($status, ['shipped','delivered']));
                        $isActive = ($key === $status || ($key === 'placed' && $status === 'pending'));
                    ?>
                        <div class="t-step <?= $isDone ? 'done' : '' ?>">
                            <div class="t-dot <?= $isDone ? 'done' : ($isActive ? 'active' : '') ?>">
                                <?= $icon ?>
                            </div>
                            <span class="t-label <?= $isDone ? 'done-label' : ($isActive ? 'active-label' : '') ?>">
                                <?= $stepNames[$key] ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Info tiles -->
                <p class="od-section-title">Order Information</p>
                <div class="od-info-grid">
                    <div class="info-tile">
                        <div class="tile-icon">📅</div>
                        <p class="tile-label">Order Date</p>
                        <p class="tile-value"><?= date("d M Y", strtotime($order['created_at'])) ?></p>
                    </div>
                    <div class="info-tile">
                        <div class="tile-icon">🕐</div>
                        <p class="tile-label">Order Time</p>
                        <p class="tile-value"><?= date("g:i A", strtotime($order['created_at'])) ?></p>
                    </div>
                    <div class="info-tile">
                        <div class="tile-icon">💳</div>
                        <p class="tile-label">Payment</p>
                        <p class="tile-value"><?= ucfirst($order['payment_method'] ?? 'Online') ?></p>
                    </div>
                    <div class="info-tile">
                        <div class="tile-icon">📦</div>
                        <p class="tile-label">Order Status</p>
                        <p class="tile-value"><?= ucfirst($status) ?></p>
                    </div>
                </div>

                <!-- Products -->
                <p class="od-section-title">Products Ordered</p>
                <div class="od-products">
                    <p class="product-line"><?= nl2br(htmlspecialchars($order['products'])) ?></p>
                </div>

                <!-- Actions -->
                <div class="od-actions">
                    <a href="orders.php" class="od-btn-back">📋 All Orders</a>
                    <a href="index.php" class="od-btn-shop">🛍️ Continue Shopping</a>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p>&copy; 2026 Clothing Adda. All Rights Reserved.</p>
    </div>
</footer>

</body>
</html>
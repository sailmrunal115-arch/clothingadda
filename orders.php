<?php
session_start();
include "db.php";

/* ===== CANCEL ORDER ===== */
if(isset($_GET['cancel'])){
    $order_id = intval($_GET['cancel']);
    mysqli_query($conn,"UPDATE orders SET status='cancelled' WHERE id='$order_id'");
    header("Location: orders.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

$result = mysqli_query($conn,"
SELECT * FROM orders
WHERE user_email='$user_email'
ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Clothing Adda</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; }

        .orders-page {
            min-height: 100vh;
            padding: 108px 0 70px;
        }

        .orders-wrap {
            max-width: 860px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Page title bar */
        .page-titlebar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-titlebar h1 {
            font-size: 28px;
            font-weight: 800;
            color: #1a1a2e;
        }

        .page-titlebar h1 span {
            background: linear-gradient(135deg, #ff512f, #dd2476);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .titlebar-back {
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
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .titlebar-back:hover {
            border-color: #e74c3c;
            color: #e74c3c;
            transform: translateX(-2px);
        }

        /* Order card */
        .order-card {
            background: #fff;
            border-radius: 20px;
            padding: 0;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            overflow: hidden;
            transition: all 0.3s;
            animation: fadeUp 0.5s ease-out backwards;
        }

        .order-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 36px rgba(0,0,0,0.1);
        }

        /* Card top strip */
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            background: #fafbfd;
            border-bottom: 1px solid #f0f2f5;
        }

        .card-header .order-id {
            font-size: 13px;
            font-weight: 600;
            color: #8892a4;
        }

        .card-header .order-id strong {
            color: #1a1a2e;
            font-size: 15px;
        }

        .card-header .order-date {
            font-size: 13px;
            color: #aab4c3;
        }

        /* Status badge */
        .order-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .order-badge.pending   { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .order-badge.completed { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .order-badge.delivered { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .order-badge.cancelled { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }

        /* Card body */
        .card-body {
            padding: 20px 24px;
        }

        .product-label {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 4px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-sub {
            font-size: 13px;
            color: #8892a4;
        }

        /* Info row */
        .info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 18px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .order-amount {
            font-size: 22px;
            font-weight: 800;
            color: #e74c3c;
        }

        .order-amount small {
            font-size: 13px;
            font-weight: 500;
            color: #aab4c3;
            margin-right: 3px;
        }

        /* Action buttons */
        .card-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-view {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #fff;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 3px 10px rgba(59,130,246,0.25);
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(59,130,246,0.35);
        }

        .btn-cancel {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            background: #fff;
            color: #e11d48;
            border: 1.5px solid #fecdd3;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #fff1f2;
            border-color: #e11d48;
            transform: translateY(-2px);
        }

        /* Animation stagger */
        .order-card:nth-child(1) { animation-delay: 0s; }
        .order-card:nth-child(2) { animation-delay: 0.07s; }
        .order-card:nth-child(3) { animation-delay: 0.14s; }
        .order-card:nth-child(4) { animation-delay: 0.21s; }
        .order-card:nth-child(5) { animation-delay: 0.28s; }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        .empty-state .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
            display: block;
        }

        .empty-state h3 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: #8892a4;
            margin-bottom: 28px;
        }

        .empty-state .btn {
            display: inline-block;
            padding: 14px 36px;
            background: linear-gradient(135deg, #ff512f, #dd2476);
            color: #fff;
            border-radius: 30px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            box-shadow: 0 6px 22px rgba(231,76,60,0.3);
            transition: all 0.3s;
        }

        .empty-state .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(231,76,60,0.4);
        }

        @media (max-width: 600px) {
            .card-header { flex-direction: column; align-items: flex-start; gap: 8px; }
            .info-row { flex-direction: column; align-items: flex-start; }
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
                <li><a href="profile.php">Profile</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="orders-page">
    <div class="orders-wrap">

        <div class="page-titlebar">
            <h1>My <span>Orders</span></h1>
            <a href="index.php" class="titlebar-back">← Back to Shop</a>
        </div>

        <?php if(mysqli_num_rows($result) > 0): ?>

            <?php while($row = mysqli_fetch_assoc($result)):
                $status = strtolower($row['status'] ?? 'pending');
                $statusLabel = ucfirst($status);
                $statusIcon = match($status) {
                    'delivered','completed' => '✅',
                    'cancelled'             => '❌',
                    default                 => '🕐'
                };
            ?>

            <div class="order-card">
                <div class="card-header">
                    <div class="order-id">
                        Order <strong>#<?= $row['id'] ?></strong>
                    </div>
                    <span class="order-badge <?= $status ?>">
                        <?= $statusIcon ?> <?= $statusLabel ?>
                    </span>
                </div>

                <div class="card-body">
                    <div class="product-label"><?= htmlspecialchars($row['products']) ?></div>
                    <p class="product-sub">📅 <?= date("d M Y, g:i A", strtotime($row['created_at'])) ?></p>

                    <div class="info-row">
                        <div class="order-amount">
                            <small>₹</small><?= number_format($row['total_price']) ?>
                        </div>
                        <div class="card-actions">
                            <a href="order_details.php?id=<?= $row['id'] ?>" class="btn-view">
                                🔍 View Details
                            </a>
                            <?php if($status != 'cancelled' && $status != 'delivered' && $status != 'completed'): ?>
                            <a href="orders.php?cancel=<?= $row['id'] ?>"
                               onclick="return confirm('Cancel this order?')"
                               class="btn-cancel">
                                ✕ Cancel
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="empty-state">
                <span class="empty-icon">🛍️</span>
                <h3>No orders yet!</h3>
                <p>Looks like you haven't placed any orders. Start shopping now.</p>
                <a href="index.php" class="btn">Explore Products</a>
            </div>

        <?php endif; ?>

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
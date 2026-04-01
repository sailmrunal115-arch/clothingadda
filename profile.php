<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = intval($_SESSION['user_id']);
$result = mysqli_query($conn,"SELECT * FROM users WHERE id=$id");

if(mysqli_num_rows($result) == 0){
    echo "User not found";
    exit();
}

$user = mysqli_fetch_assoc($result);
$firstLetter = strtoupper(substr($user['name'], 0, 1));

/* Total orders count */
$orderRes = mysqli_query($conn,"SELECT COUNT(*) as cnt FROM orders WHERE user_email='".$user['email']."'");
$orderCount = mysqli_fetch_assoc($orderRes)['cnt'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Clothing Adda</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; }

        /* ── PAGE WRAPPER ── */
        .profile-page {
            min-height: 100vh;
            padding: 110px 0 60px;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f2f5 100%);
        }

        .profile-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 28px;
            align-items: start;
        }

        /* ── SIDEBAR ── */
        .profile-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Avatar Card */
        .avatar-card {
            background: #fff;
            border-radius: 24px;
            padding: 36px 24px;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            animation: fadeUp 0.5s ease-out;
        }

        .big-avatar {
            width: 100px;
            height: 100px;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff512f, #dd2476);
            color: #fff;
            font-size: 42px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 28px rgba(221,36,118,0.35);
            position: relative;
        }

        .big-avatar::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 3px solid transparent;
            background: linear-gradient(135deg, #ff512f, #dd2476) border-box;
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: destination-out;
            mask-composite: exclude;
        }

        .avatar-card h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .avatar-card .user-email {
            font-size: 13.5px;
            color: #8892a4;
            margin-bottom: 20px;
        }

        .member-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #fff5f5, #ffe0e0);
            color: #e74c3c;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 600;
            border: 1px solid #ffc5c5;
        }

        /* Stats Card */
        .stats-card {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            animation: fadeUp 0.5s ease-out 0.1s backwards;
        }

        .stats-card h4 {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #aab4c3;
            margin-bottom: 18px;
            font-weight: 600;
        }

        .stat-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid #f4f6f8;
        }

        .stat-row:last-child { border-bottom: none; }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .stat-icon.orders { background: #fffbeb; color: #f59e0b; }
        .stat-icon.member { background: #f0fdf4; color: #22c55e; }

        .stat-info p { font-size: 13px; color: #8892a4; margin-bottom: 2px; }
        .stat-info strong { font-size: 18px; color: #1a1a2e; font-weight: 700; }

        /* Quick Links */
        .quick-links {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            animation: fadeUp 0.5s ease-out 0.2s backwards;
        }

        .quick-links a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 22px;
            color: #4a5568;
            font-size: 14.5px;
            font-weight: 500;
            transition: all 0.2s;
            border-bottom: 1px solid #f7f8fa;
            text-decoration: none;
        }

        .quick-links a:last-child { border-bottom: none; color: #e74c3c; }
        .quick-links a:hover { background: #fafbff; color: #e74c3c; padding-left: 26px; }

        .quick-links a .qicon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #f4f6f8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        /* ── MAIN FORM AREA ── */
        .profile-main {
            animation: fadeUp 0.5s ease-out 0.15s backwards;
        }

        .form-card {
            background: #fff;
            border-radius: 24px;
            padding: 38px 40px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        }

        .form-card-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 32px;
            padding-bottom: 22px;
            border-bottom: 1px solid #f0f2f5;
        }

        .form-card-header .header-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #ff512f, #dd2476);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
        }

        .form-card-header h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .form-card-header p {
            font-size: 13.5px;
            color: #8892a4;
        }

        /* 2-col grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px 28px;
        }

        .form-grid .full { grid-column: 1 / -1; }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field-group label {
            font-size: 13px;
            font-weight: 600;
            color: #5a6478;
            letter-spacing: 0.3px;
        }

        .field-group .field-wrap {
            position: relative;
        }

        .field-prefix {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            color: #c0c7d4;
            pointer-events: none;
        }

        .field-group input,
        .field-group select,
        .field-group textarea {
            width: 100%;
            padding: 14px 14px 14px 42px;
            border: 2px solid #eef0f4;
            border-radius: 12px;
            font-size: 14.5px;
            font-family: 'Inter', sans-serif;
            color: #1a1a2e;
            background: #fafbfd;
            transition: all 0.25s;
            outline: none;
        }

        .field-group textarea { padding-top: 14px; resize: vertical; min-height: 100px; }

        .field-group input:focus,
        .field-group select:focus,
        .field-group textarea:focus {
            border-color: #e74c3c;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(231,76,60,0.08);
        }

        .field-group input:focus + .field-border,
        .field-group select:focus + .field-border{ opacity:1; }

        /* Save button */
        .save-btn {
            margin-top: 30px;
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #ff512f, #dd2476);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            letter-spacing: 0.4px;
            transition: all 0.3s;
            box-shadow: 0 6px 22px rgba(231,76,60,0.3);
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(231,76,60,0.4);
        }

        /* Success alert */
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .profile-wrapper { grid-template-columns: 1fr; }
            .profile-sidebar { flex-direction: row; flex-wrap: wrap; }
            .avatar-card, .stats-card, .quick-links { flex: 1; min-width: 260px; }
        }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-card { padding: 24px 18px; }
            .profile-sidebar { flex-direction: column; }
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

<div class="profile-page">
    <div class="profile-wrapper">

        <!-- ── SIDEBAR ── -->
        <aside class="profile-sidebar">

            <!-- Avatar Card -->
            <div class="avatar-card">
                <div class="big-avatar"><?= $firstLetter ?></div>
                <h2><?= htmlspecialchars($user['name']) ?></h2>
                <p class="user-email"><?= htmlspecialchars($user['email']) ?></p>
                <span class="member-badge">⭐ Valued Member</span>
            </div>

            <!-- Stats -->
            <div class="stats-card">
                <h4>Overview</h4>
                <div class="stat-row">
                    <div class="stat-icon orders">🛍️</div>
                    <div class="stat-info">
                        <p>Total Orders</p>
                        <strong><?= $orderCount ?></strong>
                    </div>
                </div>
                <div class="stat-row">
                    <div class="stat-icon member">✅</div>
                    <div class="stat-info">
                        <p>Account Status</p>
                        <strong>Active</strong>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="quick-links">
                <a href="orders.php">
                    <span class="qicon">📦</span> My Orders
                </a>
                <a href="cart.php">
                    <span class="qicon">🛒</span> View Cart
                </a>
                <a href="index.php">
                    <span class="qicon">🏠</span> Back to Shop
                </a>
                <a href="logout.php">
                    <span class="qicon">🚪</span> Logout
                </a>
            </div>

        </aside>

        <!-- ── MAIN FORM ── -->
        <main class="profile-main">
            <div class="form-card">

                <div class="form-card-header">
                    <div class="header-icon">👤</div>
                    <div>
                        <h2>Edit Profile</h2>
                        <p>Update your personal information</p>
                    </div>
                </div>

                <?php if(isset($_GET['success'])): ?>
                <div class="alert-success">✅ Profile updated successfully!</div>
                <?php endif; ?>

                <form method="POST" action="update_profile.php">
                    <div class="form-grid">

                        <div class="field-group">
                            <label>Full Name</label>
                            <div class="field-wrap">
                                <span class="field-prefix">👤</span>
                                <input type="text" name="name"
                                       value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                        </div>

                        <div class="field-group">
                            <label>Email Address</label>
                            <div class="field-wrap">
                                <span class="field-prefix">✉️</span>
                                <input type="email" name="email"
                                       value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                        </div>

                        <div class="field-group">
                            <label>Phone Number</label>
                            <div class="field-wrap">
                                <span class="field-prefix">📞</span>
                                <input type="text" name="phone"
                                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="field-group">
                            <label>Pincode</label>
                            <div class="field-wrap">
                                <span class="field-prefix">📍</span>
                                <input type="text" name="pincode"
                                       value="<?= htmlspecialchars($user['pincode'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="field-group">
                            <label>Gender</label>
                            <div class="field-wrap">
                                <span class="field-prefix">⚧</span>
                                <select name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male"   <?= (($user['gender'] ?? '') == 'Male')   ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= (($user['gender'] ?? '') == 'Female') ? 'selected' : '' ?>>Female</option>
                                    <option value="Other"  <?= (($user['gender'] ?? '') == 'Other')  ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="field-group full">
                            <label>Delivery Address</label>
                            <div class="field-wrap">
                                <textarea name="address" style="padding-left:14px;"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            </div>
                        </div>

                    </div>

                    <button type="submit" class="save-btn">💾 Save Changes</button>
                </form>

            </div>
        </main>

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
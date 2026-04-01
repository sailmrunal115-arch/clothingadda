<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$initial = strtoupper(substr($admin_username, 0, 1));

$message = "";
$msg_type = "";

if (isset($_POST['update_profile'])) {
    $new_username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // fetch current admin details
    $query = mysqli_query($conn, "SELECT password FROM admins WHERE id='$admin_id'");
    $admin = mysqli_fetch_assoc($query);

    if (password_verify($current_password, $admin['password'])) {
        // If they want to change password
        if (!empty($new_password)) {
            if ($new_password === $confirm_password) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE admins SET username='$new_username', password='$hashed' WHERE id='$admin_id'");
                $_SESSION['admin_username'] = $new_username;
                $message = "Profile and password successfully secured!";
                $msg_type = "success";
            } else {
                $message = "Your new passwords did not match. Please try again.";
                $msg_type = "error";
            }
        } else {
            // Only updating username
            mysqli_query($conn, "UPDATE admins SET username='$new_username' WHERE id='$admin_id'");
            $_SESSION['admin_username'] = $new_username;
            $message = "Account information successfully updated!";
            $msg_type = "success";
        }
        // Update local variables for header display
        $admin_username = $_SESSION['admin_username'];
        $initial = strtoupper(substr($admin_username, 0, 1));
    } else {
        $message = "The current password you entered is incorrect.";
        $msg_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile | Clothing Adda</title>
    <link rel="stylesheet" href="admin_style.css">
    <style>
        .profile-page-wrapper {
            display: flex;
            gap: 30px;
            max-width: 1000px;
            margin-top: 10px;
            align-items: flex-start;
        }

        /* Left Side: Avatar Card */
        .profile-side-card {
            width: 320px;
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #eef0f4;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            overflow: hidden;
            position: relative;
        }

        .side-cover {
            height: 120px;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            position: relative;
        }

        /* Subtle abstract shapes on cover */
        .side-cover::before {
            content: '';
            position: absolute;
            top: 20px; left: -20px; width: 100px; height: 100px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .side-cover::after {
            content: '';
            position: absolute;
            bottom: -30px; right: 20px; width: 80px; height: 80px;
            background: rgba(231,76,60,0.2);
            border-radius: 50%;
        }

        .side-avatar-wrapper {
            position: absolute;
            top: 70px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #fff;
            padding: 5px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .side-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 36px;
            font-weight: 800;
        }

        .side-info {
            padding: 65px 24px 30px;
            text-align: center;
        }

        .side-name {
            font-size: 22px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }

        .side-role {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .side-stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding-top: 20px;
            border-top: 1px solid #eef0f4;
        }

        .stat-item {
            text-align: center;
        }

        .stat-num {
            display: block;
            font-size: 18px;
            font-weight: 800;
            color: #1a1a2e;
        }

        .stat-label {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
        }


        /* Right Side: Form Content */
        .profile-main-card {
            flex: 1;
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #eef0f4;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            padding: 35px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title svg {
            width: 22px; height: 22px;
            fill: #e74c3c;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 28px;
            font-weight: 600;
            font-size: 14.5px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .alert svg { width: 20px; height: 20px; flex-shrink: 0; }
        .alert.success { background: #f0fdf4; color: #166534; border: 1px solid #bcefd0; }
        .alert.success svg { fill: #166534; }
        .alert.error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .alert.error svg { fill: #991b1b; }

        .divider {
            height: 1px;
            background: #eef0f4;
            margin: 35px 0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn-update {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #fff;
            padding: 15px 30px;
            border-radius: 14px;
            border: none;
            font-size: 15.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 6px 15px rgba(15,23,42,0.2);
            float: right;
            margin-top: 10px;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(15,23,42,0.3);
            background: linear-gradient(135deg, #0f172a, #000000);
        }

        /* Override input fields to be slightly more premium in profile */
        input[type="text"], input[type="password"] {
            border-radius: 10px;
            border: 2px solid #f1f5f9;
            background: #f8fafc;
            font-weight: 500;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            background: #fff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,0.1);
        }

        @media (max-width: 900px) {
            .profile-page-wrapper {
                flex-direction: column;
            }
            .profile-side-card { width: 100%; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-brand">
                <div class="brand-icon">C</div>
                Clothing Adda
            </a>
        </div>
        <div class="sidebar-menu">
            <a href="index.php" class="menu-item">
                <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                Dashboard
            </a>
            <a href="orders.php" class="menu-item">
                <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                Orders
            </a>
            <a href="products.php" class="menu-item">
                <svg viewBox="0 0 24 24"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/></svg>
                Products
            </a>
            <a href="add_product.php" class="menu-item">
                <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                Add Product
            </a>
        </div>
        <div class="logout-container">
            <a href="logout.php" class="logout-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                Logout
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <header class="top-header">
            <div class="page-title">Personal Settings</div>
            <a href="profile.php" class="admin-profile" style="text-decoration:none;">
                <span><?= htmlspecialchars($admin_username) ?></span>
                <div class="admin-avatar"><?= $initial ?></div>
            </a>
        </header>

        <div class="content-wrapper">
            
            <div class="profile-page-wrapper">

                <!-- Left Column -->
                <div class="profile-side-card">
                    <div class="side-cover"></div>
                    <div class="side-avatar-wrapper">
                        <div class="side-avatar"><?= $initial ?></div>
                    </div>
                    <div class="side-info">
                        <div class="side-name"><?= htmlspecialchars($admin_username) ?></div>
                        <div class="side-role">Super Admin ✨</div>
                        
                        <div class="side-stats">
                            <div class="stat-item">
                                <span class="stat-num">🟢</span>
                                <span class="stat-label">Status</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-num">System</span>
                                <span class="stat-label">Access</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="profile-main-card">

                    <?php if($message): ?>
                        <div class="alert <?= $msg_type ?>">
                            <?php if($msg_type == 'success'): ?>
                                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            <?php else: ?>
                                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                            <?php endif; ?>
                            <span><?= $message ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="section-title">
                            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            Basic Details
                        </div>
                        
                        <div class="form-group">
                            <label>Administration Username</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($admin_username) ?>" required autocomplete="off">
                            <p style="font-size: 13px; color: #94a3b8; margin-top: 6px;">This is the public handle used to log into the dashboard.</p>
                        </div>

                        <div class="divider"></div>

                        <div class="section-title">
                            <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/></svg>
                            Security & Authentication
                        </div>

                        <div class="form-group">
                            <label>Current Password <span style="color:#e74c3c;">*</span></label>
                            <input type="password" name="current_password" required placeholder="Required for any changes" autocomplete="new-password">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" placeholder="Leave blank to keep same" autocomplete="new-password">
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" placeholder="Must match new password">
                            </div>
                        </div>

                        <div style="margin-top: 10px; overflow: hidden;">
                            <button type="submit" name="update_profile" class="btn-update">
                                Save Changes ✨
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </main>

</body>
</html>

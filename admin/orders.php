<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$initial = strtoupper(substr($admin_username, 0, 1));

/* ===== UPDATE ORDER STATUS ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id='$order_id'");
    header("Location: orders.php");
    exit();
}

/* ===== DELETE ORDER ===== */
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM orders WHERE id='$delete_id'");
    header("Location: orders.php");
    exit();
}

/* ===== FETCH ORDERS ===== */
$result = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders | Admin</title>
    <link rel="stylesheet" href="admin_style.css">
    <style>
        .order-products {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.5;
            margin-top: 6px;
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
            <a href="orders.php" class="menu-item active">
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
            <div class="page-title">Manage Orders</div>
            <a href="profile.php" class="admin-profile" style="text-decoration:none;">
                <span><?= htmlspecialchars($admin_username) ?></span>
                <div class="admin-avatar"><?= $initial ?></div>
            </a>
        </header>

        <div class="content-wrapper">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="font-size: 20px; font-weight: 700;">Recent Orders</h2>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 80px;">Order ID</th>
                            <th>Customer & Products</th>
                            <th>Total Amount</th>
                            <th>Status Control</th>
                            <th>Order Date</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td style="font-weight: 700; color: #1a1a2e;">#<?= $row['id'] ?></td>
                            <td>
                                <div style="font-weight: 700; color: #1a1a2e; margin-bottom:4px;">
                                    <?= htmlspecialchars($row['user_name']) ?>
                                    <span style="font-weight: 500; color: #94a3b8; font-size: 13.5px; margin-left:8px;">
                                        <?= htmlspecialchars($row['user_email']) ?>
                                    </span>
                                </div>
                                <div class="order-products">
                                    <?php 
                                        $prods = explode(", ", rtrim($row['products'], ", "));
                                        foreach($prods as $p) {
                                            echo "<div style='margin-bottom:2px;'>• " . htmlspecialchars($p) . "</div>";
                                        }
                                    ?>
                                </div>
                            </td>
                            <td style="font-weight: 800; color: #10b981; font-size:16px;">
                                ₹<?= number_format($row['total_price'], 2) ?>
                            </td>
                            <td>
                                <form method="POST" action="orders.php" style="margin:0;">
                                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                    <select name="status" class="select-status" onchange="this.form.submit()" 
                                            style="
                                                color: <?= $row['status'] == 'pending' ? '#b45309' : ($row['status'] == 'delivered' ? '#15803d' : '#b91c1c') ?>;
                                                border-color: <?= $row['status'] == 'pending' ? '#fcd34d' : ($row['status'] == 'delivered' ? '#86efac' : '#fca5a5') ?>;
                                            ">
                                        <option value="pending" <?= $row['status'] == "pending" ? "selected" : "" ?>>🟡 Pending</option>
                                        <option value="delivered" <?= $row['status'] == "delivered" ? "selected" : "" ?>>🟢 Delivered</option>
                                        <option value="cancelled" <?= $row['status'] == "cancelled" ? "selected" : "" ?>>🔴 Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td style="font-size: 14px; color: #64748b; font-weight: 600;">
                                <?= date("M d, Y", strtotime($row['created_at'])) ?>
                            </td>
                            <td style="text-align: right;">
                                <a href="orders.php?delete=<?= $row['id'] ?>" class="btn-danger" onclick="return confirm('Delete this order permanently?')">
                                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px; height:16px; margin-right:4px; vertical-align:-3px;"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else:
                        ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">No orders have been placed yet.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

</body>
</html>
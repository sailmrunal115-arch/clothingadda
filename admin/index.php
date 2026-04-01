<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$initial = strtoupper(substr($admin_username, 0, 1));

// Count products
$product_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM products"));

// Count orders
$order_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM orders"));

// Total revenue
$revenue_query = mysqli_query($conn,"SELECT SUM(total_price) as revenue FROM orders WHERE status='delivered'");
$revenue = mysqli_fetch_assoc($revenue_query)['revenue'] ?? 0;

// Total users
$user_query = mysqli_query($conn,"SELECT COUNT(*) as users FROM users");
$total_users = mysqli_fetch_assoc($user_query)['users'];

// Order status
$pending = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM orders WHERE status='pending'"))['c'];
$delivered = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM orders WHERE status='delivered'"))['c'];
$cancelled = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM orders WHERE status='cancelled'"))['c'];

// Weekly revenue data
$week_revenue = array_fill(0,7,0);
$orders_this_week = mysqli_query($conn, "
    SELECT total_price, DAYOFWEEK(created_at) as day
    FROM orders
    WHERE status='delivered'
      AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
");
while($row = mysqli_fetch_assoc($orders_this_week)){
    $day_index = $row['day'] - 1;
    $week_revenue[$day_index] += $row['total_price'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Clothing Adda</title>
    <link rel="stylesheet" href="admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="index.php" class="menu-item active">
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
            <div class="page-title">Store Overview</div>
            <a href="profile.php" class="admin-profile" style="text-decoration:none;">
                <span><?= htmlspecialchars($admin_username) ?></span>
                <div class="admin-avatar"><?= $initial ?></div>
            </a>
        </header>

        <div class="content-wrapper">
            
            <div class="metric-grid">
                
                <a href="products.php" class="metric-card">
                    <div class="metric-title">Total Products</div>
                    <div class="metric-value"><?= $product_count ?></div>
                    <svg class="metric-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/></svg>
                </a>
                
                <a href="orders.php" class="metric-card">
                    <div class="metric-title">Total Orders</div>
                    <div class="metric-value"><?= $order_count ?></div>
                    <svg class="metric-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                </a>

                <div class="metric-card">
                    <div class="metric-title">Total Revenue</div>
                    <div class="metric-value" style="color: #10b981;">₹<?= number_format($revenue, 2) ?></div>
                    <svg class="metric-icon" viewBox="0 0 24 24" fill="currentColor">
                        <text x="50%" y="60%" font-size="18" font-family="Inter, sans-serif" font-weight="800" text-anchor="middle" dominant-baseline="middle">₹</text>
                    </svg>
                </div>

                <div class="metric-card">
                    <div class="metric-title">Registered Users</div>
                    <div class="metric-value" style="color: #3b82f6;"><?= $total_users ?></div>
                    <svg class="metric-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>

            </div>

            <div class="charts-row">
                <div class="chart-card">
                    <h3>Weekly Revenue</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3>Order Status Distribution</h3>
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

        </div>
    </main>

<script>
const weekRevenue = <?php echo json_encode($week_revenue); ?>;

new Chart(document.getElementById("revenueChart"), {
    type: "bar",
    data: {
        labels: ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],
        datasets: [{
            label: "Revenue (₹)",
            data: weekRevenue,
            backgroundColor: "rgba(231, 76, 60, 0.8)",
            borderRadius: 6,
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
        },
        scales: {
            y: { 
                beginAtZero: true,
                grid: {
                    color: '#f1f5f9'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

new Chart(document.getElementById("statusChart"), {
    type: "doughnut",
    data: {
        labels: ["Pending","Delivered","Cancelled"],
        datasets: [{
            data: [<?php echo $pending ?>, <?php echo $delivered ?>, <?php echo $cancelled ?>],
            backgroundColor:["#f59e0b", "#10b981", "#ef4444"],
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        cutout: '70%',
        plugins: { 
            legend: { 
                position: 'bottom',
                labels: {
                    padding: 20,
                    font: {
                        family: 'Inter',
                        weight: '600'
                    }
                }
            } 
        }
    }
});
</script>

</body>
</html>
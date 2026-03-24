<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

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

// Weekly revenue data (current week)
$week_revenue = array_fill(0,7,0); // Sun=0 ... Sat=6
$orders_this_week = mysqli_query($conn, "
    SELECT total_price, DAYOFWEEK(created_at) as day
    FROM orders
    WHERE status='delivered'
      AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
");
while($row = mysqli_fetch_assoc($orders_this_week)){
    $day_index = $row['day'] - 1; // DAYOFWEEK: Sunday=1, Saturday=7
    $week_revenue[$day_index] += $row['total_price'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            font-family: Arial, sans-serif;
        }
        h2 { margin-bottom: 20px; }
        .btn { 
            background-color: #e74c3c; color: #fff; text-decoration: none; padding: 6px 12px; border-radius: 5px; font-weight: bold;
        }
        .dashboard-card {
            width: 220px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        .dashboard-card h3 { margin-bottom: 10px; }
        .dashboard-card p { font-size: 1.5em; margin-bottom: 10px; }
        .dashboard-card .btn { font-size: 0.85em; }
    </style>
</head>
<body>

<div class="admin-container">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
        <h2>Dashboard</h2>
        <a href="logout.php" class="btn">Logout</a>
    </div>

    <hr><br>

    <div style="display:flex; gap:20px; flex-wrap:wrap;">

        <div class="dashboard-card">
            <h3>Total Products</h3>
            <p><?php echo $product_count; ?></p>
            <a href="products.php" class="btn">Manage Products</a>
        </div>

        <div class="dashboard-card">
            <h3>Total Orders</h3>
            <p><?php echo $order_count; ?></p>
            <a href="orders.php" class="btn">Manage Orders</a>
        </div>

        <div class="dashboard-card">
            <h3>Add Product</h3>
            <p>+</p>
            <a href="add_product.php" class="btn">Add New Product</a>
        </div>

        <div class="dashboard-card">
            <h3>Total Revenue</h3>
            <p>₹<?php echo number_format($revenue,2); ?></p>
        </div>

        <div class="dashboard-card">
            <h3>Registered Customers</h3>
            <p><?php echo $total_users; ?></p>
        </div>

    </div>

    <br><br>

    <div style="display:flex; gap:40px; flex-wrap:wrap;">
        <div style="width:60%;">
            <h3>Weekly Revenue</h3>
            <canvas id="revenueChart"></canvas>
        </div>

        <div style="width:35%;">
            <h3>Order Status</h3>
            <canvas id="statusChart"></canvas>
        </div>
    </div>

</div>

<script>
const weekRevenue = <?php echo json_encode($week_revenue); ?>;

new Chart(document.getElementById("revenueChart"), {
    type: "line",
    data: {
        labels: ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],
        datasets: [{
            label: "Revenue",
            data: weekRevenue,
            borderColor: "#e74c3c",
            fill: false,
            tension: 0.3,
            pointBackgroundColor: "#e74c3c",
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

new Chart(document.getElementById("statusChart"), {
    type: "doughnut",
    data: {
        labels: ["Pending","Delivered","Cancelled"],
        datasets: [{
            data: [<?php echo $pending ?>, <?php echo $delivered ?>, <?php echo $cancelled ?>],
            backgroundColor:["orange","green","red"]
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>

</body>
</html>
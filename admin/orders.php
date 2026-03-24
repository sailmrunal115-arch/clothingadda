<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

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
<title>Manage Orders</title>
<link rel="stylesheet" href="admin_style.css">
<style>
/* ===== Container & Header ===== */
.container {
    max-width: 1100px;
    margin: 40px auto;
    padding: 0 20px;
    font-family: Arial, sans-serif;
}

.back-btn {
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 20px;
    background-color: #e74c3c; /* red background */
    color: #fff; /* white text */
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: 0.3s;
}
.back-btn:hover {
    background-color: #c0392b;
}

.page-title {
    font-size: 28px;
    margin-bottom: 20px;
    color: #333;
}

/* ===== Orders Table ===== */
.order-table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

.order-table th, .order-table td {
    padding: 12px 15px;
    text-align: left;
}

.order-table th {
    background-color: #f7f7f7;
    font-weight: 600;
    color: #555;
}

.order-table tr {
    border-bottom: 1px solid #e0e0e0;
}

.order-table tr:last-child {
    border-bottom: none;
}

.order-table td select {
    padding: 6px 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
}

/* ===== Delete Button ===== */
.delete-btn-small {
    display: inline-block;
    padding: 5px 10px;
    background-color: #e74c3c;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    font-size: 0.9em;
    transition: 0.3s;
}
.delete-btn-small:hover {
    background-color: #c0392b;
}
</style>
</head>
<body>

<div class="container">

    <a href="index.php" class="back-btn">&larr; Back to Dashboard</a>

    <h2 class="page-title">Orders Management</h2>

    <?php if(mysqli_num_rows($result) > 0): ?>
        <table class="order-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['user_name']); ?></strong><br>
                            <small><?php echo htmlspecialchars($row['user_email']); ?></small>
                        </td>
                        <td>₹<?php echo number_format($row['total_price'],2); ?></td>
                        <td>
                            <form method="POST" action="orders.php">
                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="pending" <?php if($row['status']=="pending") echo "selected"; ?>>Pending</option>
                                    <option value="delivered" <?php if($row['status']=="delivered") echo "selected"; ?>>Delivered</option>
                                    <option value="cancelled" <?php if($row['status']=="cancelled") echo "selected"; ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="orders.php?delete=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Delete this order permanently?')" 
                               class="delete-btn-small">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="padding: 20px; text-align: center;">No orders found.</p>
    <?php endif; ?>

</div>

</body>
</html>
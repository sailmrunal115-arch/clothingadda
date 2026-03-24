<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$id = $_GET['id'];
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$id");
    header("Location: orders.php");
    exit();
}

$order_result = mysqli_query($conn, "SELECT * FROM orders WHERE id=$id");
$order = mysqli_fetch_assoc($order_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Order</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="admin-container">
    <h2>Update Order #<?php echo $order['id']; ?></h2>

    <form method="POST">
        <label>Status:</label><br>
        <select name="status" required>
            <option value="pending" <?php if($order['status']=='pending') echo 'selected'; ?>>Pending</option>
            <option value="completed" <?php if($order['status']=='completed') echo 'selected'; ?>>Completed</option>
            <option value="canceled" <?php if($order['status']=='canceled') echo 'selected'; ?>>Canceled</option>
        </select><br><br>
        <button class="btn">Update Order</button>
    </form>

    <br>
    <a href="orders.php" class="btn">Back to Orders</a>
</div>

</body>
</html>

<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM products ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Products</title>
<link rel="stylesheet" href="admin_style.css">
<style>
.admin-container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 0 20px;
    font-family: Arial, sans-serif;
}

.admin-container h2 {
    margin-bottom: 20px;
}

/* Table styling */
.admin-container table {
    width: 100%;
    border-collapse: collapse;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    background-color: #fff;
    border-radius: 5px;
    overflow: hidden;
}

.admin-container table th,
.admin-container table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.admin-container table th {
    background-color: #f7f7f7;
    font-weight: 600;
}

.admin-container table tr:last-child td {
    border-bottom: none;
}

/* Buttons */
.admin-container .btn {
    display: inline-block;
    padding: 6px 12px;
    background-color: #e74c3c; /* red */
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.9em;
    transition: 0.3s;
}

.admin-container .btn:hover {
    background-color: #c0392b;
}

/* Red Back to Dashboard button */
.admin-container .back-btn {
    display: inline-block;
    margin-bottom: 20px;
    padding: 8px 15px;
    background-color: #e74c3c; /* red */
    color: #fff; /* white text */
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
}

.admin-container .back-btn:hover {
    background-color: #c0392b; /* darker red on hover */
}
</style>
</head>

<body>

<div class="admin-container">

<a href="index.php" class="back-btn">&larr; Back to Dashboard</a>

<h2>Manage Products</h2>

<table>
<tr>
<th>ID</th>
<th>Image</th>
<th>Name</th>
<th>Price</th>
<th>Action</th>
</tr>

<?php 
$counter = 1; // Initialize counter
while($row = mysqli_fetch_assoc($result)) { 
?>

<tr>
<td data-label="ID"><?php echo $counter; ?></td>

<td data-label="Image">
<?php
$image = $row['image'];
if(strpos($image,'images/') !== false){
    $path = "../".$image;
}else{
    $path = "../uploads/".$image;
}
?>
<img src="<?php echo $path; ?>" width="60">
</td>

<td data-label="Name"><?php echo $row['name']; ?></td>
<td data-label="Price">₹<?php echo $row['price']; ?></td>
<td data-label="Action">
<a href="delete_product.php?id=<?php echo $row['id']; ?>" 
class="btn"
onclick="return confirm('Delete this product?')">
Delete
</a>
</td>
</tr>

<?php 
$counter++; // increment counter
} 
?>

</table>

</div>

</body>
</html>
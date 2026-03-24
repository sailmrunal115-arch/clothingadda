<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if(isset($_POST['add_product'])){

    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $price = mysqli_real_escape_string($conn,$_POST['price']);
    $category = mysqli_real_escape_string($conn,$_POST['category']);

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    $image_name = time().'_'.$image;

    move_uploaded_file($tmp,"../uploads/".$image_name);

    // FIXED QUERY (removed extra comma)
    mysqli_query($conn,"INSERT INTO products (name,price,category,image)
    VALUES ('$name','$price','$category','$image_name')");

    echo "<script>alert('Product Added Successfully');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Product</title>
<link rel="stylesheet" href="admin_style.css">
</head>

<body>

<div class="admin-container">

<a href="index.php" class="btn">&larr; Back to Dashboard</a>

<h2>Add New Product</h2>

<form method="POST" enctype="multipart/form-data">

<label>Product Name</label>
<input type="text" name="name" required>

<label>Price</label>
<input type="number" name="price" required>

<label>Category</label>
<select name="category" required>
<option value="">Select Category</option>
<option value="men">Men</option>
<option value="women">Women</option>
</select>

<br>
<label>Product Image</label>
<input type="file" name="image" required>
<br><br>

<button type="submit" name="add_product" class="btn">Add Product</button>

</form>

</div>

</body>
</html>
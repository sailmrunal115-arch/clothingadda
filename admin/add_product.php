<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$initial = strtoupper(substr($admin_username, 0, 1));

if(isset($_POST['add_product'])){
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $price = mysqli_real_escape_string($conn,$_POST['price']);
    $category = mysqli_real_escape_string($conn,$_POST['category']);

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];
    $image_name = time().'_'.$image;

    move_uploaded_file($tmp,"../uploads/".$image_name);

    mysqli_query($conn,"INSERT INTO products (name,price,category,image) VALUES ('$name','$price','$category','$image_name')");

    echo "<script>alert('Product Added Successfully'); window.location.href='products.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product | Admin</title>
    <link rel="stylesheet" href="admin_style.css">
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
            <a href="add_product.php" class="menu-item active">
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
            <div class="page-title">Add New Product</div>
            <a href="profile.php" class="admin-profile" style="text-decoration:none;">
                <span><?= htmlspecialchars($admin_username) ?></span>
                <div class="admin-avatar"><?= $initial ?></div>
            </a>
        </header>

        <div class="content-wrapper">
            
            <div class="form-card" style="margin-top: 24px;">
                <h3 style="font-size: 18px; color: #1a1a2e; margin-bottom: 24px;">Product Information</h3>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" required placeholder="e.g., Premium Cotton T-Shirt">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div class="form-group">
                            <label>Price (₹)</label>
                            <input type="number" name="price" required placeholder="e.g., 999">
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" required>
                                <option value="" disabled selected>Select Category</option>
                                <option value="men">Men's Apparel</option>
                                <option value="women">Women's Apparel</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Product Image</label>
                        <div style="padding: 24px; border: 2px dashed #e2e8f0; border-radius: 12px; background: #f8fafc;">
                            <input type="file" name="image" required style="background: transparent; border: none; padding: 0;">
                        </div>
                    </div>

                    <div style="margin-top: 32px; display: flex; gap: 16px;">
                        <button type="submit" name="add_product" class="btn-primary">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="#fff"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                            Publish Product
                        </button>
                        <a href="products.php" class="btn-secondary" style="display:inline-flex; align-items:center;">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </main>

</body>
</html>
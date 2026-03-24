<?php
session_start();
include "../db.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if product ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // First, get the product to delete its image
    $res = mysqli_query($conn, "SELECT image FROM products WHERE id=$id");
    if ($res && mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);

        // Delete image file if exists
        $imgPath = "../uploads/" . $row['image'];
        if (file_exists($imgPath)) {
            unlink($imgPath);
        }

        // Delete product from database
        mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    }
}

// Redirect back to products page
header("Location: products.php");
exit();
?>
      
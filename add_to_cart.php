<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {

    $user_id = $_SESSION['user_id'];
    $product_id = intval($_GET['id']);

    // Check if product already exists in cart
    $check = "SELECT * FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        // Update quantity
        $update = "UPDATE cart SET quantity = quantity + 1 
                   WHERE user_id = '$user_id' AND product_id = '$product_id'";
        mysqli_query($conn, $update);
    } else {
        // Insert new product
        $insert = "INSERT INTO cart (user_id, product_id, quantity) 
                   VALUES ('$user_id', '$product_id', 1)";
        mysqli_query($conn, $insert);
    }
}

header("Location: index.php");
exit();
?>

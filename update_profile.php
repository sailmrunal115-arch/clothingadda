<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = intval($_SESSION['user_id']);

/* Get and sanitize form data safely */
$name    = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$email   = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
$phone   = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
$pincode = mysqli_real_escape_string($conn, $_POST['pincode'] ?? '');
$gender  = mysqli_real_escape_string($conn, $_POST['gender'] ?? '');
$address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');

/* Update query */
mysqli_query($conn,
    "UPDATE users SET 
        name='$name',
        email='$email',
        phone='$phone',
        pincode='$pincode',
        gender='$gender',
        address='$address'
     WHERE id='$id'"
);

header("Location: profile.php");
exit();
?>
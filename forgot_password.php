<?php
session_start();
include "db.php";

$message = "";

if(isset($_POST['reset'])){

$email = mysqli_real_escape_string($conn,$_POST['email']);

$check = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($check) > 0){

$token = md5(rand());

mysqli_query($conn,"UPDATE users SET reset_token='$token' WHERE email='$email'");

$message = "Reset Link: 
<a href='reset_password.php?token=$token'>Click here to reset password</a>";

}else{
$message = "Email not found!";
}

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">

<style>
body { background: #fdfdfd; font-family: 'Inter', Arial, sans-serif; }
.auth-wrapper {
    min-height: calc(100vh - 400px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    margin-top: 80px;
}
.auth-container {
    width: 100%;
    max-width: 420px;
    background: #fff;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0;
}
.auth-container h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #1e293b;
    font-size: 26px;
}
.auth-container input {
    width: 100%;
    padding: 14px 20px;
    margin-bottom: 16px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 15px;
    font-family: inherit;
    background: #f8fafc;
    color: #1e293b;
    transition: 0.3s;
    box-sizing: border-box;
    outline: none;
}
.auth-container input:focus {
    background: #fff;
    border-color: #e74c3c;
    box-shadow: 0 0 0 4px rgba(231,76,60,0.08);
}
.auth-container button {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    border: none;
    border-radius: 30px;
    font-size: 15.5px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    cursor: pointer;
    box-shadow: 0 6px 15px rgba(231,76,60,0.25);
    margin-top: 10px;
    transition: 0.3s;
}
.auth-container button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(231,76,60,0.35);
}
.message {
    background: #e0f2fe;
    color: #0369a1;
    padding: 12px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 20px;
    font-size: 14.5px;
    font-weight: 500;
}
.message a { color: #0284c7; font-weight: 600; text-decoration: underline; }
</style>

</head>

<header>
<div class="container header-flex">
<div class="logo"><h1>Clothing Adda</h1></div>
<button class="hamburger" onclick="document.querySelector('.nav-links').classList.toggle('active')">☰</button>
<nav>
<ul class="nav-links">
<li><a href="index.php">Home</a></li>
<li><a href="men.php">Men</a></li>
<li><a href="women.php">Women</a></li>
<li><a href="products.php">All Products</a></li>
<li><a href="login.php" class="active">Login</a></li>
<li><a href="register.php" class="btn" style="padding:8px 16px; margin-left:10px; color:#fff;">Register</a></li>
</ul>
</nav>
</div>
</header>

<div class="auth-wrapper">
<div class="auth-container">
<h2>Forgot Password</h2>

<?php if($message!=""){ ?>
<div class="message"><?php echo $message; ?></div>
<?php } ?>

<form method="POST">
<input type="email" name="email" placeholder="Enter your email" required>
<button name="reset">Send Reset Link</button>
</form>
</div>
</div>

<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            
            <div class="footer-col">
                <h3>Clothing Adda</h3>
                <p>Your ultimate destination for modern, trendy, and comfortable clothing. We strive to bring the best styles right to your doorstep.</p>
                <div class="social-icons">
                    <a href="#">F</a>
                    <a href="#">T</a>
                    <a href="#">I</a>
                </div>
            </div>

            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">All Products</a></li>
                    <li><a href="cart.php">My Cart</a></li>
                    <li><a href="login.php">Login / Register</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Categories</h3>
                <ul class="footer-links">
                    <li><a href="men.php">Men's Collection</a></li>
                    <li><a href="women.php">Women's Collection</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Contact Us</h3>
                <ul class="footer-links">
                    <li>📍 123 Fashion Street, NY 10001</li>
                    <li>📞 +1 234 567 8900</li>
                    <li>✉️ support@clothingadda.com</li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 <strong>Clothing Adda</strong>. All Rights Reserved.</p>
        </div>
    </div>
</footer>

</body>
</html>
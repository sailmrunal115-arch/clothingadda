<?php
session_start();
include "db.php";

$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "All fields are required!";
    } else {
        $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                header("Location: index.php");
                exit();

            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "Email not registered!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | Clothing Adda</title>
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
.auth-container p { text-align: center; margin-top: 25px; color: #64748b; font-size: 14.5px; }
.auth-container p a, .forgot a { color: #e74c3c; text-decoration: none; font-weight: 600; transition: 0.2s; }
.auth-container p a:hover, .forgot a:hover { color: #c0392b; text-decoration: underline; }

.error {
    background: #fee2e2;
    color: #b91c1c;
    padding: 12px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 20px;
    font-size: 14.5px;
    font-weight: 500;
}

/* PASSWORD EYE */
.password-box { position: relative; }
.password-box input { padding-right: 45px; }
.eye {
    position: absolute;
    right: 18px;
    top: 50%;
    transform: translateY(-70%);
    cursor: pointer;
    font-size: 18px;
    color: #94a3b8;
    transition: 0.2s;
}
.eye:hover { color: #475569; }

/* forgot password */
.forgot { text-align: right; margin-top: -8px; margin-bottom: 20px; }
.forgot a { font-size: 13.5px; }
</style>

</head>
<body>

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

<h2>Sign In</h2>

<?php if ($error): ?>
<div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST">

<input type="email" name="email" placeholder="Email address" required>

<div class="password-box">
<input type="password" name="password" id="password" placeholder="Password" required>
<span class="eye" onclick="togglePassword()">👁</span>
</div>

<div class="forgot">
<a href="forgot_password.php">Forgot Password?</a>
</div>

<button type="submit" name="login">Login</button>

</form>

<p>
Don't have an account?
<a href="register.php">Register here</a>
</p>

</div>
</div>

<script>

function togglePassword(){
    var pass = document.getElementById("password");

    if(pass.type === "password"){
        pass.type = "text";
    }else{
        pass.type = "password";
    }
}

</script>

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
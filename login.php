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

.auth-container {
    max-width: 400px;
    margin: 120px auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.auth-container h2 {
    text-align: center;
    margin-bottom: 20px;
}

.auth-container input {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.auth-container button {
    width: 100%;
    padding: 12px;
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
}

.auth-container button:hover {
    background: #c0392b;
}

.auth-container p {
    text-align: center;
    margin-top: 15px;
}

.error {
    color: red;
    text-align: center;
    margin-bottom: 15px;
}

/* PASSWORD EYE */

.password-box{
    position: relative;
}

.password-box input{
    padding-right: 40px;
}

.eye{
    position: absolute;
    right: 12px;
    top: 40%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 18px;
}

/* forgot password */

.forgot{
    text-align:right;
    margin-top:-10px;
    margin-bottom:15px;
}

.forgot a{
    font-size:14px;
    color:#e74c3c;
    text-decoration:none;
}

</style>

</head>
<body>

<div class="auth-container">

<h2>Login</h2>

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

</body>
</html>
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
<link rel="stylesheet" href="style.css">

<style>

.auth-container{
max-width:420px;
margin:120px auto;
background:#fff;
padding:35px;
border-radius:10px;
box-shadow:0 5px 15px rgba(0,0,0,0.1);
text-align:center;
}

.auth-container h2{
margin-bottom:20px;
}

.auth-container input{
width:100%;
padding:12px;
margin-bottom:15px;
border:1px solid #ccc;
border-radius:5px;
}

.auth-container button{
width:100%;
padding:12px;
background:#e74c3c;
color:white;
border:none;
border-radius:5px;
cursor:pointer;
font-weight:bold;
}

.auth-container button:hover{
background:#c0392b;
}

.message{
margin-bottom:15px;
color:#e74c3c;
}

</style>

</head>

<body>

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

</body>
</html>
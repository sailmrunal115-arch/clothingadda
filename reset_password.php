<?php
include "db.php";

if(!isset($_GET['token'])){
echo "Invalid request";
exit();
}

$token = $_GET['token'];

$error="";

if(isset($_POST['update'])){

$pass1 = $_POST['password'];
$pass2 = $_POST['confirm_password'];

if($pass1 != $pass2){
$error="Passwords do not match!";
}else{

$password = password_hash($pass1,PASSWORD_DEFAULT);

mysqli_query($conn,"UPDATE users SET password='$password', reset_token=NULL WHERE reset_token='$token'");

echo "Password updated successfully! <a href='login.php'>Login</a>";
exit();

}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
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

.password-box{
position:relative;
margin-bottom:15px;
}

.password-box input{
width:100%;
padding:12px;
padding-right:40px;
border:1px solid #ccc;
border-radius:5px;
}

.eye{
position:absolute;
right:12px;
top:50%;
transform:translateY(-50%);
cursor:pointer;
}

button{
width:100%;
padding:12px;
background:#e74c3c;
color:white;
border:none;
border-radius:5px;
cursor:pointer;
font-weight:bold;
}

.error{
color:red;
margin-bottom:10px;
}

</style>

</head>

<body>

<div class="auth-container">

<h2>Reset Password</h2>

<?php if($error!=""){ ?>
<div class="error"><?php echo $error; ?></div>
<?php } ?>

<form method="POST">

<div class="password-box">
<input type="password" name="password" id="password" placeholder="New Password" required>
<span class="eye" onclick="togglePassword('password')">👁</span>
</div>

<div class="password-box">
<input type="password" name="confirm_password" id="confirm" placeholder="Confirm Password" required>
<span class="eye" onclick="togglePassword('confirm')">👁</span>
</div>

<button name="update">Update Password</button>

</form>

</div>

<script>

function togglePassword(id){
var x=document.getElementById(id);

if(x.type==="password"){
x.type="text";
}else{
x.type="password";
}
}

</script>

</body>
</html>
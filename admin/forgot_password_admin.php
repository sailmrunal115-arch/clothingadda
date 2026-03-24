<?php
session_start();
include "../db.php";

$message="";

if(isset($_POST['reset'])){

$username = mysqli_real_escape_string($conn,$_POST['username']);

$check = mysqli_query($conn,"SELECT * FROM admins WHERE username='$username'");

if(mysqli_num_rows($check)>0){

$token = md5(rand());

mysqli_query($conn,"UPDATE admins SET reset_token='$token' WHERE username='$username'");

$message = "Reset Link:
<a href='reset_password_admin.php?token=$token'>Click here to reset password</a>";

}else{

$message="Admin username not found!";

}

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Admin Forgot Password</title>
<link rel="stylesheet" href="admin_style.css">

<style>

.admin-container{
max-width:420px;
margin:120px auto;
background:#fff;
padding:35px;
border-radius:10px;
box-shadow:0 5px 15px rgba(0,0,0,0.1);
text-align:center;
}

.admin-container input{
width:100%;
padding:12px;
margin-bottom:15px;
border:1px solid #ccc;
border-radius:5px;
}

.admin-container button{
width:100%;
padding:12px;
background:#e74c3c;
color:white;
border:none;
border-radius:5px;
cursor:pointer;
font-weight:bold;
}

.admin-container button:hover{
background:#c0392b;
}

.message{
margin-bottom:15px;
color:#e74c3c;
}

</style>

</head>

<body>

<div class="admin-container">

<h2>Admin Forgot Password</h2>

<?php if($message!=""){ ?>
<div class="message"><?php echo $message; ?></div>
<?php } ?>

<form method="POST">

<input type="text" name="username" placeholder="Enter Admin Username" required>

<button name="reset">Generate Reset Link</button>

</form>

</div>

</body>
</html>
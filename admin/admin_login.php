<?php
session_start();
include "../db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM admins WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);

        if (password_verify($password, $admin['password'])) {

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            header("Location: index.php");
            exit();

        } else {
            $error = "Invalid Password!";
        }
    } else {
        $error = "Admin not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<link rel="stylesheet" href="admin_style.css">

<style>

.password-box{
    position: relative;
}

.password-box input{
    width: 100%;
    padding-right: 40px;
}

.eye{
    position: absolute;
    right: 12px;
    top: 50%;
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

<div class="admin-container login">

<h2>Admin Login</h2>

<?php if($error != ""): ?>
<p style="color:red; text-align:center;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">

<input type="text" name="username" placeholder="Username" required>

<div class="password-box">
<input type="password" name="password" id="password" placeholder="Password" required>
<span class="eye" onclick="togglePassword()">👁</span>
</div>

<div class="forgot">
<a href="forgot_password_admin.php">Forgot Password?</a>
</div>

<button class="btn">Login</button>

</form>

</div>

<script>

function togglePassword() {

    var pass = document.getElementById("password");

    if (pass.type === "password") {
        pass.type = "text";
    } else {
        pass.type = "password";
    }

}

</script>

</body>
</html>
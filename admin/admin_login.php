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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Clothing Adda</title>
    <!-- Use the premium Inter font matching main site -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Matches site background #f0f2f5 */
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f2f5 100%);
            position: relative;
            overflow: hidden;
            color: #333;
        }

        /* Abstract Subtle Background Elements */
        body::before {
            content: '';
            position: absolute;
            top: -10%; left: -10%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(231,76,60,0.06) 0%, transparent 70%);
            border-radius: 50%;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -15%; right: -5%;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(59,130,246,0.04) 0%, transparent 70%);
            border-radius: 50%;
        }

        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 0 20px;
            animation: fadeUp 0.6s ease-out;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Branding */
        .brand-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-logo {
            width: 64px;
            height: 64px;
            /* Matches main site red #e74c3c */
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 10px 25px rgba(231,76,60,0.3);
        }

        .brand-logo svg {
            width: 32px;
            height: 32px;
            fill: #fff;
        }

        .brand-title {
            color: #1a1a2e;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            color: #64748b;
            font-size: 14.5px;
            margin-top: 6px;
            font-weight: 500;
        }

        /* Card Container */
        .login-card {
            background: #ffffff;
            border: 1px solid #eef0f4;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        }

        .error-message {
            background: #fff1f2;
            border: 1px solid #ffe4e6;
            color: #e11d48;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .input-group {
            margin-bottom: 22px;
        }

        .input-group label {
            display: block;
            color: #475569;
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 8px;
            transition: color 0.3s;
        }

        .input-field {
            position: relative;
        }

        .input-field input {
            width: 100%;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            color: #1e293b;
            padding: 14px 16px 14px 44px;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.3s;
        }

        .input-field input::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        .input-field input:focus {
            background: #fff;
            border-color: #e74c3c;
            box-shadow: 0 0 0 4px rgba(231,76,60,0.1);
        }

        .input-field input:focus + .icon-left {
            color: #e74c3c;
        }

        .icon-left {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            pointer-events: none;
            transition: color 0.3s;
        }

        .icon-right {
            position: absolute;
            top: 50%;
            right: 16px;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            cursor: pointer;
            transition: color 0.2s;
            background: none;
            border: none;
            display: flex;
        }

        .icon-right:hover {
            color: #1e293b;
        }

        .forgot-pass {
            text-align: right;
            margin-top: -8px;
            margin-bottom: 28px;
        }

        .forgot-pass a {
            color: #64748b;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-pass a:hover {
            color: #e74c3c;
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: #fff;
            border: none;
            padding: 16px;
            border-radius: 14px;
            font-size: 15.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(231,76,60,0.25);
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.3px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(231,76,60,0.35);
            background: linear-gradient(135deg, #c0392b, #a93226);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Back to site */
        .back-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #1a1a2e;
        }

        /* SVG Icons */
        .svg-icon {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <div class="brand-header">
        <div class="brand-logo">
            <!-- Shield icon -->
            <svg viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-6h-2v6zm1-8c-.83 0-1.5-.67-1.5-1.5S11.17 6 12 6s1.5.67 1.5 1.5S12.83 9 12 9z"/>
            </svg>
        </div>
        <h1 class="brand-title">Admin Login</h1>
        <p class="brand-subtitle">Clothing Adda Management</p>
    </div>

    <div class="login-card">
        
        <?php if($error != ""): ?>
            <div class="error-message">
                <svg class="svg-icon" viewBox="0 0 24 24" style="width:18px;height:18px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            
            <div class="input-group">
                <label for="username">Username</label>
                <div class="input-field">
                    <input type="text" name="username" id="username" placeholder="Enter admin username" required autofocus autocomplete="off">
                    <span class="icon-left">
                        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </span>
                </div>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <div class="input-field">
                    <input type="password" name="password" id="password" placeholder="Enter password" required>
                    <span class="icon-left">
                        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M12 17c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm6-9h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6-5c1.66 0 3 1.34 3 3v2H9V6c0-1.66 1.34-3 3-3z"/></svg>
                    </span>
                    <button type="button" class="icon-right" onclick="togglePassword()" tabindex="-1">
                        <svg class="svg-icon" id="eye-icon" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                    </button>
                </div>
            </div>

            <div class="forgot-pass">
                <a href="forgot_password_admin.php">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-submit">Sign In to Dashboard</button>

        </form>

    </div>

    <a href="../index.php" class="back-link">← Back to Main Website</a>

</div>

<script>
function togglePassword() {
    var pass = document.getElementById("password");
    var eyeIcon = document.getElementById("eye-icon");
    
    if (pass.type === "password") {
        pass.type = "text";
        eyeIcon.innerHTML = '<path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.28 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2.01 3.87l2.68 2.68C3.06 7.83 1.77 9.53 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l3.29 3.29 1.41-1.41L3.42 2.45 2.01 3.87zm7.5 7.5l2.61 2.61c-.04.01-.08.02-.12.02-1.66 0-3-1.34-3-3 0-.04.01-.08.02-.12zm3.4-3.4l1.75 1.75c-.23-.84-.87-1.48-1.71-1.71z"/>';
    } else {
        pass.type = "password";
        eyeIcon.innerHTML = '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>';
    }
}
</script>

</body>
</html>
<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = intval($_SESSION['user_id']);
$result = mysqli_query($conn,"SELECT * FROM users WHERE id=$id");

if(mysqli_num_rows($result) == 0){
    echo "User not found";
    exit();
}

$user = mysqli_fetch_assoc($result);

/* Get First Letter for Avatar */
$firstLetter = strtoupper(substr($user['name'], 0, 1));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Profile | Clothing Adda</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- HEADER -->
<header>
    <div class="container header-flex">
        <div class="logo">
            <h1>Clothing Adda</h1>
        </div>

        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="orders.php">My Orders</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="profile-section">
    <div class="container">

        <div class="profile-card">

            <!-- Profile Header -->
            <div class="profile-header">

                <!-- Gradient Letter Avatar -->
                <div class="profile-avatar">
                    <?= $firstLetter ?>
                </div>

                <h2><?= htmlspecialchars($user['name']); ?></h2>
                <p><?= htmlspecialchars($user['email']); ?></p>

            </div>

            <!-- Profile Form -->
            <form method="POST" action="update_profile.php" class="profile-form">

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name"
                           value="<?= htmlspecialchars($user['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email"
                           value="<?= htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone"
                           value="<?= htmlspecialchars($user['phone']); ?>">
                </div>

                <div class="form-group">
                    <label>Pincode</label>
                    <input type="text" name="pincode"
                           value="<?= htmlspecialchars($user['pincode'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?= ($user['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= ($user['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" rows="4"><?= htmlspecialchars($user['address']); ?></textarea>
                </div>

                <button type="submit" class="profile-btn">
                    Update Profile
                </button>

            </form>

        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p>&copy; 2026 Clothing Adda. All Rights Reserved.</p>
    </div>
</footer>

</body>
</html>
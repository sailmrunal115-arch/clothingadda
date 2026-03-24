<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Online Payment</title>
    <style>
        body{
            font-family: Arial;
            background:#f5f5f5;
            text-align:center;
        }

        .payment-box{
            width:350px;
            background:white;
            margin:80px auto;
            padding:30px;
            border-radius:12px;
            box-shadow:0 5px 20px rgba(0,0,0,0.1);
        }

        .qr-box{
            width:200px;
            height:200px;
            margin:20px auto;
        }

        .btn{
            padding:10px 20px;
            border:none;
            background:#28a745;
            color:white;
            border-radius:5px;
            cursor:pointer;
            width:100%;
        }

        .processing{
            display:none;
            margin-top:15px;
            color:#ff9800;
            font-weight:bold;
        }
    </style>
</head>
<body>

<div class="payment-box">
    <h2>Scan QR to Pay</h2>

    <div class="qr-box">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=FAKEPAYMENT123" width="200">
    </div>

    <p>Amount: ₹500</p>

    <!-- EXISTING PAY BUTTON -->
    <button class="btn" onclick="fakePay()">Pay ₹500</button>

    <!-- ✅ ADDED BUTTON (ONLY CHANGE) -->
    <br><br>
    <button class="btn" onclick="fakePay()">I Have Paid via QR</button>

    <div class="processing" id="processing">
        Processing Payment...
    </div>
</div>

<script>
function fakePay(){
    document.getElementById("processing").style.display = "block";

    setTimeout(function(){
        window.location.href = "payment_success.php?order_id=<?php echo $order_id; ?>";
    }, 3000);
}
</script>

</body>
</html>
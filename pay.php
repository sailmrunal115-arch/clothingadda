<?php
session_start();
include "db.php";

if (!isset($_SESSION['last_order_id'])) {
    header("Location: index.php");
    exit;
}

$order_id = intval($_SESSION['last_order_id']);

$query = mysqli_query($conn, "SELECT total_price FROM orders WHERE id=$order_id");

if (!$query || mysqli_num_rows($query) == 0) {
    die("Order not found");
}

$order = mysqli_fetch_assoc($query);
$total = floatval($order['total_price']);

if(isset($_GET['success'])){
    mysqli_query($conn,"UPDATE orders SET payment_status='paid', status='confirmed' WHERE id=$order_id");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Payment - Clothing Adda</title>

<style>
/* SAME CSS (UNCHANGED) */
body{margin:0;font-family:Arial;background:#e9edf3;}
.overlay{height:100vh;display:flex;align-items:center;justify-content:center;}
.box{width:1000px;height:560px;background:#fff;border-radius:18px;box-shadow:0 30px 80px rgba(0,0,0,0.25);display:flex;overflow:hidden;position:relative;}
.left{width:35%;background:linear-gradient(135deg,#ff6b6b,#c0392b);color:#fff;padding:40px;display:flex;flex-direction:column;justify-content:space-between;}
.price{font-size:30px;font-weight:bold;}
.right{width:65%;display:flex;}
.menu{width:35%;background:#f6f8fb;padding:25px;border-right:1px solid #eee;}
.menu div{padding:12px;border-radius:8px;margin-bottom:8px;cursor:pointer;}
.menu div.active{background:#ffe0e0;font-weight:bold;}
.menu div:hover{background:#fff0f0;}
.content{width:65%;padding:30px;}
.timer{text-align:right;font-size:14px;color:#666;}
.qr-box{background:#f4f6f9;padding:20px;border-radius:14px;text-align:center;margin-top:15px;}
.qr-box img{width:170px;}
input, select{width:100%;padding:10px;margin-top:10px;border:1px solid #ccc;border-radius:6px;}
button{margin-top:15px;width:100%;padding:14px;background:#c0392b;color:#fff;border:none;border-radius:8px;font-weight:bold;cursor:pointer;}
button:hover{background:#a93226;}
.loader{display:none;margin-top:15px;text-align:center;font-weight:bold;}
.success{display:none;position:absolute;width:100%;height:100%;background:#16a34a;color:#fff;align-items:center;justify-content:center;flex-direction:column;font-size:22px;}
</style>
</head>
<body>

<div class="overlay">
<div class="box">

<div class="left">
  <div>
    <h2>Clothing Adda</h2>
    <div>Order Summary</div>
    <div class="price">₹<?php echo $total; ?></div>
  </div>
  <div>Secure Payment</div>
</div>

<div class="right">

<div class="menu">
  <div class="active" onclick="showTab('upi',this)">UPI</div>
  <div onclick="showTab('card',this)">Cards</div>
  <div onclick="showTab('netbanking',this)">Netbanking</div>
  <div onclick="showTab('wallet',this)">Wallet</div>
</div>

<div class="content">

<div class="timer">Session expires in <span id="time">10:00</span></div>

<!-- UPI -->
<div id="upi">
  <div class="qr-box">
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=clothingadda@upi">
    <p>Scan QR or Enter UPI ID</p>
  </div>

  <!-- ✅ ID ADDED -->
  <input type="text" id="upi_id" placeholder="Enter UPI ID (example@upi)">

  <!-- ✅ QR BUTTON (SKIPS VALIDATION) -->
  <button onclick="processPayment(true)">I Have Paid via QR</button>
</div>

<!-- CARD -->
<div id="card" style="display:none;">
  <input type="text" id="card_number" placeholder="Card Number">
  <input type="text" id="card_expiry" placeholder="MM/YY">
  <input type="text" id="card_cvv" placeholder="CVV">
</div>

<!-- NETBANKING -->
<div id="netbanking" style="display:none;">
  <select id="bank">
    <option>Select Bank</option>
    <option>SBI</option>
    <option>HDFC</option>
    <option>ICICI</option>
  </select>
</div>

<!-- WALLET -->
<div id="wallet" style="display:none;">
  <select id="wallet_select">
    <option>Select Wallet</option>
    <option>Paytm</option>
    <option>PhonePe</option>
    <option>Amazon Pay</option>
  </select>
</div>

<!-- MAIN PAY BUTTON -->
<button onclick="processPayment()">Pay ₹<?php echo $total; ?></button>

<div class="loader" id="loader">Processing Payment...</div>

</div>
</div>

<div class="success" id="success">
✔ Payment Successful<br><br>
Redirecting...
</div>

</div>
</div>

<script>

/* TAB SWITCH */
function showTab(tab,el){
  document.querySelectorAll('.menu div').forEach(x=>x.classList.remove('active'));
  el.classList.add('active');

  document.getElementById('upi').style.display='none';
  document.getElementById('card').style.display='none';
  document.getElementById('netbanking').style.display='none';
  document.getElementById('wallet').style.display='none';

  document.getElementById(tab).style.display='block';
}

/* TIMER */
let minutes = 10;
let seconds = 0;

setInterval(function(){
  if(seconds==0){
    if(minutes==0){
      alert("Session expired!");
      window.location.href="checkout.php";
    }
    minutes--;
    seconds=59;
  } else {
    seconds--;
  }

  document.getElementById("time").innerHTML =
    minutes + ":" + (seconds<10 ? "0"+seconds : seconds);

},1000);

/* ✅ VALIDATION + PAYMENT */
function processPayment(isQR=false){

  let activeTab = document.querySelector('.menu .active').innerText.toLowerCase();

  // UPI VALIDATION (skip if QR)
  if(activeTab === "upi" && !isQR){
    let upi = document.getElementById("upi_id").value.trim();
    if(upi === ""){
      alert("Please enter UPI ID or click 'I Have Paid via QR'");
      return;
    }
  }

  // CARD
  if(activeTab === "cards"){
    if(
      document.getElementById("card_number").value.trim()==="" ||
      document.getElementById("card_expiry").value.trim()==="" ||
      document.getElementById("card_cvv").value.trim()===""
    ){
      alert("Please fill all card details");
      return;
    }
  }

  // NETBANKING
  if(activeTab === "netbanking"){
    if(document.getElementById("bank").value==="Select Bank"){
      alert("Please select a bank");
      return;
    }
  }

  // WALLET
  if(activeTab === "wallet"){
    if(document.getElementById("wallet_select").value==="Select Wallet"){
      alert("Please select a wallet");
      return;
    }
  }

  // ✅ PROCESS
  document.getElementById("loader").style.display="block";

  setTimeout(()=>{
    document.getElementById("success").style.display="flex";
    setTimeout(()=>{
     window.location.href="payment_success.php?success=1";
    },2000);
  },2000);
}

</script>

</body>
</html>
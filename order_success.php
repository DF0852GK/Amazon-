<?php
require_login();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT o.*, a.full_name,a.phone,a.address_line,a.city,a.state,a.postal_code,a.country FROM orders o JOIN addresses a ON a.id=o.address_id WHERE o.id=? AND o.user_id=?");
$stmt->execute([$id,$_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order) { header('Location: orders.php'); exit; }
include 'header.php';
?>
<div class="success-page">
<div class="success">Payment successful!</div>
<h1>Order #<?= (int)$order['id'] ?> confirmed</h1>
<p>Payment ID: <?= e($order['razorpay_payment_id']) ?></p>
<h3>Delivery address</h3>
<p><?= e($order['full_name']) ?><br><?= e($order['phone']) ?><br><?= e($order['address_line']) ?><br><?= e($order['city']) ?>, <?= e($order['state']) ?> - <?= e($order['postal_code']) ?><br><?= e($order['country']) ?></p>
<h2>Total: ₹<?= number_format($order['total_amount'],2) ?></h2>
<a class="btn" href="orders.php">View My Orders</a>
</div>
<?php include 'footer.php'; ?>

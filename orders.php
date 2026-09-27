<?php
require_once'checkout.php';

$stmt = $pdo->prepare("SELECT o.*, a.full_name,a.city,a.state,a.postal_code FROM orders o JOIN addresses a ON a.id=o.address_id WHERE o.user_id=? ORDER BY o.id DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
include 'header.php';
?>
<h2>My Orders</h2>
<?php if (!$orders): ?><div class="empty">No orders yet. <a href="index.php">Start shopping</a></div><?php endif; ?>
<div class="orders">
    <?php foreach ($orders as $o): ?>
    <div class="order-card">
        <div><strong>Order #<?= (int)$o['id'] ?></strong><span><?= e($o['created_at']) ?></span></div>
        <p>₹<?= number_format($o['total_amount'],2) ?> · Payment: <?= e($o['payment_status']) ?> · Status:
            <?= e($o['status']) ?></p>
        <p>Deliver to: <?= e($o['full_name']) ?>, <?= e($o['city']) ?>, <?= e($o['state']) ?> -
            <?= e($o['postal_code']) ?></p>
    </div>
    <?php endforeach; ?>
</div>
<?php include 'footer.php'; ?>
<?php
require_login();
$orderId = (int)($_POST['order_id'] ?? 0);
$paymentId = trim($_POST['razorpay_payment_id'] ?? '');
$razorpayOrderId = trim($_POST['razorpay_order_id'] ?? '');
$signature = trim($_POST['razorpay_signature'] ?? '');
if (!$orderId || !$paymentId || !$razorpayOrderId || !$signature) die('Invalid payment response.');

$stmt = $pdo->prepare('SELECT * FROM orders WHERE id=? AND user_id=?');
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order || $order['razorpay_order_id'] !== $razorpayOrderId) die('Payment order mismatch.');

$expected = hash_hmac('sha256', $razorpayOrderId . '|' . $paymentId, RAZORPAY_KEY_SECRET);
if (RAZORPAY_KEY_SECRET === 'YOUR_RAZORPAY_TEST_KEY_SECRET' || !hash_equals($expected, $signature)) die('Payment signature verification failed.');

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("UPDATE orders SET payment_status='paid', razorpay_payment_id=?, status='confirmed' WHERE id=? AND payment_status='pending'");
    $stmt->execute([$paymentId, $orderId]);

    $itemsStmt = $pdo->prepare('SELECT product_id, quantity FROM order_items WHERE order_id=?');
    $itemsStmt->execute([$orderId]);
    $items = $itemsStmt->fetchAll();
    $stockStmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id=? AND stock >= ?');
    foreach ($items as $item) {
        $stockStmt->execute([(int)$item['quantity'], (int)$item['product_id'], (int)$item['quantity']]);
        if ($stockStmt->rowCount() !== 1) throw new RuntimeException('Insufficient stock.');
    }
    $pdo->commit();
    unset($_SESSION['cart'], $_SESSION['checkout_order_id']);
    header('Location: order_success.php?id=' . $orderId); exit;
} catch (Throwable $e) {
    $pdo->rollBack();
    die('Payment received, but order confirmation failed. Please check My Orders.');
}

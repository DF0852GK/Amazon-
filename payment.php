<?php
require_login();
$orderId = (int)($_SESSION['checkout_order_id'] ?? 0);
if (!$orderId) { header('Location: cart.php'); exit; }
$stmt = $pdo->prepare("SELECT o.*, a.full_name, a.phone, a.address_line, a.city, a.state, a.postal_code, a.country FROM orders o JOIN addresses a ON a.id=o.address_id WHERE o.id=? AND o.user_id=?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order) { header('Location: index.php'); exit; }

$razorpayOrderId = $order['razorpay_order_id'];
$paymentReady = RAZORPAY_KEY_ID !== 'YOUR_RAZORPAY_TEST_KEY_ID' && RAZORPAY_KEY_SECRET !== 'YOUR_RAZORPAY_TEST_KEY_SECRET';
$apiError = '';

// Create a Razorpay order on the server. The secret key never reaches the browser.
if ($paymentReady && !$razorpayOrderId) {
    $payload = json_encode([
        'amount' => (int)round($order['total_amount'] * 100),
        'currency' => 'INR',
        'receipt' => 'SHOPKART_' . $order['id'],
        'notes' => ['shopkart_order_id' => (string)$order['id']]
    ]);
    $ch = curl_init('https://api.razorpay.com/v1/orders');
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload, CURLOPT_USERPWD => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET, CURLOPT_HTTPHEADER => ['Content-Type: application/json'], CURLOPT_TIMEOUT => 20]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    $data = json_decode($response ?: '', true);
    if (!$curlError && $httpCode >= 200 && $httpCode < 300 && !empty($data['id'])) {
        $razorpayOrderId = $data['id'];
        $save = $pdo->prepare('UPDATE orders SET razorpay_order_id=? WHERE id=? AND user_id=?');
        $save->execute([$razorpayOrderId, $orderId, $_SESSION['user_id']]);
    } else {
        $apiError = 'Razorpay order could not be created. Check your TEST keys and PHP cURL extension.';
        $paymentReady = false;
    }
}

$page_title = 'Payment | ShopKart';
include 'header.php';
?>
<div class="payment-layout">
  <section class="payment-card">
    <div class="payment-title"><div><span class="eyebrow">SECURE CHECKOUT</span><h2>Choose your payment</h2></div><div class="secure-badge">🔒 Secure</div></div>
    <p class="muted">Order #<?= (int)$order['id'] ?> · Delivery to <?= e($order['city']) ?>, <?= e($order['state']) ?></p>
    <?php if ($apiError): ?><div class="alert"><?= e($apiError) ?></div><?php endif; ?>
    <?php if (!$paymentReady): ?>
      <div class="setup-box"><h3>Payment setup required</h3><p>Add your Razorpay <strong>TEST Key ID</strong> and <strong>TEST Key Secret</strong> in <code>config.php</code>. After that, this page will open Razorpay Checkout with <strong>Credit/Debit Card, UPI, Netbanking and Wallets</strong>.</p></div>
    <?php else: ?>
      <div class="payment-methods"><div class="method active">💳 <strong>Card</strong><small>Credit / Debit Card</small></div><div class="method">📱 <strong>UPI</strong><small>Google Pay, PhonePe etc.</small></div><div class="method">🏦 <strong>Other</strong><small>Netbanking & Wallets</small></div></div>
      <button id="rzp-button" class="btn pay-btn">Pay ₹<?= number_format($order['total_amount'],2) ?></button>
      <p class="payment-note">You will enter your card details securely inside Razorpay Checkout. Your card number is <strong>not stored</strong> by this PHP demo.</p>
      <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
      <script>
      const options = {
        key: <?= json_encode(RAZORPAY_KEY_ID) ?>,
        amount: <?= (int)round($order['total_amount'] * 100) ?>,
        currency: "INR",
        name: "ShopKart",
        description: "Order #<?= (int)$order['id'] ?>",
        order_id: <?= json_encode($razorpayOrderId) ?>,
        prefill: {name: <?= json_encode($order['full_name']) ?>, contact: <?= json_encode($order['phone']) ?>},
        notes: {shopkart_order_id: "<?= (int)$order['id'] ?>"},
        theme: {color: "#111827"},
        handler: function (response) {
          const form = document.createElement('form'); form.method = 'POST'; form.action = 'payment_success.php';
          const fields = {order_id:"<?= (int)$order['id'] ?>", razorpay_payment_id:response.razorpay_payment_id, razorpay_order_id:response.razorpay_order_id, razorpay_signature:response.razorpay_signature};
          Object.entries(fields).forEach(([k,v]) => { const i=document.createElement('input'); i.type='hidden'; i.name=k; i.value=v||''; form.appendChild(i); });
          document.body.appendChild(form); form.submit();
        }
      };
      document.getElementById('rzp-button').onclick = function(e){e.preventDefault(); new Razorpay(options).open();};
      </script>
    <?php endif; ?>
  </section>
  <aside class="summary payment-summary"><h3>Order summary</h3><p><span>Order</span><strong>#<?= (int)$order['id'] ?></strong></p><p><span>Delivery</span><span><?= e($order['city']) ?></span></p><hr><p class="total-line"><strong>Total</strong><strong>₹<?= number_format($order['total_amount'],2) ?></strong></p></aside>
</div>
<?php include 'footer.php'; ?>

<?php
require_once'config.php';
$items = cart_items($pdo);
if (!$items) {
    header('Location: cart.php');
    exit;
}
$total = cart_total($items);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address_line = trim($_POST['address_line'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $postal = trim($_POST['postal_code'] ?? '');
    $country = trim($_POST['country'] ?? 'India');

    if (!$full_name || !$phone || !$address_line || !$city || !$state || !$postal || !$country) {
        $error = 'Please fill all delivery details.';
    } elseif (!preg_match('/^[0-9+\-\s]{10,15}$/', $phone)) {
        $error = 'Please enter a valid phone number.';
    } else {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO addresses (user_id,full_name,phone,address_line,city,state,postal_code,country) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$_SESSION['user_id'], $full_name, $phone, $address_line, $city, $state, $postal, $country]);
            $addressId = $pdo->lastInsertId();

            // For a production Razorpay integration, create the Razorpay Order server-side
            // using your secret key and store its ID in orders. This demo uses a local order
            // first, then opens Checkout with the amount.
            $stmt = $pdo->prepare("INSERT INTO orders (user_id,address_id,total_amount,payment_method,payment_status,status) VALUES (?,?,?,'razorpay','pending','created')");
            $stmt->execute([$_SESSION['user_id'], $addressId, $total]);
            $orderId = $pdo->lastInsertId();

            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id,product_id,product_name,price,quantity) VALUES (?,?,?,?,?)");
            foreach ($items as $item) {
                $stmtItem->execute([$orderId, $item['id'], $item['name'], $item['price'], $item['quantity']]);
            }
            $pdo->commit();
            $_SESSION['checkout_order_id'] = $orderId;
            header('Location: payment.php');
            exit;
        } catch (Throwable $e) {
            $pdo->rollBack();
            $error = 'Could not create order. Please try again.';
        }
    }
}
include 'header.php';
?>
<div class="checkout">
    <div>
        <h2>Delivery Address</h2>
        <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
        <form method="post" class="form">
            <label>Full name<input required name="full_name"
                    value="<?= e($_POST['full_name'] ?? $_SESSION['user_name'] ?? '') ?>"></label>
            <label>Mobile number<input required name="phone" maxlength="15" placeholder="10-digit mobile number"
                    value="<?= e($_POST['phone'] ?? '') ?>"></label>
            <label>Address / House / Street<input required name="address_line"
                    value="<?= e($_POST['address_line'] ?? '') ?>"></label>
            <div class="two">
                <label>City<input required name="city" value="<?= e($_POST['city'] ?? '') ?>"></label>
                <label>State<input required name="state" value="<?= e($_POST['state'] ?? '') ?>"></label>
            </div>
            <div class="two">
                <label>PIN code<input required name="postal_code" value="<?= e($_POST['postal_code'] ?? '') ?>"></label>
                <label>Country<input required name="country" value="<?= e($_POST['country'] ?? 'India') ?>"></label>
            </div>
            <button class="btn bg-primary">Continue to Payment</button>
        </form>
    </div>
    <aside class="summary">
        <h3>Order Summary</h3>
        <?php foreach ($items as $item): ?><p><?= e($item['name']) ?> × <?= (int)$item['quantity'] ?>
            <span>₹<?= number_format($item['subtotal'], 2) ?></span>
        </p><?php endforeach; ?>
        <hr><strong>Total <span>₹<?= number_format($total, 2) ?></span></strong>
    </aside>
</div>
<?php include 'footer.php'; ?>
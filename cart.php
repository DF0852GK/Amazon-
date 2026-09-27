<?php
require_once 'config.php';
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['product_id'] ?? 0);
    if ($action === 'add' && $id > 0) {
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id=?");
        $stmt->execute([$id]);
        $p = $stmt->fetch();
        if ($p && $p['stock'] > 0) {
            $_SESSION['cart'][$id] = min(($_SESSION['cart'][$id] ?? 0) + 1, (int)$p['stock']);
        }
    } elseif ($action === 'update') {
        foreach (($_POST['qty'] ?? []) as $id => $qty) {
            $id = (int)$id;
            $qty = max(0, (int)$qty);
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id=?");
            $stmt->execute([$id]);
            $p = $stmt->fetch();
            if (!$p || $qty === 0) unset($_SESSION['cart'][$id]);
            else $_SESSION['cart'][$id] = min($qty, (int)$p['stock']);
        }
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$id]);
    }
    header('Location: cart.php');
    exit;
}
$items = cart_items($pdo);
$total = cart_total($items);
include 'header.php';
?>
<h2>Your Cart</h2>
<?php if (!$items): ?>
<div class="empty">
    <p>Your cart is empty.</p><a class="btn" href="index.php">Continue Shopping</a>
</div>
<?php else: ?>
<form method="post">
    <input type="hidden" name="action" value="update">
    <div class="cart-list">
        <?php foreach ($items as $item): ?>
        <div class="cart-item">
            <img src="<?= e($item['image_url']) ?>" alt="">
            <div>
                <h3><?= e($item['name']) ?></h3>
                <p>₹<?= number_format($item['price'], 2) ?></p>
            </div>
            <input class="qty" type="number" min="0" max="<?= (int)$item['stock'] ?>"
                name="qty[<?= (int)$item['id'] ?>]" value="<?= (int)$item['quantity'] ?>">
            <strong>₹<?= number_format($item['subtotal'], 2) ?></strong>
        </div>
        <?php endforeach; ?>
    </div>
    <button class="btn secondary btn-primary">Update Cart</button>
</form>
<div class="checkout-box">
    <h3>Total: ₹<?= number_format($total, 2) ?></h3>
    <a class="btn  bg-primary" href="checkout.php">Proceed to Checkout</a>
</div>
<?php endif; ?>
<?php include 'footer.php'; ?>
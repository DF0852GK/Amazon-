<?php
// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'shopkart');
define('DB_USER', 'root');
define('DB_PASS', '');

// Razorpay TEST key ID only.
// Never put the Razorpay secret key in this file if it will be committed publicly.
define('RAZORPAY_KEY_ID', 'YOUR_RAZORPAY_TEST_KEY_ID');
define('RAZORPAY_KEY_SECRET', 'YOUR_RAZORPAY_TEST_KEY_SECRET');

session_start();

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed. Check config.php and MySQL.');
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php?redirect=checkout.php');
        exit;
    }
}

function cart_count() {
    return array_sum($_SESSION['cart'] ?? []);
}

function cart_items($pdo) {
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
    foreach ($products as &$p) {
        $p['quantity'] = (int)$cart[$p['id']];
        $p['subtotal'] = $p['price'] * $p['quantity'];
    }
    return $products;
}

function cart_total($items) {
    return array_reduce($items, fn($sum, $item) => $sum + $item['subtotal'], 0);
}
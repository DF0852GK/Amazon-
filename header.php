<?php require_once __DIR__ . '/config.php'; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="ShopKart - modern PHP MySQL e-commerce store">
    <title><?= e($page_title ?? 'ShopKart') ?></title>
    <link rel="icon" type="image/svg+xml" href="assets/logo.svg">
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <header class="site-header">
        <div class="container nav">
            <a class="brand" href="index.php"><img src="assets/logo.svg"
                    alt="ShopKart logo"><span>Shop<span>Kart</span></span></a>
            <form class="search" action="index.php" method="get" role="search">
                <input id="searchInput" name="q" autocomplete="off" placeholder="Search products..."
                    value="<?= e($_GET['q'] ?? '') ?>">
                <button type="submit">Search</button>
            </form>
            <nav>
                <a href="index.php">Home</a>
                <a href="cart.php">Cart <span class="cart-badge"><?= cart_count() ?></span></a>
                <?php if (is_logged_in()): ?>
                <a href="orders.php">My Orders</a>
                <a href="logout.php">Logout</a>
                <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
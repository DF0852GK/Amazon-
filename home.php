<?php
require_once 'config.php';
$page_title = 'ShopKart | Online Store';
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ? ORDER BY id DESC");
    $like = "%$q%";
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
}
$products = $stmt->fetchAll();
include 'header.php';
?>

<?php if ($q === ''): ?>
<section class="home-hero">
    <div class="hero-copy">
        <span class="eyebrow">WELCOME TO SHOPKART</span>
        <h1>Everything you need, <span>all in one place.</span></h1>
        <p>Discover useful tech, accessories and everyday essentials at simple prices.</p>
        <div class="hero-actions">
            <a class="btn hero-btn" href="#products">Shop now</a>
            <?php if (!is_logged_in()): ?><a class="btn secondary" href="register.php">Create account</a><?php endif; ?>
        </div>
        <div class="trust-row"><span>✓ Secure checkout</span><span>✓ Easy cart</span><span>✓ Order tracking</span></div>
    </div>
    <div class="hero-visual">
        <div class="hero-logo-card"><img src="assets/logo.svg" alt="ShopKart"></div>
        <div class="floating-card one">⚡ Fast shopping</div>
        <div class="floating-card two">💳 Card & UPI</div>
        <div class="floating-card three">📦 Easy orders</div>
    </div>
</section>

<section class="feature-strip">
    <div><b>🚚 Fast delivery</b><span>Convenient order flow</span></div>
    <div><b>💳 Secure payments</b><span>Powered by Razorpay Checkout</span></div>
    <div><b>🔐 Safe account</b><span>Password hashing included</span></div>
</section>
<?php endif; ?>

<section id="products" class="products-section <?= $q !== '' ? 'search-page' : '' ?>">
    <div class="section-heading">
        <div>
            <span class="eyebrow"><?= $q !== '' ? 'SHOPKART SEARCH' : 'OUR COLLECTION' ?></span>
            <h2><?= $q ? 'Search results' : 'Featured products' ?></h2>
            <?php if ($q !== ''): ?><p class="search-query">Showing results for <strong><?= e($q) ?></strong></p>
            <?php endif; ?>
        </div>
        <span class="result-count"><?= count($products) ?> item<?= count($products) === 1 ? '' : 's' ?></span>
    </div>
    <?php if (!$products): ?>
    <div class="empty">
        <h3>No products found</h3>
        <p>Try another search term.</p><a class="btn" href="index.php">View all products</a>
    </div>
    <?php else: ?>
    <div class="grid <?= $q !== '' ? 'search-results-grid' : '' ?>" id="productGrid">
        <?php foreach ($products as $p): ?>
        <article class="card product-card">
            <div class="product-image-wrap"><img src="<?= e($p['image_url']) ?>" alt="<?= e($p['name']) ?>"
                    loading="lazy"><span class="stock-pill"><?= (int)$p['stock'] > 0 ? 'In stock' : 'Sold out' ?></span>
            </div>
            <div class="card-body">
                <h3><?= e($p['name']) ?></h3>
                <p><?= e($p['description']) ?></p>
                <div class="price-row">
                    <strong>₹<?= number_format($p['price'], 2) ?></strong><span><?= (int)$p['stock'] ?> left</span>
                </div>
                <?php if ($p['stock'] > 0): ?>
                <form action="cart.php" method="post"><input type="hidden" name="action" value="add"><input
                        type="hidden" name="product_id" value="<?= (int)$p['id'] ?>"><button type="submit"
                        class="btn w-100 bg-primary">Add to Cart</button></form>
                <?php else: ?><button class="btn disabled w-100 bg-primary" disabled>Out of
                    stock</button><?php endif; ?>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
<?php include 'footer.php'; ?>
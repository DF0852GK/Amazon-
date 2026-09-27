<?php
require_once 'config.php';
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}
$error = '';
$redirect = $_GET['redirect'] ?? 'home.php';
$allowed = ['checkout.php', 'home.php'];
if (!in_array($redirect, $allowed, true)) $redirect = 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: ' . $redirect);
        exit;
    }
    $error = 'Invalid email or password.';
}
include 'header.php';
?>
<div class="form-wrap">
    <h2>Login</h2>
    <?php if (isset($_GET['registered'])): ?><div class="success">Account created. Please login.</div><?php endif; ?>
    <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="form">
        <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
        <label>Email<input required type="email" name="email"></label>
        <label>Password<input required type="password" name="password"></label>
        <button class="btn bg-primary ">Login</button>
    </form>
    <p>New here? <a href="register.php">Create an account</a></p>
</div>
<?php include 'footer.php'; ?>
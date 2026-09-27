<?php
require_once 'config.php';
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8 || $password !== $confirm) {
        $error = 'Enter valid details. Password must be at least 8 characters and both passwords must match.';
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash) VALUES (?,?,?)");
            $stmt->execute([$name, $email, $hash]);
            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000' ? 'Email already registered.' : 'Registration failed.';
        }
    }
}
include 'header.php';
?>
<div class="form-wrap">
    <h2>Create account</h2>
    <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="form">
        <label>Name<input required name="name" value="<?= e($_POST['name'] ?? '') ?>"></label>
        <label>Email<input required type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>"></label>
        <label>Password<input required type="password" name="password" minlength="8"></label>
        <label>Confirm password<input required type="password" name="confirm_password" minlength="8"></label>
        <button class="btn bg-primary">Create Account</button>
    </form>
    <p>Already registered? <a href="login.php">Login</a></p>
</div>
<?php include 'footer.php'; ?>
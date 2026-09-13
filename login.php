<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$errors = [];
$rememberedEmail = $_COOKIE['remember_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if ($email === '' || $password === '') {
        $errors[] = 'Please enter both email and password.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, full_name, password FROM customers WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $customer = mysqli_fetch_assoc($result);

        if ($customer && password_verify($password, $customer['password'])) {
            // PHP Session
            $_SESSION['customer_id'] = $customer['id'];
            $_SESSION['customer_name'] = $customer['full_name'];

            // Link any guest cart items to this customer
            $sid = mysqli_real_escape_string($conn, $_SESSION['cart_sid']);
            mysqli_query($conn, "UPDATE cart SET customer_id = {$customer['id']} WHERE session_id = '$sid'");

            // Cookie requirement: remember email for next visit
            if ($remember) {
                setcookie('remember_email', $email, time() + (86400 * 30), '/');
            } else {
                setcookie('remember_email', '', time() - 3600, '/');
            }

            logActivity("Customer logged in: $email");
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}

$pageTitle = 'Login';
require_once __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-5">
      <div class="auth-card">
        <h3 class="fw-bold text-center mb-1">Welcome Back</h3>
        <p class="text-muted text-center mb-4">Login to continue shopping</p>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($rememberedEmail); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" <?php echo $rememberedEmail ? 'checked' : ''; ?>>
            <label class="form-check-label" for="rememberMe">Remember my email</label>
          </div>
          <button type="submit" class="btn btn-gradient w-100">Login</button>
        </form>
        <p class="text-center mt-3 mb-1">Don't have an account? <a href="register.php">Sign Up</a></p>
        <p class="text-center small text-muted">Test account: test@shopease.com / password123</p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

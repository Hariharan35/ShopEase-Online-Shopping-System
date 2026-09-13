<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($fullName === '') $errors[] = 'Full name is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirmPassword) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $checkEmail = mysqli_query($conn, "SELECT id FROM customers WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'");
        if (mysqli_num_rows($checkEmail) > 0) {
            $errors[] = 'An account with this email already exists.';
        }
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO customers (full_name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sssss', $fullName, $email, $hashed, $phone, $address);
        if (mysqli_stmt_execute($stmt)) {
            logActivity("New customer registered: $email");
            $success = true;
        } else {
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}

$pageTitle = 'Register';
require_once __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="auth-card">
        <h3 class="fw-bold text-center mb-1">Create Your Account</h3>
        <p class="text-muted text-center mb-4">Join ShopEase and start shopping today</p>

        <?php if ($success): ?>
          <div class="alert alert-success">Registration successful! You can now <a href="login.php">login here</a>.</div>
        <?php else: ?>

          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form method="POST" action="register.php" id="registerForm" novalidate>
            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" name="full_name" class="form-control" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Phone Number</label>
              <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Address</label>
              <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" id="regPassword" class="form-control" required minlength="6">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" id="regConfirmPassword" class="form-control" required minlength="6">
              </div>
            </div>
            <div id="pwdMatchMsg" class="small mb-3"></div>
            <button type="submit" class="btn btn-gradient w-100">Create Account</button>
          </form>
          <p class="text-center mt-3 mb-0">Already have an account? <a href="login.php">Login</a></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

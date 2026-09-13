<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/database.php';

// Cart count badge (from MySQL cart table, tied to this browser's session)
$cartCount = 0;
$sid = $_SESSION['cart_sid'];
$cq = mysqli_query($conn, "SELECT SUM(quantity) AS total FROM cart WHERE session_id = '" . mysqli_real_escape_string($conn, $sid) . "'");
if ($cq && $row = mysqli_fetch_assoc($cq)) {
    $cartCount = $row['total'] ? (int)$row['total'] : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ShopEase' : 'ShopEase - Shop Smarter'; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Top utility bar -->
<div class="top-bar d-none d-md-block">
  <div class="container d-flex justify-content-between align-items-center">
    <span><i class="fa-solid fa-truck-fast me-1"></i> Free delivery on orders above ₹999</span>
    <span>
      <?php if (isLoggedIn()): ?>
        <i class="fa-solid fa-user me-1"></i> Hi, <?php echo htmlspecialchars(currentCustomerName()); ?>
      <?php else: ?>
        Welcome to ShopEase
      <?php endif; ?>
    </span>
  </div>
</div>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top shopease-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
      <i class="fa-solid fa-bag-shopping me-2"></i>Shop<span class="brand-accent">Ease</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="products.php">All Products</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Categories</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="products.php?category=Electronics">Electronics</a></li>
            <li><a class="dropdown-item" href="products.php?category=Fashion">Fashion</a></li>
            <li><a class="dropdown-item" href="products.php?category=Home+%26+Kitchen">Home & Kitchen</a></li>
            <li><a class="dropdown-item" href="products.php?category=Books">Books</a></li>
            <li><a class="dropdown-item" href="products.php?category=Toys">Toys</a></li>
            <li><a class="dropdown-item" href="products.php?category=Sports">Sports</a></li>
          </ul>
        </li>
      </ul>

      <form class="d-flex search-form me-3" role="search" action="products.php" method="GET">
        <input class="form-control" type="search" name="search" id="navSearchInput" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button class="btn btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>

      <ul class="navbar-nav align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link position-relative" href="cart.php">
            <i class="fa-solid fa-cart-shopping fs-5"></i>
            <span class="badge rounded-pill bg-danger cart-badge" id="cartCount"><?php echo $cartCount; ?></span>
          </a>
        </li>
        <?php if (isLoggedIn()): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="fa-solid fa-user-circle fs-5"></i></a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><span class="dropdown-item-text fw-semibold"><?php echo htmlspecialchars(currentCustomerName()); ?></span></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="checkout.php">My Orders</a></li>
              <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-2" href="login.php">Login</a></li>
          <li class="nav-item"><a class="btn btn-light btn-sm ms-2" href="register.php">Sign Up</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Toast container for JS/jQuery notifications -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
  <div id="appToast" class="toast align-items-center text-bg-success border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="appToastBody">Done!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<main>

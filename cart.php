<?php
$pageTitle = 'My Cart';
require_once __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
  <div class="section-heading">
    <h2>Shopping Cart</h2>
    <p>Review your items before checkout</p>
  </div>

  <div class="row" id="cartContainer">
    <div class="col-lg-8">
      <div id="cartItemsWrap">
        <!-- Populated by jQuery/AJAX from ajax/cart.php -->
      </div>
      <div id="emptyCartMsg" class="text-center py-5" style="display:none;">
        <i class="fa-solid fa-cart-shopping fa-4x text-muted mb-3"></i>
        <h5>Your cart is empty</h5>
        <p class="text-muted">Looks like you haven't added anything yet.</p>
        <a href="products.php" class="btn btn-gradient">Start Shopping</a>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="cart-summary-box">
        <h5 class="fw-bold mb-3">Order Summary</h5>
        <div class="d-flex justify-content-between mb-2">
          <span>Subtotal</span>
          <span id="summarySubtotal">₹0.00</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span>Delivery</span>
          <span id="summaryDelivery">₹0.00</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
          <span>Total</span>
          <span id="summaryTotal">₹0.00</span>
        </div>
        <a href="checkout.php" class="btn btn-gradient w-100" id="checkoutBtn">Proceed to Checkout</a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

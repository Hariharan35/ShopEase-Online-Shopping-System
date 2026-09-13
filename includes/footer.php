</main>

<footer class="shopease-footer mt-5">
  <div class="container py-5">
    <div class="row g-4">
      <div class="col-md-4">
        <h5 class="fw-bold text-white mb-3"><i class="fa-solid fa-bag-shopping me-2"></i>ShopEase</h5>
        <p class="text-white-50">Your one-stop online store for electronics, fashion, home essentials, books and more — delivered fast, priced right.</p>
        <div class="social-icons">
          <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#"><i class="fa-brands fa-instagram"></i></a>
          <a href="#"><i class="fa-brands fa-twitter"></i></a>
          <a href="#"><i class="fa-brands fa-youtube"></i></a>
        </div>
      </div>
      <div class="col-md-2 col-6">
        <h6 class="text-white fw-semibold mb-3">Shop</h6>
        <ul class="list-unstyled footer-links">
          <li><a href="products.php">All Products</a></li>
          <li><a href="products.php?category=Electronics">Electronics</a></li>
          <li><a href="products.php?category=Fashion">Fashion</a></li>
          <li><a href="products.php?category=Sports">Sports</a></li>
        </ul>
      </div>
      <div class="col-md-2 col-6">
        <h6 class="text-white fw-semibold mb-3">Account</h6>
        <ul class="list-unstyled footer-links">
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Register</a></li>
          <li><a href="cart.php">My Cart</a></li>
          <li><a href="checkout.php">My Orders</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <h6 class="text-white fw-semibold mb-3">Stay Updated</h6>
        <p class="text-white-50">Subscribe for offers and new arrivals.</p>
        <form class="d-flex" onsubmit="return false;">
          <input type="email" class="form-control me-2" placeholder="Your email">
          <button class="btn btn-gradient">Join</button>
        </form>
      </div>
    </div>
    <hr class="border-secondary">
    <p class="text-center text-white-50 mb-0">&copy; <?php echo date('Y'); ?> ShopEase. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="js/app.js"></script>
<script src="js/cart.js"></script>
</body>
</html>

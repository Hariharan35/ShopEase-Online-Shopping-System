<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';

$featured = mysqli_query($conn, "SELECT * FROM products ORDER BY rating DESC LIMIT 8");
$categories = [
    ['name' => 'Electronics', 'icon' => 'fa-mobile-screen'],
    ['name' => 'Fashion', 'icon' => 'fa-shirt'],
    ['name' => 'Home & Kitchen', 'icon' => 'fa-kitchen-set'],
    ['name' => 'Books', 'icon' => 'fa-book'],
    ['name' => 'Toys', 'icon' => 'fa-puzzle-piece'],
    ['name' => 'Sports', 'icon' => 'fa-dumbbell'],
];
?>

<!-- HERO -->
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 hero-text">
        <span class="badge bg-light text-primary mb-3 px-3 py-2">🔥 Mega Sale — Up to 40% Off</span>
        <h1 class="display-4 fw-bold text-white mb-3">Shop Smarter.<br>Live Better.</h1>
        <p class="lead text-white-50 mb-4">Discover top electronics, trendy fashion, and everyday essentials — all in one place, at prices you'll love.</p>
        <a href="products.php" class="btn btn-lg btn-gradient px-4">Shop Now <i class="fa-solid fa-arrow-right ms-2"></i></a>
      </div>
      <div class="col-lg-6 d-none d-lg-block text-center">
        <img src="https://images.unsplash.com/photo-1607082349566-187342175e2f?w=700" class="hero-img" alt="Shopping">
      </div>
    </div>
  </div>
</section>

<!-- CATEGORIES -->
<section class="container py-5">
  <div class="section-heading">
    <h2>Shop by Category</h2>
    <p>Find exactly what you're looking for</p>
  </div>
  <div class="row g-3 g-md-4">
    <?php foreach ($categories as $cat): ?>
    <div class="col-6 col-md-4 col-lg-2">
      <a href="products.php?category=<?php echo urlencode($cat['name']); ?>" class="category-card">
        <div class="cat-icon"><i class="fa-solid <?php echo $cat['icon']; ?>"></i></div>
        <div class="cat-name"><?php echo htmlspecialchars($cat['name']); ?></div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- FEATURED PRODUCTS (loaded server-side, further filtering happens via AJAX on products.php) -->
<section class="container py-4">
  <div class="section-heading">
    <h2>Featured Products</h2>
    <p>Hand-picked items our customers love</p>
  </div>
  <div class="row g-4" id="featuredProductsGrid">
    <?php while ($p = mysqli_fetch_assoc($featured)): ?>
      <div class="col-6 col-md-4 col-lg-3">
        <?php include __DIR__ . '/includes/product_card.php'; ?>
      </div>
    <?php endwhile; ?>
  </div>
  <div class="text-center mt-4">
    <a href="products.php" class="btn btn-outline-primary btn-lg">View All Products</a>
  </div>
</section>

<!-- PROMO BANNERS -->
<section class="container py-5">
  <div class="row g-4">
    <div class="col-md-6">
      <div class="promo-banner promo-blue">
        <h3>New Arrivals</h3>
        <p>Fresh electronics just landed</p>
        <a href="products.php?category=Electronics" class="btn btn-light btn-sm">Explore</a>
      </div>
    </div>
    <div class="col-md-6">
      <div class="promo-banner promo-purple">
        <h3>Fashion Sale</h3>
        <p>Up to 40% off on trending styles</p>
        <a href="products.php?category=Fashion" class="btn btn-light btn-sm">Explore</a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

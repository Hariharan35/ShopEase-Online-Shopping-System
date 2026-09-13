<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
$product = $res ? mysqli_fetch_assoc($res) : null;

if (!$product) {
    header('Location: products.php');
    exit;
}

// Cookie + file-handling requirements
addRecentlyViewedCookie($product['id']);
logActivity("Viewed product #{$product['id']} - {$product['name']}");

$pageTitle = $product['name'];

// Related products (same category)
$related = mysqli_query($conn, "SELECT * FROM products WHERE category = '" . mysqli_real_escape_string($conn, $product['category']) . "' AND id != $id LIMIT 4");

// Recently viewed (from cookie)
$recentIds = array_diff(getRecentlyViewedIds(), [$product['id']]);
$recentProducts = [];
if (!empty($recentIds)) {
    $idsStr = implode(',', array_map('intval', $recentIds));
    $rres = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($idsStr)");
    while ($r = mysqli_fetch_assoc($rres)) { $recentProducts[] = $r; }
}

require_once __DIR__ . '/includes/header.php';

$hasDiscount = !empty($product['discount_price']) && $product['discount_price'] < $product['price'];
$discountPct = $hasDiscount ? round((($product['price'] - $product['discount_price']) / $product['price']) * 100) : 0;
$displayPrice = $hasDiscount ? $product['discount_price'] : $product['price'];
?>

<section class="container py-5">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="products.php?category=<?php echo urlencode($product['category']); ?>"><?php echo htmlspecialchars($product['category']); ?></a></li>
      <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
    </ol>
  </nav>

  <div class="row g-5">
    <div class="col-md-6">
      <div class="pd-image-wrap">
        <?php if ($hasDiscount): ?><span class="discount-badge">-<?php echo $discountPct; ?>%</span><?php endif; ?>
        <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid rounded pd-main-img" id="pdMainImage" alt="<?php echo htmlspecialchars($product['name']); ?>">
      </div>
    </div>
    <div class="col-md-6">
      <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
      <h2 class="fw-bold mt-2"><?php echo htmlspecialchars($product['name']); ?></h2>
      <div class="product-rating mb-3">
        <?php
          $rating = round($product['rating']);
          for ($i = 1; $i <= 5; $i++) {
            echo $i <= $rating ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
          }
        ?>
        <span class="rating-num">(<?php echo number_format($product['rating'], 1); ?> rating)</span>
      </div>

      <div class="pd-price mb-3">
        <span class="current-price fs-2">₹<?php echo number_format($displayPrice, 2); ?></span>
        <?php if ($hasDiscount): ?>
          <span class="old-price fs-5">₹<?php echo number_format($product['price'], 2); ?></span>
          <span class="badge bg-success ms-2">You save ₹<?php echo number_format($product['price'] - $product['discount_price'], 2); ?></span>
        <?php endif; ?>
      </div>

      <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>

      <p class="mb-3">
        <?php if ($product['stock'] > 0): ?>
          <span class="text-success fw-semibold"><i class="fa-solid fa-circle-check me-1"></i>In Stock (<?php echo (int)$product['stock']; ?> available)</span>
        <?php else: ?>
          <span class="text-danger fw-semibold"><i class="fa-solid fa-circle-xmark me-1"></i>Out of Stock</span>
        <?php endif; ?>
      </p>

      <div class="d-flex align-items-center gap-3 mb-4">
        <div class="qty-selector">
          <button class="qty-btn" id="pdQtyMinus">−</button>
          <input type="text" id="pdQty" value="1" readonly>
          <button class="qty-btn" id="pdQtyPlus">+</button>
        </div>
        <button class="btn btn-add-cart flex-grow-1" id="pdAddToCart" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo $displayPrice; ?>">
          <i class="fa-solid fa-cart-plus me-2"></i>Add to Cart
        </button>
      </div>

      <ul class="list-unstyled pd-features">
        <li><i class="fa-solid fa-truck me-2"></i>Free delivery on orders above ₹999</li>
        <li><i class="fa-solid fa-rotate-left me-2"></i>7-day easy returns</li>
        <li><i class="fa-solid fa-shield-halved me-2"></i>100% secure payment</li>
      </ul>
    </div>
  </div>

  <?php if (mysqli_num_rows($related) > 0): ?>
  <div class="section-heading mt-5">
    <h2>You May Also Like</h2>
  </div>
  <div class="row g-4">
    <?php while ($p = mysqli_fetch_assoc($related)): ?>
      <div class="col-6 col-md-3">
        <?php include __DIR__ . '/includes/product_card.php'; ?>
      </div>
    <?php endwhile; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($recentProducts)): ?>
  <div class="section-heading mt-5">
    <h2>Recently Viewed</h2>
  </div>
  <div class="row g-4">
    <?php foreach ($recentProducts as $p): ?>
      <div class="col-6 col-md-3">
        <?php include __DIR__ . '/includes/product_card.php'; ?>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

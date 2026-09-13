<?php
/**
 * Expects $p (assoc array of a product row) to be set before include.
 */
$hasDiscount = !empty($p['discount_price']) && $p['discount_price'] < $p['price'];
$discountPct = $hasDiscount ? round((($p['price'] - $p['discount_price']) / $p['price']) * 100) : 0;
$displayPrice = $hasDiscount ? $p['discount_price'] : $p['price'];
?>
<div class="product-card" data-id="<?php echo $p['id']; ?>">
  <?php if ($hasDiscount): ?>
    <span class="discount-badge">-<?php echo $discountPct; ?>%</span>
  <?php endif; ?>
  <a href="product-details.php?id=<?php echo $p['id']; ?>" class="product-img-wrap">
    <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" loading="lazy">
    <span class="quick-view">Quick View</span>
  </a>
  <div class="product-info">
    <span class="product-category"><?php echo htmlspecialchars($p['category']); ?></span>
    <h6 class="product-name">
      <a href="product-details.php?id=<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></a>
    </h6>
    <div class="product-rating">
      <?php
        $rating = round($p['rating']);
        for ($i = 1; $i <= 5; $i++) {
          echo $i <= $rating ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
        }
      ?>
      <span class="rating-num">(<?php echo number_format($p['rating'], 1); ?>)</span>
    </div>
    <div class="product-price">
      <span class="current-price">₹<?php echo number_format($displayPrice, 2); ?></span>
      <?php if ($hasDiscount): ?>
        <span class="old-price">₹<?php echo number_format($p['price'], 2); ?></span>
      <?php endif; ?>
    </div>
    <button class="btn btn-add-cart w-100 add-to-cart-btn"
            data-id="<?php echo $p['id']; ?>"
            data-name="<?php echo htmlspecialchars($p['name']); ?>"
            data-price="<?php echo $displayPrice; ?>">
      <i class="fa-solid fa-cart-plus me-1"></i> Add to Cart
    </button>
  </div>
</div>

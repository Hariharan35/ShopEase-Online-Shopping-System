<?php
$pageTitle = 'All Products';
require_once __DIR__ . '/includes/header.php';

$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$search   = isset($_GET['search']) ? trim($_GET['search']) : '';

// File handling requirement: log every search performed
if ($search !== '') {
    logActivity("Search performed: \"$search\"");
}

$allCatsRes = mysqli_query($conn, "SELECT DISTINCT category FROM products ORDER BY category");
$allCats = [];
while ($c = mysqli_fetch_assoc($allCatsRes)) { $allCats[] = $c['category']; }
?>

<section class="container py-4">
  <div class="section-heading">
    <h2><?php echo $category ? htmlspecialchars($category) : 'All Products'; ?></h2>
    <p>Browse our full collection — filter, search and discover</p>
  </div>

  <div class="row">
    <!-- Sidebar filters -->
    <div class="col-lg-3 mb-4">
      <div class="filter-box">
        <h6 class="fw-bold mb-3"><i class="fa-solid fa-sliders me-2"></i>Categories</h6>
        <ul class="list-unstyled category-filter-list">
          <li><a href="#" class="cat-filter-link <?php echo $category === '' ? 'active' : ''; ?>" data-category="">All Products</a></li>
          <?php foreach ($allCats as $c): ?>
            <li><a href="#" class="cat-filter-link <?php echo $category === $c ? 'active' : ''; ?>" data-category="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></a></li>
          <?php endforeach; ?>
        </ul>

        <hr>
        <h6 class="fw-bold mb-3"><i class="fa-solid fa-sort me-2"></i>Sort By</h6>
        <select class="form-select" id="sortSelect">
          <option value="default">Featured</option>
          <option value="price_asc">Price: Low to High</option>
          <option value="price_desc">Price: High to Low</option>
          <option value="rating">Top Rated</option>
        </select>
      </div>
    </div>

    <!-- Product grid -->
    <div class="col-lg-9">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span id="resultCount" class="text-muted"></span>
        <div class="input-group search-inline d-none d-lg-flex" style="max-width:300px;">
          <input type="text" class="form-control" id="liveSearchInput" placeholder="Refine search..." value="<?php echo htmlspecialchars($search); ?>">
          <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
        </div>
      </div>

      <div id="productsLoader" class="text-center py-5" style="display:none;">
        <div class="spinner-border text-primary" role="status"></div>
      </div>

      <div class="row g-4" id="productsGrid">
        <!-- Loaded via AJAX (ajax/products.php) using jQuery -->
      </div>
    </div>
  </div>
</section>

<script>
  // Initial filter state passed from PHP to JS for first AJAX load
  var initialCategory = <?php echo json_encode($category); ?>;
  var initialSearch = <?php echo json_encode($search); ?>;
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

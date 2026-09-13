<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$search   = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort     = isset($_GET['sort']) ? trim($_GET['sort']) : 'default';

$where = [];
if ($category !== '') {
    $where[] = "category = '" . mysqli_real_escape_string($conn, $category) . "'";
}
if ($search !== '') {
    $safeSearch = mysqli_real_escape_string($conn, $search);
    $where[] = "(name LIKE '%$safeSearch%' OR description LIKE '%$safeSearch%' OR category LIKE '%$safeSearch%')";
}

$sql = "SELECT * FROM products";
if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY COALESCE(discount_price, price) ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY COALESCE(discount_price, price) DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY rating DESC";
        break;
    default:
        $sql .= " ORDER BY id ASC";
}

$result = mysqli_query($conn, $sql);
$count = mysqli_num_rows($result);

ob_start();
if ($count === 0) {
    echo '<div class="col-12 text-center py-5">
            <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
            <h5>No products found</h5>
            <p class="text-muted">Try a different search or category.</p>
          </div>';
} else {
    while ($p = mysqli_fetch_assoc($result)) {
        echo '<div class="col-6 col-md-4 col-lg-4">';
        include __DIR__ . '/../includes/product_card.php';
        echo '</div>';
    }
}
$html = ob_get_clean();

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'count'   => $count,
    'html'    => $html
]);

<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
requireLogin('login.php');

$customerId = (int)$_SESSION['customer_id'];
$sid = mysqli_real_escape_string($conn, $_SESSION['cart_sid']);
$message = '';
$error = '';

// Place order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $shippingAddress = trim($_POST['shipping_address'] ?? '');

    $cartRes = mysqli_query($conn, "SELECT c.product_id, c.quantity, COALESCE(p.discount_price, p.price) AS unit_price, p.stock
                                     FROM cart c JOIN products p ON c.product_id = p.id
                                     WHERE c.session_id = '$sid'");
    $items = [];
    $total = 0;
    while ($row = mysqli_fetch_assoc($cartRes)) {
        $items[] = $row;
        $total += $row['unit_price'] * $row['quantity'];
    }

    if (empty($items)) {
        $error = 'Your cart is empty.';
    } elseif ($shippingAddress === '') {
        $error = 'Please provide a shipping address.';
    } else {
        mysqli_begin_transaction($conn);
        try {
            $stmt = mysqli_prepare($conn, "INSERT INTO orders (customer_id, total_amount, status, shipping_address) VALUES (?, ?, 'Pending', ?)");
            mysqli_stmt_bind_param($stmt, 'ids', $customerId, $total, $shippingAddress);
            mysqli_stmt_execute($stmt);
            $orderId = mysqli_insert_id($conn);

            $itemStmt = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($items as $it) {
                mysqli_stmt_bind_param($itemStmt, 'iiid', $orderId, $it['product_id'], $it['quantity'], $it['unit_price']);
                mysqli_stmt_execute($itemStmt);
                mysqli_query($conn, "UPDATE products SET stock = GREATEST(0, stock - {$it['quantity']}) WHERE id = {$it['product_id']}");
            }

            mysqli_query($conn, "DELETE FROM cart WHERE session_id = '$sid'");
            mysqli_commit($conn);

            logActivity("Order #$orderId placed by customer ID $customerId - Total ₹$total");
            $message = "Order #$orderId placed successfully!";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = 'Failed to place order. Please try again.';
        }
    }
}

// Order history
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE customer_id = $customerId ORDER BY order_date DESC");

$pageTitle = 'Checkout';
require_once __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
  <div class="section-heading">
    <h2>Checkout</h2>
    <p>Confirm your shipping details and place your order</p>
  </div>

  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-6">
      <div class="cart-summary-box">
        <h5 class="fw-bold mb-3">Shipping Details</h5>
        <form method="POST" action="checkout.php">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars(currentCustomerName()); ?>" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">Shipping Address</label>
            <textarea name="shipping_address" class="form-control" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select class="form-select">
              <option>Cash on Delivery</option>
              <option>Credit / Debit Card</option>
              <option>UPI</option>
            </select>
          </div>
          <button type="submit" name="place_order" class="btn btn-gradient w-100">Place Order</button>
        </form>
      </div>
    </div>

    <div class="col-lg-6">
      <h5 class="fw-bold mb-3">My Order History</h5>
      <?php if (mysqli_num_rows($orders) === 0): ?>
        <p class="text-muted">You haven't placed any orders yet.</p>
      <?php else: ?>
        <div class="accordion" id="ordersAccordion">
          <?php $i = 0; while ($o = mysqli_fetch_assoc($orders)): $i++; ?>
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#order<?php echo $o['id']; ?>">
                  Order #<?php echo $o['id']; ?> — ₹<?php echo number_format($o['total_amount'], 2); ?>
                  <span class="badge bg-info text-dark ms-2"><?php echo htmlspecialchars($o['status']); ?></span>
                </button>
              </h2>
              <div id="order<?php echo $o['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#ordersAccordion">
                <div class="accordion-body">
                  <p class="small text-muted mb-2">Placed on <?php echo date('d M Y, h:i A', strtotime($o['order_date'])); ?></p>
                  <p class="small mb-2"><strong>Shipping to:</strong> <?php echo htmlspecialchars($o['shipping_address']); ?></p>
                  <?php
                    $itemsRes = mysqli_query($conn, "SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = {$o['id']}");
                    while ($it = mysqli_fetch_assoc($itemsRes)):
                  ?>
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                      <div class="d-flex align-items-center">
                        <img src="<?php echo htmlspecialchars($it['image']); ?>" width="40" height="40" class="rounded me-2" style="object-fit:cover;">
                        <span><?php echo htmlspecialchars($it['name']); ?> × <?php echo $it['quantity']; ?></span>
                      </div>
                      <span>₹<?php echo number_format($it['price'] * $it['quantity'], 2); ?></span>
                    </div>
                  <?php endwhile; ?>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

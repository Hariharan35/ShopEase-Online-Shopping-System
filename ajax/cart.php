<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$sid = mysqli_real_escape_string($conn, $_SESSION['cart_sid']);
$customerId = isLoggedIn() ? (int)$_SESSION['customer_id'] : null;

function cartSummary($conn, $sid) {
    $totalQty = 0;
    $totalAmount = 0;
    $items = [];
    $res = mysqli_query($conn, "SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.image,
                                        COALESCE(p.discount_price, p.price) AS unit_price
                                 FROM cart c JOIN products p ON c.product_id = p.id
                                 WHERE c.session_id = '$sid'");
    while ($row = mysqli_fetch_assoc($res)) {
        $lineTotal = $row['unit_price'] * $row['quantity'];
        $totalQty += (int)$row['quantity'];
        $totalAmount += $lineTotal;
        $items[] = [
            'cart_id'    => $row['cart_id'],
            'product_id' => $row['product_id'],
            'name'       => $row['name'],
            'image'      => $row['image'],
            'unit_price' => (float)$row['unit_price'],
            'quantity'   => (int)$row['quantity'],
            'line_total' => (float)$lineTotal
        ];
    }
    return ['items' => $items, 'total_qty' => $totalQty, 'total_amount' => $totalAmount];
}

switch ($action) {

    case 'add':
        $productId = (int)($_POST['product_id'] ?? 0);
        $qty = max(1, (int)($_POST['quantity'] ?? 1));
        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product']);
            exit;
        }
        $check = mysqli_query($conn, "SELECT id, quantity FROM cart WHERE session_id = '$sid' AND product_id = $productId");
        if ($row = mysqli_fetch_assoc($check)) {
            $newQty = $row['quantity'] + $qty;
            mysqli_query($conn, "UPDATE cart SET quantity = $newQty WHERE id = " . $row['id']);
        } else {
            $custVal = $customerId ? $customerId : 'NULL';
            mysqli_query($conn, "INSERT INTO cart (session_id, customer_id, product_id, quantity) VALUES ('$sid', $custVal, $productId, $qty)");
        }
        logActivity("Product ID $productId added to cart (qty $qty)");
        echo json_encode(['success' => true, 'message' => 'Added to cart', 'cart' => cartSummary($conn, $sid)]);
        break;

    case 'update':
        $cartId = (int)($_POST['cart_id'] ?? 0);
        $qty = (int)($_POST['quantity'] ?? 1);
        if ($qty <= 0) {
            mysqli_query($conn, "DELETE FROM cart WHERE id = $cartId AND session_id = '$sid'");
        } else {
            mysqli_query($conn, "UPDATE cart SET quantity = $qty WHERE id = $cartId AND session_id = '$sid'");
        }
        echo json_encode(['success' => true, 'message' => 'Cart updated', 'cart' => cartSummary($conn, $sid)]);
        break;

    case 'remove':
        $cartId = (int)($_POST['cart_id'] ?? 0);
        mysqli_query($conn, "DELETE FROM cart WHERE id = $cartId AND session_id = '$sid'");
        echo json_encode(['success' => true, 'message' => 'Item removed', 'cart' => cartSummary($conn, $sid)]);
        break;

    case 'get':
        echo json_encode(['success' => true, 'cart' => cartSummary($conn, $sid)]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
}

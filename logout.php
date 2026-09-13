<?php
require_once __DIR__ . '/includes/auth.php';

if (isset($_SESSION['customer_id'])) {
    logActivity("Customer logged out: " . ($_SESSION['customer_name'] ?? ''));
}

// Clear session data but keep the cart_sid cookie logic intact for guest cart
unset($_SESSION['customer_id']);
unset($_SESSION['customer_name']);
session_destroy();

header('Location: login.php');
exit;

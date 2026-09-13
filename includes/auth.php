<?php
/**
 * ShopEase - Session & Auth Helpers
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Every visitor (guest or logged in) gets a stable cart session id.
// This is what ties rows in the `cart` table to a browser.
if (!isset($_SESSION['cart_sid'])) {
    $_SESSION['cart_sid'] = session_id();
}

function isLoggedIn() {
    return isset($_SESSION['customer_id']);
}

function currentCustomerName() {
    return $_SESSION['customer_name'] ?? 'Guest';
}

function requireLogin($redirectTo = 'login.php') {
    if (!isLoggedIn()) {
        header('Location: ' . $redirectTo);
        exit;
    }
}

/**
 * File-handling requirement:
 * Append a line to data/recent_views.txt every time a product
 * is viewed or searched, so we keep a lightweight activity log
 * without needing a database table for it.
 */
function logActivity($message) {
    $logFile = __DIR__ . '/../data/recent_views.txt';
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    $fp = fopen($logFile, 'a');
    if ($fp) {
        fwrite($fp, $line);
        fclose($fp);
    }
}

/**
 * Cookie requirement:
 * Track the last few product IDs a visitor looked at in a cookie
 * so we can show a "Recently Viewed" strip even for guests.
 */
function addRecentlyViewedCookie($productId) {
    $recent = isset($_COOKIE['recently_viewed']) ? explode(',', $_COOKIE['recently_viewed']) : [];
    $recent = array_diff($recent, [$productId]); // remove duplicate if present
    array_unshift($recent, $productId);
    $recent = array_slice($recent, 0, 6);
    setcookie('recently_viewed', implode(',', $recent), time() + (86400 * 30), '/');
    $_COOKIE['recently_viewed'] = implode(',', $recent); // usable immediately in same request
}

function getRecentlyViewedIds() {
    if (!isset($_COOKIE['recently_viewed']) || $_COOKIE['recently_viewed'] === '') {
        return [];
    }
    return array_filter(array_map('intval', explode(',', $_COOKIE['recently_viewed'])));
}

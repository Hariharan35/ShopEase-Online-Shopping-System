<?php
/**
 * ShopEase - Database Connection
 * WAMP default MySQL credentials: user 'root', password ''
 */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'online_shopping');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error() .
        '<br>Make sure WAMP is running and the "online_shopping" database has been imported.');
}

mysqli_set_charset($conn, 'utf8mb4');

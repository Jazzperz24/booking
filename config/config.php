<?php
// ============================================================
// FILE: config.php
// PATH: config/config.php
// DESC: Database connection using PDO.
//
// HOW TO REQUIRE:
//   homepage.php (root)  →  require 'config/config.php';
//   pages/*.php          →  require '../config/config.php';
// ============================================================

session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'coach_booking_db');
define('DB_USER', 'root');
define('DB_PASS', '');                // XAMPP default is empty string

try {
    $db = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:40px;color:#f87171;background:#0e0e12">
            <h2>Database Connection Failed</h2>
            <p>' . htmlspecialchars($e->getMessage()) . '</p>
            <p style="color:#7b7b8e;font-size:13px">
                Check that XAMPP MySQL is running and your credentials in config.php are correct.
            </p>
         </div>');
}
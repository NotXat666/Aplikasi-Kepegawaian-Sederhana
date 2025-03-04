<?php
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

$host = "localhost";
$user = "root";
$password = "wecant";
$database = "db_kepegawaian";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("A system error occurred. Please try again later.");
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return hash_equals($_SESSION['csrf_token'], $token);
}

function is_authenticated() {
    return isset($_SESSION['logged_in'], $_SESSION['nip'], $_SESSION['user_role']) && $_SESSION['logged_in'];
}

function redirect_if_not_authenticated() {
    if (!is_authenticated()) {
        header("Location: index.php");
        exit();
    }
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
?>
<?php
require 'authentication.php'; // Admin authentication check 

// Database connection setup
$host = 'localhost';
$db = 'taskmatic';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Auth check
$user_id = $_SESSION['admin_id'];
$security_key = $_SESSION['security_key'];

if ($user_id == NULL || $security_key == NULL) {
    header('Location: index.php');
    exit();
}

// Handle removal of a member from the group
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $remove_user_id = $_POST['user_id'];

    // Set the user's group to an empty string
    $remove_stmt = $pdo->prepare("UPDATE tbl_admin SET user_group = '' WHERE user_id = :user_id");
    if ($remove_stmt->execute(['user_id' => $remove_user_id])) {
        header("Location: group-info.php?status=member_removed");
    } else {
        header("Location: group-info.php?status=error");
    }
    exit();
}

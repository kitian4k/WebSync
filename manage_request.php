<?php

require 'authentication.php'; // Admin authentication check 

// Database connection
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
$user_name = $_SESSION['name'];
$security_key = $_SESSION['security_key'];
if ($user_id == NULL || $security_key == NULL) {
    header('Location: index.php');
    exit();
}

// Check if action and request_id are set
if (isset($_POST['action'], $_POST['request_id'])) {
    $action = $_POST['action'];
    $request_id = $_POST['request_id'];

    // Retrieve the join request details
    $request_stmt = $pdo->prepare("SELECT user_id, user_group FROM group_join_requests WHERE request_id = :request_id AND status = 'Pending'");
    $request_stmt->execute(['request_id' => $request_id]);
    $request = $request_stmt->fetch(PDO::FETCH_ASSOC);

    if ($request) {
        $user_to_add = $request['user_id'];
        $user_group = $request['user_group'];

        if ($action == 'approve') {
            // Approve the request: Update user's group in `tbl_admin` and mark request as approved
            $pdo->beginTransaction();
            try {
                // Update the user's group in `tbl_admin`
                $update_user_stmt = $pdo->prepare("UPDATE tbl_admin SET user_group = :user_group WHERE user_id = :user_id");
                $update_user_stmt->execute([
                    'user_group' => $user_group,
                    'user_id' => $user_to_add
                ]);

                // Mark the request as approved
                $update_request_stmt = $pdo->prepare("UPDATE group_join_requests SET status = 'Approved' WHERE request_id = :request_id");
                $update_request_stmt->execute(['request_id' => $request_id]);

                $pdo->commit();
                header("Location: group-info.php?status=request_approved");
            } catch (Exception $e) {
                $pdo->rollBack();
                header("Location: group-info.php?status=error");
            }
        } elseif ($action == 'reject') {
            // Reject the request: Update the request status to rejected
            $update_request_stmt = $pdo->prepare("UPDATE group_join_requests SET status = 'Rejected' WHERE request_id = :request_id");
            $update_request_stmt->execute(['request_id' => $request_id]);
            header("Location: group-info.php?status=request_rejected");
        }
    } else {
        // If the request is not found or already processed
        header("Location: group-info.php?status=error");
    }
} else {
    header("Location: group-info.php?status=invalid_request");
}

exit();

<?php
require 'authentication.php'; // Ensure user is authenticated

// Initialize variables
$user_id = $_SESSION['admin_id'];
$user_name = $_SESSION['name'];
$security_key = $_SESSION['security_key'];
$user_role = $_SESSION['user_role'];

if ($user_id == NULL || $security_key == NULL) {
    header('Location: index.php');
    exit();
}

// Include database connection
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

// Initialize error and success messages
$success_msg = "";
$error_msg = "";

// Handle profile update form submission (Full Name & Email)
if (isset($_POST['update_profile'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];

    if ($fullname && $email) {
        $stmt = $pdo->prepare("UPDATE tbl_admin SET fullname = :fullname, email = :email WHERE user_id = :user_id");
        $stmt->execute([
            'fullname' => $fullname,
            'email' => $email,
            'user_id' => $user_id
        ]);
        $success_msg = "Profile updated successfully!";
    } else {
        $error_msg = "Please fill out all required fields.";
    }
}

// Reload user information from the database to display updated values
$stmt = $pdo->prepare("SELECT fullname, email FROM tbl_admin WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle password update form submission
if (isset($_POST['change_password'])) {
    $password = $_POST['password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if new passwords match
    if (!empty($new_password) && $new_password == $confirm_password) {
        $stmt = $pdo->prepare("UPDATE tbl_admin SET password = :password WHERE user_id = :user_id");
        $stmt->execute([
            'password' => password_hash($new_password, PASSWORD_DEFAULT),
            'user_id' => $user_id
        ]);
        $success_msg = "Password changed successfully!";
    } else {
        $error_msg = "Passwords do not match or are empty.";
    }
}

$page_name = "Profile";
include("include/sidebar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="row">
    <div class="col-md-12">
        <div class="well well-custom">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-1"></div>

    <div class="container mt-5">
        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <h4>Update Profile</h4>
            <form method="post" action="">
                <div class="form-group">
                    <label for="fullname">Full Name:</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user_info['fullname']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_info['email']); ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="col-md-6">
            <h4>Change Password</h4>
            <form method="post" action="">
                <div class="form-group">
                    <label for="password">Current Password:</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter current password">
                </div>

                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                </div>
                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>

    <hr>

    <!-- Contact Admin/Teacher Section -->
    <h4>Contact Admin/Teacher</h4>
    <p>If you have any issues or questions, please contact the admin or teacher via email:</p>
    <p><a href="mailto:admin@example.com">admin@tup.edu.ph</a></p>
</div>
</body>
</html>



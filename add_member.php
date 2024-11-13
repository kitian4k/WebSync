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

// Get the logged-in user's course and group information
$stmt = $pdo->prepare("SELECT user_course, user_group FROM tbl_admin WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

$user_course = $user_info['user_course'];
$user_group = $user_info['user_group'];

// Ensure that the logged-in user has a group
if (empty($user_group)) {
    header("Location: group-info.php?status=no_group");
    exit();
}

// Handle form submission for adding a member to the group
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $selected_user_id = $_POST['user_id'];

    // Update the selected user's group to match the logged-in user's group
    $update_stmt = $pdo->prepare("UPDATE tbl_admin SET user_group = :user_group WHERE user_id = :user_id");
    $update_success = $update_stmt->execute([
        'user_group' => $user_group,
        'user_id' => $selected_user_id
    ]);

    if ($update_success) {
        header("Location: group-info.php?status=member_added");
    } else {
        header("Location: group-info.php?status=error");
    }
    exit();
}

// Fetch users with the same course as the logged-in user and no group
$available_users_stmt = $pdo->prepare("SELECT user_id, fullname FROM tbl_admin WHERE user_course = :user_course AND (user_group IS NULL OR user_group = '') AND user_id != :user_id");
$available_users_stmt->execute([
    'user_course' => $user_course,
    'user_id' => $user_id,
]);

$page_name = "Add Group Member";
include("include/sidebar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_name; ?></title>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="well well-custom">
                <center><h3>Add Member to Group</h3></center>
                
                <form action="add_member.php" method="post">
                    <div class="form-group">
                        <label for="user_select">Select User to Add:</label>
                        <select name="user_id" id="user_select" class="form-control" required>
                            <option value="">-- Select a User --</option>
                            <?php
                            while ($user = $available_users_stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value=\"{$user['user_id']}\">{$user['fullname']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="add_member" class="btn btn-primary">Add to Group</button>
                    <a href="group-info.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("include/footer.php"); ?>

</body>
</html>

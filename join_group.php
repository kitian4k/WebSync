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

// Fetch the user's course
$user_course_stmt = $pdo->prepare("SELECT user_course FROM tbl_admin WHERE user_id = :user_id");
$user_course_stmt->execute(['user_id' => $user_id]);
$user_course_info = $user_course_stmt->fetch(PDO::FETCH_ASSOC);
$user_course = $user_course_info['user_course'];

// Fetch existing groups from the user's course
$groups_stmt = $pdo->prepare("SELECT DISTINCT user_group FROM tbl_admin WHERE user_course = :user_course AND user_group IS NOT NULL");
$groups_stmt->execute(['user_course' => $user_course]);
$groups = $groups_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for join request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_group = $_POST['user_group'];

    // Check if a pending request already exists for this user and group
    $check_request_stmt = $pdo->prepare("SELECT * FROM group_join_requests WHERE user_id = :user_id AND user_group = :user_group AND status = 'Pending'");
    $check_request_stmt->execute([
        'user_id' => $user_id,
        'user_group' => $selected_group,
    ]);

    if ($check_request_stmt->rowCount() > 0) {
        header("Location: join_group.php?status=already_requested");
        exit();
    }

    // Insert join request
    $stmt = $pdo->prepare("INSERT INTO group_join_requests (user_id, user_group, status, requested_at) VALUES (:user_id, :user_group, 'Pending', NOW())");
    $stmt->execute([
        'user_id' => $user_id,
        'user_group' => $selected_group,
    ]);

    header("Location: group-info.php?status=join_requested");
    exit();
}

$page_name = "Join Group";
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
                <center><h3>Request to Join Group</h3></center>
                
                <!-- Display Status Message -->
                <?php if (isset($_GET['status']) && $_GET['status'] == 'already_requested'): ?>
                    <div class="alert alert-warning">You already have a pending request for this group.</div>
                <?php endif; ?>

                <form action="join_group.php" method="post">
                    <div class="form-group">
                        <label for="user_group">Select Group:</label>
                        <select name="user_group" id="user_group" class="form-control" required>
                            <option value="">-- Select Group --</option>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?php echo htmlspecialchars($group['user_group']); ?>">
                                    <?php echo htmlspecialchars($group['user_group']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Request to Join</button>
                    <a href="group-info.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("include/footer.php"); ?>

</body>
</html>

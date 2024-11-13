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
$user_name = $_SESSION['name'];
$security_key = $_SESSION['security_key'];
$user_role = $_SESSION['user_role'];

if ($user_id == NULL || $security_key == NULL) {
    header('Location: index.php');
    exit();
}

// Handle form submission to create a group
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_group'])) {
    $group_name = $_POST['group_name'];
    
    if (!empty($group_name)) {
        // Update the logged-in user's group in the database
        $stmt = $pdo->prepare("UPDATE tbl_admin SET user_group = :user_group WHERE user_id = :user_id");
        $stmt->execute([
            'user_group' => $group_name,
            'user_id' => $user_id
        ]);

        echo "<div class='alert alert-success'>Group created successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Please provide a group name.</div>";
    }
}

$page_name = "Create Group";
include("include/sidebar.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_name; ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
<div class="container">
    <h3><?php echo $page_name; ?></h3>
    <form method="POST" action="">
        <div class="form-group">
            <label for="group_name">Group Name:</label>
            <input type="text" name="group_name" id="group_name" class="form-control" required>
        </div>
        <button type="submit" name="create_group" class="btn btn-primary">Create Group</button>
    </form>
</div>

<?php include("include/footer.php"); ?>

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>

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
$user_role = $_SESSION['user_role'];
if ($user_id == NULL || $security_key == NULL) {
    header('Location: index.php');
    exit();
}

// Get the user's course and group from the database
$stmt = $pdo->prepare("SELECT user_course, user_group FROM tbl_admin WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

$user_course = $user_info['user_course'];
$user_group = $user_info['user_group'];

// Fetch members of the same group
$group_members_sql = "SELECT user_id, fullname FROM tbl_admin WHERE user_course = :user_course AND user_group = :user_group AND user_id != :user_id";
$group_members_stmt = $pdo->prepare($group_members_sql);
$group_members_stmt->execute([
    'user_course' => $user_course,
    'user_group' => $user_group,
    'user_id' => $user_id // Exclude the logged-in user from the list
]);

$page_name = "Group Members";
include("include/sidebar.php");

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="row">
    <div class="col-md-12">
        <div class="well well-custom">
            <center><h3>Group Members</h3></center>
            <div class="gap"></div>

            <div class="table-responsive">
                <table class="table table-condensed display" id="example" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Member Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $serial = 1;
                        $num_row = $group_members_stmt->rowCount();
                        if ($num_row == 0) {
                            echo '<tr><td colspan="2">No Members found in your group</td></tr>';
                        } else {
                            while ($row = $group_members_stmt->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <tr>
                                    <td><?php echo $serial; $serial++; ?></td>
                                    <td><?php echo $row['fullname']; ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include("include/footer.php"); ?>

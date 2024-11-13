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
$stmt = $pdo->prepare("SELECT user_course, user_group, fullname FROM tbl_admin WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

$user_course = $user_info['user_course'];
$user_group = $user_info['user_group'];

$page_name = "Group Members";
include("include/sidebar.php");

?>

<div class="row">
    <div class="col-md-12">
        <div class="well well-custom">
            <center><h3>Group Members</h3></center>
            <div class="gap"></div>

            <!-- Display Success/Error Messages -->
            <?php if (isset($_GET['status'])): ?>
                <?php if ($_GET['status'] == 'added'): ?>
                    <div class="alert alert-success">Member added successfully!</div>
                <?php elseif ($_GET['status'] == 'error'): ?>
                    <div class="alert alert-danger">Error adding member. Please try again.</div>
                <?php elseif ($_GET['status'] == 'join_requested'): ?>
                    <div class="alert alert-info">Join request sent successfully!</div>
                <?php elseif ($_GET['status'] == 'member_removed'): ?>
                    <div class="alert alert-success">Member removed successfully!</div>
                <?php endif; ?>
            <?php endif; ?>
            
            <div class="text-left mb-3">
                <?php if (empty($user_group)): ?>
                    <a href="create_group.php" class="btn btn-success btn-menu">Create Group</a>
                    <a href="join_group.php" class="btn btn-info btn-menu">Join Group</a>
                <?php else: ?>
                    <a href="add_member.php" class="btn btn-primary btn-menu">Add Member</a>
                <?php endif; ?>
            </div>
        

            
            <!-- Group Members Table -->
            <?php if (!empty($user_course) && !empty($user_group)): ?>
                <div class="table-responsive">
                    <table class="table table-condensed display" id="example" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Member Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Display the logged-in user as the first row
                            echo "<tr>
                                    <td>1</td>
                                    <td>{$user_info['fullname']} (You)</td>
                                    <td>Logged-in User</td>
                                    <td></td>
                                  </tr>";
                            $serial = 2;

                            // Fetch other members of the same group
                            $group_members_sql = "SELECT user_id, fullname FROM tbl_admin WHERE user_course = :user_course AND user_group = :user_group AND user_id != :user_id";
                            $group_members_stmt = $pdo->prepare($group_members_sql);
                            $group_members_stmt->execute([
                                'user_course' => $user_course,
                                'user_group' => $user_group,
                                'user_id' => $user_id
                            ]);

                            // Display other group members
                            $num_row = $group_members_stmt->rowCount();
                            if ($num_row == 0) {
                                echo '<tr><td colspan="4">No Other Members found in your group</td></tr>';
                            } else {
                                while ($row = $group_members_stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>
                                            <td>{$serial}</td>
                                            <td>{$row['fullname']}</td>
                                            <td>Member</td>
                                            <td>
                                                <form method='post' action='remove_member.php' style='display:inline;'>
                                                    <input type='hidden' name='user_id' value='{$row['user_id']}'>
                                                    <button type='submit' class='btn btn-danger btn-sm' title='Remove Member'>
                                                        <i class='glyphicon glyphicon-trash'></i>
                                                    </button>
                                                </form>
                                            </td>
                                          </tr>";
                                    $serial++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    You are not assigned to any group. You can either create a new group or join an existing one.
                </div>
            <?php endif; ?>

            

            <!-- Pending Join Requests -->
            <?php if (!empty($user_group)): ?>
                <center><h3>Pending Join Group Requests</h3></center>
                <div class="table-responsive">
                    <table class="table table-condensed display" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User Name</th>
                                <th>Requested Group</th>
                                <th>Requested At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch pending requests for the user's group
                            $requests_sql = "SELECT g.request_id, u.fullname, g.user_group, g.requested_at 
                                             FROM group_join_requests g 
                                             JOIN tbl_admin u ON g.user_id = u.user_id 
                                             WHERE g.user_group = :user_group AND g.status = 'Pending'";
                            $requests_stmt = $pdo->prepare($requests_sql);
                            $requests_stmt->execute(['user_group' => $user_group]);

                            $serial = 1;
                            $num_requests = $requests_stmt->rowCount();
                            if ($num_requests == 0) {
                                echo '<tr><td colspan="5">No pending requests for your group.</td></tr>';
                            } else {
                                while ($request = $requests_stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>
                                            <td>{$serial}</td>
                                            <td>{$request['fullname']}</td>
                                            <td>{$request['user_group']}</td>
                                            <td>{$request['requested_at']}</td>
                                            <td>
                                                <form method='post' action='manage_request.php' style='display:inline;'>
                                                    <input type='hidden' name='request_id' value='{$request['request_id']}'>
                                                    <button type='submit' name='action' value='approve' class='btn btn-success btn-sm'>Approve</button>
                                                    <button type='submit' name='action' value='reject' class='btn btn-danger btn-sm'>Reject</button>
                                                </form>
                                            </td>
                                          </tr>";
                                    $serial++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include("include/footer.php"); ?>

<style>
    .table-responsive {
    overflow-x: auto;
    }
    .btn-menu{
        margin-bottom: 10px !important;
        margin-left: 15px !important;

    }

@media (max-width: 576px) {
    .btn-menu {
        width: 100%;
        margin-left: 0px !important;
    }

    .btn-group {
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }
}
</style>

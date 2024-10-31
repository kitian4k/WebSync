<?php
// Authentication and session management
require 'authentication.php'; // admin authentication check

// Auth check
$user_id = $_SESSION['admin_id']; // Logged-in user ID
$user_name = $_SESSION['name'];
$security_key = $_SESSION['security_key'];
if ($user_id == NULL || $security_key == NULL) {
    header('Location: index.php');
    exit();
}

// Check user role (1 = Admin, 2 = Student)
$user_role = $_SESSION['user_role']; // Assume 1 for Admin, 2 for Students

// Database connection
$host = 'localhost';
$db = 'taskmatic';  // Ensure this is the correct database name
$user = 'root';  // Default XAMPP username
$pass = '';  // Default XAMPP password (empty)
$dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Connection successful
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Fetch tasks based on role
if ($user_role == 1) {
    // Admin: Fetch all tasks
    $totalTasks = $pdo->query("SELECT COUNT(*) FROM task_info")->fetchColumn();
    $completedTasks = $pdo->query("SELECT COUNT(*) FROM task_info WHERE status = 2")->fetchColumn();
    $inProgressTasks = $pdo->query("SELECT COUNT(*) FROM task_info WHERE status = 1")->fetchColumn();
    $incompleteTasks = $pdo->query("SELECT COUNT(*) FROM task_info WHERE status = 0")->fetchColumn();
    $overdueTasks = $pdo->query("SELECT COUNT(*) FROM task_info WHERE status = 0 AND t_end_time < NOW()")->fetchColumn();

    // Fetch details of in-progress tasks for admin
    $inProgressTaskDetails = $pdo->query("SELECT t_title, t_start_time, t_end_time FROM task_info WHERE status = 1")->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Student: Fetch only the tasks assigned to them
    $totalTasks = $pdo->query("SELECT COUNT(*) FROM task_info WHERE t_user_id = $user_id")->fetchColumn();
    $completedTasks = $pdo->query("SELECT COUNT(*) FROM task_info WHERE status = 2 AND t_user_id = $user_id")->fetchColumn();
    $inProgressTasks = $pdo->query("SELECT COUNT(*) FROM task_info WHERE status = 1 AND t_user_id = $user_id")->fetchColumn();
    $incompleteTasks = $pdo->query("SELECT COUNT(*) FROM task_info WHERE status = 0 AND t_user_id = $user_id")->fetchColumn();
    $overdueTasks = $pdo->query("SELECT COUNT(*) FROM task_info WHERE status = 0 AND t_user_id = $user_id AND t_end_time < NOW()")->fetchColumn();

    // Fetch details of in-progress tasks for the student
    $inProgressTaskDetails = $pdo->query("SELECT t_title, t_start_time, t_end_time FROM task_info WHERE status = 1 AND t_user_id = $user_id")->fetchAll(PDO::FETCH_ASSOC);
}

// Page name for the dashboard
$page_name = "Task Dashboard";
include("include/sidebar.php");  // This includes your header and sidebar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="row">
    <div class="col-md-12">
        <div class="well well-custom">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar inclusion -->
            <div class="col-md-1">
                <!-- Sidebar comes from the include/sidebar.php file -->
            </div>

            <!-- Main content area for the dashboard -->
            <div class="col-md-10">
                <div class="container mt-5">
                    <div class="row">
                        <!-- Completed Tasks -->
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Done</h5>
                                    <h2><?php echo $completedTasks; ?></h2> <!-- PHP injects completed tasks -->
                                    <p>Task Count</p>
                                </div>
                            </div>
                        </div>

                        <!-- In Progress Tasks -->
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">In Progress Tasks</h5>
                                    <h2><?php echo $inProgressTasks; ?></h2> <!-- PHP injects in-progress tasks -->
                                    <p>Task Count</p>
                                </div>
                            </div>
                        </div>

                        <!-- Incomplete Tasks -->
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">To-do</h5>
                                    <h2><?php echo $incompleteTasks; ?></h2> <!-- PHP injects incomplete tasks -->
                                    <p>Task Count</p>
                                </div>
                            </div>
                        </div>

                        <!-- Overdue Tasks -->
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Overdue Tasks</h5>
                                    <h2><?php echo $overdueTasks; ?></h2> <!-- PHP injects overdue tasks -->
                                    <p>Task Count</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Task Completion Status Pie Chart and In-Progress Task List -->
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <canvas id="taskCompletionChart"></canvas>
                        </div>

                        <!-- In Progress Task List -->
                        <div class="col-md-6">
                            <h4>In Progress Tasks</h4>
                            <ul class="list-group">
                                <?php foreach ($inProgressTaskDetails as $task) { ?>
                                    <li class="list-group-item">
                                        <strong><?php echo $task['t_title']; ?></strong><br>
                                        Start: <?php echo $task['t_start_time']; ?><br>
                                        End: <?php echo $task['t_end_time']; ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- Chart.js setup -->
<script>
    var ctx = document.getElementById('taskCompletionChart').getContext('2d');
    var taskCompletionChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Done', 'In Progress', 'To-do'],
            datasets: [{
                data: [
                    <?php echo $completedTasks; ?>, 
                    <?php echo $inProgressTasks; ?>, 
                    <?php echo $incompleteTasks; ?>
                ], // PHP data passed to the chart
                backgroundColor: ['#36A2EB', '#FFCE56', '#FF6384'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>

</body>

<?php

include("include/footer.php");
?>

</html>

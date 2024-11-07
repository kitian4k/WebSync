<?php
require 'authentication.php'; // admin authentication check

// Auth check
$user_id = $_SESSION['admin_id'];
$user_name = $_SESSION['name'];
$security_key = $_SESSION['security_key'];
$user_course = $_SESSION['user_course'];
$user_group = $_SESSION['user_group'];

if ($user_id == NULL || $security_key == NULL) {
    header('Location: index.php');
    exit();
}

// Check user role (1 = Admin, 2 = Student)
$user_role = $_SESSION['user_role'];

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

// Initialize default values for task counts
$taskCounts = [
    'total' => 0,
    'completed' => 0,
    'in_progress' => 0,
    'incomplete' => 0,
    'overdue' => 0
];

// Fetch tasks based on user group and course
$sql = "SELECT COUNT(*) as total,
               SUM(status = 2) as completed,
               SUM(status = 1) as in_progress,
               SUM(status = 0) as incomplete,
               SUM(status = 0 AND t_end_time < NOW()) as overdue
        FROM task_info
        INNER JOIN tbl_admin ON task_info.t_user_id = tbl_admin.user_id
        WHERE tbl_admin.user_course = :user_course AND tbl_admin.user_group = :user_group";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_course' => $user_course, 'user_group' => $user_group]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// If query returned results, update $taskCounts
if ($result) {
    $taskCounts = $result;
}

// Fetch details of in-progress tasks
$inProgressTaskDetails = $pdo->prepare("SELECT t_title, t_start_time, t_end_time 
                                        FROM task_info 
                                        INNER JOIN tbl_admin ON task_info.t_user_id = tbl_admin.user_id
                                        WHERE tbl_admin.user_course = :user_course 
                                        AND tbl_admin.user_group = :user_group 
                                        AND task_info.status = 1");
$inProgressTaskDetails->execute(['user_course' => $user_course, 'user_group' => $user_group]);
$inProgressTasks = $inProgressTaskDetails->fetchAll(PDO::FETCH_ASSOC) ?: [];

$page_name = "Task Dashboard";
include("include/sidebar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .dashboard-card {
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background: #fff;
            margin: 10px;
        }
        .dashboard-container {
            padding: 20px;
        }
        .chart-container {
            margin-top: 20px;
        }
        .chart-container canvas {
            max-width: 100%;
            height: auto;
        }
        .task-list {
            max-height: 250px;
            overflow-y: auto;
        }
        .main-content {
            margin-left: 250px; /* Adjust based on sidebar width */
            padding: 20px;
        }
        .task-list {
        max-height: 250px; /* Adjust height as needed */
        overflow-y: auto;
         }
    </style>

</head>
<body>
<div class="row">
    <div class="col-md-12">
        <div class="well well-custom">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-1"></div>

                    <!-- Main content area for the dashboard -->
                    <div class="col-md-10">
                    <h2 class="text-center">Dashboard</h2>
                        <div class="dashboard-container">
                            <div class="row text-center my-4">
                                <div class="col-md-3">
                                    <div class="dashboard-card">
                                        <h5>Done</h5>
                                        <h2><?php echo $taskCounts['completed']; ?></h2>
                                        <p>Tasks Completed</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="dashboard-card">
                                        <h5>In Progress</h5>
                                        <h2><?php echo $taskCounts['in_progress']; ?></h2>
                                        <p>Tasks In Progress</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="dashboard-card">
                                        <h5>To-do</h5>
                                        <h2><?php echo $taskCounts['incomplete']; ?></h2>
                                        <p>Tasks To-do</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="dashboard-card">
                                        <h5>Overdue</h5>
                                        <h2><?php echo $taskCounts['overdue']; ?></h2>
                                        <p>Tasks Overdue</p>
                                    </div>
                                </div>

                            <!-- Task Completion Status Pie Chart and In-Progress Task List -->

                            <div class="col-md-6">
                                <div class="dashboard-card">
                                    <h4>In Progress Tasks</h4>
                                    <ul class="list-group task-list">
                                        <?php foreach ($inProgressTasks as $task) { ?>
                                            <li class="list-group-item">
                                                <strong><?php echo htmlspecialchars($task['t_title']); ?></strong><br>
                                                Start: <?php echo htmlspecialchars($task['t_start_time']); ?><br>
                                                End: <?php echo htmlspecialchars($task['t_end_time']); ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                        </div>
                            <div class="row mt-5">
                                <div class="col-md-6">
                                <div class="dashboard-card">
                                    <canvas id="taskCompletionChart"></canvas>
                                 </div>
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
                    <?php echo $taskCounts['completed']; ?>, 
                    <?php echo $taskCounts['in_progress']; ?>, 
                    <?php echo $taskCounts['incomplete']; ?>
                ],
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

<?php include("include/footer.php"); ?>
</html>

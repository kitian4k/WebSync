<?php
require 'authentication.php'; // admin authentication check

// auth check
$user_id = $_SESSION['admin_id'];
$user_name = $_SESSION['name'];
$security_key = $_SESSION['security_key'];
$user_course = $_SESSION['user_course']; // assuming this is set in session after login
$user_group = $_SESSION['user_group'];   // assuming this is set in session after login

if ($user_id == NULL || $security_key == NULL) {
    header('Location: index.php');
    exit();
}

if (isset($_POST['add_task'])) {
    // Add task logic (assuming $obj_admin->add_new_task adds a task and returns true/false)
    $result = $obj_admin->add_new_task($_POST);
    if ($result) {
        header('Location: task-info.php');
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error adding task. Please try again.</div>";
    }
}


$page_name = "Assign New Task";
include("include/sidebar.php");
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="row">
    <div class="col-md-12">
        <div class="well well-custom">
            <div class="row">
                <h3 class="" style="padding: 7px;">Add New Task</h3><br>

                <div class="row">
                    <div class="col-md-12">
                        <form class="row" role="form" action="" method="post" autocomplete="off">
                            <div class="form-group col-md-6">
                                <label class="control-label">Task Title</label>
                                <input type="text" placeholder="Task Title" id="task_title" name="task_title" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Task Description</label>
                                <textarea name="task_description" id="task_description" placeholder="Task Description" class="form-control" rows="5" required></textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Start Time</label>
                                <input type="text" name="t_start_time" id="t_start_time" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">End Time</label>
                                <input type="text" name="t_end_time" id="t_end_time" class="form-control" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Assign To</label>

                                <?php 
                                // Fetch only users from the same user_course and user_group
                                $sql = "SELECT user_id, fullname 
                                        FROM tbl_admin 
                                        WHERE user_course = :user_course AND user_group = :user_group";
                                $stmt = $obj_admin->db->prepare($sql);
                                $stmt->bindParam(':user_course', $user_course);
                                $stmt->bindParam(':user_group', $user_group);
                                $stmt->execute();
                                ?>

                                <select class="form-control" name="assign_to" id="assign_to">
                                    <option value="">Select</option>

                                    <?php while($rows = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                                    <option value="<?php echo $rows['user_id']; ?>">
                                        <?php echo $rows['fullname']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Status</label>
                                
                                <select class="form-control" name="status" id="status">
                                    <option value="0">To-do</option>
                                    <option value="1">In Progress</option>
                                    <option value="2">Done</option>
                                </select>
                            </div>

                            <div class="form-group col-md-12">
                                <button type="submit" name="add_task" class="btn btn-primary">Add Task</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script type="text/javascript">
    flatpickr('#t_start_time', {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });

    flatpickr('#t_end_time', {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });
</script>

<?php
include("include/footer.php");
?>

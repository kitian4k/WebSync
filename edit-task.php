<?php

require 'authentication.php'; // admin authentication check 

// auth check
$user_id = $_SESSION['admin_id'];
$user_name = $_SESSION['name'];
$security_key = $_SESSION['security_key'];
$user_course = $_SESSION['user_course'];
$user_group = $_SESSION['user_group'];

if ($user_id == NULL || $security_key == NULL) {
    header('Location: index.php');
    exit();
}

$user_role = $_SESSION['user_role'];
$task_id = $_GET['task_id'];

if (isset($_POST['update_task_info'])) {
    $obj_admin->update_task_info($_POST, $task_id, $user_role);
}

$page_name = "Edit Task";
include("include/sidebar.php");

// Fetch task info
$sql = "SELECT * FROM task_info WHERE task_id = ?";
$info_stmt = $obj_admin->db->prepare($sql);  // Directly access $db from Admin_Class
$info_stmt->execute([$task_id]);
$row = $info_stmt->fetch(PDO::FETCH_ASSOC);

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="row">
    <div class="col-md-12">
        <div class="well well-custom">
            <div class="row">
                <h3 style="padding: 7px;">Edit Task</h3><br>

                <div class="row">
                    <div class="col-md-12">
                        <form class="row" role="form" action="" method="post" autocomplete="off">
                            <div class="form-group col-md-6">
                                <label class="control-label">Task Title</label>
                                <input type="text" placeholder="Task Title" id="task_title" name="task_title" class="form-control" value="<?php echo htmlspecialchars($row['t_title']); ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Task Description</label>
                                <textarea name="task_description" id="task_description" placeholder="Task Description" class="form-control" rows="5" required><?php echo htmlspecialchars($row['t_description']); ?></textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Start Time</label>
                                <input type="text" name="t_start_time" id="t_start_time" class="form-control" value="<?php echo $row['t_start_time']; ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">End Time</label>
                                <input type="text" name="t_end_time" id="t_end_time" class="form-control" value="<?php echo $row['t_end_time']; ?>" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Assign To</label>

                                <?php
                                // Fetch users within the same user_course and user_group
                                $sql = "SELECT user_id, fullname FROM tbl_admin WHERE user_course = ? AND user_group = ?";
                                $info_stmt = $obj_admin->db->prepare($sql);
                                $info_stmt->execute([$user_course, $user_group]);
                                ?>
                                <select class="form-control" name="assign_to" id="assign_to">
                                    <option value="">Select</option>
                                    <?php while ($rows = $info_stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <option value="<?php echo $rows['user_id']; ?>" <?php if ($rows['user_id'] == $row['t_user_id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($rows['fullname']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Status</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="0" <?php if ($row['status'] == 0) echo 'selected'; ?>>To-do</option>
                                    <option value="1" <?php if ($row['status'] == 1) echo 'selected'; ?>>In Progress</option>
                                    <option value="2" <?php if ($row['status'] == 2) echo 'selected'; ?>>Done</option>
                                </select>
                            </div>

                            <div class="form-group col-md-12">
                                <button type="submit" name="update_task_info" class="btn btn-primary">Update Now</button>
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



<?php

require 'authentication.php'; // admin authentication check 

// auth check
$user_id = $_SESSION['admin_id'];
$user_name = $_SESSION['name'];
$security_key = $_SESSION['security_key'];
if ($user_id == NULL || $security_key == NULL) {
    header('Location: login_header.php');
}

// check admin role
$user_role = $_SESSION['user_role'];

if(isset($_GET['delete_task'])){
  $action_id = $_GET['task_id'];
  
  $sql = "DELETE FROM task_info WHERE task_id = :id";
  $sent_po = "task-info.php";
  $obj_admin->delete_data_by_this_method($sql,$action_id,$sent_po);
}

$page_name="Task_Info";
include("include/sidebar.php");

?>

<div class="row">
  <div class="col-md-12">
    <div class="well well-custom">
      <div class="gap"></div>
      <div class="row">
        <div class="col-md-8">
          <div class="btn-group">
            <!-- Assign New Task button will redirect to assign_new_task.php -->
            <?php if($user_role == 1){ ?>
            <a href="assign_new-task.php" class="btn btn-primary btn-menu">Add New Task</a>
            <?php } ?>
          </div>
        </div>
      </div>
      <center><h3>Task Management Section</h3></center>
      <div class="gap"></div>
      <div class="table-responsive">
        <table class="table table-condensed display" id="example" style="width:100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Task Title</th>
              <th>Assigned To</th>
              <th>Start Time</th>
              <th>End Time</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if($user_role == 1){
              $sql = "SELECT a.*, b.fullname 
                      FROM task_info a
                      INNER JOIN tbl_admin b ON(a.t_user_id = b.user_id)
                      ORDER BY a.task_id DESC";
            } else {
              $sql = "SELECT a.*, b.fullname 
                      FROM task_info a
                      INNER JOIN tbl_admin b ON(a.t_user_id = b.user_id)
                      WHERE a.t_user_id = $user_id
                      ORDER BY a.task_id DESC";
            }

            $info = $obj_admin->manage_all_info($sql);
            $serial  = 1;
            $num_row = $info->rowCount();
            if($num_row == 0){
              echo '<tr><td colspan="7">No Data found</td></tr>';
            }
            while($row = $info->fetch(PDO::FETCH_ASSOC)){
            ?>
              <tr>
                <td><?php echo $serial++; ?></td>
                <td><?php echo $row['t_title']; ?></td>
                <td><?php echo $row['fullname']; ?></td>
                <td><?php echo $row['t_start_time']; ?></td>
                <td><?php echo $row['t_end_time']; ?></td>
                <td>
                  <?php if($row['status'] == 1){
                      echo "<span class='label label-warning'><i class='glyphicon glyphicon-refresh'></i> In Progress</span>";
                  } elseif($row['status'] == 2){
                      echo "<span class='label label-success'><i class='glyphicon glyphicon-ok'></i> Done </span>";
                  } else {
                      echo "<span class='label label-danger'><i class='glyphicon glyphicon-remove'></i> To-do </span>";
                  } ?>
                </td>
                <td>
                  <a title="Update Task" href="edit-task.php?task_id=<?php echo $row['task_id'];?>" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-edit"></span></a>
                  <a title="View" href="task-details.php?task_id=<?php echo $row['task_id']; ?>" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-folder-open"></span></a>
                  <?php if($user_role == 1){ ?>
                  <a class="btn btn-danger btn-sm" title="Delete" href="?delete_task=delete_task&task_id=<?php echo $row['task_id']; ?>" onclick="return confirm('Are you sure?');"><span class="glyphicon glyphicon-trash"></span></a>
                  <?php } ?>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php
include("include/footer.php");
?>

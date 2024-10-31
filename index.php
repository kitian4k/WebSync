<?php
require 'authentication.php'; // admin authentication check 

// auth check
if(isset($_SESSION['admin_id'])){
  $user_id = $_SESSION['admin_id'];
  $user_name = $_SESSION['admin_name'];
  $security_key = $_SESSION['security_key'];
  if ($user_id != NULL && $security_key != NULL) {
    header('Location: task-info.php');
  }
}

if(isset($_POST['login_btn'])){
 $info = $obj_admin->admin_login_check($_POST);
}

$page_name="Login";
include("include/login_header.php");

?>
<div class="ad-auth-wrapper">
    <div class="well col-md-6" style="position:relative;">
        <div class="row">
            <div class="col-md-6">
                <img src="assets/img/WebSync_Banner.png" width="90%" style="margin-top: 60px;">
            </div>
            <div class="col-md-6">
                <form class="row" action="" method="POST">
                    <div class="form-heading">
                        <h2 class="text-left">Welcome to Websync!</h2>
                    </div>
                    <form class="mt-4" action="" method="POST">
            <?php if(isset($info)){ ?>
            <div class="alert alert-danger text-center"><?php echo $info; ?></div>
            <?php } ?>
            
            <!-- Username Textbox -->
            <div class="input-group form-group mb-3">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
				<input type="text" class="form-control" name="username" placeholder="Username" 
                               pattern="TUPM-\d{2}-\d{4}" 
                               title="Username must be in the format TUPM-##-####" 
                               required>
                    </div>
            
            <!-- Password Textbox -->
            <div class="input-group form-group mb-3">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input type="password" class="form-control" name="admin_password" placeholder="Password">
            </div>
            
            <!-- Text Links Aligned Side by Side -->
			<div class="d-flex justify-content-between mb-3">
			<a href="forgot-password.php" class="text-secondary">Forgot Password?</a>
			<span> <p1 class="text-secondary">Don't have an account? </p1>
				<a href="index_register.php" class="sign-up-link">Sign up</a>
			</span>
		</div>

            <!-- Login Button -->
			<div class="text-center" style="margin-top: 20px;">
				<button type="submit" name="login_btn" class="btn btn-primary">Log In</button>
			</div>
        </form>
    </div>
</div>

<style>
    .ad-auth-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .well {
        background-color: #f9f9f9;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .text-secondary {
        color: #6c757d;
        text-decoration: none;
    }

    .d-flex {
        display: flex;
    }

    .justify-content-between {
        justify-content: space-between;
    }
</style>

<?php

include("include/footer.php");

?>

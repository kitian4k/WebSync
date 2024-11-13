<?php
require 'authentication.php';

if (isset($_POST['register_btn'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $user_course = $_POST['user_course'];
    $user_group = $_POST['user_group'];

    // Check if fullname, username, or email already exists
    if ($obj_admin->checkUserExists($fullname, $username, $email)) {
        $errorMessage = "Full name, username, or email already exists. Please try with different details.";
    } else {
        // Pass new fields to the registration method
        $obj_admin->admin_register([
            'fullname' => $fullname,
            'username' => $username,
            'email' => $email,
            'user_course' => $user_course,
            'user_group' => $user_group,
            'password' => $_POST['password']
        ]);
        header('Location: index.php');
        exit();
    }
}

$page_name = "Register";
include("include/login_header.php");
?>

<div class="ad-auth-wrapper">
    <div class="well col-md-6" style="position:relative;">
        <div class="row">
            <div class="col-md-6">
                <img src="assets/img/WebSync_Banner.png" width="90%" style="margin-top: 170px;" class="logo-image">
            </div>
            <div class="col-md-6">
                <form class="row" action="" method="POST" onsubmit="return validateForm()">
                    <div class="form-heading">
                        <h2 class="text-left">Create an Account</h2>
                    </div>

                    <?php if (isset($errorMessage)) { ?>
                            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                    <?php } ?>

                    <!-- Full Name Textbox -->
                    <div class="input-group form-group mb-3">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input type="text" class="form-control" name="fullname" placeholder="Full Name" required>
                    </div>

                    <!-- Username Textbox with Pattern -->
                    <div class="input-group form-group mb-3">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input type="text" class="form-control" name="username" placeholder="TUP ID Number" 
                            pattern="TUPM-\d{2}-\d{4}" 
                            title="Username must be in the format TUPM-XX-XXXX" 
                            required>
                    </div>

                    <!-- Email Input -->
                    <div class="input-group form-group mb-3">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                        <input type="email" class="form-control" name="email" placeholder="TUP Email Address" 
                            pattern="^[a-zA-Z0-9._%+-]+@tup\.edu\.ph$" 
                            title="Email must be in the format yourname@tup.edu.ph" 
                            required>
                    </div>

                    <!-- Course Dropdown -->
                    <div class="input-group form-group mb-3">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-education"></i></span>
                        <select class="form-control" name="user_course" required>
                            <option value="" disabled selected>Select Course</option>
                            <option value="BSIE-ICT">BSIE-ICT</option>
                            <option value="BTVTED-Compro">BTVTED-Compro</option>
                            <option value="BTVTED-EST">BTVTED-EST</option>
                        </select>
                    </div>

                    
                    <!-- Password Textbox -->
                    <div class="input-group form-group mb-3">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>

                    <!-- Links and Register Button -->
                    <div class="link-container">
                        <span>Already have an account? <a href="index.php" class="sign-up-link">Log In</a></span>
                    </div>

                    <div class="text-center" style="margin-top: 20px;">
                        <button type="submit" name="register_btn" class="btn btn-primary" id="register-btn">Register</button>
                    </div>
                </form>
            </div>
        </div>
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

    /* Responsive Design */
    @media (max-width: 768px) {
        .logo-image {
        margin-top: 0 !important;
        }
    }
</style>



<?php
require 'authentication.php';

if (isset($_POST['reset_password_btn'])) {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    $repeat_password = $_POST['repeat_password'];

    // Check if the new passwords match
    if ($new_password !== $repeat_password) {
        $errorMessage = "New passwords do not match.";
    } else {
        // Check if username exists and update the password
        $userExists = $obj_admin->checkUserByUsername($username);
        if ($userExists) {
            $obj_admin->resetPassword($username, $new_password);
            $successMessage = "Password reset successful! You can now <a href='index.php'>Log In</a>.";
        } else {
            $errorMessage = "Username does not exist.";
        }
    }
}

$page_name = "Forgot Password";
include("include/login_header.php");
?>

<div class="ad-auth-wrapper">
    <div class="well col-md-6" style="position:relative;">
        <div class="row">
            <div class="col-md-6">
                <img src="assets/img/WebSync_Banner.png" width="90%" style="margin-top: 70px;" class="logo-image">
            </div>
            <div class="col-md-6">
                <form class="row" action="" method="POST">
                    <form class="mt-4" action="" method="POST">
            <h2 class="text-left">Forgot Password</h2>

            <?php if (isset($errorMessage)) { ?>
                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
            <?php } ?>
            <?php if (isset($successMessage)) { ?>
                <div class="alert alert-success"><?php echo $successMessage; ?></div>
            <?php } ?>

            <!-- Username -->
            <div class="input-group form-group mb-3">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>

            <!-- New Password -->
            <div class="input-group form-group mb-3">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input type="password" class="form-control" name="new_password" placeholder="New Password" required>
            </div>

            <!-- Repeat New Password -->
            <div class="input-group form-group mb-3">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat New Password" required>
            </div>

             <!-- Links and Register Button -->
             <div class="link-container">
                <span>Already have an account? <a href="index.php" class="sign-up-link">Log In</a></span>
            </div>

            <button type="submit" name="reset_password_btn" class="btn btn-primary">Reset Password</button>
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

    .link-container {   
        margin-bottom: 10px;
    }

     /* Responsive Design */
     @media (max-width: 768px) {
        .form-heading h2 {
            font-size: 1.5rem;
        }
        
        .well {
            padding: 15px;
        }

        .link-container {
        flex-direction: column;
        align-items: center;
        text-align: center;
        }
        .link-container a,
        .link-container span {
            margin-bottom: 8px; /* Optional spacing for mobile */
        }
        .logo-image {
        margin-top: 0 !important;
        }

    }



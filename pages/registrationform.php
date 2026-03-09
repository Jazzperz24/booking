<?php
// pages/registrationform.php
$page = 'registrationform';
$base = '../';
require '../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Kaya Pa?</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/stylemeow.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/navbar.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/registrationform.css">
</head>
<body>

<?php require '../includes/navbar.php'; ?>

<div class="register-page">
    <div class="register-card">

        <div class="card-tag">&#10022; Join Us</div>
        <h1>Create Your <span>Account</span></h1>
        <p class="card-subtitle">Fill in your details below to get started with Kaya Pa?</p>

        <form id="registerForm" action="" method="post" novalidate>

            <div class="form-row">
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" id="firstname" name="firstname" placeholder="e.g. Maria" required>
                    <span class="field-error" id="error-firstname">Must start with a capital letter.</span>
                </div>
                <div class="form-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="lastname" placeholder="e.g. Santos" required>
                    <span class="field-error" id="error-lastname">Must start with a capital letter.</span>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" placeholder="09XXXXXXXXX" required
                       pattern="[0-9]+" inputmode="numeric" maxlength="11">
                <span class="field-error" id="error-phone">Must start with 09 and be 11 digits.</span>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon-wrap">
                    <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                    <i class="fa-solid fa-eye toggle-eye" data-target="password"></i>
                </div>
                <div class="strength-bar-wrap">
                    <span></span><span></span><span></span><span></span>
                </div>
                <span class="strength-label"></span>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-icon-wrap">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat your password" required>
                    <i class="fa-solid fa-eye toggle-eye" data-target="confirm_password"></i>
                </div>
            </div>

            <button type="submit" class="btn-register" id="registerBtn">
                <span class="spinner"></span>
                <span class="btn-text">
                    <i class="fa-solid fa-user-plus"></i> Create Account
                </span>
            </button>

        </form>

        <p class="card-footer-text">
            Already have an account?
            <a href="loginpage.php">Sign in here</a>
        </p>

    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/REGISTRATIONSFORM/assets/scripts/navbar.js"></script>
<script src="/REGISTRATIONSFORM/assets/scripts/registrationform.js"></script>
</body>
</html>
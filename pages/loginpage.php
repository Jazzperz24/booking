<?php

$page = 'login';
$base = '../';
require '../config/config.php';

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kaya Pa?</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/navbar.css">
    <link rel="stylesheet" href="/REGISTRATIONSFORM/assets/css/login.css">
</head>
<body>

<?php require '../includes/navbar.php'; ?>

<div class="login-page">
    <div class="login-card">

        <div class="card-icon">
            <i class="fa-solid fa-dumbbell" style="color:var(--gold)"></i>
        </div>
        <h1>Welcome <span>Back</span></h1>
        <p class="card-subtitle">Sign in to your Kaya Pa? account to book your next session.</p>

        <form id="loginForm" action="" method="post" novalidate>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       placeholder="you@example.com" required>
            </div>

            <div class="form-group">
                <label for="password" style="display:flex;justify-content:space-between;align-items:center">
                    <span>Password</span>
                    
                    <a href="forgotpassword.php"
                       style="font-size:11px;color:var(--muted);text-decoration:none;
                              font-weight:500;letter-spacing:.3px;transition:color .2s"
                       onmouseover="this.style.color='var(--gold)'"
                       onmouseout="this.style.color='var(--muted)'">
                        <i class="fa-solid fa-key" style="margin-right:3px"></i>Forgot password?
                    </a>
                </label>
                <div class="input-icon-wrap">
                    <input type="password" id="password" name="password"
                           placeholder="Your password" required>
                    <i class="fa-solid fa-eye toggle-eye"></i>
                </div>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <span class="spinner"></span>
                <span class="btn-text">
                    <i class="fa-solid fa-right-to-bracket"></i> Sign In
                </span>
            </button>

        </form>

        <div class="divider">or</div>

        <p class="card-footer-text">
            Don't have an account?
            <a href="registrationform.php">Register for free</a>
        </p>

    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/REGISTRATIONSFORM/assets/scripts/navbar.js"></script>
<script src="/REGISTRATIONSFORM/assets/scripts/login.js"></script>
</body>
</html>